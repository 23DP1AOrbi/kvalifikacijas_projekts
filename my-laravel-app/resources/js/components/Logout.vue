<template>
  <button @click="logout">Logout</button>
</template>

<script setup>
import axios from "axios";

const logout = async () => {
  try {
    await axios.post("/logout");
    window.location.href = "/login"; // redirect after logout
  } catch (err) {
    console.error(err);
  }
};
</script>
