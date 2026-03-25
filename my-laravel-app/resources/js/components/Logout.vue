<script setup>
// import axios from "../bootstrap.js";
import { logout } from "../services/auth";
const emit = defineEmits(["logged-out"]);


const logoutUser = async () => {
  // emit("logged-out");
  await logout();
};

// const logout = async () => {
//   try {
//     await logout();
//     // await axios.get("/sanctum/csrf-cookie");
//     // await axios.post("/logout");
//     // await fetchUser();
//     // emit("logged-out"); // update App.vue state
    
//   } catch (err) {
//     console.error(err);
//   }
// };
</script>

<template>
  <button @click="logoutUser">Logout</button>
</template>
