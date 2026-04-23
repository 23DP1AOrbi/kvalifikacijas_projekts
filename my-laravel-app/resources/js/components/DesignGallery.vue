<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import { user } from "../services/auth"; 
import axios from "../bootstrap.js";

const designs = ref([]);
const categories = ref([]);
const loading = ref(true);
const router = useRouter();

const searchQuery = ref("");
// Changed to an empty array for multiple selection
const selectedCategories = ref([]); 

const fetchData = async () => {
  try {
    await axios.get("/sanctum/csrf-cookie");

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
  let filtered = designs.value.filter((design) => {
    // 1. Filter by Name
    const matchesName = design.name
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase());

    // 2. Filter by Category (OR logic: show if at least one category matches)
    const matchesCategory = 
      selectedCategories.value.length === 0 || 
      (design.categories && design.categories.some(cat => selectedCategories.value.includes(cat.id)));

    return matchesName && matchesCategory;
  });

  // 3. Rank by relevance (The more matching categories, the higher it appears)
  if (selectedCategories.value.length > 0) {
    return filtered.slice().sort((a, b) => {
      const aMatches = a.categories?.filter(c => selectedCategories.value.includes(c.id)).length || 0;
      const bMatches = b.categories?.filter(c => selectedCategories.value.includes(c.id)).length || 0;
      return bMatches - aMatches; // Descending order
    });
  }

  return filtered;
});

const goToDesign = (id) => {
  if (user.value?.role === 'admin') {
    router.push(`/dizaini/${id}/edit`);
  } else {
    router.push(`/dizaini/${id}`);
  }
};

onMounted(fetchData);
</script>

<template>
  <VContainer fluid style="max-width: 1400px; padding-left: 16px; padding-right: 16px;">
    <h1 class="mb-4 mt-4">Dizaini</h1>

    <VRow>
      <VCol cols="12" md="6">
        <VTextField
          v-model="searchQuery"
          label="Meklēt pēc nosaukuma..."
          prepend-inner-icon="mdi-magnify"
          clearable
          hide-details
        />
      </VCol>
      <VCol cols="12" md="6">
        <VSelect
          v-model="selectedCategories"
          :items="categories"
          item-title="name"
          item-value="id"
          label="Filtrēt pēc kategorijām"
          multiple
          chips
          closable-chips
          clearable
          hide-details
        />
      </VCol>
    </VRow>

    <div v-if="loading" class="text-center pa-10">Ielādē...</div>
    <div v-else-if="filteredDesigns.length === 0" class="text-center pa-10">Nekas netika atrasts.</div>

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
          class="design-card d-flex flex-column"
          @click="goToDesign(design.id)"
          elevation="2"
        >
          <VCardTitle class="text-center text-subtitle-1 font-weight-bold">
            {{ design.name }}
          </VCardTitle>

          <VCardText class="preview-container">
            <img
              :src="design.file_url"
              class="design-img"
              alt="Design SVG"
            />
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>

<style scoped>
/* Prevent the card from ever pushing the width */
.design-card {
  background-color: #bdbdbd !important;
  height: 350px;
  width: 100%; /* Ensure it stays inside the VCol */
  overflow: hidden;
  transition: transform 0.2s ease, background-color 0.3s ease;
}

.design-card:hover {
  background-color: #e0e0e0 !important; 
  transform: translateY(-5px);
}

.preview-container {
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: white;
  margin: 8px;
  border-radius: 4px;
  flex-grow: 1;
  /* CRITICAL: This prevents the image from expanding the card width */
  min-width: 0; 
  min-height: 0;
  overflow: hidden;
}

.design-img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  display: block;
}
</style>