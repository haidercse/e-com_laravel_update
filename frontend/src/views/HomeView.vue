<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import { RouterLink } from 'vue-router';
import ProductCard from '../components/ProductCard.vue';
import ProductSkeleton from '../components/ProductSkeleton.vue';
import api, { extractList, resolveAssetUrl } from '../services/api';
import { useCart } from '../services/cart';
import { useI18n } from '../services/i18n';

const { t } = useI18n();
useHead({
  title: computed(() => t('home.title')),
  meta: [{ name: 'description', content: computed(() => t('home.description')) }],
});

const products = ref([]);
const categories = ref([]);
const flashDeals = ref([]);
const flashProducts = ref([]);
const laptopProducts = ref([]);
const banners = ref([]);
const brands = ref([]);
const loading = ref(true);
const flashLoading = ref(true);
const laptopLoading = ref(true);
const error = ref('');
const categoryError = ref('');
const flashError = ref('');
const laptopError = ref('');
const addingId = ref(null);
const activeSlide = ref(0);
const now = ref(Date.now());
const cart = useCart();
let slideTimer;
let countdownTimer;

const slides = computed(() => [
  {
    eyebrow: t('home.slide1Eyebrow'),
    title: t('home.slide1Title'),
    description: t('home.slide1Description'),
    action: t('home.slide1Action'),
    theme: 'from-brand via-[#ed7132] to-brand-hover',
  },
  {
    eyebrow: t('home.slide2Eyebrow'),
    title: t('home.slide2Title'),
    description: t('home.slide2Description'),
    action: t('home.slide2Action'),
    theme: 'from-gray-900 via-gray-800 to-[#4b5563]',
  },
]);

const visibleCategories = computed(() => categories.value.slice(0, 8));
const visibleProducts = computed(() => products.value.slice(0, 10));
const heroImage = computed(() => banners.value.length ? resolveAssetUrl(banners.value[activeSlide.value % banners.value.length]?.photo) : '');
const flashDeal = computed(() => flashDeals.value[0] || null);
const flashCountdown = computed(() => {
  const endDate = Number(flashDeal.value?.date);

  if (!endDate) {
    return '';
  }

  const endTime = endDate < 1000000000000 ? endDate * 1000 : endDate;
  const remaining = Math.max(0, Math.floor((endTime - now.value) / 1000));
  const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
  const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
  const seconds = String(remaining % 60).padStart(2, '0');

  return `${hours}:${minutes}:${seconds}`;
});

onMounted(async () => {
  slideTimer = window.setInterval(() => {
    activeSlide.value = (activeSlide.value + 1) % slides.value.length;
  }, 6000);
  countdownTimer = window.setInterval(() => {
    now.value = Date.now();
  }, 1000);

  const [productResult, categoryResult, flashResult, laptopResult, bannerResult, brandResult] = await Promise.allSettled([
    api.get('/products'),
    api.get('/categories'),
    api.get('/flash-deals'),
    api.get('/products/search', { params: { name: 'laptop' } }),
    api.get('/banners'),
    api.get('/brands/top'),
  ]);

  if (bannerResult.status === 'fulfilled') {
    try { banners.value = extractList(bannerResult.value.data); } catch { banners.value = []; }
  }
  if (brandResult.status === 'fulfilled') {
    try { brands.value = extractList(brandResult.value.data); } catch { brands.value = []; }
  }

  if (productResult.status === 'fulfilled') {
    try {
      products.value = extractList(productResult.value.data);
    } catch (responseError) {
      error.value = responseError.message;
    }
  } else {
    error.value = productResult.reason.response?.data?.message || productResult.reason.message;
  }

  if (categoryResult.status === 'fulfilled') {
    try {
      categories.value = extractList(categoryResult.value.data);
    } catch (responseError) {
      categoryError.value = responseError.message;
    }
  } else {
    categoryError.value = categoryResult.reason.response?.data?.message || categoryResult.reason.message;
  }

  if (flashResult.status === 'fulfilled') {
    try {
      flashDeals.value = extractList(flashResult.value.data);
    } catch (responseError) {
      flashError.value = responseError.message;
    }
  } else {
    flashError.value = flashResult.reason.response?.data?.message || flashResult.reason.message;
  }

  if (flashDeals.value.length) {
    try {
      const response = await api.get(`/flash-deal-products/${encodeURIComponent(flashDeals.value[0].id)}`);
      flashProducts.value = extractList(response.data);
    } catch (requestError) {
      flashError.value = requestError.response?.data?.message || requestError.message;
    }
  }

  if (laptopResult.status === 'fulfilled') {
    try {
      laptopProducts.value = extractList(laptopResult.value.data);
    } catch (responseError) {
      laptopError.value = responseError.message;
    }
  } else {
    laptopError.value = laptopResult.reason.response?.data?.message || laptopResult.reason.message;
  }

  loading.value = false;
  flashLoading.value = false;
  laptopLoading.value = false;
});

onUnmounted(() => {
  window.clearInterval(slideTimer);
  window.clearInterval(countdownTimer);
});

function showSlide(index) {
  activeSlide.value = (index + slides.value.length) % slides.value.length;
}

async function addToCart(product) {
  addingId.value = product.id;
  try {
    await cart.add(product);
  } finally {
    addingId.value = null;
  }
}
</script>

<template>
  <div class="grid min-w-0 gap-5 sm:grid-cols-[200px_minmax(0,1fr)] xl:grid-cols-[250px_minmax(0,1fr)] xl:gap-7">
    <aside class="hidden sm:block">
      <div class="sticky top-36 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-card">
        <div class="flex items-center gap-2 border-b border-gray-100 px-4 py-4">
          <svg viewBox="0 0 24 24" class="h-5 w-5 text-brand" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <rect x="3" y="3" width="8" height="8" rx="1.5" />
            <rect x="13" y="3" width="8" height="8" rx="1.5" />
            <rect x="3" y="13" width="8" height="8" rx="1.5" />
            <rect x="13" y="13" width="8" height="8" rx="1.5" />
          </svg>
          <h2 class="font-bold text-ink">{{ t('home.allCategories') }}</h2>
        </div>
        <p v-if="categoryError" class="p-4 text-xs text-red-700" role="alert">{{ t('home.categoriesError') }}: {{ categoryError }}</p>
        <nav v-else class="max-h-[460px] overflow-y-auto p-2" aria-label="পণ্যের ক্যাটাগরি">
          <RouterLink
            v-for="category in visibleCategories"
            :key="category.id"
            :to="{ name: 'category', params: { id: category.id } }"
            class="group flex items-center justify-between gap-2 rounded-lg px-3 py-3 text-sm text-muted transition-colors hover:bg-brand-tint hover:text-brand"
          >
            <span class="truncate">{{ category.name || category.title || 'ক্যাটাগরি' }}</span>
            <span class="text-gray-300 transition-transform group-hover:translate-x-0.5 group-hover:text-brand" aria-hidden="true">›</span>
          </RouterLink>
          <p v-if="!loading && !visibleCategories.length" class="px-3 py-4 text-xs text-muted">{{ t('home.noCategories') }}</p>
        </nav>
        <RouterLink to="/categories" class="block border-t border-gray-100 px-4 py-3 text-sm font-semibold text-brand hover:bg-brand-tint">
          {{ t('home.seeAllCategories') }}
        </RouterLink>
      </div>

      <div class="mt-4 rounded-xl bg-brand-tint p-4">
        <p class="text-xs font-bold uppercase tracking-wide text-brand">{{ t('home.specialOffer') }}</p>
        <p class="mt-2 text-sm font-semibold leading-5 text-ink">{{ t('home.shopAtHome') }}</p>
        <RouterLink to="/categories" class="mt-3 inline-flex text-xs font-bold text-brand hover:text-brand-hover">{{ t('home.slide2Action') }} →</RouterLink>
      </div>
    </aside>

    <section class="min-w-0">
      <div class="group relative isolate h-[270px] overflow-hidden rounded-2xl bg-gradient-to-br shadow-card sm:h-[320px] lg:h-[360px]" :class="slides[activeSlide].theme">
        <img v-if="heroImage" :src="heroImage" alt="" class="absolute inset-0 z-0 h-full w-full object-cover opacity-30 mix-blend-overlay" />
        <div class="pointer-events-none absolute -right-16 -top-20 h-64 w-64 rounded-full border-[36px] border-white/10 sm:h-80 sm:w-80" />
        <div class="pointer-events-none absolute -bottom-28 right-1/4 h-64 w-64 rounded-full bg-white/10 blur-2xl" />

        <Transition name="hero-fade" mode="out-in">
          <div :key="activeSlide" class="absolute inset-0 flex items-center px-6 py-7 text-white sm:px-12 lg:px-16">
            <div class="relative z-10 max-w-xl">
              <span class="inline-flex rounded-full border border-white/25 bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-white/90 backdrop-blur">
                {{ slides[activeSlide].eyebrow }}
              </span>
              <h1 class="mt-4 max-w-lg text-3xl font-extrabold leading-tight sm:text-4xl lg:text-5xl">
                {{ slides[activeSlide].title }}
              </h1>
              <p class="mt-3 max-w-md text-sm leading-6 text-white/80 sm:text-base">
                {{ slides[activeSlide].description }}
              </p>
              <RouterLink to="/categories" class="mt-5 inline-flex items-center gap-2 rounded-full bg-white px-5 py-3 text-sm font-bold text-brand shadow-sm transition-transform hover:-translate-y-0.5 active:scale-95">
                {{ slides[activeSlide].action }}
                <span aria-hidden="true">→</span>
              </RouterLink>
            </div>
            <div class="pointer-events-none absolute bottom-7 right-8 hidden h-36 w-36 items-center justify-center rounded-full border border-white/25 bg-white/10 text-6xl font-black text-white/20 sm:flex lg:right-16 lg:h-52 lg:w-52 lg:text-8xl">
              F
            </div>
          </div>
        </Transition>

        <button class="absolute left-3 top-1/2 z-20 hidden h-9 w-9 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-white backdrop-blur transition-colors hover:bg-white/30 active:scale-95 sm:left-5 sm:grid sm:h-10 sm:w-10" type="button" :aria-label="t('home.previousBanner')" @click="showSlide(activeSlide - 1)">
          <span class="text-2xl leading-none" aria-hidden="true">‹</span>
        </button>
        <button class="absolute right-3 top-1/2 z-20 hidden h-9 w-9 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-white backdrop-blur transition-colors hover:bg-white/30 active:scale-95 sm:right-5 sm:grid sm:h-10 sm:w-10" type="button" :aria-label="t('home.nextBanner')" @click="showSlide(activeSlide + 1)">
          <span class="text-2xl leading-none" aria-hidden="true">›</span>
        </button>
        <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2">
          <button
            v-for="(slide, index) in slides"
            :key="slide.eyebrow"
            class="h-2 rounded-full transition-all"
            :class="index === activeSlide ? 'w-7 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"
            type="button"
            :aria-label="`${t('home.showBanner')} ${index + 1}`"
            :aria-current="index === activeSlide ? 'true' : undefined"
            @click="showSlide(index)"
          />
        </div>
      </div>

      <div v-if="visibleCategories.length" class="mt-6 sm:hidden">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-bold text-ink">{{ t('home.exploreCategories') }}</h2>
          <RouterLink to="/categories" class="text-xs font-semibold text-brand">{{ t('home.seeAll') }} →</RouterLink>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2">
          <RouterLink
            v-for="(category, index) in visibleCategories"
            :key="category.id"
            :to="{ name: 'category', params: { id: category.id } }"
            class="flex w-[88px] shrink-0 flex-col items-center gap-2 rounded-xl bg-white p-3 text-center shadow-card transition-transform active:scale-95"
          >
            <span class="grid h-12 w-12 place-items-center rounded-full bg-brand-tint text-lg font-bold text-brand">{{ (category.name || category.title || 'ক').slice(0, 1) }}</span>
            <span class="line-clamp-2 text-[11px] font-medium leading-4 text-ink">{{ category.name || category.title || 'ক্যাটাগরি' }}</span>
          </RouterLink>
        </div>
      </div>

      <div class="mt-5 grid grid-cols-1 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-card sm:grid-cols-3">
        <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3 sm:border-b-0 sm:border-r">
          <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-tint text-brand">✓</span>
          <div><p class="text-sm font-bold text-ink">{{ t('home.secureShopping') }}</p><p class="text-xs text-muted">{{ t('home.trustedPayment') }}</p></div>
        </div>
        <div class="flex items-center gap-3 border-b border-gray-100 px-4 py-3 sm:border-b-0 sm:border-r">
          <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-tint text-brand">↗</span>
          <div><p class="text-sm font-bold text-ink">{{ t('home.fastDelivery') }}</p><p class="text-xs text-muted">{{ t('home.deliveryDescription') }}</p></div>
        </div>
        <div class="flex items-center gap-3 px-4 py-3">
          <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-brand-tint text-brand">↺</span>
          <div><p class="text-sm font-bold text-ink">{{ t('home.easyReturns') }}</p><p class="text-xs text-muted">{{ t('home.returnDescription') }}</p></div>
        </div>
      </div>

      <section class="mt-8 overflow-hidden rounded-xl border border-brand/15 bg-white shadow-card" aria-labelledby="flash-sale-heading">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-4 py-4 sm:px-5">
          <div class="flex items-center gap-3">
            <span class="grid h-10 w-10 place-items-center rounded-full bg-brand-tint text-lg text-brand" aria-hidden="true">%</span>
            <div>
              <h2 id="flash-sale-heading" class="font-bold text-ink">{{ t('home.flashSale') }}</h2>
              <p class="text-xs text-muted">{{ t('home.flashSubtitle') }}</p>
            </div>
          </div>
          <div v-if="flashCountdown" class="rounded-lg bg-brand px-3 py-2 font-mono text-sm font-bold tracking-wider text-white" aria-live="polite">
            {{ flashCountdown }}
          </div>
        </div>
        <p v-if="flashError" class="m-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
          {{ t('home.flashError') }}: {{ flashError }}
        </p>
        <div v-else-if="flashLoading" class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3">
          <ProductSkeleton v-for="item in 3" :key="item" />
        </div>
        <div v-else-if="flashDeals.length" class="p-4">
          <p v-if="!flashProducts.length" class="rounded-lg bg-brand-tint px-4 py-6 text-center text-sm text-muted">
            {{ t('home.flashProductsEmpty') }}
          </p>
          <div v-else class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
            <ProductCard v-for="product in flashProducts.slice(0, 4)" :key="product.id" :product="product" @add-to-cart="cart.add" />
          </div>
        </div>
        <div v-else class="flex flex-col items-center gap-3 px-4 py-7 text-center sm:flex-row sm:justify-between sm:px-6 sm:text-left">
          <div>
            <p class="font-semibold text-ink">{{ t('home.flashUpcoming') }}</p>
            <p class="mt-1 text-sm text-muted">{{ t('home.flashEmpty') }}</p>
          </div>
          <RouterLink to="/categories" class="rounded-full bg-brand-tint px-4 py-2 text-sm font-semibold text-brand transition-colors hover:bg-brand hover:text-white">
            {{ t('home.seeAllCategories') }}
          </RouterLink>
        </div>
      </section>

      <section v-if="laptopLoading || laptopError || laptopProducts.length" class="mt-8" aria-labelledby="laptop-heading">
        <div class="mb-4 flex items-end justify-between gap-3">
          <div>
            <p class="text-xs font-bold uppercase tracking-wide text-brand">{{ t('home.laptopSubtitle') }}</p>
            <h2 id="laptop-heading" class="mt-1 text-xl font-bold text-ink sm:text-2xl">{{ t('home.laptopTitle') }}</h2>
          </div>
          <RouterLink :to="{ name: 'search', query: { q: 'laptop' } }" class="shrink-0 text-xs font-semibold text-brand hover:text-brand-hover sm:text-sm">
            {{ t('home.laptopViewAll') }} →
          </RouterLink>
        </div>
        <p v-if="laptopError" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
          {{ t('home.productsError') }}: {{ laptopError }}
        </p>
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
          <template v-if="laptopLoading">
            <ProductSkeleton v-for="item in 2" :key="item" />
          </template>
          <ProductCard v-for="product in laptopProducts.slice(0, 4)" v-else :key="product.id" :product="product" @add-to-cart="cart.add" />
        </div>
        <p v-if="!laptopLoading && !laptopError && !laptopProducts.length" class="rounded-xl bg-white p-6 text-center text-sm text-muted">
          {{ t('home.laptopEmpty') }}
        </p>
      </section>

      <section v-if="brands.length" class="mt-8 rounded-xl border border-gray-100 bg-white p-4 shadow-card sm:p-5" aria-labelledby="top-brands-title">
        <div class="mb-4 flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wide text-brand">Popular labels</p><h2 id="top-brands-title" class="mt-1 text-lg font-bold text-ink">Shop top brands</h2></div><RouterLink to="/categories" class="text-sm font-semibold text-brand">Browse products →</RouterLink></div>
        <div class="flex gap-3 overflow-x-auto pb-1 sm:gap-4">
          <RouterLink v-for="brand in brands" :key="brand.id" :to="{ name: 'brand-products', params: { id: brand.id } }" class="flex w-24 shrink-0 flex-col items-center gap-2 rounded-lg p-2 text-center transition hover:bg-brand-tint sm:w-28"><img v-if="brand.logo" :src="brand.logo" :alt="brand.name" class="h-14 w-14 rounded-full bg-gray-50 object-contain p-2"/><span v-else class="grid h-14 w-14 place-items-center rounded-full bg-brand-tint font-bold text-brand">{{ brand.name?.slice(0, 1) }}</span><span class="line-clamp-1 text-xs font-semibold text-ink">{{ brand.name }}</span></RouterLink>
        </div>
      </section>

      <div class="mb-4 mt-8 flex items-end justify-between gap-3 border-b border-gray-200 pb-4">
        <div>
          <p class="text-xs font-bold uppercase tracking-wide text-brand">{{ t('home.pickedForYou') }}</p>
          <h2 class="mt-1 text-xl font-bold text-ink sm:text-2xl">{{ t('home.popularProducts') }}</h2>
          <p class="mt-1 hidden text-sm text-muted sm:block">{{ t('home.collectionDescription') }}</p>
        </div>
        <RouterLink to="/categories" class="shrink-0 text-xs font-semibold text-brand transition-colors hover:text-brand-hover sm:text-sm">
          {{ t('home.seeAll') }} <span aria-hidden="true">→</span>
        </RouterLink>
      </div>

      <p v-if="error" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
        {{ t('home.productsError') }}: {{ error }}
      </p>
      <p v-if="cart.error.value" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
        {{ cart.error.value }}
      </p>
      <div v-if="categoryError && !visibleCategories.length" class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-sm text-amber-800" role="alert">
        {{ t('home.categoriesError') }}: {{ categoryError }}
      </div>

      <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4 xl:grid-cols-4">
        <template v-if="loading">
          <ProductSkeleton v-for="item in 8" :key="item" />
        </template>
        <ProductCard
          v-for="product in visibleProducts"
          v-else
          :key="product.id"
          :product="product"
          :adding="addingId === product.id"
          @add-to-cart="addToCart"
        />
      </div>
      <p v-if="!loading && !error && !products.length" class="rounded-xl bg-white p-8 text-center text-sm text-muted">
        {{ t('home.noProducts') }}
      </p>
      <div v-if="!loading && products.length > 10" class="mt-6 text-center">
        <RouterLink to="/categories" class="inline-flex rounded-full border border-brand px-6 py-2.5 text-sm font-semibold text-brand transition-colors hover:bg-brand hover:text-white active:scale-95">
          {{ t('home.moreProducts') }}
        </RouterLink>
      </div>
    </section>
  </div>
</template>

<style scoped>
.hero-fade-enter-active,
.hero-fade-leave-active {
  transition: opacity 200ms ease;
}

.hero-fade-enter-from,
.hero-fade-leave-to {
  opacity: 0;
}
</style>
