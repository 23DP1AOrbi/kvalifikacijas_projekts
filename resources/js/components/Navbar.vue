<template>
<v-navigation-drawer 
    v-model="drawer" 
    app
    temporary 
    location="right"
>
    <v-list nav color="secondary">
      <v-list-item prepend-icon="mdi-home" title="Sākums" to="/"></v-list-item>
      <v-list-item prepend-icon="mdi-information" title="Par Mums" to="/par-mums"></v-list-item>
      <v-list-item prepend-icon="mdi-image-multiple" title="Dizaini" to="/dizaini"></v-list-item>
      
      <v-divider class="my-2" v-if="user?.role === 'admin'"></v-divider>

      <template v-if="user?.role === 'admin'">
        <v-list-item prepend-icon="mdi-view-list" title="Kategorijas" to="/kategorijas"></v-list-item>
        <v-list-item prepend-icon="mdi-plus" title="Pievienot" to="/pievienot"></v-list-item>
      </template>

      <v-divider class="my-2"></v-divider>

      <v-list-item @click="toggleTheme">
        <template v-slot:prepend>
          <v-icon :icon="isDarkMode ? 'mdi-weather-sunny' : 'mdi-weather-night'"></v-icon>
        </template>
        <v-list-item-title>{{ isDarkMode ? 'Gaišais režīms' : 'Tumšais režīms' }}</v-list-item-title>
      </v-list-item>

      <v-divider class="my-2"></v-divider>

      <template v-if="!user">
        <v-list-item prepend-icon="mdi-login" title="Ienākt" to="/pieslegties"></v-list-item>
        <v-list-item prepend-icon="mdi-account-plus" title="Reģistrēties" to="/registracija" color="primary"></v-list-item>
      </template>
      
      <template v-else>
        <v-list-item prepend-icon="mdi-account-circle" title="Mans Profils" to="/profils"></v-list-item>
        <v-list-item prepend-icon="mdi-logout" title="Iziet" color="error" @click="handleLogout"></v-list-item>
      </template>
    </v-list>
  </v-navigation-drawer>

  <v-app-bar flat border color="surface">
    <div class="d-flex align-center w-100 px-4 px-md-8 mx-auto" >
      <v-icon 
        icon="mdi-material-design" 
        color="primary" 
        size="32" 
        class="mr-2"
      ></v-icon>
      <v-toolbar-title 
        class="font-weight-bold text-h6 text-sm-h5 flex-grow-0" 
        style="cursor: pointer" 
        @click="router.push('/')"
      >
        <span class="text-primary">Dizaina</span>Portāls
      </v-toolbar-title>

      <div class="hidden-sm-and-down d-flex align-center ml-md-16 ml-4">
        <v-btn variant="text" to="/" color="secondary" class="text-none mx-1">Sākums</v-btn>
        <v-btn variant="text" to="/par-mums" color="secondary" class="text-none mx-1">Par Mums</v-btn>
        <v-btn variant="text" to="/dizaini" color="secondary" class="text-none mx-1">Dizaini</v-btn>
        
        <template v-if="user?.role === 'admin'">
          <v-btn variant="text" color="secondary" to="/kategorijas" class="text-none mx-1">Kategorijas</v-btn>
          <v-btn variant="tonal" color="secondary" to="/pievienot" prepend-icon="mdi-plus" class="text-none mx-1">Pievienot</v-btn>
        </template>
      </div>

      <v-spacer></v-spacer>

      <div class="d-flex align-center gap-2">
        <v-btn
          class="hidden-sm-and-down"
          :icon="isDarkMode ? 'mdi-weather-sunny' : 'mdi-weather-night'"
          variant="text"
          @click="toggleTheme"
          density="comfortable"
        ></v-btn>

        <div class="hidden-sm-and-down d-flex align-center">
          <template v-if="!user">
            <v-btn variant="text" to="/pieslegties" color="secondary" class="text-none mx-1">Ienākt</v-btn>
            
            <v-btn 
              color="primary" 
              variant="elevated" 
              to="/registracija" 
              :active="false"
              class="text-none rounded-pill px-6 ml-2"
            >
              Reģistrēties
            </v-btn>
          </template>

          <template v-else>
            <v-btn variant="text" to="/profils" color="secondary" class="text-none px-2 rounded-lg mr-2">
              <v-avatar color="primary" size="30" class="mr-2">
                <span class="text-caption" v-if="user.name">{{ user.name.charAt(0).toUpperCase() }}</span>
                <v-icon v-else icon="mdi-account" size="small"></v-icon>
              </v-avatar>
              <span class="hidden-md-and-down">{{ user.name }}</span>
            </v-btn>
            <v-btn variant="tonal" color="error" icon="mdi-logout" density="comfortable" @click="handleLogout"></v-btn>
          </template>
        </div>

        <v-app-bar-nav-icon 
          class="hidden-md-and-up ml-2" 
          @click="drawer = !drawer"
        ></v-app-bar-nav-icon>
      </div>
      
    </div>
  </v-app-bar>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { user, logout } from "../services/auth";
import { useTheme } from "vuetify";

const router = useRouter();
const drawer = ref(false);
const theme = useTheme();

const isDarkMode = computed(() => theme.global.name.value === 'dark');

const toggleTheme = () => {
  const newTheme = isDarkMode.value ? 'light' : 'dark';
  theme.global.name.value = newTheme;
  localStorage.setItem('theme', newTheme);
};

const loadThemePreference = () => {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
    theme.global.name.value = savedTheme;
  }
};

onMounted(loadThemePreference);

const handleLogout = async () => {
  try {
    await logout();
    drawer.value = false;
  } catch (err) {
    console.error("Logout failed:", err);
  }
};
</script>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>