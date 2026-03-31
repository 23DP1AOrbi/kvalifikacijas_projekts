<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
// Ensure this import points to the reactive 'user' ref in your auth service
import { user } from "../services/auth"; 
import axios from "../bootstrap.js";

// Vuetify components
import { 
  VContainer, VRow, VCol, VCard, VCardTitle, VCardText, 
  VTextField, VSelect, VBtn, VIcon 
} from 'vuetify/components';

const designs = ref([]);
const categories = ref([]);
const loading = ref(true);
const router = useRouter();

const searchQuery = ref("");
const selectedCategory = ref(null);

const fetchData = async () => {
  try {
    const [designRes, catRes] = await Promise.all([
      axios.get("/api/dizaini"),
      axios.get("/api/categories")
    ]);
    designs.value = designRes.data;
    categories.value = catRes.data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const filteredDesigns = computed(() => {
  return designs.value.filter((design) => {
    const matchesName = design.name
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase());

    const matchesCategory = !selectedCategory.value || 
      (design.categories && design.categories.some(cat => cat.id === selectedCategory.value));

    return matchesName && matchesCategory;
  });
});

const deleteDesign = async (id) => {
  if (!confirm("Are you sure you want to delete this design?")) return;

  try {
    // Note: ensure this route exists in your routes/api.php
    await axios.delete(`/api/dizaini/${id}`);
    designs.value = designs.value.filter(d => d.id !== id);
  } catch (err) {
    console.error("Delete failed:", err);
    alert("Could not delete design.");
  }
};

const goToDesign = (id) => {
  router.push(`/dizaini/${id}`);
};

onMounted(fetchData);
</script>

<template>
  <VContainer>
    <h1 class="mb-4">Dizaini</h1>

    <VRow class="mb-6">
      <VCol cols="12" md="6">
        <VTextField
          v-model="searchQuery"
          label="Search by name..."
          prepend-inner-icon="mdi-magnify"
          clearable
          hide-details
        />
      </VCol>
      <VCol cols="12" md="6">
        <VSelect
          v-model="selectedCategory"
          :items="categories"
          item-title="name"
          item-value="id"
          label="Filter by Category"
          clearable
          hide-details
        />
      </VCol>
    </VRow>

    <div v-if="loading">Loading...</div>
    <div v-else-if="filteredDesigns.length === 0">No designs match your criteria.</div>

    <VRow v-else>
      <VCol
        v-for="design in filteredDesigns"
        :key="design.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
      >
        <VCard
          class="hoverable d-flex flex-column"
          @click="goToDesign(design.id)"
          style="background-color: #bdbdbd; height: 350px; padding: 8px; position: relative;"
        >
          <div v-if="user?.role === 'admin'" style="position: absolute; top: 8px; right: 8px; z-index: 20;">
            <v-btn
              icon="mdi-delete"
              color="white"
              class="text-error"
              elevation="4"
              size="small"
              @click.stop="deleteDesign(design.id)"
            ></v-btn>
          </div>

          <VCardTitle class="text-center">{{ design.name }}</VCardTitle>

          <VCardText
            class="d-flex align-center justify-center bg-white rounded mt-2"
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