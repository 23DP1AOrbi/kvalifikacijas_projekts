<template>
  <v-navigation-drawer v-model="drawer" temporary location="right">
    <v-list nav>
      <v-list-item prepend-icon="mdi-home" title="Sākums" to="/"></v-list-item>
      <v-list-item prepend-icon="mdi-image-multiple" title="Dizaini" to="/dizaini"></v-list-item>
      
      <v-divider class="my-2"></v-divider>

      <template v-if="user?.role === 'admin'">
        <v-list-item prepend-icon="mdi-view-list" title="Kategorijas" to="/kategorijas"></v-list-item>
        <v-list-item prepend-icon="mdi-plus" title="Pievienot" to="/pievienot"></v-list-item>
        <v-divider class="my-2"></v-divider>
      </template>

      <template v-if="!user">
        <v-list-item prepend-icon="mdi-login" title="Ienākt" to="/login"></v-list-item>
        <v-list-item prepend-icon="mdi-account-plus" title="Reģistrēties" to="/register"></v-list-item>
      </template>
      
      <template v-else>
        <v-list-item prepend-icon="mdi-account-circle" title="Mans Profils" to="/profils"></v-list-item>
        <v-list-item prepend-icon="mdi-logout" title="Iziet" color="error" @click="handleLogout"></v-list-item>
      </template>
    </v-list>
  </v-navigation-drawer>

  <v-app-bar flat border color="white">
    <v-container class="d-flex align-center pa-0">
      
      <v-toolbar-title 
        class="font-weight-bold text-h5 flex-grow-0 mr-4" 
        style="cursor: pointer" 
        @click="router.push('/')"
      >
        <span class="text-primary">Dizaina</span>Portāls
      </v-toolbar-title>

      <div class="hidden-sm-and-down d-flex gap-2">
        <v-btn variant="text" to="/" class="text-none">Sākums</v-btn>
        <v-btn variant="text" to="/dizaini" class="text-none">Dizaini</v-btn>
        
        <template v-if="user?.role === 'admin'">
          <v-btn variant="text" color="indigo" to="/kategorijas" class="text-none">Kategorijas</v-btn>
          <v-btn variant="tonal" color="indigo" to="/pievienot" prepend-icon="mdi-plus" class="text-none">Pievienot</v-btn>
        </template>
      </div>

      <v-spacer></v-spacer>

      <div class="hidden-sm-and-down d-flex align-center gap-2">
        <template v-if="!user">
          <v-btn variant="text" to="/login" class="text-none">Ienākt</v-btn>
          <v-btn color="primary" variant="elevated" to="/register" class="text-none rounded-pill px-6">
            Reģistrēties
          </v-btn>
        </template>

        <template v-else>
          <v-btn variant="text" to="/profils" class="text-none px-2 rounded-lg">
            <v-avatar color="primary" size="30" class="mr-2">
              <span class="text-caption" v-if="user.name">{{ user.name.charAt(0).toUpperCase() }}</span>
              <v-icon v-else icon="mdi-account" size="small"></v-icon>
            </v-avatar>
            <span>{{ user.name }}</span>
          </v-btn>
          <v-btn variant="tonal" color="error" icon="mdi-logout" class="ml-2" @click="handleLogout"></v-btn>
        </template>
      </div>

      <v-app-bar-nav-icon 
        class="hidden-md-and-up" 
        @click="drawer = !drawer"
      ></v-app-bar-nav-icon>
      
    </v-container>
  </v-app-bar>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { user, logout } from "../services/auth";

const router = useRouter();
const drawer = ref(false); // Controls the mobile side menu

const handleLogout = async () => {
  try {
    await logout();
    drawer.value = false; // Close drawer on logout
    router.push('/login');
  } catch (err) {
    console.error("Logout failed:", err);
  }
};
</script>

<style scoped>
.gap-2 {
  display: flex;
  gap: 8px;
  align-items: center;
}

@media (max-width: 600px) {
  .v-container {
    padding: 0 16px !important;
  }
}
</style>