<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import ProductSkeleton from '../components/ProductSkeleton.vue';
import api, { extractList } from '../services/api';
import { useCart } from '../services/cart';
import { useI18n } from '../services/i18n';

const route = useRoute();
const category = ref(null);
const products = ref([]);
const loading = ref(true);
const error = ref('');
const cart = useCart();
const { t } = useI18n();
const title = computed(() => category.value?.name || t('category.eyebrow'));

useHead({
  title: computed(() => `${title.value} — FlixShop`),
  meta: [{ name: 'description', content: computed(() => `${t('category.eyebrow')}: ${title.value}.`) }],
});

async function loadCategory() {
  loading.value = true;
  error.value = '';

  try {
    const [categoriesResponse, productsResponse] = await Promise.all([
      api.get('/categories'),
      api.get(`/products/category/${encodeURIComponent(route.params.id)}`),
    ]);

    category.value =
      extractList(categoriesResponse.data).find((item) => String(item.id) === String(route.params.id)) || null;
    products.value = extractList(productsResponse.data);
  } catch (requestError) {
    error.value = requestError.response?.data?.message || requestError.message;
  } finally {
    loading.value = false;
  }
}

onMounted(loadCategory);
watch(() => route.params.id, loadCategory);
</script>

<template>
  <section>
    <p class="text-xs font-semibold uppercase tracking-wide text-brand">{{ t('category.eyebrow') }}</p>
    <h1 class="mt-1 text-2xl font-bold text-ink">{{ title }}</h1>
    <p v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ t('category.error') }}: {{ error }}</p>
    <p v-if="cart.error.value" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ cart.error.value }}</p>
    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4 xl:grid-cols-5">
      <template v-if="loading">
        <ProductSkeleton v-for="item in 8" :key="item" />
      </template>
      <ProductCard v-for="product in products" v-else :key="product.id" :product="product" @add-to-cart="cart.add" />
    </div>
    <p v-if="!loading && !error && !products.length" class="mt-5 rounded-xl bg-white p-8 text-center text-sm text-muted">{{ t('category.empty') }}</p>
  </section>
</template>
