<script setup>
import { onMounted } from "vue";
import { user, fetchUser, logout } from "../services/auth";

onMounted(fetchUser);
</script>

<template>
  <nav>
    <router-link to="/">Home</router-link>
    <router-link to="/about">About</router-link>
    <router-link to="/dizaini">Dizaini</router-link>

    <router-link v-if="!user" to="/register">Register</router-link>
    <router-link v-if="!user" to="/login">Login</router-link>

    <router-link v-if="user?.role === 'admin'" to="/pievienot">Pievienot</router-link>
    <router-link v-if="user" to="/profils">Profils</router-link>
    <button v-if="user" @click="logout">Logout</button>
  </nav>

  <router-view />
</template>