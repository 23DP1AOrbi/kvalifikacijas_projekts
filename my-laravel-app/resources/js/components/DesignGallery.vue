<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "../bootstrap.js";

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
        <VCard class="pa-3 hoverable" @click="goToDesign(design.id)">
          <VCardTitle>{{ design.name }}</VCardTitle>

          <VCardText>
            <!-- ✅ render SVG as file -->
            <img :src="design.file_url" style="width:100%;" />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>