<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { resolveAssetUrl } from '../services/api';
import { useI18n } from '../services/i18n';

const { t } = useI18n();

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  adding: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['add-to-cart']);

function amount(value) {
  const parsed = Number(String(value ?? '').replace(/[^\d.-]/g, ''));
  return Number.isFinite(parsed) ? parsed : 0;
}

const name = computed(() => props.product.name || props.product.title || 'Product');
const image = computed(
  () => resolveAssetUrl(
    props.product.thumbnail_image ||
    props.product.thumbnail_image_url ||
    props.product.thumbnail_img_url ||
    props.product.image ||
    '',
  ),
);
const price = computed(() =>
  amount(
    props.product.base_discounted_price ??
      props.product.discounted_price ??
      props.product.unit_price ??
      props.product.price ??
      props.product.main_price,
  ),
);
const originalPrice = computed(() =>
  amount(
    props.product.base_price ??
      props.product.unit_price ??
      props.product.price ??
      props.product.stroked_price ??
      price.value,
  ),
);
const discount = computed(() => {
  if (props.product.discount_percentage) {
    return Math.round(amount(props.product.discount_percentage));
  }

  return (props.product.has_discount || originalPrice.value > price.value) && originalPrice.value > price.value
    ? Math.round(((originalPrice.value - price.value) / originalPrice.value) * 100)
    : 0;
});
const currency = computed(() => {
  const formattedPrice = String(props.product.main_price || '');
  const symbol = formattedPrice.match(/^[^\d\s.,]+/);

  return props.product.currency_symbol || symbol?.[0] || '৳';
});
const rating = computed(() => amount(props.product.rating || props.product.rating_average));
const stock = computed(() => Number(props.product.current_stock ?? props.product.stock ?? 0));

function formatPrice(value) {
  return new Intl.NumberFormat('en-BD', { maximumFractionDigits: 0 }).format(value);
}

function priceLabel(value, formattedValue) {
  if (formattedValue && !/[<>]/.test(String(formattedValue))) {
    return String(formattedValue).trim();
  }

  return `${currency.value} ${formatPrice(value)}`;
}
</script>

<template>
  <article class="group relative overflow-hidden rounded-xl border border-gray-100 bg-white shadow-card transition duration-200 hover:-translate-y-1 hover:shadow-lg active:scale-[0.98]">
    <RouterLink :to="{ name: 'product', params: { id: product.id } }" class="block">
      <div class="relative aspect-[4/3] overflow-hidden bg-gray-50 sm:aspect-square">
        <img v-if="image" :src="image" :alt="name" class="h-full w-full object-contain p-3 transition-transform duration-300 group-hover:scale-105 sm:p-5" loading="lazy" />
        <div v-else class="flex h-full items-center justify-center bg-gradient-to-br from-brand-tint to-gray-100">
          <svg viewBox="0 0 64 64" class="h-14 w-14 text-brand/40" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22h40l-4 30H16l-4-30Z" />
            <path stroke-linecap="round" d="M23 23v-5a9 9 0 0 1 18 0v5" />
          </svg>
        </div>
        <span v-if="discount" class="absolute left-2 top-2 rounded-md bg-brand px-2 py-1 text-[10px] font-bold text-white sm:left-3 sm:top-3">{{ discount }}{{ t('product.discount') }}</span>
        <span v-else-if="product.is_new" class="absolute left-2 top-2 rounded-md bg-ink px-2 py-1 text-[10px] font-bold text-white sm:left-3 sm:top-3">{{ t('product.new') }}</span>
      </div>
      <div class="p-3 pb-2 sm:p-4 sm:pb-2">
        <h2 class="line-clamp-2 min-h-10 text-[13px] font-medium leading-5 text-ink sm:text-sm">{{ name }}</h2>
        <div class="mt-1 flex min-h-6 flex-wrap items-baseline gap-x-2">
          <span class="text-base font-extrabold text-brand sm:text-lg">{{ priceLabel(price, product.main_price) }}</span>
          <span v-if="discount" class="text-[11px] text-gray-400 line-through">{{ priceLabel(originalPrice, product.stroked_price) }}</span>
        </div>
        <div class="mt-1 flex min-h-4 items-center justify-between gap-2">
          <p v-if="rating > 0" class="flex items-center gap-1 text-[11px] text-muted">
            <span class="text-amber-400" aria-hidden="true">★</span>{{ rating.toFixed(1) }}
          </p>
          <p v-if="stock > 0" class="ml-auto text-[10px] font-medium text-success">{{ t('product.inStock') }}</p>
        </div>
      </div>
    </RouterLink>
    <div class="px-3 pb-3 sm:px-4 sm:pb-4">
      <button
        class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-tint px-3 py-2.5 text-xs font-bold text-brand transition-colors hover:bg-brand hover:text-white active:scale-95 disabled:cursor-wait disabled:opacity-60 sm:py-2"
        type="button"
        :disabled="adding"
        @click.prevent.stop="$emit('add-to-cart', product)"
      >
        <span class="grid h-5 w-5 place-items-center rounded-full bg-white/80 text-base leading-none" aria-hidden="true">{{ adding ? '…' : '+' }}</span>
        {{ adding ? t('product.adding') : t('product.addToCart') }}
      </button>
    </div>
  </article>
</template>
