

import api from '../services/api';

// await api.get('/sanctum/csrf-cookie');
// await api.post('/login', this.form);
// await axios.post("/logout");
// await api.post('/register', this.form);
axios.get("/user").then(res => console.log(res.data));
