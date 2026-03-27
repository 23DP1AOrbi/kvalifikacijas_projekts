<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "../bootstrap.js";

// Vuetify components
import { VContainer, VRow, VCol, VCard, VCardTitle, VCardText } from 'vuetify/components';

const designs = ref([]);
const loading = ref(true);
const router = useRouter();

const fetchDesigns = async () => {
  try {
    const res = await axios.get("/api/dizaini");
    designs.value = res.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const goToDesign = (id) => {
  router.push(`/dizaini/${id}`);
};

onMounted(fetchDesigns);
</script>

<template>
  <VContainer>
    <h1 class="mb-4">Dizaini</h1>

    <div v-if="loading">Loading...</div>
    <div v-else-if="designs.length === 0">No designs found.</div>

    <VRow v-else>
      <VCol
        v-for="design in designs"
        :key="design.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <VCard
          class="hoverable d-flex flex-column"
          @click="goToDesign(design.id)"
          style="background-color: #bdbdbd; height: 300px; padding: 8px;"
        >
          <!-- Name always visible -->
          <VCardTitle class="text-center" style="flex-shrink: 0;">{{ design.name }}</VCardTitle>

          <!-- SVG container -->
          <VCardText
            class="d-flex align-center justify-center"
            style="flex-grow: 1; overflow: hidden;"
          >
            <img
              :src="design.file_url"
              style="max-width: 100%; max-height: 100%; object-fit: contain;"
              alt="Design SVG"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>

<style scoped>
.v-card.hoverable:hover {
  transform: translateY(-5px);
  transition: transform 0.2s ease-in-out;
}
</style>