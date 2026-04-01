<template>
  <v-container class="text-center">
    <h3 class="mb-4">Pārvaldīt Kategorijas</h3>
    
    <v-row justify="center" class="mb-4">
      <v-col cols="12" sm="8" md="6">
        <v-text-field 
          v-model="searchQuery" 
          label="Meklēt kategorijas..." 
          variant="underlined"
          prepend-inner-icon="mdi-magnify"
          clearable
          class="mb-4"
        />

        <v-form @submit.prevent="addCategory" class="d-flex gap-2">
          <v-text-field 
            v-model="newCategory" 
            label="Jaunas Kategorijas Nosaukums" 
            variant="outlined"
            density="compact" 
            hide-details
          />
          <v-btn type="submit" color="primary" height="40">Pievienot</v-btn>
        </v-form>
      </v-col>
    </v-row>

    <div class="d-flex justify-center flex-wrap gap-3">
      <v-chip 
        v-for="cat in filteredCategories" 
        :key="cat.id" 
        color="primary"
        variant="flat"
        class="pr-1.5" 
      >
        <span class="mr-2">{{ cat.name }}</span>
        
        <v-badge
          color="white"
          :content="cat.designs_count || '0'"
          inline
          text-color="primary"
          class="mr-2"
        ></v-badge>

        <v-icon 
          size="small" 
          icon="mdi-close-circle" 
          class="delete-icon"
          @click="openDeleteModal(cat)"
        ></v-icon>
      </v-chip>
      
      <p v-if="filteredCategories.length === 0" class="text-grey italic">
        Netika atrasta neviena kategorija: "{{ searchQuery }}"
      </p>
    </div>

    <ConfirmAlert
      ref="confirmAlert" 
      :item-name="categoryToDelete?.name" 
      @confirm="executeDelete" 
    />
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from '../bootstrap.js';
import ConfirmAlert from './ConfirmAlert.vue';

const categories = ref([]);
const newCategory = ref('');
const searchQuery = ref('');

// Logic for the Modal
const confirmAlert = ref(null);
const categoryToDelete = ref(null);

const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value;
  const query = searchQuery.value.toLowerCase();
  return categories.value.filter(cat => 
    cat.name.toLowerCase().includes(query)
  );
});

const fetchCategories = async () => {
  const res = await axios.get('/api/categories');
  categories.value = res.data;
};

const addCategory = async () => {
  if (!newCategory.value) return;
  try {
    await axios.post('/api/categories', { name: newCategory.value });
    newCategory.value = '';
    fetchCategories(); 
  } catch (err) {
    console.error(err);
  }
};

/**
 * 1. Store the category object in a ref so the modal knows the name/id
 * 2. Open the modal
 */
const openDeleteModal = (category) => {
  categoryToDelete.value = category;
  confirmAlert.value.open();
};

/**
 * 3. Run this ONLY after the user clicks "Dzēst" in the modal
 */
const executeDelete = async () => {
  if (!categoryToDelete.value) return;

  try {
    await axios.delete(`/api/categories/${categoryToDelete.value.id}`);
    fetchCategories(); 
    categoryToDelete.value = null; // Reset
  } catch (err) {
    console.error("Delete failed", err);
  }
};

onMounted(fetchCategories);
</script>

<style scoped>
.gap-3 {
  gap: 12px;
}

.delete-icon {
  cursor: pointer;
  opacity: 0.7;
  transition: opacity 0.2s;
}

.delete-icon:hover {
  opacity: 1;
  color: #ff5252 !important; 
}

:deep(.v-badge__badge) {
  font-size: 10px;
  height: 18px;
  min-width: 18px;
  color: rgb(var(--v-theme-primary)) !important;
}
</style>