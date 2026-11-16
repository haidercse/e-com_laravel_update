<script setup>
import { computed, onMounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import { deliveryApi } from '../services/endpoints';
import { useAuth } from '../services/auth';

const auth = useAuth();
const userId = computed(() => auth.user.value?.id || localStorage.getItem('ecom_user_id'));
const summary = ref({});
const rows = ref([]);
const active = ref('assigned');
const loading = ref(false);
const error = ref('');
const updating = ref(null);
const periods = ref({ collection: {}, earning: {} });
const tabs = [
  { id: 'assigned', label: 'Assigned' },
  { id: 'picked', label: 'Picked up' },
  { id: 'onWay', label: 'On the way' },
  { id: 'completed', label: 'Completed' },
  { id: 'cancelled', label: 'Cancelled' },
  { id: 'collection', label: 'Collections' },
  { id: 'earning', label: 'Earnings' },
];

useHead({ title: 'Delivery dashboard — FlixShop' });
onMounted(async () => {
  if (!userId.value) { error.value = 'Sign in with a delivery account to continue.'; return; }
  try {
    const [summaryResponse, collectionResponse, earningResponse] = await Promise.all([
      deliveryApi.summary(userId.value),
      deliveryApi.collectionSummary(userId.value),
      deliveryApi.earningSummary(userId.value),
    ]);
    summary.value = summaryResponse.data || {};
    periods.value = { collection: collectionResponse.data || {}, earning: earningResponse.data || {} };
  } catch (problem) { error.value = message(problem); }
  await loadTab('assigned');
});

function listOf(payload) { return payload?.data?.data || payload?.data || []; }
function message(problem) { return problem.response?.data?.message || problem.message || 'Request failed.'; }

async function loadTab(tab) {
  active.value = tab;
  loading.value = true;
  error.value = '';
  try {
    const request = {
      assigned: () => deliveryApi.assigned(userId.value),
      picked: () => deliveryApi.pickedUp(userId.value),
      onWay: () => deliveryApi.onTheWay(userId.value),
      completed: () => deliveryApi.completed(userId.value),
      cancelled: () => deliveryApi.cancelled(userId.value),
      collection: () => deliveryApi.collection(userId.value),
      earning: () => deliveryApi.earning(userId.value),
    }[tab];
    const { data } = await request();
    rows.value = listOf(data);
  } catch (problem) { error.value = message(problem); rows.value = []; }
  finally { loading.value = false; }
}

async function advance(order, status) {
  updating.value = order.id;
  error.value = '';
  try {
    const { data } = await deliveryApi.changeStatus({ order_id: order.id, delivery_boy_id: userId.value, status });
    if (!data?.result) throw new Error(data?.message || 'Could not update delivery status.');
    await loadTab(active.value);
    const result = await deliveryApi.summary(userId.value);
    summary.value = result.data || {};
  } catch (problem) { error.value = message(problem); }
  finally { updating.value = null; }
}

async function requestCancel(order) {
  updating.value = order.id;
  error.value = '';
  try {
    const { data } = await deliveryApi.cancelRequest(order.id);
    if (!data?.result) throw new Error(data?.message || 'Could not send cancellation request.');
    error.value = data.message || 'Cancellation requested.';
    await loadTab(active.value);
  } catch (problem) { error.value = message(problem); }
  finally { updating.value = null; }
}

const next = { pending: ['picked_up', 'Pick up'], confirmed: ['picked_up', 'Pick up'], picked_up: ['on_the_way', 'Mark on the way'], on_the_way: ['delivered', 'Complete delivery'] };
</script>

<template>
  <section class="mx-auto max-w-7xl">
    <header class="mb-6 flex flex-wrap items-end justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wider text-brand">FlixShop operations</p><h1 class="mt-1 text-3xl font-extrabold text-ink">Delivery dashboard</h1></div><span class="rounded-full bg-brand-tint px-4 py-2 text-sm font-semibold text-brand">{{ auth.user.value?.name || 'Delivery account' }}</span></header>
    <p v-if="error" class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">{{ error }}</p>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
      <div v-for="stat in [{ key:'assigned', label:'Assigned' }, { key:'picked', label:'Picked up' }, { key:'on_the_way', label:'On the way' }, { key:'completed_delivery', label:'Completed' }, { key:'total_collection', label:'Collected' }, { key:'total_earning', label:'Earnings' }]" :key="stat.key" class="rounded-xl bg-white p-4 shadow-card"><p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ stat.label }}</p><p class="mt-2 text-xl font-extrabold text-brand">{{ summary[stat.key] ?? '—' }}</p></div>
    </div>
    <div class="mt-3 grid gap-3 sm:grid-cols-2"><div class="rounded-xl bg-white p-4 shadow-card"><p class="text-xs font-semibold uppercase tracking-wide text-muted">Today’s collection · {{ periods.collection.today_date || '' }}</p><p class="mt-1 text-lg font-extrabold text-ink">{{ periods.collection.today_collection || '—' }}</p><p class="mt-2 text-xs text-muted">Yesterday: {{ periods.collection.yesterday_collection || '—' }}</p></div><div class="rounded-xl bg-white p-4 shadow-card"><p class="text-xs font-semibold uppercase tracking-wide text-muted">Today’s earnings · {{ periods.earning.today_date || '' }}</p><p class="mt-1 text-lg font-extrabold text-ink">{{ periods.earning.today_earning || '—' }}</p><p class="mt-2 text-xs text-muted">Yesterday: {{ periods.earning.yesterday_earning || '—' }}</p></div></div>
    <section class="mt-6 overflow-hidden rounded-xl bg-white shadow-card">
      <div class="flex gap-1 overflow-x-auto border-b border-gray-100 p-3">
        <button v-for="tab in tabs" :key="tab.id" class="shrink-0 rounded-lg px-4 py-2 text-sm font-semibold" :class="active === tab.id ? 'bg-brand text-white' : 'text-muted hover:bg-gray-50'" type="button" @click="loadTab(tab.id)">{{ tab.label }}</button>
      </div>
      <div v-if="loading" class="p-8 text-center text-sm text-muted">Loading records…</div>
      <div v-else-if="!rows.length" class="p-10 text-center text-sm text-muted">No records in this section.</div>
      <div v-else class="divide-y divide-gray-100">
        <article v-for="(order, index) in rows" :key="order.id || index" class="flex flex-wrap items-center justify-between gap-4 p-4 sm:p-5">
          <div class="min-w-0"><p class="font-bold text-ink">{{ order.code || order.order_code || `Order #${order.order_id || order.id}` }}</p><p class="mt-1 text-xs text-muted">{{ order.date || order.delivery_history_date || order.created_at }} · {{ order.delivery_status_string || order.delivery_status || order.payment_method || '' }}</p><p v-if="order.shipping_address?.address" class="mt-1 text-sm text-muted">{{ order.shipping_address.address }}</p></div>
          <div class="flex items-center gap-3"><span class="font-bold text-ink">{{ order.grand_total || order.collection || order.earning || '' }}</span><button v-if="next[order.delivery_status] && ['assigned','picked','onWay'].includes(active)" class="rounded-lg bg-brand px-4 py-2 text-xs font-bold text-white disabled:opacity-50" :disabled="updating === order.id" type="button" @click="advance(order, next[order.delivery_status][0])">{{ updating === order.id ? 'Saving…' : next[order.delivery_status][1] }}</button><button v-if="['assigned','picked','onWay'].includes(active) && !order.cancel_request" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-muted hover:border-red-300 hover:text-red-600 disabled:opacity-50" :disabled="updating === order.id" type="button" @click="requestCancel(order)">Request cancel</button></div>
        </article>
      </div>
    </section>
  </section>
</template>
