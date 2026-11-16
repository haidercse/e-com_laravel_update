<script setup>
import { computed, onMounted, ref } from 'vue';
import { useHead } from '@unhead/vue';
import api, { extractList } from '../services/api';
import { useI18n } from '../services/i18n';

const { t } = useI18n();
useHead({
  title: computed(() => t('categories.title')),
  meta: [{ name: 'description', content: computed(() => t('categories.description')) }],
});

const categories = ref([]);
const loading = ref(true);
const error = ref('');

onMounted(async () => {
  try {
    const response = await api.get('/categories');
    categories.value = extractList(response.data);
  } catch (requestError) {
    error.value = requestError.response?.data?.message || requestError.message;
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <section>
    <h1 class="text-2xl font-bold text-ink">{{ t('categories.heading') }}</h1>
    <p class="mt-1 text-sm text-muted">{{ t('categories.intro') }}</p>
    <p v-if="error" class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">{{ t('categories.error') }}: {{ error }}</p>
    <div v-if="loading" class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
      <div v-for="item in 8" :key="item" class="h-28 animate-pulse rounded-xl bg-white shadow-card" />
    </div>
    <div v-else class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
      <RouterLink
        v-for="category in categories"
        :key="category.id"
        :to="{ name: 'category', params: { id: category.id } }"
        class="flex min-h-28 items-end rounded-xl bg-white p-4 shadow-card transition-transform active:scale-95"
      >
        <span class="font-semibold text-ink">{{ category.name || category.title || t('categories.heading') }}</span>
      </RouterLink>
    </div>
    <p v-if="!loading && !error && !categories.length" class="mt-5 rounded-xl bg-white p-8 text-center text-sm text-muted">
      {{ t('categories.empty') }}
    </p>
  </section>
</template>
