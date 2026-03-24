

import api from '../services/api';

await api.get('/sanctum/csrf-cookie');
await api.post('/login', this.form);
// await api.post('/register', this.form);