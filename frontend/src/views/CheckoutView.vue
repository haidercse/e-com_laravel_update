<script setup>
import { computed, onMounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import { useRouter } from 'vue-router';
import { useCart } from '../services/cart';
import { useAuth } from '../services/auth';
import { customerApi, commerceApi, catalogApi, gatewayApi } from '../services/endpoints';

const router = useRouter();
const cart = useCart();
const auth = useAuth();
const userId = computed(() => auth.user.value?.id || localStorage.getItem('ecom_user_id'));
const addresses = ref([]);
const paymentMethods = ref([]);
const addressId = ref('');
const paymentType = ref('cash_on_delivery');
const coupon = ref('');
const message = ref('');
const busy = ref(false);
const shipping = ref('');
const summary = ref({});
const receiptOrderId = ref('');
const receipt = ref({ name: '', amount: '', trx_id: '', photo: '' });

useHead({ title: 'Checkout — FlixShop' });

onMounted(async () => {
  if (!userId.value || !localStorage.getItem('ecom_access_token')) {
    router.replace({ name: 'account', query: { next: '/checkout' } });
    return;
  }
  await Promise.all([loadAddresses(), loadPaymentMethods(), cart.refresh()]);
  await loadSummary();
});

async function loadSummary() {
  if (!userId.value) return;
  try { const { data } = await commerceApi.cartSummary(userId.value); summary.value = data || {}; }
  catch (problem) { message.value = errorText(problem); }
}

async function loadAddresses() {
  try {
    const response = await customerApi.addresses(userId.value);
    addresses.value = listOf(response.data);
    const preferred = addresses.value.find((address) => Number(address.set_default)) || addresses.value[0];
    addressId.value = preferred ? String(preferred.id) : '';
    if (addressId.value) await setAddress();
  } catch (error) { message.value = errorText(error); }
}

async function loadPaymentMethods() {
  try {
    const response = await catalogApi.paymentTypes();
    paymentMethods.value = listOf(response.data);
    if (paymentMethods.value.length && !paymentMethods.value.some((method) => method.payment_type_key === paymentType.value)) {
      paymentType.value = paymentMethods.value[0].payment_type_key;
    }
  } catch (error) { message.value = errorText(error); }
}

function listOf(payload) {
  if (Array.isArray(payload)) return payload;
  if (Array.isArray(payload?.data)) return payload.data;
  if (Array.isArray(payload?.data?.data)) return payload.data.data;
  return [];
}

function errorText(error) { return error.response?.data?.message || error.message || 'Something went wrong.'; }

async function setAddress() {
  if (!addressId.value) return;
  message.value = '';
  try {
    await commerceApi.setCartAddress({ user_id: userId.value, address_id: addressId.value });
    const { data } = await commerceApi.calculateShipping({ user_id: userId.value, address_id: addressId.value });
    shipping.value = data?.value_string || '';
    await cart.refresh();
    await loadSummary();
  } catch (error) { message.value = errorText(error); }
}

async function applyCoupon() {
  message.value = '';
  try {
    const { data } = await commerceApi.applyCoupon({ user_id: userId.value, coupon_code: coupon.value });
    message.value = data?.message || 'Coupon updated.';
    if (data?.result) { await cart.refresh(); await loadSummary(); }
  } catch (error) { message.value = errorText(error); }
}

async function removeCoupon() {
  try {
    const { data } = await commerceApi.removeCoupon({ user_id: userId.value });
    message.value = data?.message || 'Coupon removed.';
    coupon.value = '';
    await cart.refresh();
    await loadSummary();
  } catch (error) { message.value = errorText(error); }
}

function gatewayFor(paymentKey, payload) {
  const key = String(paymentKey || '').toLowerCase();
  if (key.includes('paypal')) return gatewayApi.paypalUrl(payload);
  if (key.includes('flutterwave')) return gatewayApi.flutterwaveUrl(payload);
  if (key.includes('paytm')) return gatewayApi.paytmPay(payload);
  if (key.includes('bkash')) return gatewayApi.bkashBegin(payload);
  if (key.includes('nagad')) return gatewayApi.nagadBegin(payload);
  if (key.includes('ssl')) return gatewayApi.sslcommerzBegin(payload);
  if (key.includes('stripe')) return gatewayApi.stripe(payload);
  if (key.includes('razor')) return gatewayApi.razorpayStart(payload);
  if (key.includes('paystack')) return gatewayApi.paystackStart(payload);
  if (key.includes('iyzico')) return gatewayApi.iyzicoStart(payload);
  return null;
}

async function placeOrder() {
  if (!cart.items.value.length) return;
  if (!addressId.value) { message.value = 'Choose a shipping address before placing your order.'; return; }
  busy.value = true;
  message.value = '';
  try {
    const cartCheck = await commerceApi.processCart({
      cart_ids: cart.items.value.map((item) => item.id).join(','),
      cart_quantities: cart.items.value.map((item) => item.quantity).join(','),
    });
    if (!cartCheck.data?.result) throw new Error(cartCheck.data?.message || 'Please review the quantities in your cart.');
    const orderPayload = {
      user_id: userId.value,
      payment_type: paymentType.value,
      amount: summary.value.grand_total_value || cart.subtotal.value,
    };
    const key = String(paymentType.value).toLowerCase();
    const paymentMethod = paymentMethods.value.find((item) => item.payment_type_key === paymentType.value);
    let response;
    if (key === 'cash_on_delivery' || key === 'cash_on_delivery_payment') {
      response = await commerceApi.cashOnDelivery(orderPayload);
    } else if (key === 'wallet') {
      response = await commerceApi.walletPayment(orderPayload);
    } else if (key.startsWith('manual_payment_')) {
      response = await commerceApi.manualPayment(orderPayload);
      if (!response.data?.result) throw new Error(response.data?.message || 'Your order could not be placed.');
      receiptOrderId.value = response.data.order_id || '';
      receipt.value.amount = String(orderPayload.amount);
      await cart.refresh();
      message.value = response.data.message || 'Order created. Submit your transfer details below.';
      return;
    } else if (key.includes('offline')) {
      response = await commerceApi.manualPayment(orderPayload);
    } else {
      const orderResult = await commerceApi.placeOrder(orderPayload);
      if (!orderResult.data?.result) throw new Error(orderResult.data?.message || 'Could not create your order.');
      const gatewayPayload = { ...orderPayload, payment_type: 'cart_payment', combined_order_id: orderResult.data.combined_order_id };
      const apiBase = import.meta.env.VITE_API_BASE_URL || '/api/v2';
      const browserGateways = {
        stripe: '/stripe',
        razor: '/razorpay/pay-with-razorpay',
        paystack: '/paystack/init',
        iyzico: '/iyzico/init',
        ssl: '/sslcommerz/begin',
        paytm: '/paytm/payment/pay',
      };
      const browserGateway = Object.entries(browserGateways).find(([needle]) => key.includes(needle));
      if (browserGateway) {
        const target = new URL(`${apiBase}${browserGateway[1]}`, window.location.origin);
        Object.entries(gatewayPayload).forEach(([name, value]) => target.searchParams.set(name, String(value ?? '')));
        window.location.assign(target.toString());
        return;
      }
      const gatewayResponse = await gatewayFor(key, gatewayPayload);
      if (!gatewayResponse) throw new Error(`Online checkout for ${paymentMethod?.name || paymentType.value} is not available.`);
      const result = gatewayResponse.data || {};
      if (result.url) { window.location.assign(result.url); return; }
      if (result.id) { message.value = `Payment session ${result.id} was created. Continue in the payment provider to finish your order.`; return; }
      if (result.result === false) throw new Error(result.message || 'The payment provider could not start checkout.');
      message.value = result.message || 'Your payment request was sent to the provider.';
      return;
    }
    const result = response.data || {};
    if (!result.result) throw new Error(result.message || 'Your order could not be placed.');
    await cart.refresh();
    message.value = result.message || 'Order placed successfully.';
    router.push({ name: 'account', query: { section: 'orders', order: result.combined_order_id } });
  } catch (error) {
    message.value = errorText(error);
  } finally { busy.value = false; }
}

async function submitReceipt() {
  busy.value = true;
  try {
    const { data } = await commerceApi.offlinePayment({
      order_id: receiptOrderId.value,
      payment_option: paymentType.value,
      ...receipt.value,
    });
    if (!data?.result) throw new Error(data?.message || 'Payment details could not be submitted.');
    message.value = data.message || 'Payment details submitted for review.';
    receiptOrderId.value = '';
    router.push({ name: 'account', query: { section: 'orders' } });
  } catch (problem) { message.value = errorText(problem); }
  finally { busy.value = false; }
}

async function uploadReceiptPhoto(event) {
  const file = event.target.files?.[0];
  if (!file) return;
  if (!/^image\/(jpeg|png|webp|gif)$/.test(file.type) || file.size > 3 * 1024 * 1024) {
    message.value = 'Choose an image under 3 MB.';
    event.target.value = '';
    return;
  }
  busy.value = true;
  try {
    const base64 = await new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(String(reader.result).split(',')[1] || '');
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
    const extension = file.name.split('.').pop().toLowerCase().replace(/[^a-z0-9]/g, '') || 'jpg';
    const { data } = await customerApi.uploadImage({ id: userId.value, filename: `payment-${Date.now()}.${extension}`, image: base64 });
    if (!data?.result) throw new Error(data?.message || 'Photo upload failed.');
    receipt.value.photo = data.upload_id;
    message.value = 'Payment proof uploaded.';
  } catch (problem) { message.value = errorText(problem); }
  finally { busy.value = false; event.target.value = ''; }
}

const selectedPayment = computed(() => paymentMethods.value.find((method) => method.payment_type_key === paymentType.value));
</script>

<template>
  <section class="mx-auto max-w-6xl">
    <div class="mb-6">
      <p class="text-xs font-bold uppercase tracking-wider text-brand">FlixShop</p>
      <h1 class="mt-1 text-3xl font-extrabold text-ink">Checkout</h1>
      <p class="mt-2 text-sm text-muted">Confirm delivery details and choose how you would like to pay.</p>
    </div>
    <p v-if="message" class="mb-5 rounded-lg bg-brand-tint px-4 py-3 text-sm text-ink" role="status">{{ message }}</p>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
      <div class="space-y-5">
        <section class="rounded-xl bg-white p-5 shadow-card sm:p-6">
          <div class="flex items-center justify-between"><h2 class="text-lg font-bold text-ink">Delivery address</h2><RouterLink to="/account" class="text-sm font-semibold text-brand">Manage addresses</RouterLink></div>
          <select v-model="addressId" class="mt-4 w-full rounded-lg border border-gray-200 bg-white px-3 py-3 text-sm outline-none focus:border-brand" @change="setAddress">
            <option value="">Select an address</option>
            <option v-for="address in addresses" :key="address.id" :value="String(address.id)">{{ address.address }} — {{ address.city_name }}, {{ address.country_name }}{{ Number(address.set_default) ? ' (Default)' : '' }}</option>
          </select>
          <p v-if="!addresses.length" class="mt-3 text-sm text-muted">Add a shipping address from your account before checkout.</p>
        </section>

        <section class="rounded-xl bg-white p-5 shadow-card sm:p-6">
          <h2 class="text-lg font-bold text-ink">Payment method</h2>
          <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <label v-for="method in paymentMethods" :key="method.payment_type_key" class="flex cursor-pointer items-center gap-3 rounded-lg border p-4 transition" :class="paymentType === method.payment_type_key ? 'border-brand bg-brand-tint' : 'border-gray-200 hover:border-gray-300'">
              <input v-model="paymentType" type="radio" name="payment" :value="method.payment_type_key" class="accent-brand" />
              <span class="min-w-0"><span class="block text-sm font-bold text-ink">{{ method.name || method.title }}</span><span class="mt-1 block text-xs text-muted" v-if="method.payment_type_key?.startsWith('manual_payment_')">Manual payment</span></span>
            </label>
          </div>
          <div v-if="selectedPayment?.details" class="prose prose-sm mt-4 max-w-none rounded-lg bg-gray-50 p-4 text-muted" v-html="selectedPayment.details" />
          <p v-if="!paymentMethods.length" class="mt-3 text-sm text-muted">No payment methods are currently available.</p>
        </section>
      </div>

      <aside class="h-fit rounded-xl bg-white p-5 shadow-card sm:p-6">
        <h2 class="text-lg font-bold text-ink">Order summary</h2>
        <div class="mt-4 max-h-64 space-y-3 overflow-y-auto border-b border-gray-100 pb-4">
          <div v-for="item in cart.items.value" :key="item.id" class="flex justify-between gap-3 text-sm"><span class="text-muted">{{ item.product_name }} × {{ item.quantity }}</span><span class="shrink-0 font-semibold text-ink">{{ item.currency_symbol }}{{ (Number(item.price || 0) * Number(item.quantity || 0)).toFixed(2) }}</span></div>
          <p v-if="!cart.items.value.length" class="text-sm text-muted">Your cart is empty.</p>
        </div>
        <form class="mt-4 flex gap-2" @submit.prevent="applyCoupon"><input v-model.trim="coupon" class="min-w-0 flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm outline-none focus:border-brand" placeholder="Coupon code" aria-label="Coupon code"/><button class="rounded-lg border border-brand px-3 text-sm font-bold text-brand hover:bg-brand-tint">Apply</button></form>
        <button v-if="cart.items.value.some((item) => item.coupon_code)" class="mt-2 text-xs font-semibold text-muted underline" type="button" @click="removeCoupon">Remove coupon</button>
        <div class="mt-5 space-y-2 border-t border-gray-100 pt-4 text-sm">
          <div class="flex justify-between"><span class="text-muted">Subtotal</span><span>{{ summary.sub_total || `${cart.items.value[0]?.currency_symbol || ''}${cart.subtotal.value.toFixed(2)}` }}</span></div>
          <div v-if="summary.tax" class="flex justify-between"><span class="text-muted">Tax</span><span>{{ summary.tax }}</span></div>
          <div class="flex justify-between"><span class="text-muted">Shipping</span><span>{{ summary.shipping_cost || shipping || 'Calculated after address selection' }}</span></div>
          <div v-if="summary.discount" class="flex justify-between"><span class="text-muted">Discount</span><span>-{{ summary.discount }}</span></div>
          <div class="flex justify-between pt-2 text-base font-extrabold"><span>Total</span><span>{{ summary.grand_total || `${cart.items.value[0]?.currency_symbol || ''}${cart.subtotal.value.toFixed(2)}` }}</span></div>
        </div>
        <button class="mt-5 w-full rounded-lg bg-brand px-4 py-3 font-bold text-white transition hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-50" :disabled="busy || !cart.items.value.length || !addressId" type="button" @click="placeOrder">{{ busy ? 'Processing…' : 'Place order' }}</button>
        <form v-if="receiptOrderId" class="mt-5 space-y-3 border-t border-gray-100 pt-5" @submit.prevent="submitReceipt">
          <h3 class="font-bold text-ink">Submit transfer details</h3>
          <label class="block text-xs font-semibold text-ink">Account holder name<input v-model.trim="receipt.name" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-normal" /></label>
          <label class="block text-xs font-semibold text-ink">Amount paid<input v-model.trim="receipt.amount" required inputmode="decimal" class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-normal" /></label>
          <label class="block text-xs font-semibold text-ink">Transaction reference<input v-model.trim="receipt.trx_id" required class="mt-1 w-full rounded-lg border border-gray-200 px-3 py-2 text-sm font-normal" /></label>
          <label class="block text-xs font-semibold text-ink">Payment proof (optional)<input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-xs font-normal text-muted" @change="uploadReceiptPhoto" /><span v-if="receipt.photo" class="mt-1 block text-emerald-700">Photo uploaded</span></label>
          <button class="w-full rounded-lg bg-brand px-4 py-3 text-sm font-bold text-white disabled:opacity-50" :disabled="busy">{{ busy ? 'Submitting…' : 'Submit payment details' }}</button>
        </form>
        <RouterLink to="/" class="mt-3 block text-center text-sm font-semibold text-muted hover:text-brand">Continue shopping</RouterLink>
      </aside>
    </div>
  </section>
</template>
