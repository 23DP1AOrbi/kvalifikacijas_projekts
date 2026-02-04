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
     async getCsrfToken() {
        try {
            const res = await axios.get('/csrf-token'); // hits the Laravel route
            axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.csrf_token;
            axios.defaults.withCredentials = true; // include session cookie
        } catch (error) {
            console.error("Failed to get CSRF token", error);
        }
    },
    
    async loginUser() {
      this.error = "";

    //   await this.getCsrfToken();
      
      try {
        const res = await axios.post("/login", this.form);
        console.log(res.data.user); // user info
        this.successMessage = 'Veiksmīgi pieslēdzāties!'
      } catch (err) {
        this.error = err.response.data.message;
      }
    }
  }
};
</script>

<style scoped>
.error { color: red; }
.success { color: green;}
</style>
