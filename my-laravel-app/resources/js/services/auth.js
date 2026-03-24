import api from './api';

export async function initCsrf() {
  await api.get('/sanctum/csrf-cookie');
}
