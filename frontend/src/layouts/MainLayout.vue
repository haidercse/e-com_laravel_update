<script setup>
import { onMounted, ref } from 'vue';
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router';
import api, { extractList } from '../services/api';
import { useCart } from '../services/cart';
import { useI18n } from '../services/i18n';
import { useAuth } from '../services/auth';
import { catalogApi } from '../services/endpoints';

const route = useRoute();
const router = useRouter();
const cart = useCart();
const auth = useAuth();
const { locale, setLocale, t } = useI18n();
const cartOpen = ref(false);
const menuOpen = ref(false);
const searchQuery = ref('');
const suggestions = ref([]);
const suggestionsOpen = ref(false);
const menuCategories = ref([]);
const menuCategoriesError = ref('');
const menuCategoriesLoaded = ref(false);

onMounted(() => {
  cart.refresh().catch(() => {});
});

function isActive(name) {
  return route.name === name || (name === 'categories' && route.name === 'category');
}

function submitSearch() {
  const query = searchQuery.value.trim();

  if (query) {
    suggestionsOpen.value = false;
    menuOpen.value = false;
    router.push({ name: 'search', query: { q: query } });
  }
}

let suggestionTimer;
function loadSuggestions() {
  window.clearTimeout(suggestionTimer);
  const query = searchQuery.value.trim();
  if (query.length < 2) { suggestions.value = []; return; }
  suggestionTimer = window.setTimeout(async () => {
    try {
      const { data } = await catalogApi.suggestions({ query_key: query, type: 'product' });
      suggestions.value = Array.isArray(data) ? data : [];
      suggestionsOpen.value = true;
    } catch { suggestions.value = []; }
  }, 220);
}

function chooseSuggestion(item) {
  searchQuery.value = item.query || '';
  suggestionsOpen.value = false;
  if (item.type === 'product') router.push({ name: 'product', params: { id: item.id } });
  else if (item.type === 'brand') router.push({ name: 'brand-products', params: { id: item.id } });
  else if (item.type === 'shop') router.push({ name: 'shop', params: { id: item.id } });
  else router.push({ name: 'search', query: { q: item.query || '' } });
}

function openCart() {
  menuOpen.value = false;
  cartOpen.value = true;
}

async function toggleMenu() {
  cartOpen.value = false;
  menuOpen.value = !menuOpen.value;

  if (!menuOpen.value || menuCategoriesLoaded.value) {
    return;
  }

  try {
    const response = await api.get('/categories');
    menuCategories.value = extractList(response.data);
    menuCategoriesLoaded.value = true;
    menuCategoriesError.value = '';
  } catch (error) {
    menuCategoriesError.value = error.response?.data?.message || error.message;
  }
}
</script>

<template>
  <div class="min-h-screen bg-app pb-20 md:pb-0">
    <header class="sticky top-0 z-30 border-b border-gray-100 bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-3 sm:px-6">
        <div class="flex justify-end pt-2">
          <div class="inline-flex items-center gap-0.5 rounded-full bg-gray-100 p-0.5" role="group" :aria-label="t('language.select')">
            <button
              class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors active:scale-95"
              :class="locale === 'bn' ? 'bg-brand text-white shadow-sm' : 'text-muted hover:text-ink'"
              type="button"
              :aria-pressed="locale === 'bn'"
              @click="setLocale('bn')"
            >
              বাংলা
            </button>
            <button
              class="rounded-full px-3 py-1 text-[11px] font-semibold transition-colors active:scale-95"
              :class="locale === 'en' ? 'bg-brand text-white shadow-sm' : 'text-muted hover:text-ink'"
              type="button"
              :aria-pressed="locale === 'en'"
              @click="setLocale('en')"
            >
              English
            </button>
          </div>
        </div>

        <div class="flex h-16 items-center gap-2 sm:gap-4">
          <button
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-ink transition-colors hover:bg-brand-tint active:scale-95 lg:hidden"
            type="button"
            :aria-label="t('header.menu')"
            :aria-expanded="menuOpen"
            @click="toggleMenu"
          >
            <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
              <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <RouterLink to="/" class="shrink-0 text-lg font-extrabold tracking-tight text-ink sm:text-xl">
            <span class="text-brand">Flix</span>Shop
          </RouterLink>

          <form
            class="relative mx-auto hidden h-10 min-w-0 max-w-2xl flex-1 overflow-visible rounded-full border border-brand/40 bg-white focus-within:border-brand md:flex"
            @submit.prevent="submitSearch"
          >
            <input
              v-model="searchQuery"
              class="min-w-0 flex-1 bg-transparent px-4 text-sm text-ink outline-none placeholder:text-gray-400"
              type="search"
              :placeholder="t('header.search')"
              :aria-label="t('header.search')"
              @input="loadSuggestions"
              @focus="suggestionsOpen = Boolean(suggestions.length)"
            />
            <button
              class="m-0.5 rounded-full bg-brand px-5 text-sm font-semibold text-white transition-colors hover:bg-brand-hover active:scale-95"
              type="submit"
            >
              {{ t('header.searchButton') }}
            </button>
            <div v-if="suggestionsOpen && suggestions.length" class="absolute left-0 right-0 top-11 z-40 overflow-hidden rounded-xl border border-gray-100 bg-white shadow-xl">
              <button v-for="item in suggestions" :key="`${item.type}-${item.id}`" class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm hover:bg-brand-tint" type="button" @mousedown.prevent="chooseSuggestion(item)"><span class="truncate font-medium text-ink">{{ item.query }}</span><span class="shrink-0 text-[10px] uppercase tracking-wide text-muted">{{ item.type_string || item.type }}</span></button>
            </div>
          </form>

          <div class="ml-auto flex shrink-0 items-center gap-1 sm:gap-2">
            <RouterLink
              to="/account"
              class="flex h-10 w-10 items-center justify-center rounded-full text-ink transition-colors hover:bg-brand-tint active:scale-95"
              :aria-label="t('nav.account')"
            >
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <circle cx="12" cy="8" r="3.5" />
                <path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0" />
              </svg>
            </RouterLink>
            <button
              class="relative flex h-10 w-10 items-center justify-center rounded-full text-ink transition-colors hover:bg-brand-tint active:scale-95"
              type="button"
              :aria-label="t('header.openCart')"
              @click="openCart"
            >
              <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L21 8H6" />
                <circle cx="10" cy="20" r="1" />
                <circle cx="18" cy="20" r="1" />
              </svg>
              <span
                v-if="cart.count.value"
                class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-brand px-1 text-[10px] font-bold text-white"
              >{{ cart.count.value }}</span>
            </button>
          </div>
        </div>

        <form
          class="flex h-10 items-center overflow-hidden rounded-full border border-brand/30 bg-white md:hidden"
          @submit.prevent="submitSearch"
        >
          <input
            v-model="searchQuery"
            class="min-w-0 flex-1 bg-transparent px-4 text-sm text-ink outline-none placeholder:text-gray-400"
            type="search"
            :placeholder="t('header.search')"
            :aria-label="t('header.search')"
          />
          <button class="m-0.5 flex h-8 w-10 items-center justify-center rounded-full bg-brand text-white active:scale-95" type="submit" :aria-label="t('header.searchButton')">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="10.8" cy="10.8" r="6.8" />
              <path stroke-linecap="round" d="m16 16 4 4" />
            </svg>
          </button>
        </form>
      </div>

      <nav class="mt-3 hidden border-t border-gray-100 md:block" :aria-label="t('header.home')">
        <div class="mx-auto flex h-11 max-w-7xl items-center gap-7 px-6 text-sm">
          <RouterLink to="/" class="font-semibold text-brand">{{ t('header.home') }}</RouterLink>
          <RouterLink to="/categories" class="text-muted transition-colors hover:text-brand">{{ t('header.categories') }}</RouterLink>
          <RouterLink to="/deals" class="text-muted transition-colors hover:text-brand">{{ t('header.deals') }}</RouterLink>
          <RouterLink to="/featured" class="text-muted transition-colors hover:text-brand">Featured</RouterLink>
          <RouterLink to="/best-sellers" class="text-muted transition-colors hover:text-brand">Best sellers</RouterLink>
          <RouterLink to="/shops" class="text-muted transition-colors hover:text-brand">Shops</RouterLink>
          <RouterLink to="/account" class="text-muted transition-colors hover:text-brand">{{ t('header.account') }}</RouterLink>
          <RouterLink to="/messages" class="text-muted transition-colors hover:text-brand">Messages</RouterLink>
          <RouterLink v-if="auth.user.value?.user_type === 'delivery_boy' || auth.user.value?.type === 'delivery_boy'" to="/delivery" class="text-muted transition-colors hover:text-brand">Delivery</RouterLink>
        </div>
      </nav>
    </header>

    <main class="mx-auto max-w-[1440px] px-3 py-4 sm:px-6 sm:py-6">
      <RouterView />
    </main>

    <footer class="mt-10 border-t border-gray-200 bg-white pb-24 pt-9 md:pb-8">
      <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 sm:px-6 md:grid-cols-4">
        <div class="col-span-2 md:col-span-1">
          <RouterLink to="/" class="text-xl font-extrabold text-ink"><span class="text-brand">Flix</span>Shop</RouterLink>
          <p class="mt-3 max-w-xs text-sm leading-6 text-muted">{{ t('footer.description') }}</p>
        </div>
        <div>
          <h2 class="font-bold text-ink">{{ t('footer.quickLinks') }}</h2>
          <div class="mt-3 flex flex-col gap-2 text-sm text-muted">
            <RouterLink to="/categories" class="hover:text-brand">{{ t('header.categories') }}</RouterLink>
            <RouterLink to="/shops" class="hover:text-brand">Shops</RouterLink>
            <RouterLink to="/deals" class="hover:text-brand">{{ t('header.deals') }}</RouterLink>
          </div>
        </div>
        <div>
          <h2 class="font-bold text-ink">{{ t('footer.customerService') }}</h2>
          <div class="mt-3 flex flex-col gap-2 text-sm text-muted">
            <RouterLink to="/account" class="hover:text-brand">{{ t('header.account') }}</RouterLink>
            <RouterLink to="/messages" class="hover:text-brand">Messages</RouterLink>
            <RouterLink to="/policies/return" class="hover:text-brand">Returns policy</RouterLink>
            <RouterLink to="/policies/support" class="hover:text-brand">Support policy</RouterLink>
          </div>
        </div>
        <div>
          <h2 class="font-bold text-ink">{{ t('footer.safeShopping') }}</h2>
          <p class="mt-3 text-sm leading-6 text-muted">{{ t('footer.safeDescription') }}</p>
        </div>
      </div>
      <div class="mx-auto mt-8 max-w-7xl border-t border-gray-100 px-4 pt-5 text-center text-xs text-gray-400 sm:px-6 md:text-left">
        © {{ new Date().getFullYear() }} FlixShop. {{ t('footer.rights') }}
      </div>
    </footer>

    <nav class="fixed inset-x-0 bottom-0 z-30 grid grid-cols-4 border-t border-gray-100 bg-white pb-[env(safe-area-inset-bottom)] shadow-[0_-2px_12px_rgb(17_24_39/6%)] md:hidden" :aria-label="t('header.home')">
      <RouterLink
        to="/"
        class="flex flex-col items-center gap-1 py-2 text-[11px] transition-colors"
        :class="isActive('home') ? 'text-brand' : 'text-muted'"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z" />
        </svg>
        <span>{{ t('nav.home') }}</span>
      </RouterLink>
      <RouterLink
        to="/categories"
        class="flex flex-col items-center gap-1 py-2 text-[11px] transition-colors"
        :class="isActive('categories') ? 'text-brand' : 'text-muted'"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <rect x="3" y="3" width="8" height="8" rx="1.5" />
          <rect x="13" y="3" width="8" height="8" rx="1.5" />
          <rect x="3" y="13" width="8" height="8" rx="1.5" />
          <rect x="13" y="13" width="8" height="8" rx="1.5" />
        </svg>
        <span>{{ t('nav.categories') }}</span>
      </RouterLink>
      <button
        class="relative flex flex-col items-center gap-1 py-2 text-[11px] text-muted transition-colors active:scale-95"
        type="button"
        :aria-label="t('header.openCart')"
        @click="openCart"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.2 11.1a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L21 8H6" />
          <circle cx="10" cy="20" r="1" />
          <circle cx="18" cy="20" r="1" />
        </svg>
        <span>{{ t('nav.cart') }}</span>
        <span
          v-if="cart.count.value"
          class="absolute left-1/2 top-1 ml-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-brand px-1 text-[9px] font-bold text-white"
        >{{ cart.count.value }}</span>
      </button>
      <RouterLink
        to="/account"
        class="flex flex-col items-center gap-1 py-2 text-[11px] transition-colors"
        :class="isActive('account') ? 'text-brand' : 'text-muted'"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <circle cx="12" cy="8" r="3.5" />
          <path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0" />
        </svg>
        <span>{{ t('nav.account') }}</span>
      </RouterLink>
    </nav>

    <Transition name="fade">
      <button
        v-if="menuOpen"
        class="fixed inset-0 z-40 cursor-default bg-black/40 lg:hidden"
        type="button"
        :aria-label="t('header.closeMenu')"
        @click="menuOpen = false"
      />
    </Transition>
    <Transition name="left-drawer">
      <aside v-if="menuOpen" class="fixed inset-y-0 left-0 z-50 flex w-[min(84vw,340px)] flex-col bg-white shadow-2xl lg:hidden" :aria-label="t('header.menu')">
        <div class="flex items-center justify-between bg-brand px-5 py-5 text-white">
          <div>
            <p class="text-xs font-medium text-white/80">FlixShop</p>
            <h2 class="mt-1 text-lg font-bold">{{ t('menu.title') }}</h2>
          </div>
          <button class="rounded-full p-2 text-white/90 hover:bg-white/15 active:scale-95" type="button" :aria-label="t('header.closeMenu')" @click="menuOpen = false">
            <span class="text-2xl leading-none" aria-hidden="true">×</span>
          </button>
        </div>
        <nav class="flex-1 overflow-y-auto p-3">
          <RouterLink to="/" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">⌂</span> {{ t('menu.home') }}
          </RouterLink>
          <RouterLink to="/categories" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">▦</span> {{ t('menu.categories') }}
          </RouterLink>
          <div class="mt-2 border-t border-gray-100 pt-2">
            <p class="px-3 py-2 text-xs font-bold uppercase tracking-wide text-muted">{{ t('menu.featuredCategories') }}</p>
            <p v-if="menuCategoriesError" class="px-3 py-2 text-xs text-red-700" role="alert">{{ menuCategoriesError }}</p>
            <RouterLink
              v-for="category in menuCategories"
              :key="category.id"
              :to="{ name: 'category', params: { id: category.id } }"
              class="block rounded-lg px-3 py-2.5 text-sm text-muted hover:bg-brand-tint hover:text-brand"
              @click="menuOpen = false"
            >
              {{ category.name || category.title || t('menu.featuredCategories') }}
            </RouterLink>
            <p v-if="menuCategoriesLoaded && !menuCategories.length" class="px-3 py-2 text-xs text-muted">{{ t('home.noCategories') }}</p>
          </div>
          <RouterLink to="/deals" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">%</span> {{ t('menu.deals') }}
          </RouterLink>
          <RouterLink to="/shops" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">S</span> Shops
          </RouterLink>
          <RouterLink to="/messages" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">✉</span> Messages
          </RouterLink>
          <RouterLink to="/account" class="flex items-center gap-3 rounded-lg px-3 py-3 font-semibold text-ink hover:bg-brand-tint" @click="menuOpen = false">
            <span class="grid h-9 w-9 place-items-center rounded-lg bg-brand-tint text-brand">♙</span> {{ t('menu.account') }}
          </RouterLink>
        </nav>
        <p class="border-t border-gray-100 px-5 py-4 text-xs text-muted">{{ t('menu.note') }}</p>
      </aside>
    </Transition>

    <Transition name="fade">
      <button
        v-if="cartOpen"
        class="fixed inset-0 z-40 cursor-default bg-black/40"
        type="button"
        :aria-label="t('header.closeCart')"
        @click="cartOpen = false"
      />
    </Transition>
    <Transition name="drawer">
      <aside
        v-if="cartOpen"
        class="fixed inset-y-0 right-0 z-50 flex w-full max-w-md flex-col bg-white shadow-2xl"
        :aria-label="t('cart.title')"
        aria-modal="true"
        role="dialog"
      >
        <div class="flex items-center justify-between border-b border-gray-100 bg-gray-900 px-5 py-4 text-white">
          <h2 class="text-lg font-bold">{{ t('cart.title') }} <span class="font-medium text-gray-300">({{ cart.count.value }})</span></h2>
          <button class="rounded-full p-2 text-gray-200 transition-colors hover:bg-white/10 active:scale-95" type="button" :aria-label="t('header.closeCart')" @click="cartOpen = false">
            <span aria-hidden="true" class="text-xl leading-none">×</span>
          </button>
        </div>

        <p v-if="cart.error.value" class="mx-5 mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">
          {{ cart.error.value }}
        </p>

        <div v-if="cart.loading.value" class="space-y-4 p-5" :aria-label="t('cart.loading')">
          <div v-for="item in 3" :key="item" class="h-20 animate-pulse rounded-xl bg-gray-100" />
        </div>
        <div v-else-if="!cart.items.value.length" class="flex flex-1 flex-col items-center justify-center px-6 text-center">
          <span class="mb-3 text-4xl text-gray-300" aria-hidden="true">♧</span>
          <p class="font-semibold text-ink">{{ t('cart.empty') }}</p>
          <p class="mt-1 text-sm text-muted">{{ t('cart.emptyDescription') }}</p>
        </div>
        <div v-else class="flex-1 space-y-4 overflow-y-auto p-5">
          <article v-for="item in cart.items.value" :key="item.id" class="flex gap-3">
            <img
              :src="item.product_thumbnail_image"
              :alt="item.product_name"
              class="h-20 w-20 rounded-lg bg-gray-50 object-contain"
            />
            <div class="min-w-0 flex-1">
              <p class="line-clamp-2 text-sm font-medium text-ink">{{ item.product_name }}</p>
              <p class="mt-1 text-sm font-bold text-brand">{{ item.currency_symbol }}{{ item.price }}</p>
              <div class="mt-2 flex items-center gap-2">
                <button class="h-7 w-7 rounded-md border border-gray-200 text-ink active:scale-95" type="button" :aria-label="`${t('cart.decrease')} ${item.product_name}`" @click="cart.setQuantity(item, Number(item.quantity) - 1)">−</button>
                <span class="min-w-5 text-center text-sm">{{ item.quantity }}</span>
                <button class="h-7 w-7 rounded-md border border-gray-200 text-ink active:scale-95" type="button" :aria-label="`${t('cart.increase')} ${item.product_name}`" @click="cart.setQuantity(item, Number(item.quantity) + 1)">+</button>
                <button class="ml-auto text-xs text-muted underline hover:text-brand" type="button" @click="cart.remove(item.id)">{{ t('cart.remove') }}</button>
              </div>
            </div>
          </article>
        </div>

        <div v-if="cart.items.value.length" class="border-t border-gray-100 p-5">
          <div class="mb-4 flex justify-between text-sm">
            <span class="text-muted">{{ t('cart.subtotal') }}</span>
            <span class="font-bold text-ink">{{ cart.items.value[0]?.currency_symbol }}{{ cart.subtotal.value.toFixed(2) }}</span>
          </div>
          <RouterLink to="/checkout" class="block rounded-full bg-brand py-3 text-center font-semibold text-white transition-transform hover:bg-brand-hover active:scale-95" @click="cartOpen = false">
            {{ t('cart.checkout') }}
          </RouterLink>
        </div>
      </aside>
    </Transition>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 180ms ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.drawer-enter-active,
.drawer-leave-active {
  transition: transform 220ms ease;
}

.drawer-enter-from,
.drawer-leave-to {
  transform: translateX(100%);
}

.left-drawer-enter-active,
.left-drawer-leave-active {
  transition: transform 220ms ease;
}

.left-drawer-enter-from,
.left-drawer-leave-to {
  transform: translateX(-100%);
}
</style>
