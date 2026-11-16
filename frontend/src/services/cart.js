import { computed, reactive } from 'vue';
import api from './api';

const state = reactive({
  items: [],
  loading: false,
  error: '',
});

function getUserId() {
  return localStorage.getItem('ecom_user_id');
}

function flattenCart(payload) {
  if (!Array.isArray(payload)) {
    throw new Error('The cart API response was not a list.');
  }

  return payload.flatMap((shop) => shop.cart_items || []);
}

function messageFrom(error) {
  return error.response?.data?.message || error.message || 'The cart request failed.';
}

async function refresh() {
  const userId = getUserId();
  state.error = '';

  if (!userId) {
    state.items = [];
    return;
  }

  state.loading = true;

  try {
    const response = await api.post(`/carts/${encodeURIComponent(userId)}`);
    state.items = flattenCart(response.data);
  } catch (error) {
    state.error = messageFrom(error);
    throw error;
  } finally {
    state.loading = false;
  }
}

async function add(product, quantity = 1) {
  const userId = getUserId();

  if (!userId) {
    state.error = 'Sign in to add items to your cart.';
    return;
  }

  state.error = '';

  try {
    const response = await api.post('/carts/add', {
      id: product.id,
      user_id: userId,
      quantity,
      variant: product.variation || '',
      cost_matrix: import.meta.env.VITE_COST_MATRIX || '',
    });

    if (!response.data?.result) {
      throw new Error(response.data?.message || 'The product could not be added.');
    }

    await refresh();
  } catch (error) {
    state.error = messageFrom(error);
  }
}

async function setQuantity(item, quantity) {
  if (quantity < 1) {
    await remove(item.id);
    return;
  }

  state.error = '';

  try {
    const response = await api.post('/carts/change-quantity', {
      id: item.id,
      quantity,
    });

    if (!response.data?.result) {
      throw new Error(response.data?.message || 'The cart could not be updated.');
    }

    await refresh();
  } catch (error) {
    state.error = messageFrom(error);
  }
}

async function remove(itemId) {
  state.error = '';

  try {
    const response = await api.delete(`/carts/${itemId}`);

    if (!response.data?.result) {
      throw new Error(response.data?.message || 'The item could not be removed.');
    }

    await refresh();
  } catch (error) {
    state.error = messageFrom(error);
  }
}

export function useCart() {
  return {
    items: computed(() => state.items),
    count: computed(() =>
      state.items.reduce((total, item) => total + Number(item.quantity || 0), 0),
    ),
    subtotal: computed(() =>
      state.items.reduce(
        (total, item) => total + Number(item.price || 0) * Number(item.quantity || 0),
        0,
      ),
    ),
    loading: computed(() => state.loading),
    error: computed(() => state.error),
    refresh,
    add,
    setQuantity,
    remove,
  };
}
