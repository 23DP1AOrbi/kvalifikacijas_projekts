<script setup>
import { onMounted, watch } from "vue";
import { useRoute } from "vue-router";
import { fetchUser } from "../services/auth";
import Navbar from "./Navbar.vue";
import Footer from "./Footer.vue";

const route = useRoute();

const routeTitles = {
  '/': 'Sākums',
  '/par-mums': 'Par Mums',
  '/dizaini': 'Dizaini',
  '/kategorijas': 'Kategorijas',
  '/pievienot': 'Pievienot',
  '/pieslegties': 'Ienākt',
  '/registracija': 'Reģistrēties',
  '/profils': 'Mans Profils',
  'NotFound': 'Lapa Nav Atrasta'
};

const updateTabMeta = () => {
  const pageName = routeTitles[route.path];
  document.title = `${pageName}`;

  // This path draws a circle with an 'M' shape inside it
  const mInCirclePath = "M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M7,17V7H9L12,10L15,7H17V17H15V9.23L12,12.23L9,9.23V17H7Z";
  
  const link = document.createElement('link');
  link.type = 'image/svg+xml';
  link.rel = 'icon';
  
  // Using %23 for the # hex code symbol
  const svgString = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="%233B82F6" d="${mInCirclePath}"/></svg>`;
  link.href = `data:image/svg+xml,${svgString}`;
  
  document.head.appendChild(link);
};

onMounted(() => {
  fetchUser();
  updateTabMeta();
});

watch(() => route.path, updateTabMeta);
</script>

<template>
  <v-app>
    <Navbar />
    <v-main>
      <router-view />
    </v-main>
    <Footer />
  </v-app>
</template>

<style>
/* 1. Reset & Base Layout */
html, body {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden; 
  position: relative;
}

#app {
  width: 100%;
  overflow-x: hidden;
}

body {
  font-family: 'Inter', sans-serif;
  transition: background-color 0.3s ease, color 0.3s ease;
}

.v-main {
  width: 100%;
  max-width: 100vw;
  overflow-x: hidden;
}

img, svg {
  max-width: 100% !important;
  height: auto !important;
  display: block;
}

/* 2. Global Overlay & Interactive States */
.v-btn__overlay,
.v-list-item__overlay,
.v-card__overlay {
  transition: opacity 0.2s ease-in-out;
  opacity: 0; 
}

.v-btn:hover > .v-btn__overlay,
.v-list-item:hover > .v-list-item__overlay,
.v-card:hover > .v-card__overlay {
  opacity: 0.08 !important;
}

.v-btn.v-btn--active > .v-btn__overlay,
.v-list-item.v-list-item--active > .v-list-item__overlay {
  opacity: 0.15 !important;
  background-color: rgb(var(--v-theme-primary)) !important;
}

.v-btn.v-btn--active > .v-btn__content,
.v-list-item.v-list-item--active .v-list-item-title,
.v-list-item.v-list-item--active .v-icon {
  color: rgb(var(--v-theme-primary)) !important;
  font-weight: 700 !important;
}

/* 3. Scrollbar Styling */
.v-theme--dark {
  ::-webkit-scrollbar { width: 12px; }
  ::-webkit-scrollbar-track { background: #2c2c2c; }
  ::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 6px;
  }
  ::-webkit-scrollbar-thumb:hover { background: #666; }
}

/* 4. Utility */
img, video {
  max-width: 100%;
  height: auto;
}
</style>