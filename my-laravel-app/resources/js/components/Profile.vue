<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <h1 class="mb-6">Mans Profils</h1>

        <ProfileInfo 
          v-if="user" 
          :user="user" 
          @updated="fetchUserData" 
        />

        <template v-if="user">
          <AdminStats v-if="user.role === 'admin'" />
          <ProjectList v-else />
        </template>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "../bootstrap.js";
import ProfileInfo from "./ProfileInfo.vue"; 
import ProjectList from "./ProjectList.vue";
import AdminStats from "./AdminStats.vue";

const user = ref(null);

const fetchUserData = async () => {
  try {
    const res = await axios.get("/api/user");
    user.value = res.data;
  } catch (err) {
    console.error("Auth error", err);
  }
};

onMounted(fetchUserData);
</script>