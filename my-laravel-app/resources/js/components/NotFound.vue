<template>
  <v-container class="fill-height d-flex align-center justify-center text-center">
    <v-row justify="center">
      <v-col cols="12" sm="8" md="6">
        <v-icon
          icon="mdi-material-design"
          size="100"
          color="primary"
          class="mb-6"
        ></v-icon>
        
        <h1 class="text-h2 font-weight-bold mb-4">404</h1>
        <h2 class="text-h5 text-medium-emphasis mb-8">
          Lapa nav atrasta
        </h2>
        
        <p class="text-body-1 mb-10">
          Atvainojiet, bet lapa, kuru meklējat, neeksistē vai ir pārvietota.
        </p>

        <v-btn
          color="primary"
          size="large"
          to="/"
          prepend-icon="mdi-home"
          class="text-none rounded-pill px-8"
        >
          Atgriezties uz sākumu
        </v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<!-- <script setup>
// No logic needed, purely UI
</script> -->