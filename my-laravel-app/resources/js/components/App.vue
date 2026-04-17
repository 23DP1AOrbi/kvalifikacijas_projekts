<script setup>
import { onMounted } from "vue";
import { fetchUser } from "../services/auth";
import Navbar from "./Navbar.vue"; // Import the new component

onMounted(fetchUser);
</script>

<template>
  <v-app>
    <Navbar />

    <v-main class="bg-grey-lighten-4">
      <router-view />
    </v-main>
  </v-app>
</template>

<style>
/* Global styles if needed */
body {
  font-family: 'Inter', sans-serif;
}
</style>