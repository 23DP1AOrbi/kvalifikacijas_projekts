<template>
  <div class="login-form">
    <h1>Pieslēgšanās</h1>
    <form @submit.prevent="loginUser">
      <input v-model="form.email" type="email" placeholder="E-pasts" required />
      <input v-model="form.password" type="password" placeholder="Parole" required />
      <button type="submit">Pieslēgties</button>
    </form>
    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
  </div>
</template>

<script>
import axios from '../bootstrap.js';

export default {
  data() {
    return {
      form: { email: '', password: '' },
      error: '',
      successMessage: ''
    };
  },
  methods: {
    async loginUser() {
      this.error = '';
      this.successMessage = '';

      try {
        // 1️⃣ Get CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        // 2️⃣ Login
        const res = await axios.post('/login', this.form);

        this.successMessage = 'Veiksmīgi pieslēdzāties!';
        console.log(res.data);
      } catch (err) {
        this.error = err.response?.data?.message || 'Kļūda pieslēdzoties';
      }
    }
  }
};
</script>

<style scoped>
.error { color: red; }
.success { color: green; }
</style>



<!-- <template>
  <div class="login-form">
    <h1>Pieslēgšanās</h1>
    <form @submit.prevent="loginUser">
      <input v-model="form.email" type="email" placeholder="E-pasts" required />
      <input v-model="form.password" type="password" placeholder="Parole" required />
      <button type="submit">Pieslēgties</button>
    </form>
    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
  </div>
</template>

<script>
// import api from '../services/api';
// import { initCsrf } from '../services/auth';

// export default {
//   data() {
//     return {
//       form: {
//         email: '',
//         password: ''
//       },
//       error: '',
//       successMessage: "",
//     };
//   },
// methods: {
//      async getCsrfToken() {
//         try {
//             const res = await axios.get('/csrf-token'); // hits the Laravel route
//             axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.csrf_token;
//             axios.defaults.withCredentials = true; // include session cookie
//         } catch (error) {
//             console.error("Failed to get CSRF token", error);
//         }
//     },
    
//     async loginUser() {
//       this.error = "";

//     //   await this.getCsrfToken();
      
//       try {
//         const res = await axios.post("/login", this.form);
//         console.log(res.data.user); // user info
//         this.successMessage = 'Veiksmīgi pieslēdzāties!'
//       } catch (err) {
//         this.error = err.response.data.message;
//       }
//     }
//   }
// };

// async function login() {
//   await initCsrf(); // only once before auth

//   await api.post('/login', {
//     email: this.email,
//     password: this.password
//   });
// }

import axios from 'axios';

export default {
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      error: '',
      successMessage: "",
    };
  },

  methods: {
    async loginUser() {
      this.error = "";
      this.successMessage = "";

      try {
        // 1. Get CSRF cookie (Sanctum)
        await axios.get('/sanctum/csrf-cookie');

        // 2. Login request
        const res = await axios.post('/login', this.form);

        console.log(res.data);
        this.successMessage = 'Veiksmīgi pieslēdzāties!';

      } catch (err) {
        this.error = err.response?.data?.message || 'Kļūda pieslēdzoties';
      }
    }
  }
};
</script>

<style scoped>
.error { color: red; }
.success { color: green;}
</style> -->
