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
const shops = ref([]);
const products = ref([]);
const shop = ref(null);
const shopBrands = ref([]);
const shopLists = ref({ all: [], top: [], featured: [], new: [] });
const activeProducts = ref('all');
const loading = ref(true);
const error = ref('');
const shopId = computed(() => route.params.id);
useHead({ title: computed(() => shop.value?.name ? `${shop.value.name} — FlixShop` : 'Shops — FlixShop') });

function listOf(payload) { return payload?.data?.data || payload?.data || []; }

async function load() {
  loading.value = true;
  error.value = '';
  try {
    if (shopId.value) {
      const [details, items, top, featured, newest, brandResponse] = await Promise.all([
        catalogApi.shop(shopId.value),
        catalogApi.shopProducts(shopId.value),
        catalogApi.shopTopProducts(shopId.value),
        catalogApi.shopFeaturedProducts(shopId.value),
        catalogApi.shopNewProducts(shopId.value),
        catalogApi.shopBrands(shopId.value),
      ]);
      shop.value = listOf(details.data)[0] || details.data?.data || details.data;
      shopLists.value = { all: listOf(items.data), top: listOf(top.data), featured: listOf(featured.data), new: listOf(newest.data) };
      products.value = shopLists.value[activeProducts.value];
      shopBrands.value = listOf(brandResponse.data);
    } else {
      const { data } = await catalogApi.shops();
      shops.value = listOf(data);
    }
  } catch (problem) { error.value = problem.response?.data?.message || problem.message; }
  finally { loading.value = false; }
}
function showProducts(type) {
  activeProducts.value = type;
  products.value = shopLists.value[type] || [];
}
onMounted(load);
watch(shopId, load);
</script>

<template>
  <section class="mx-auto max-w-7xl">
    <RouterLink v-if="shopId" to="/shops" class="text-sm font-semibold text-brand">← All shops</RouterLink>
    <header class="mb-5 mt-3"><p class="text-xs font-bold uppercase tracking-wider text-brand">Marketplace</p><h1 class="mt-1 text-3xl font-extrabold text-ink">{{ shop?.name || 'Explore shops' }}</h1><p class="mt-2 text-sm text-muted">{{ shopId ? 'Browse products from this shop.' : 'Discover verified shops and their latest products.' }}</p></header>
    <div v-if="shopId && !loading" class="mb-5 flex flex-wrap gap-2"><button v-for="tab in [{id:'all',label:'All products'},{id:'top',label:'Top selling'},{id:'featured',label:'Featured'},{id:'new',label:'New arrivals'}]" :key="tab.id" class="rounded-full px-4 py-2 text-sm font-semibold" :class="activeProducts === tab.id ? 'bg-brand text-white' : 'bg-white text-muted shadow-card'" type="button" @click="showProducts(tab.id)">{{ tab.label }}</button><span v-for="brand in shopBrands" :key="brand.id" class="rounded-full bg-brand-tint px-3 py-2 text-xs font-semibold text-brand">{{ brand.name }}</span></div>
    <p v-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ error }}</p>
    <div v-else-if="loading" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5"><ProductSkeleton v-for="item in 10" :key="item" /></div>
    <div v-else-if="shopId" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"><ProductCard v-for="item in products" :key="item.id" :product="item" @add-to-cart="cart.add" /><p v-if="!products.length" class="col-span-full rounded-xl bg-white p-8 text-center text-sm text-muted">This shop has no published products.</p></div>
    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <RouterLink v-for="item in shops" :key="item.id" :to="`/shops/${item.id}`" class="group flex items-center gap-4 rounded-xl bg-white p-5 shadow-card transition hover:-translate-y-0.5 hover:shadow-lg"><img :src="item.logo" :alt="item.name" class="h-16 w-16 rounded-full bg-gray-50 object-contain p-2"/><div class="min-w-0 flex-1"><h2 class="truncate text-lg font-bold text-ink group-hover:text-brand">{{ item.name }}</h2><span class="mt-1 inline-flex text-sm font-semibold text-brand">Visit shop →</span></div></RouterLink>
      <p v-if="!shops.length" class="col-span-full rounded-xl bg-white p-8 text-center text-sm text-muted">No shops found.</p>
    </div>
  </section>
</template>
