<template>
  <v-footer class="bg-grey-darken-4 text-white py-6">
    <v-container>
      <div class="d-flex align-center justify-space-between w-100">
        <div class="text-subtitle-1 font-weight-bold">
          Kontakti
        </div>
        
        <div class="d-flex align-center">
          <v-btn 
            icon="mdi-facebook" 
            variant="tonal" 
            color="white" 
            size="small" 
            class="ml-4"
          />
          <v-btn 
            icon="mdi-instagram" 
            variant="tonal" 
            color="white" 
            size="small" 
            class="ml-4"
          />
          <v-btn 
            icon="mdi-linkedin" 
            variant="tonal" 
            color="white" 
            size="small" 
            class="ml-4"
          />
        </div>
      </div>

      <v-divider class="my-6 border-opacity-25" />

      <div class="text-center text-caption text-grey-lighten-1">
        &copy; {{ new Date().getFullYear() }} Dizaina Portāls. Visas tiesības aizsargātas.
      </div>
    </v-container>
  </v-footer>
</template>

<style scoped>

@media (max-width: 400px) {
  .d-flex {
    flex-direction: row;
    gap: 0px;
  }
}
</style>