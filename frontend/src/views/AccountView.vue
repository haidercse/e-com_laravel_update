<script setup>
import { computed, onMounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute, useRouter } from 'vue-router';
import api, { extractList } from '../services/api';
import { useAuth } from '../services/auth';
import { useCart } from '../services/cart';
import { authApi, catalogApi, customerApi, gatewayApi } from '../services/endpoints';

const auth = useAuth();
const route = useRoute();
const router = useRouter();
const cart = useCart();
const mode = ref('login');
const form = ref({ name: '', email: '', password: '', accountType: 'customer' });
const notice = ref('');
const busy = ref(false);
const orders = ref([]);
const addresses = ref([]);
const counters = ref({});
const section = ref('orders');
const profileName = ref('');
const profilePassword = ref('');
const signupUserId = ref(null);
const verificationCode = ref('');
const resetCode = ref('');
const resetPassword = ref('');
const wishlist = ref([]);
const wallet = ref({ balance: '—', last_recharged: '' });
const walletHistory = ref([]);
const walletPaymentTypes = ref([]);
const rechargeOpen = ref(false);
const rechargeAmount = ref('');
const rechargeType = ref('');
const rechargeMessage = ref('');
const clubPoints = ref([]);
const refundRequests = ref([]);
const addressFormOpen = ref(false);
const addressForm = ref({ address: '', country_id: '', state_id: '', city_id: '', postal_code: '', phone: '', latitude: '', longitude: '' });
const countries = ref([]);
const states = ref([]);
const cities = ref([]);
const returnOrderId = ref('');
const orderItems = ref([]);
const refundItemId = ref('');
const refundReason = ref('');
const expandedOrderId = ref('');
const orderDetails = ref(null);
const detailItems = ref([]);
const user = computed(() => auth.user.value);

useHead({ title: computed(() => `${user.value ? 'My account' : 'Sign in'} — FlixShop`) });

onMounted(async () => {
  if (typeof route.query.section === 'string') section.value = route.query.section;
  if (!localStorage.getItem('ecom_access_token')) return;
  try {
    user.value.avatar_original = data.path;
    localStorage.setItem('ecom_user', JSON.stringify(user.value));
    await loadAccountData();
  } catch (error) {
    notice.value = error.response?.data?.message || error.message;
  }
});

async function loadAccountData() {
  const id = user.value?.id || localStorage.getItem('ecom_user_id');
  if (!id) return;
  profileName.value = user.value?.name || '';
  const countryResult = await customerApi.countries().catch(() => null);
  countries.value = countryResult ? safeList(countryResult.data) : [];
  const results = await Promise.allSettled([
    api.get(`/purchase-history/${encodeURIComponent(id)}`),
    api.get(`/user/shipping/address/${encodeURIComponent(id)}`),
    api.get(`/profile/counters/${encodeURIComponent(id)}`),
    customerApi.wishlist(id),
    customerApi.walletBalance(id),
    customerApi.walletHistory(id),
    customerApi.clubPoints(id),
    customerApi.refunds(id),
    catalogApi.paymentTypes({ mode: 'wallet' }),
  ]);
  if (results[0].status === 'fulfilled') orders.value = safeList(results[0].value.data);
  if (results[1].status === 'fulfilled') addresses.value = safeList(results[1].value.data);
  if (results[2].status === 'fulfilled') counters.value = results[2].value.data || {};
  if (results[3].status === 'fulfilled') wishlist.value = safeList(results[3].value.data);
  if (results[4].status === 'fulfilled') wallet.value = results[4].value.data || wallet.value;
  if (results[5].status === 'fulfilled') walletHistory.value = safeList(results[5].value.data);
  if (results[6].status === 'fulfilled') clubPoints.value = safeList(results[6].value.data);
  if (results[7].status === 'fulfilled') refundRequests.value = safeList(results[7].value.data);
  if (results[8].status === 'fulfilled') {
    walletPaymentTypes.value = safeList(results[8].value.data);
    rechargeType.value ||= walletPaymentTypes.value[0]?.payment_type_key || '';
  }
}

async function startWalletRecharge() {
  const amount = Number(rechargeAmount.value);
  if (!Number.isFinite(amount) || amount <= 0 || !rechargeType.value) { rechargeMessage.value = 'Enter an amount and choose a payment method.'; return; }
  busy.value = true;
  rechargeMessage.value = '';
  const key = rechargeType.value.toLowerCase();
  const payload = { payment_type: 'wallet_payment', user_id: user.value.id, amount };
  try {
    const apiBase = import.meta.env.VITE_API_BASE_URL || '/api/v2';
    const browserGateways = { stripe: '/stripe', razor: '/razorpay/pay-with-razorpay', paystack: '/paystack/init', iyzico: '/iyzico/init', ssl: '/sslcommerz/begin', paytm: '/paytm/payment/pay' };
    const browserGateway = Object.entries(browserGateways).find(([needle]) => key.includes(needle));
    if (browserGateway) {
      const target = new URL(`${apiBase}${browserGateway[1]}`, window.location.origin);
      Object.entries(payload).forEach(([name, value]) => target.searchParams.set(name, String(value)));
      window.location.assign(target.toString());
      return;
    }
    let response;
    if (key.includes('paypal')) response = await gatewayApi.paypalUrl(payload);
    else if (key.includes('flutterwave')) response = await gatewayApi.flutterwaveUrl(payload);
    else if (key.includes('bkash')) response = await gatewayApi.bkashBegin(payload);
    else if (key.includes('nagad')) response = await gatewayApi.nagadBegin(payload);
    else throw new Error('This wallet payment method is not supported in the browser.');
    if (response.data?.url) { window.location.assign(response.data.url); return; }
    throw new Error(response.data?.message || 'The payment provider did not return a checkout link.');
  } catch (error) { rechargeMessage.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; }
}

async function loadStates() {
  states.value = [];
  cities.value = [];
  addressForm.value.state_id = '';
  addressForm.value.city_id = '';
  if (!addressForm.value.country_id) return;
  try { const { data } = await customerApi.statesByCountry(addressForm.value.country_id); states.value = safeList(data); }
  catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function loadCities() {
  cities.value = [];
  addressForm.value.city_id = '';
  if (!addressForm.value.state_id) return;
  try { const { data } = await customerApi.citiesByState(addressForm.value.state_id); cities.value = safeList(data); }
  catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function saveAddress() {
  busy.value = true;
  notice.value = '';
  try {
    const { data } = await customerApi.createAddress({ ...addressForm.value, user_id: user.value.id });
    if (!data?.result) throw new Error(data?.message || 'Could not save address.');
    addressFormOpen.value = false;
    addressForm.value = { address: '', country_id: '', state_id: '', city_id: '', postal_code: '', phone: '', latitude: '', longitude: '' };
    addresses.value = safeList((await customerApi.addresses(user.value.id)).data);
    notice.value = data.message || 'Address saved.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; }
}

async function setDefaultAddress(address) {
  try {
    await customerApi.makeDefaultAddress({ id: address.id, user_id: user.value.id });
    addresses.value = safeList((await customerApi.addresses(user.value.id)).data);
    notice.value = 'Default address updated.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function deleteAddress(address) {
  try {
    await customerApi.deleteAddress(address.id);
    addresses.value = safeList((await customerApi.addresses(user.value.id)).data);
    notice.value = 'Address removed.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function removeSaved(item) {
  try {
    await customerApi.removeWishlist(item.id);
    wishlist.value = wishlist.value.filter((entry) => entry.id !== item.id);
    notice.value = 'Item removed from your saved list.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function convertPoints(point) {
  try {
    const { data } = await customerApi.convertClubPoints({ id: point.id, user_id: user.value.id });
    notice.value = data.message || 'Points converted to wallet balance.';
    await loadAccountData();
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function openReturnForm(order) {
  returnOrderId.value = String(order.id);
  orderItems.value = [];
  refundItemId.value = '';
  try {
    const { data } = await customerApi.purchaseItems(order.id);
    orderItems.value = safeList(data).filter((item) => item.refund_button);
    refundItemId.value = orderItems.value[0]?.id ? String(orderItems.value[0].id) : '';
    if (!orderItems.value.length) notice.value = 'This order has no items currently eligible for a refund.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function toggleOrderDetails(order) {
  if (expandedOrderId.value === String(order.id)) { expandedOrderId.value = ''; return; }
  expandedOrderId.value = String(order.id);
  orderDetails.value = null;
  detailItems.value = [];
  try {
    const [detailResponse, itemResponse] = await Promise.all([
      customerApi.purchaseDetails(order.id),
      customerApi.purchaseItems(order.id),
    ]);
    orderDetails.value = safeList(detailResponse.data)[0] || null;
    detailItems.value = safeList(itemResponse.data);
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

async function submitRefund() {
  try {
    const { data } = await customerApi.requestRefund({ id: refundItemId.value, user_id: user.value.id, reason: refundReason.value });
    if (!data?.success) throw new Error(data?.message || 'Could not send refund request.');
    notice.value = data.message || 'Refund request sent.';
    returnOrderId.value = '';
    refundReason.value = '';
    refundRequests.value = safeList((await customerApi.refunds(user.value.id)).data);
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
}

function safeList(payload) {
  try { return extractList(payload); } catch { return []; }
}

async function submitAuth() {
  busy.value = true;
  notice.value = '';
  try {
    if (mode.value === 'login') {
      await auth.login(form.value);
      if (auth.user.value?.type === 'delivery_boy' || auth.user.value?.user_type === 'delivery_boy') {
        await router.replace({ name: 'delivery' });
        return;
      }
      await cart.refresh();
      if (typeof route.query.next === 'string' && route.query.next.startsWith('/') && !route.query.next.startsWith('//')) {
        await router.replace(route.query.next);
        return;
      }
      await loadAccountData();
      notice.value = 'You are signed in.';
    } else {
      const result = await auth.signup(form.value);
      signupUserId.value = result.user_id;
      form.value.password = '';
      mode.value = 'verify';
      notice.value = result.message || 'Account created. Enter the verification code sent to your email.';
    }
  } catch (error) {
    notice.value = error.response?.data?.message || error.message;
  } finally {
    busy.value = false;
  }
}

async function confirmSignup() {
  busy.value = true;
  notice.value = '';
  try {
    const { data } = await api.post('/auth/confirm_code', {
      user_id: signupUserId.value,
      verification_code: verificationCode.value,
    });
    if (!data?.result) throw new Error(data?.message || 'The verification code was not accepted.');
    mode.value = 'login';
    verificationCode.value = '';
    notice.value = data.message || 'Your email is verified. You can sign in now.';
  } catch (error) {
    notice.value = error.response?.data?.message || error.message;
  } finally {
    busy.value = false;
  }
}

async function resendCode() {
  busy.value = true;
  try {
    const { data } = await api.post('/auth/resend_code', { user_id: signupUserId.value, verify_by: 'email' });
    notice.value = data?.message || 'A new verification code has been sent.';
  } catch (error) {
    notice.value = error.response?.data?.message || error.message;
  } finally {
    busy.value = false;
  }
}

async function requestPasswordReset() {
  busy.value = true;
  notice.value = '';
  try {
    const { data } = await authApi.forgetPassword({ email_or_phone: form.value.email, send_code_by: 'email' });
    if (!data?.result) throw new Error(data?.message || 'Could not send reset code.');
    mode.value = 'reset';
    notice.value = data.message || 'A reset code was sent to your email.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; }
}

async function confirmPasswordReset() {
  busy.value = true;
  notice.value = '';
  try {
    const { data } = await authApi.confirmPasswordReset({ verification_code: resetCode.value, password: resetPassword.value });
    if (!data?.result) throw new Error(data?.message || 'Could not reset your password.');
    mode.value = 'login';
    resetCode.value = '';
    resetPassword.value = '';
    notice.value = data.message || 'Password reset. Sign in with your new password.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; }
}

async function resendResetCode() {
  busy.value = true;
  try {
    const { data } = await authApi.resendPasswordCode({ email_or_phone: form.value.email, verify_by: 'email' });
    notice.value = data?.message || 'A new reset code was sent.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; }
}

async function saveProfile() {
  busy.value = true;
  notice.value = '';
  try {
    const { data } = await api.post('/profile/update', {
      id: user.value.id,
      name: profileName.value,
      password: profilePassword.value,
    });
    if (!data?.result) throw new Error(data?.message || 'Profile could not be updated.');
    const updated = { ...user.value, name: profileName.value };
    localStorage.setItem('ecom_user', JSON.stringify(updated));
    await auth.loadUser();
    profilePassword.value = '';
    notice.value = data.message || 'Profile updated.';
  } catch (error) {
    notice.value = error.response?.data?.message || error.message;
  } finally {
    busy.value = false;
  }
}

async function uploadAvatar(event) {
  const file = event.target.files?.[0];
  if (!file) return;
  if (!/^image\/(jpeg|png|webp|gif)$/.test(file.type) || file.size > 2 * 1024 * 1024) {
    notice.value = 'Choose a JPG, PNG, WebP, or GIF image under 2 MB.';
    event.target.value = '';
    return;
  }
  busy.value = true;
  try {
    const encoded = await new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(String(reader.result).split(',')[1] || '');
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
    const extension = file.name.split('.').pop().toLowerCase().replace(/[^a-z0-9]/g, '') || 'jpg';
    const { data } = await customerApi.updateProfileImage({ id: user.value.id, filename: `profile-${Date.now()}.${extension}`, image: encoded });
    if (!data?.result) throw new Error(data?.message || 'Could not update your photo.');
    await auth.loadUser();
    notice.value = data.message || 'Profile photo updated.';
  } catch (error) { notice.value = error.response?.data?.message || error.message; }
  finally { busy.value = false; event.target.value = ''; }
}

async function signOut() {
  try { await auth.logout(); } catch { auth.clear(); }
  orders.value = [];
  addresses.value = [];
  counters.value = {};
  wishlist.value = [];
  notice.value = 'You have signed out.';
}

function orderReference(order) {
  return order.code || order.order_code || `Order #${order.id}`;
}

function money(value) {
  if (typeof value === 'string' && /[^\d.,\s-]/.test(value)) return value;
  return `৳${Number(value || 0).toLocaleString(undefined, { maximumFractionDigits: 2 })}`;
}
</script>

<template>
  <section v-if="!user" class="mx-auto max-w-md rounded-2xl border border-gray-100 bg-white p-6 shadow-card sm:p-8">
    <div class="mb-6">
      <p class="text-xs font-bold uppercase tracking-wider text-brand">FlixShop account</p>
      <h1 class="mt-2 text-2xl font-extrabold text-ink">{{ mode === 'login' ? 'Welcome back' : mode === 'verify' ? 'Verify your email' : mode === 'forgot' ? 'Reset your password' : mode === 'reset' ? 'Choose a new password' : 'Create your account' }}</h1>
      <p class="mt-2 text-sm leading-6 text-muted">Sign in to manage your orders, addresses, and shopping profile.</p>
    </div>
    <form v-if="mode === 'forgot'" class="space-y-4" @submit.prevent="requestPasswordReset">
      <label class="block text-sm font-semibold text-ink">Email address<input v-model.trim="form.email" required type="email" autocomplete="email" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" /></label>
      <p v-if="notice" class="rounded-lg bg-brand-tint px-3 py-2 text-sm text-ink" role="status">{{ notice }}</p>
      <button class="w-full rounded-lg bg-brand px-4 py-3 font-bold text-white disabled:opacity-60" :disabled="busy">{{ busy ? 'Sending…' : 'Send reset code' }}</button>
    </form>
    <form v-else-if="mode === 'reset'" class="space-y-4" @submit.prevent="confirmPasswordReset">
      <label class="block text-sm font-semibold text-ink">Reset code<input v-model.trim="resetCode" required inputmode="numeric" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" /></label>
      <label class="block text-sm font-semibold text-ink">New password<input v-model="resetPassword" required minlength="6" type="password" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" /></label>
      <p v-if="notice" class="rounded-lg bg-brand-tint px-3 py-2 text-sm text-ink" role="status">{{ notice }}</p>
      <button class="w-full rounded-lg bg-brand px-4 py-3 font-bold text-white disabled:opacity-60" :disabled="busy">{{ busy ? 'Saving…' : 'Reset password' }}</button>
      <button class="w-full py-2 text-sm font-semibold text-brand" type="button" :disabled="busy" @click="resendResetCode">Resend code</button>
    </form>
    <form v-else-if="mode !== 'verify'" class="space-y-4" @submit.prevent="submitAuth">
      <label v-if="mode === 'login'" class="block text-sm font-semibold text-ink">Sign in as<select v-model="form.accountType" class="mt-1.5 w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 font-normal outline-none focus:border-brand"><option value="customer">Customer or seller</option><option value="delivery_boy">Delivery staff</option></select></label>
      <label v-if="mode === 'signup'" class="block text-sm font-semibold text-ink">Full name
        <input v-model.trim="form.name" required autocomplete="name" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" />
      </label>
      <label class="block text-sm font-semibold text-ink">Email address
        <input v-model.trim="form.email" required type="email" autocomplete="email" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" />
      </label>
      <label class="block text-sm font-semibold text-ink">Password
        <input v-model="form.password" required minlength="6" type="password" :autocomplete="mode === 'login' ? 'current-password' : 'new-password'" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" />
      </label>
      <p v-if="notice || auth.error.value" class="rounded-lg bg-brand-tint px-3 py-2 text-sm text-ink" role="status">{{ auth.error.value || notice }}</p>
      <button class="w-full rounded-lg bg-brand px-4 py-3 font-bold text-white transition hover:bg-brand-hover disabled:opacity-60" :disabled="busy || auth.loading.value">
        {{ busy || auth.loading.value ? 'Please wait…' : mode === 'login' ? 'Sign in' : 'Create account' }}
      </button>
    </form>
    <form v-else class="space-y-4" @submit.prevent="confirmSignup">
      <label class="block text-sm font-semibold text-ink">Verification code
        <input v-model.trim="verificationCode" required inputmode="numeric" autocomplete="one-time-code" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" />
      </label>
      <p v-if="notice" class="rounded-lg bg-brand-tint px-3 py-2 text-sm text-ink" role="status">{{ notice }}</p>
      <button class="w-full rounded-lg bg-brand px-4 py-3 font-bold text-white hover:bg-brand-hover disabled:opacity-60" :disabled="busy">{{ busy ? 'Please wait…' : 'Verify email' }}</button>
      <button class="w-full py-2 text-sm font-semibold text-brand" type="button" :disabled="busy" @click="resendCode">Resend code</button>
    </form>
    <button v-if="mode === 'login'" class="mt-4 w-full text-sm font-semibold text-muted hover:text-brand" type="button" @click="mode = 'forgot'; notice = ''">Forgot password?</button>
    <button v-if="mode === 'forgot' || mode === 'reset'" class="mt-4 w-full text-sm font-semibold text-brand" type="button" @click="mode = 'login'; notice = ''">Back to sign in</button>
    <button v-if="mode === 'login' || mode === 'signup'" class="mt-5 w-full text-sm font-semibold text-brand hover:text-brand-hover" type="button" @click="mode = mode === 'login' ? 'signup' : 'login'; notice = ''">
      {{ mode === 'login' ? 'New here? Create an account' : 'Already have an account? Sign in' }}
    </button>
  </section>

  <section v-else class="mx-auto max-w-5xl">
    <div class="flex flex-wrap items-start justify-between gap-4 rounded-2xl bg-gradient-to-r from-brand to-brand-hover p-6 text-white shadow-card sm:p-8">
      <div>
        <p class="text-xs font-bold uppercase tracking-wider text-white/75">Your account</p>
        <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Hello, {{ user.name || user.email }}</h1>
        <p class="mt-1 text-sm text-white/80">{{ user.email || user.phone }}</p>
      </div>
      <button class="rounded-full border border-white/40 px-4 py-2 text-sm font-semibold transition hover:bg-white/10" type="button" @click="signOut">Sign out</button>
    </div>
    <p v-if="notice" class="mt-4 rounded-lg bg-brand-tint px-4 py-3 text-sm text-ink" role="status">{{ notice }}</p>

    <div class="mt-5 grid grid-cols-3 gap-3">
      <div class="rounded-xl bg-white p-4 text-center shadow-card"><p class="text-2xl font-extrabold text-brand">{{ counters.order_count ?? orders.length }}</p><p class="mt-1 text-xs text-muted">Orders</p></div>
      <div class="rounded-xl bg-white p-4 text-center shadow-card"><p class="text-2xl font-extrabold text-brand">{{ counters.cart_item_count ?? cart.count.value }}</p><p class="mt-1 text-xs text-muted">Cart items</p></div>
      <div class="rounded-xl bg-white p-4 text-center shadow-card"><p class="text-2xl font-extrabold text-brand">{{ counters.wishlist_item_count ?? 0 }}</p><p class="mt-1 text-xs text-muted">Saved items</p></div>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
      <nav class="flex gap-2 overflow-x-auto rounded-xl bg-white p-2 shadow-card lg:block lg:space-y-1" aria-label="Account sections">
        <button v-for="item in [{ id: 'orders', label: 'Order history' }, { id: 'addresses', label: 'Addresses' }, { id: 'wishlist', label: 'Saved items' }, { id: 'wallet', label: 'Wallet' }, { id: 'points', label: 'Club points' }, { id: 'refunds', label: 'Refund requests' }, { id: 'profile', label: 'Profile settings' }]" :key="item.id" class="shrink-0 rounded-lg px-4 py-3 text-left text-sm font-semibold lg:block lg:w-full" :class="section === item.id ? 'bg-brand-tint text-brand' : 'text-muted hover:bg-gray-50'" type="button" @click="section = item.id">{{ item.label }}</button>
      </nav>

      <div class="min-h-64 rounded-xl bg-white p-5 shadow-card sm:p-6">
        <template v-if="section === 'orders'">
          <h2 class="text-lg font-bold text-ink">Order history</h2>
          <div v-if="orders.length" class="mt-4 divide-y divide-gray-100">
            <article v-for="(order, index) in orders" :key="order.id || index" class="flex flex-wrap items-center justify-between gap-3 py-4">
              <div><p class="font-semibold text-ink">{{ orderReference(order) }}</p><p class="mt-1 text-xs text-muted">{{ order.date || order.created_at || 'Order placed' }} · {{ order.delivery_status_string || order.delivery_status || order.status || 'Processing' }}</p></div>
              <div class="flex items-center gap-3"><p class="font-bold text-ink">{{ money(order.grand_total || order.total) }}</p><button class="text-xs font-semibold text-brand" type="button" @click="toggleOrderDetails(order)">{{ expandedOrderId === String(order.id) ? 'Hide details' : 'Details' }}</button><button class="text-xs font-semibold text-brand" type="button" @click="openReturnForm(order)">Request return</button></div>
            </article>
            <div v-if="expandedOrderId === String(order.id)" class="-mt-2 rounded-lg bg-gray-50 p-4">
              <p v-if="!orderDetails && !detailItems.length" class="text-xs text-muted">Loading order information…</p>
              <div v-if="orderDetails" class="grid gap-2 text-xs text-muted sm:grid-cols-2"><p>Payment: {{ orderDetails.payment_type }} · {{ orderDetails.payment_status_string }}</p><p>Delivery: {{ orderDetails.delivery_status_string }}</p><p>Subtotal: {{ orderDetails.subtotal }}</p><p>Shipping: {{ orderDetails.shipping_cost }}</p><p>Tax: {{ orderDetails.tax }}</p><p>Total: {{ orderDetails.grand_total }}</p></div>
              <div v-if="detailItems.length" class="mt-3 divide-y divide-gray-200"> <p v-for="item in detailItems" :key="item.id" class="py-2 text-sm text-ink">{{ item.product_name }} × {{ item.quantity }} <span class="float-right text-muted">{{ item.price }}</span></p></div>
            </div>
          </div>
          <p v-else class="mt-4 rounded-lg bg-gray-50 p-5 text-sm text-muted">Your orders will appear here after checkout.</p>
          <form v-if="returnOrderId" class="mt-4 space-y-3 rounded-lg bg-gray-50 p-4" @submit.prevent="submitRefund">
            <h3 class="font-bold text-ink">Return request for order #{{ returnOrderId }}</h3>
            <label class="block text-sm font-semibold text-ink">Eligible item<select v-model="refundItemId" required class="mt-1.5 w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 font-normal"><option v-for="item in orderItems" :key="item.id" :value="String(item.id)">{{ item.product_name }} · {{ item.quantity }} item(s)</option></select></label>
            <label class="block text-sm font-semibold text-ink">Reason<textarea v-model.trim="refundReason" required rows="3" class="mt-1.5 w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 font-normal" /></label>
            <button class="rounded-lg bg-brand px-4 py-2.5 text-sm font-bold text-white" :disabled="!orderItems.length">Send request</button>
          </form>
        </template>
        <template v-else-if="section === 'addresses'">
          <div class="flex items-center justify-between gap-3"><h2 class="text-lg font-bold text-ink">Saved addresses</h2><button class="rounded-lg bg-brand px-3 py-2 text-xs font-bold text-white" type="button" @click="addressFormOpen = !addressFormOpen">{{ addressFormOpen ? 'Cancel' : 'Add address' }}</button></div>
          <form v-if="addressFormOpen" class="mt-4 grid gap-3 rounded-lg bg-gray-50 p-4 sm:grid-cols-2" @submit.prevent="saveAddress">
            <label class="text-xs font-semibold text-ink sm:col-span-2">Street address<input v-model.trim="addressForm.address" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal" /></label>
            <label class="text-xs font-semibold text-ink">Country<select v-model="addressForm.country_id" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal" @change="loadStates"><option value="">Choose country</option><option v-for="country in countries" :key="country.id" :value="country.id">{{ country.name }}</option></select></label>
            <label class="text-xs font-semibold text-ink">State<select v-model="addressForm.state_id" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal" @change="loadCities"><option value="">Choose state</option><option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option></select></label>
            <label class="text-xs font-semibold text-ink">City<select v-model="addressForm.city_id" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal"><option value="">Choose city</option><option v-for="city in cities" :key="city.id" :value="city.id">{{ city.name }}</option></select></label>
            <label class="text-xs font-semibold text-ink">Postal code<input v-model.trim="addressForm.postal_code" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal" /></label>
            <label class="text-xs font-semibold text-ink">Phone<input v-model.trim="addressForm.phone" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-normal" /></label>
            <div class="sm:col-span-2"><button class="rounded-lg bg-brand px-4 py-2.5 text-sm font-bold text-white disabled:opacity-50" :disabled="busy">{{ busy ? 'Saving…' : 'Save address' }}</button></div>
          </form>
          <div v-if="addresses.length" class="mt-4 grid gap-3 sm:grid-cols-2">
            <article v-for="(address, index) in addresses" :key="address.id || index" class="rounded-lg border border-gray-100 p-4 text-sm text-muted">
              <p class="font-semibold text-ink">{{ address.address || 'Shipping address' }}</p>
              <p class="mt-1">{{ [address.city_name, address.state_name, address.country_name, address.postal_code].filter(Boolean).join(', ') }}</p>
              <p v-if="address.phone" class="mt-1">{{ address.phone }}</p>
              <div class="mt-3 flex items-center gap-3"><span v-if="Number(address.set_default)" class="inline-flex rounded-full bg-brand-tint px-2.5 py-1 text-xs font-semibold text-brand">Default</span><button v-else class="text-xs font-semibold text-brand" type="button" @click="setDefaultAddress(address)">Make default</button><button class="text-xs font-semibold text-red-600" type="button" @click="deleteAddress(address)">Delete</button></div>
            </article>
          </div>
          <p v-else class="mt-4 rounded-lg bg-gray-50 p-5 text-sm text-muted">No saved addresses yet.</p>
        </template>
        <template v-else-if="section === 'wishlist'">
          <h2 class="text-lg font-bold text-ink">Saved items</h2>
          <div v-if="wishlist.length" class="mt-4 divide-y divide-gray-100">
            <article v-for="entry in wishlist" :key="entry.id" class="flex items-center gap-4 py-3">
              <img :src="entry.product?.thumbnail_image" :alt="entry.product?.name" class="h-16 w-16 rounded-lg bg-gray-50 object-contain" />
              <div class="min-w-0 flex-1"><RouterLink :to="`/products/${entry.product?.id}`" class="font-semibold text-ink hover:text-brand">{{ entry.product?.name }}</RouterLink><p class="mt-1 text-sm text-muted">{{ entry.product?.base_price }}</p></div>
              <button class="text-xs font-semibold text-red-600" type="button" @click="removeSaved(entry)">Remove</button>
            </article>
          </div>
          <p v-else class="mt-4 rounded-lg bg-gray-50 p-5 text-sm text-muted">You have no saved items yet.</p>
        </template>
        <template v-else-if="section === 'wallet'">
          <h2 class="text-lg font-bold text-ink">Wallet</h2>
          <div class="mt-4 rounded-xl bg-brand-tint p-5"><p class="text-sm text-muted">Current balance</p><p class="mt-1 text-2xl font-extrabold text-brand">{{ wallet.balance }}</p><p class="mt-1 text-xs text-muted">Last recharged: {{ wallet.last_recharged || '—' }}</p></div>
          <button class="mt-4 rounded-lg bg-brand px-4 py-2.5 text-sm font-bold text-white" type="button" @click="rechargeOpen = !rechargeOpen">{{ rechargeOpen ? 'Close wallet top up' : 'Add funds' }}</button>
          <form v-if="rechargeOpen" class="mt-4 grid gap-3 rounded-lg bg-gray-50 p-4 sm:grid-cols-[1fr_1fr_auto]" @submit.prevent="startWalletRecharge">
            <label class="text-xs font-semibold text-ink">Amount<input v-model="rechargeAmount" required min="1" step="0.01" type="number" class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-normal" /></label>
            <label class="text-xs font-semibold text-ink">Payment method<select v-model="rechargeType" required class="mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-normal"><option v-for="method in walletPaymentTypes" :key="method.payment_type_key" :value="method.payment_type_key">{{ method.name }}</option></select></label>
            <button class="self-end rounded-lg bg-brand px-4 py-2.5 text-sm font-bold text-white disabled:opacity-50" :disabled="busy">{{ busy ? 'Starting…' : 'Continue' }}</button>
            <p v-if="rechargeMessage" class="text-sm text-red-700 sm:col-span-3" role="alert">{{ rechargeMessage }}</p>
          </form>
          <h3 class="mt-6 font-bold text-ink">Recent activity</h3>
          <div v-if="walletHistory.length" class="mt-2 divide-y divide-gray-100"><div v-for="(entry, index) in walletHistory" :key="index" class="flex justify-between py-3 text-sm"><span>{{ entry.payment_method }} · {{ entry.date }}</span><span class="font-semibold">{{ entry.amount }}</span></div></div>
          <p v-else class="mt-3 text-sm text-muted">No wallet activity yet.</p>
        </template>
        <template v-else-if="section === 'points'">
          <h2 class="text-lg font-bold text-ink">Club points</h2>
          <div v-if="clubPoints.length" class="mt-4 divide-y divide-gray-100"><div v-for="point in clubPoints" :key="point.id" class="flex flex-wrap items-center justify-between gap-3 py-3"><span><strong class="text-ink">{{ point.points }} points</strong><span class="ml-2 text-xs text-muted">{{ point.date }}</span></span><button v-if="!Number(point.convert_status)" class="rounded-lg bg-brand px-3 py-2 text-xs font-bold text-white" type="button" @click="convertPoints(point)">Convert to wallet</button><span v-else class="text-xs font-semibold text-emerald-700">Converted</span></div></div>
          <p v-else class="mt-4 text-sm text-muted">No club points yet.</p>
        </template>
        <template v-else-if="section === 'refunds'">
          <h2 class="text-lg font-bold text-ink">Refund requests</h2>
          <div v-if="refundRequests.length" class="mt-4 divide-y divide-gray-100"><div v-for="request in refundRequests" :key="request.id" class="flex flex-wrap justify-between gap-3 py-3 text-sm"><span><strong class="text-ink">{{ request.product_name || request.order_code }}</strong><span class="ml-2 text-muted">{{ request.date }}</span></span><span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800">{{ request.refund_label }}</span></div></div>
          <p v-else class="mt-4 rounded-lg bg-gray-50 p-5 text-sm text-muted">No refund requests. Contact support if an item from your order needs attention.</p>
        </template>
        <template v-else>
          <h2 class="text-lg font-bold text-ink">Profile settings</h2>
          <form class="mt-4 max-w-lg space-y-4" @submit.prevent="saveProfile">
            <label class="flex items-center gap-4 text-sm font-semibold text-ink"><img v-if="user.avatar_original || user.avatar" :src="user.avatar_original || user.avatar" alt="Profile" class="h-16 w-16 rounded-full bg-gray-100 object-cover"/><span v-else class="grid h-16 w-16 place-items-center rounded-full bg-brand-tint text-xl font-bold text-brand">{{ (user.name || 'U').slice(0, 1) }}</span><span>Profile photo<input type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-xs font-normal text-muted" @change="uploadAvatar" /></span></label>
            <label class="block text-sm font-semibold text-ink">Full name<input v-model.trim="profileName" required class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" /></label>
            <label class="block text-sm font-semibold text-ink">New password <span class="font-normal text-muted">(leave blank to keep current)</span><input v-model="profilePassword" minlength="6" type="password" autocomplete="new-password" class="mt-1.5 w-full rounded-lg border border-gray-200 px-3 py-2.5 font-normal outline-none focus:border-brand" /></label>
            <button class="rounded-lg bg-brand px-5 py-2.5 text-sm font-bold text-white hover:bg-brand-hover disabled:opacity-60" :disabled="busy">{{ busy ? 'Saving…' : 'Save changes' }}</button>
          </form>
        </template>
      </div>
    </div>
  </section>
</template>
