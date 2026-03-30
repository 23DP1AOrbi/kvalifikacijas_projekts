<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "../bootstrap.js";

// Vuetify components
import { VContainer, VRow, VCol, VCard, VCardTitle, VCardText, VTextField, VSelect, VChip } from 'vuetify/components';

const designs = ref([]);
const categories = ref([]); // To store available categories for the dropdown
const loading = ref(true);
const router = useRouter();

// Filter States
const searchQuery = ref("");
const selectedCategory = ref(null);

const fetchData = async () => {
  try {
    // Fetch both designs and categories
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

// COMPUTED FILTERING LOGIC
const filteredDesigns = computed(() => {
  return designs.value.filter((design) => {
    // Match Name
    const matchesName = design.name
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase());

    // Match Category (Checks if design has the selected category ID in its categories array)
    const matchesCategory = !selectedCategory.value || 
      design.categories.some(cat => cat.id === selectedCategory.value);

    return matchesName && matchesCategory;
  });
});

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
          style="background-color: #bdbdbd; height: 350px; padding: 8px;"
        >
          <VCardTitle class="text-center" style="flex-shrink: 0;">{{ design.name }}</VCardTitle>
          
          <div class="px-2 pb-2 d-flex flex-wrap justify-center" style="gap: 4px;">
            <VChip 
              v-for="cat in design.categories" 
              :key="cat.id" 
              size="x-small" 
              color="primary"
            >
              {{ cat.name }}
            </VChip>
          </div>

          <VCardText
            class="d-flex align-center justify-center bg-white rounded"
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