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

const selectedCategories = ref([]); 

const selectedColorType = ref("all"); 

const colorOptions = [
  { title: 'Visi', value: 'all' },
  { title: 'Krāsaini', value: 'color' },
  { title: 'Melnbalti', value: 'bw' }
];

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
  // 1. Create the filtered list first
  const filtered = designs.value.filter((design) => {
    
    const matchesName = design.name
      .toLowerCase()
      .includes(searchQuery.value.toLowerCase());

    // filter by category
    const matchesCategory = 
      selectedCategories.value.length === 0 || 
      (design.categories && design.categories.some(cat => selectedCategories.value.includes(cat.id)));

    // filter by color
    let matchesColor = true;
    if (selectedColorType.value === 'color') {
      matchesColor = design.is_color === true || design.is_color === 1;
    } else if (selectedColorType.value === 'bw') {
      matchesColor = design.is_color === false || design.is_color === 0;
    }

    return matchesName && matchesCategory && matchesColor;
  });

  // sort the filtered results
  if (selectedCategories.value.length > 0) {
    return filtered.sort((a, b) => {
      const aMatches = a.categories?.filter(c => selectedCategories.value.includes(c.id)).length || 0;
      const bMatches = b.categories?.filter(c => selectedCategories.value.includes(c.id)).length || 0;
      return bMatches - aMatches;
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
    <h1 class="mb-4 mt-8 font-weight-bold">Dizaini</h1>

    <VRow class="mb-8">
      <VCol cols="12" md="4">
        <VTextField
          v-model="searchQuery"
          label="Meklēt pēc nosaukuma..."
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          density="comfortable"
          clearable
          hide-details
        />
      </VCol>
      <VCol cols="12" md="5">
        <VSelect
          v-model="selectedCategories"
          :items="categories"
          item-title="name"
          item-value="id"
          label="Filtrēt pēc kategorijām"
          variant="outlined"
          density="comfortable"
          multiple
          chips
          closable-chips
          clearable
          hide-details
        />
      </VCol>
      <VCol cols="12" md="3">
        <VSelect
          v-model="selectedColorType"
          :items="colorOptions"
          label="Tips"
          variant="outlined"
          density="comfortable"
          hide-details
        />
      </VCol>
    </VRow>

    <div v-if="loading" class="text-center pa-10">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>
    <div v-else-if="filteredDesigns.length === 0" class="text-center pa-10 text-medium-emphasis">
      Nekas netika atrasts.
    </div>

    <VRow v-else>
      <VCol
        v-for="design in filteredDesigns"
        :key="design.id"
        cols="12"
        sm="6"
        md="4"
        lg="3"
        class="pa-3 d-flex justify-center"
      >
        <VCard
          color="surface"
          class="design-card rounded-xl pa-4 d-flex flex-column h-100"
          @click="goToDesign(design.id)"
        >
          <VCardTitle class="text-center font-weight-bold pt-0 pb-4 text-subtitle-1 text-truncate">
            {{ design.name }}
          </VCardTitle>

          <div class="white-box-wrapper">
            <div class="white-box-inner">
              <img 
                :src="design.file_url" 
                :alt="design.name" 
                class="responsive-svg" 
              />
            </div>
          </div>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>

<style scoped>
.design-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  width: 100%;
  /* Removed hardcoded background-color to let Vuetify theme handle it */
  border: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
}

.design-card:hover {
  transform: translateY(-8px);
    background-color: rgba(var(--v-theme-primary), 0.8) !important; 
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
}

/* MATCHING HOME.VUE BOX LOGIC */
.white-box-wrapper {
  width: 100%;
  position: relative;
  padding-top: 100%; /* Perfect Square */
  background-color: white; /* Keep white so SVGs look clean regardless of theme */
  border-radius: 12px;
  overflow: hidden;
}

.white-box-inner {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12%; 
}

.responsive-svg {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  display: block;
}

.v-card-title {
  display: block;
}
</style>