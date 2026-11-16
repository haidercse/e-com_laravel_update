<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import ProductSkeleton from '../components/ProductSkeleton.vue';
import { catalogApi } from '../services/endpoints';
import { useCart } from '../services/cart';

const route = useRoute();
const cart = useCart();
const products = ref([]);
const heading = ref('Products');
const loading = ref(true);
const error = ref('');
const routeKey = computed(() => `${route.name}:${route.params.id || ''}`);
useHead({ title: computed(() => `${heading.value} — FlixShop`) });
function listOf(payload) { return payload?.data?.data || payload?.data || []; }

async function load() {
  loading.value = true;
  error.value = '';
  products.value = [];
  try {
    let response;
    if (route.name === 'brand-products') {
      const [brandResponse, productsResponse] = await Promise.all([catalogApi.brands({ id: route.params.id }), catalogApi.brandProducts(route.params.id)]);
      heading.value = listOf(brandResponse.data).find((brand) => String(brand.id) === String(route.params.id))?.name || 'Brand products';
      response = productsResponse;
    } else if (route.name === 'today-deals') {
      heading.value = "Today's deals";
      response = await catalogApi.todaysDeals();
    } else if (route.name === 'featured-products') {
      heading.value = 'Featured products';
      response = await catalogApi.featuredProducts();
    } else {
      heading.value = 'Best sellers';
      response = await catalogApi.bestSellers();
    }
    products.value = listOf(response.data);
  } catch (problem) { error.value = problem.response?.data?.message || problem.message; }
  finally { loading.value = false; }
}
onMounted(load);
watch(routeKey, load);
</script>

<template>
  <section class="mx-auto max-w-7xl">
    <header class="mb-5"><p class="text-xs font-bold uppercase tracking-wide text-brand">FlixShop collection</p><h1 class="mt-1 text-3xl font-extrabold text-ink">{{ heading }}</h1><p class="mt-2 text-sm text-muted">Browse the latest products selected for you.</p></header>
    <p v-if="error" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ error }}</p>
    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-5">
      <template v-if="loading"><ProductSkeleton v-for="item in 10" :key="item" /></template>
      <ProductCard v-for="product in products" v-else :key="product.id" :product="product" @add-to-cart="cart.add" />
    </div>
    <p v-if="!loading && !error && !products.length" class="mt-5 rounded-xl bg-white p-8 text-center text-sm text-muted">There are no products in this collection yet.</p>
  </section>
</template>
