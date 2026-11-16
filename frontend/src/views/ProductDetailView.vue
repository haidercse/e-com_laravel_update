<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useHead } from '@unhead/vue';
import { useRoute } from 'vue-router';
import api, { resolveAssetUrl } from '../services/api';
import { useCart } from '../services/cart';
import { catalogApi, customerApi } from '../services/endpoints';
import { useI18n } from '../services/i18n';

const route = useRoute();
const product = ref(null);
const loading = ref(true);
const error = ref('');
const cart = useCart();
const reviews = ref([]);
const wishlisted = ref(false);
const reviewForm = ref({ rating: 5, comment: '' });
const feedback = ref('');
const selectedOptions = ref({});
const selectedColor = ref('');
const variantInfo = ref(null);
const variantLoading = ref(false);
const { t } = useI18n();
const userId = () => localStorage.getItem('ecom_user_id');
const title = computed(() => product.value?.name || t('search.prompt'));
const image = computed(() => resolveAssetUrl(product.value?.thumbnail_image || product.value?.thumbnail_img_url || product.value?.image));
const description = computed(() => product.value?.description || t('detail.fallback'));

useHead({ title: computed(() => `${title.value} — FlixShop`), meta: [{ name: 'description', content: description }] });

async function loadProduct() {
  loading.value = true;
  error.value = '';
  reviews.value = [];
  try {
    const response = await api.get(`/products/${encodeURIComponent(route.params.id)}`);
    const envelope = response.data?.data || response.data;
    product.value = Array.isArray(envelope?.data) ? envelope.data[0] : Array.isArray(envelope) ? envelope[0] : envelope;
    selectedOptions.value = {};
    selectedColor.value = '';
    variantInfo.value = null;
    const [reviewResult, wishlistResult] = await Promise.allSettled([
      customerApi.reviews(route.params.id),
      userId() ? customerApi.isWishlisted({ product_id: route.params.id, user_id: userId() }) : Promise.resolve(null),
    ]);
    if (reviewResult.status === 'fulfilled') reviews.value = reviewResult.value.data?.data?.data || reviewResult.value.data?.data || [];
    if (wishlistResult.status === 'fulfilled' && wishlistResult.value) wishlisted.value = Boolean(wishlistResult.value.data?.is_in_wishlist);
  } catch (problem) {
    error.value = problem.response?.data?.message || problem.message;
  } finally { loading.value = false; }
}

async function updateVariant() {
  if (!product.value) return;
  const options = product.value.choice_options || [];
  if (options.some((option) => !selectedOptions.value[option.name])) return;
  if (!options.length && !selectedColor.value) return;
  variantLoading.value = true;
  try {
    const params = {
      id: product.value.id,
      variants: options.map((option) => selectedOptions.value[option.name]).filter(Boolean).join(','),
      color: selectedColor.value.replace(/^#/, ''),
    };
    const { data } = await catalogApi.variantPrice(params);
    variantInfo.value = data;
    if (data.image) product.value.thumbnail_image = data.image;
  } catch (problem) {
    variantInfo.value = null;
    feedback.value = problem.response?.data?.message || 'That product option is currently unavailable.';
  } finally { variantLoading.value = false; }
}

function addProductToCart() {
  if ((product.value.choice_options?.length || product.value.colors?.length) && !variantInfo.value) {
    feedback.value = 'Choose an available product option first.';
    return;
  }
  cart.add({ ...product.value, variation: variantInfo.value?.variant || '' });
}

async function toggleWishlist() {
  if (!userId()) { feedback.value = 'Sign in to save products.'; return; }
  try {
    const params = { product_id: product.value.id, user_id: userId() };
    const { data } = wishlisted.value ? await customerApi.removeWishlistProduct(params) : await customerApi.addWishlistProduct(params);
    wishlisted.value = Boolean(data?.is_in_wishlist);
    feedback.value = data?.message || '';
  } catch (problem) { feedback.value = problem.response?.data?.message || problem.message; }
}

async function submitReview() {
  if (!userId()) { feedback.value = 'Sign in to leave a review.'; return; }
  try {
    const { data } = await customerApi.submitReview({ product_id: product.value.id, user_id: userId(), ...reviewForm.value });
    feedback.value = data?.message || 'Review submitted.';
    if (data?.result) {
      reviewForm.value.comment = '';
      const response = await customerApi.reviews(product.value.id);
      reviews.value = response.data?.data?.data || response.data?.data || [];
    }
  } catch (problem) { feedback.value = problem.response?.data?.message || problem.message; }
}

onMounted(loadProduct);
watch(() => route.params.id, loadProduct);
</script>

<template>
  <p v-if="loading" class="h-80 animate-pulse rounded-xl bg-white" aria-label="Loading product" />
  <p v-else-if="error" class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ t('detail.error') }}: {{ error }}</p>
  <article v-else-if="product" class="overflow-hidden rounded-xl bg-white shadow-card">
    <div class="grid gap-6 p-4 sm:grid-cols-2 sm:p-6">
      <img :src="image" :alt="title" class="aspect-square w-full rounded-lg bg-gray-50 object-contain" />
      <div class="flex flex-col">
        <p class="text-xs font-semibold uppercase tracking-wide text-brand">{{ t('detail.eyebrow') }}</p>
        <h1 class="mt-2 text-2xl font-bold text-ink">{{ title }}</h1>
        <p v-if="product.brand?.name" class="mt-2 text-sm text-muted">{{ product.brand.name }}</p>
        <p class="mt-3 text-sm leading-6 text-muted">{{ description }}</p>
        <div v-for="choice in product.choice_options || []" :key="choice.name" class="mt-4">
          <label class="text-sm font-semibold text-ink">{{ choice.title }}
            <select v-model="selectedOptions[choice.name]" class="mt-1.5 block w-full max-w-xs rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-normal" @change="updateVariant"><option value="">Choose {{ choice.title }}</option><option v-for="option in choice.options" :key="option" :value="option">{{ option }}</option></select>
          </label>
        </div>
        <div v-if="product.colors?.length" class="mt-4"><p class="text-sm font-semibold text-ink">Color</p><div class="mt-2 flex gap-2"><button v-for="color in product.colors" :key="color" class="h-8 w-8 rounded-full border-2" :class="selectedColor === color ? 'border-brand ring-2 ring-brand/20' : 'border-gray-200'" :style="{ backgroundColor: color }" type="button" :aria-label="`Select color ${color}`" @click="selectedColor = color; updateVariant()" /></div></div>
        <p class="mt-5 text-2xl font-extrabold text-brand">{{ variantInfo?.price_string || product.main_price || `${product.currency_symbol || '৳'} ${product.base_discounted_price || product.unit_price || product.price}` }}</p>
        <p class="mt-2 text-sm text-muted">{{ variantInfo?.stock ?? product.current_stock }} in stock · {{ product.rating || 0 }} / 5 ({{ product.rating_count || reviews.length }} reviews)</p>
        <p v-if="cart.error.value" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ cart.error.value }}</p>
        <p v-if="feedback" class="mt-3 rounded-lg bg-brand-tint px-3 py-2 text-sm text-ink" role="status">{{ feedback }}</p>
        <div class="mt-5 flex flex-wrap gap-3">
          <button class="rounded-full bg-brand px-6 py-3 font-semibold text-white transition hover:bg-brand-hover disabled:opacity-60" :disabled="variantLoading || (variantInfo && !variantInfo.stock)" type="button" @click="addProductToCart">{{ variantLoading ? 'Checking option…' : 'Add to cart' }}</button>
          <button class="rounded-full border border-brand px-6 py-3 font-semibold text-brand transition hover:bg-brand-tint" type="button" @click="toggleWishlist">{{ wishlisted ? '♥ Saved' : '♡ Save item' }}</button>
          <RouterLink v-if="product.seller_id" :to="{ name: 'messages', query: { product: product.id } }" class="rounded-full border border-gray-200 px-6 py-3 text-sm font-semibold text-ink hover:border-brand">Message seller</RouterLink>
        </div>
      </div>
    </div>
    <section class="border-t border-gray-100 p-4 sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-lg font-bold text-ink">Customer reviews <span class="font-normal text-muted">({{ product.rating_count || reviews.length }})</span></h2><RouterLink to="/messages" class="text-sm font-semibold text-brand">Ask the seller</RouterLink></div>
      <form v-if="userId()" class="mt-4 grid gap-3 sm:grid-cols-[120px_minmax(0,1fr)_auto]" @submit.prevent="submitReview">
        <select v-model.number="reviewForm.rating" class="rounded-lg border border-gray-200 px-3 py-2 text-sm"><option v-for="score in 5" :key="score" :value="6-score">{{ 6-score }} stars</option></select>
        <input v-model.trim="reviewForm.comment" required class="min-w-0 rounded-lg border border-gray-200 px-3 py-2 text-sm" placeholder="Share your experience" />
        <button class="rounded-lg bg-brand px-4 py-2 text-sm font-bold text-white">Submit review</button>
      </form>
      <div v-if="reviews.length" class="mt-4 divide-y divide-gray-100"><article v-for="(review, index) in reviews" :key="index" class="py-3"><p class="text-sm font-semibold text-ink">{{ review.user_name }} <span class="ml-2 text-amber-500">{{ '★'.repeat(Math.round(review.rating || 0)) }}</span></p><p class="mt-1 text-sm text-muted">{{ review.comment }}</p><p class="mt-1 text-xs text-gray-400">{{ review.time }}</p></article></div>
      <p v-else class="mt-3 text-sm text-muted">No reviews yet.</p>
    </section>
  </article>
</template>
