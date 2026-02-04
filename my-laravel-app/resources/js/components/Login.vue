<template>
  <div class="login-form">
    <h1>Login</h1>
    <form @submit.prevent="loginUser">
      <input v-model="form.email" type="email" placeholder="Email" required />
      <input v-model="form.password" type="password" placeholder="Password" required />
      <button type="submit">Login</button>
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
    async loginUser() {
      this.error = '';
      this.successMessage = '';

      // Important: enable sending cookies for session
      axios.defaults.withCredentials = true;

      try {
        const response = await axios.post('/login', this.form);
        this.successMessage = 'Jūs pieslēdzāties!'

         this.form = {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            role: "user",
          };

        console.log(response.data); // Shows success message and user info
      } catch (err) {
        if (err.response) {
          this.error = err.response.data.message;
        } else {
          console.error(err);
        }
      }
    }
  }
};
</script>

<style scoped>
.error { color: red; }
.success { color: green;}
</style>
