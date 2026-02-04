<template>
  <nav>
    <a href="/">Home</a>
    <a href="/about">About</a>
    <a href="register">Register</a>
    <a href="/login">Login</a>
    <!-- <a href="/logout">Log out</a> -->
  </nav>
  <div>
    <Logout />
    <router-view />
  </div>
</template>

<!-- <script>
  import Logout from './components/Logout.vue';

  export default {
    components: { Logout },
  };
</script> -->
<script setup>
  import Logout from "../components/Logout.vue";
</script>