import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v2',
  headers: {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('ecom_access_token');

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

export function extractList(payload) {
  const candidates = [
    payload,
    payload?.data,
    payload?.products,
    payload?.categories,
    payload?.data?.data,
    payload?.data?.products,
    payload?.data?.categories,
  ];

  const list = candidates.find(Array.isArray);

  if (!list) {
    throw new Error('The API response did not contain a product or category list.');
  }

  return list;
}

export function resolveAssetUrl(value) {
  const path = String(value || '').trim();

  if (!path || /^(https?:|data:|\/)/i.test(path)) {
    return path;
  }

  return `/${path.replace(/^\.?\//, '')}`;
}

export default api;
