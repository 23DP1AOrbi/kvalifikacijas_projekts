<template>
  <div class="page-wrapper">
    <v-sheet
      height="65vh"
      class="hero-section d-flex align-center justify-center text-center"
      :color="isDarkMode ? 'background' : '#F1F5F9'"
    >
      <div class="hero-overlay"></div>
      <v-container>
        <v-row justify="center"> 
          <v-col cols="12" md="10" lg="8">
            <h1 class="text-h2 text-sm-h1 font-weight-bold mb-6">
              Personalizējiet <span class="text-secondary">Dizainus</span>
            </h1>
            <p class="text-h6 text-medium-emphasis mb-10 mx-auto" style="max-width: 700px;">
              Pārlūkojiet mūsu jaunāko kolekciju un atrodiet iedvesmu savam nākamajam projektam.
            </p>
            <v-btn color="primary" size="x-large" to="/dizaini" class="text-none px-10 rounded-pill">
              Apskatīt Galeriju
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-sheet>

    <v-container class="py-16">
      <v-row justify="center" no-gutters>
        <v-col cols="12" md="10">
          
          <div class="d-flex align-center justify-space-between mb-8 px-2">
            <h2 class="text-h4 font-weight-bold">Nesen Pievienotie</h2>
            <v-btn 
              variant="text" 
              color="secondary" 
              to="/dizaini" 
              append-icon="mdi-arrow-right"
              class="text-none px-0" 
            >
              Skatīt visus
            </v-btn>
          </div>

          <v-row v-if="loading">
            <v-col v-for="n in 3" :key="n" cols="12" sm="4" class="pa-2">
              <v-skeleton-loader type="card" height="350"></v-skeleton-loader>
            </v-col>
          </v-row>

          <v-row v-else-if="designs.length > 0">
            <v-col 
              v-for="design in designs" 
              :key="design.id" 
              cols="12" 
              sm="4" 
              class="pa-2 d-flex justify-center"
            >
              <v-card 
                color="secondary" 
                class="design-card rounded-xl pa-4 d-flex flex-column h-100"
                @click="goToDesign(design.id)"
              >
                <v-card-title class="text-center font-weight-bold pt-0 pb-4 text-h6 text-truncate">
                  {{ design.name }}
                </v-card-title>
                
                <div class="white-box-wrapper">
                  <div class="white-box-inner">
                    <img :src="design.file_url" :alt="design.name" class="responsive-svg" />
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
          
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { useTheme } from "vuetify";
import axios from "../bootstrap.js";
// Using the same user import as your DesignGallery.vue
import { user } from "../services/auth"; 

const designs = ref([]);
const loading = ref(true);
const router = useRouter();
const theme = useTheme();

const isDarkMode = computed(() => theme.global.name.value === 'dark');

const fetchLatestDesigns = async () => {
  try {
    await axios.get("/sanctum/csrf-cookie");
    const res = await axios.get("/api/dizaini");
    // Sort by ID descending and take latest 3
    designs.value = res.data.sort((a, b) => b.id - a.id).slice(0, 3);
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Admin Logic matching your DesignGallery.vue
const goToDesign = (id) => {
  if (user.value?.role === 'admin') {
    router.push(`/dizaini/${id}/edit`);
  } else {
    router.push(`/dizaini/${id}`);
  }
};

onMounted(fetchLatestDesigns);
</script>

<style scoped>
.page-wrapper {
  overflow-x: hidden;
  width: 100%;
}

.hero-section {
  position: relative;
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, rgba(var(--v-theme-secondary), 0.05) 100%) !important;
}

.design-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  width: 100%;
  max-width: 400px; /* Prevents card from becoming too huge on wide screens */
}

.design-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
  background-color: #e0e0e0 !important; /* Slight lighten like your Gallery design */
}

.white-box-wrapper {
  width: 100%;
  position: relative;
  padding-top: 100%; /* Perfect Square */
  background-color: white;
  border-radius: 12px;
  overflow: hidden;
}

.white-box-inner {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15%; /* Safe space for SVGs */
}

.responsive-svg {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.v-card-title {
  display: block;
}
</style>