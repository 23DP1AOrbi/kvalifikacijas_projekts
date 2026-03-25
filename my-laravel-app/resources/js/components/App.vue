<script setup>
import { onMounted } from "vue";
import { user, fetchUser, logout } from "../services/auth";
// import Logout from "./Logout.vue";

onMounted(fetchUser);
</script>

<template>
  <nav>
    <a href="/">Home</a>
    <a href="/about">About</a>

    <a v-if="!user" href="/register">Register</a>
    <a v-if="!user" href="/login">Login</a>

    <button v-if="user" @click="logout">Logout</button>
    <!-- <Logout v-if="user" /> -->
  </nav>

  <router-view />
</template>
