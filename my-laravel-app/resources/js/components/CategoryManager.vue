<template>
  <v-container>
    <h3>Manage Categories</h3>
    <v-form @submit.prevent="addCategory" class="d-flex gap-2 mb-4">
      <v-text-field v-model="newCategory" label="New Category Name" hide-details dense />
      <v-btn type="submit" color="primary">Add</v-btn>
    </v-form>

    <v-chip-group>
      <v-chip v-for="cat in categories" :key="cat.id" closable @click:close="deleteCategory(cat.id)">
        {{ cat.name }}
      </v-chip>
    </v-chip-group>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../bootstrap.js';

const categories = ref([]);
const newCategory = ref('');

const fetchCategories = async () => {
  const res = await axios.get('/api/categories');
  categories.value = res.data;
};

const addCategory = async () => {
  if (!newCategory.value) return;
  try {
    // Note the /api/ prefix!
    await axios.post('/api/categories', { name: newCategory.value });
    newCategory.value = '';
    fetchCategories(); // Refresh the list
  } catch (err) {
    console.error("Could not add category:", err.response.data);
    alert("Error: " + err.response.data.message);
  }
};

const deleteCategory = async (id) => {
  await axios.delete(`/api/categories/${id}`);
  fetchCategories();
};

onMounted(fetchCategories);
</script>