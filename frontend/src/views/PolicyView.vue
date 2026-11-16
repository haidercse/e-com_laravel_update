<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import { catalogApi } from '../services/endpoints';

const route = useRoute();
const page = ref(null);
const loading = ref(true);
const error = ref('');
const titles = { seller: 'Seller policy', support: 'Support policy', return: 'Return policy' };
const title = computed(() => titles[route.params.type] || 'Store policy');
useHead({ title: computed(() => `${title.value} — FlixShop`) });

async function load() {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await catalogApi.policies(route.params.type);
    const list = data?.data?.data || data?.data || [];
    page.value = Array.isArray(list) ? list[0] : list;
  } catch (problem) { error.value = problem.response?.data?.message || problem.message; }
  finally { loading.value = false; }
}
onMounted(load);
watch(() => route.params.type, load);
</script>

<template>
  <article class="mx-auto max-w-4xl rounded-2xl bg-white p-6 shadow-card sm:p-10">
    <p class="text-xs font-bold uppercase tracking-wider text-brand">Customer information</p><h1 class="mt-2 text-3xl font-extrabold text-ink">{{ page?.title || title }}</h1>
    <p v-if="loading" class="mt-6 h-32 animate-pulse rounded-xl bg-gray-100" />
    <p v-else-if="error" class="mt-5 rounded-lg bg-red-50 p-4 text-sm text-red-700" role="alert">{{ error }}</p>
    <div v-else-if="page?.content" class="prose prose-sm mt-6 max-w-none text-muted" v-html="page.content" />
    <p v-else class="mt-6 rounded-lg bg-gray-50 p-5 text-sm text-muted">There is no published {{ title.toLowerCase() }} at the moment.</p>
  </article>
</template>
