<template>
  <v-container class="text-center">
    <h3 class="mb-4">Manage Categories</h3>
    
    <v-row justify="center" class="mb-4">
      <v-col cols="12" sm="8" md="6">
        <v-text-field 
          v-model="searchQuery" 
          label="Search Categories..." 
          variant="underlined"
          prepend-inner-icon="mdi-magnify"
          clearable
          class="mb-4"
        />

        <v-form @submit.prevent="addCategory" class="d-flex gap-2">
          <v-text-field 
            v-model="newCategory" 
            label="New Category Name" 
            variant="outlined"
            density="compact" 
            hide-details
          />
          <v-btn type="submit" color="primary" height="40">Add</v-btn>
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
          @click="deleteCategory(cat.id)"
        ></v-icon>
      </v-chip>
      
      <p v-if="filteredCategories.length === 0" class="text-grey italic">
        No categories found matching "{{ searchQuery }}"
      </p>
    </div>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from '../bootstrap.js';

const categories = ref([]);
const newCategory = ref('');
const searchQuery = ref('');

// Filtered categories computed property
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
    searchQuery.value = ''; // Optional: clear search to show the new item
    fetchCategories(); 
  } catch (err) {
    console.error(err);
  }
};

const deleteCategory = async (id) => {
  if (!confirm("Are you sure? This will remove the category from all designs.")) return;
  
  try {
    await axios.delete(`/api/categories/${id}`);
    fetchCategories(); 
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