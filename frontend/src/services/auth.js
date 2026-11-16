import { computed, reactive } from 'vue';
import api from './api';

const state = reactive({
  user: readStoredUser(),
  loading: false,
  error: '',
});

function readStoredUser() {
  try {
    return JSON.parse(localStorage.getItem('ecom_user') || 'null');
  } catch {
    return null;
  }
}

function persist(user, token) {
  if (token) localStorage.setItem('ecom_access_token', token);
  if (user?.id) localStorage.setItem('ecom_user_id', String(user.id));
  if (user) localStorage.setItem('ecom_user', JSON.stringify(user));
  state.user = user || null;
  state.error = '';
}

function errorMessage(error) {
  return error.response?.data?.message || error.message || 'The request failed.';
}

async function login(credentials) {
  state.loading = true;
  state.error = '';
  try {
    const { data } = await api.post('/auth/login', {
      email: credentials.email,
      password: credentials.password,
      ...(credentials.accountType === 'delivery_boy' ? { user_type: 'delivery_boy' } : {}),
    });
    if (!data?.result || !data?.access_token) throw new Error(data?.message || 'Unable to sign in.');
    persist(data.user, data.access_token);
    return data.user;
  } catch (error) {
    state.error = errorMessage(error);
    throw error;
  } finally {
    state.loading = false;
  }
}

async function signup(details) {
  state.loading = true;
  state.error = '';
  try {
    const { data } = await api.post('/auth/signup', {
      name: details.name,
      email_or_phone: details.email,
      password: details.password,
      register_by: 'email',
    });
    if (!data?.result) throw new Error(data?.message || 'Unable to create your account.');
    return data;
  } catch (error) {
    state.error = errorMessage(error);
    throw error;
  } finally {
    state.loading = false;
  }
}

async function loadUser() {
  if (!localStorage.getItem('ecom_access_token')) return null;
  try {
    const { data } = await api.get('/auth/user');
    state.user = data;
    localStorage.setItem('ecom_user', JSON.stringify(data));
    if (data?.id) localStorage.setItem('ecom_user_id', String(data.id));
    return data;
  } catch (error) {
    if (error.response?.status === 401) clear();
    throw error;
  }
}

async function logout() {
  try {
    if (localStorage.getItem('ecom_access_token')) await api.get('/auth/logout');
  } finally {
    clear();
  }
}

function clear() {
  localStorage.removeItem('ecom_access_token');
  localStorage.removeItem('ecom_user_id');
  localStorage.removeItem('ecom_user');
  state.user = null;
  state.error = '';
}

export function useAuth() {
  return {
    user: computed(() => state.user),
    loading: computed(() => state.loading),
    error: computed(() => state.error),
    login,
    signup,
    loadUser,
    logout,
    clear,
  };
}
