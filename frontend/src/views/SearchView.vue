<script setup>
import { computed, ref, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import ProductSkeleton from '../components/ProductSkeleton.vue';
import api, { extractList } from '../services/api';
import { useCart } from '../services/cart';
import { useI18n } from '../services/i18n';

const route = useRoute();
const products = ref([]);
const loading = ref(false);
const error = ref('');
const query = computed(() => String(route.query.q || '').trim());
const cart = useCart();
const { t } = useI18n();

useHead({
  title: computed(() => `${t('search.title')}${query.value ? `: ${query.value}` : ''} — FlixShop`),
  meta: [{ name: 'description', content: computed(() => `${t('search.title')}: ${query.value || t('search.prompt')}.`) }],
});

watch(
  query,
  async (searchTerm) => {
    products.value = [];
    error.value = '';

    if (!searchTerm) {
      loading.value = false;
      return;
    }

    loading.value = true;

    try {
      const response = await api.get('/products/search', { params: { name: searchTerm } });
      products.value = extractList(response.data);
    } catch (requestError) {
      error.value = requestError.response?.data?.message || requestError.message;
    } finally {
      loading.value = false;
    }
  },
  { immediate: true },
);
</script>

<template>
  <section>
    <p class="text-xs font-semibold uppercase tracking-wide text-brand">{{ t('search.title') }}</p>
    <h1 class="mt-1 text-2xl font-bold text-ink">{{ query ? `“${query}”` : t('search.prompt') }}</h1>
    <p v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ t('search.error') }}: {{ error }}</p>
    <p v-if="cart.error.value" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ cart.error.value }}</p>
    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
      <template v-if="loading">
        <ProductSkeleton v-for="item in 8" :key="item" />
      </template>
      <ProductCard v-for="product in products" v-else :key="product.id" :product="product" @add-to-cart="cart.add" />
    </div>
    <p v-if="!loading && !error && query && !products.length" class="mt-5 rounded-xl bg-white p-8 text-center text-sm text-muted">
      {{ t('search.empty') }}
    </p>
  </section>
</template>
