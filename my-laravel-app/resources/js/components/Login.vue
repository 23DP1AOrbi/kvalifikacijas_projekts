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

<script setup>
import { reactive, ref } from "vue";
import { login } from "../services/auth";

// Reactive form state
const form = reactive({
  email: "",
  password: ""
});

const error = ref("");
const successMessage = ref("");

// Login method
const loginUser = async () => {
  error.value = "";
  successMessage.value = "";

  try {
    const res = await login(form);  // now res is defined
    successMessage.value = "Veiksmīgi pieslēdzāties!";
    console.log(res.data);

    // Optional: redirect
    window.location.href = "/";

  } catch (err) {
    error.value = err.response?.data?.message || "Kļūda pieslēdzoties";
  }
};
</script>

<style scoped>
.error { color: red; }
.success { color: green; }
</style>
