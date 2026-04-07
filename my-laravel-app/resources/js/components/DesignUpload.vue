<template>
  <v-container>
    <v-card class="pa-6" max-width="600">
      <v-card-title>Augšupielādēt Jaunu Dizainu</v-card-title>
      
      <v-form @submit.prevent="uploadDesign">
        <div v-if="previewUrl" class="preview-box mb-4">
          <p class="text-caption">Priekšskatījums:</p>
          <div v-html="previewUrl" class="svg-preview"></div>
        </div>

        <v-text-field 
          v-model="name" 
          label="Dizaina Nosaukums" 
          required 
        />

        <v-file-input
          label="Izvēlēties SVG Failu"
          accept=".svg"
          prepend-icon="mdi-svg"
          @change="onFileChange"
          @click:clear="clearFile"
          required
          persistent-hint
          hint="Tikai .svg faili"
        />

        <v-select
          v-model="selectedCategories"
          :items="categories"
          item-title="name"
          item-value="id"
          label="Izvēlēties Kategorijas"
          multiple
          chips
          hint="Izvēlieties tik daudz, cik piemērojams"
          persistent-hint
          class="mt-4"
        />

        <v-btn 
          type="submit" 
          color="success" 
          block 
          class="mt-6"
          :disabled="!file || !name"
        >
          Augšupielādēt Dizainu
        </v-btn>
      </v-form>
    </v-card>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '../bootstrap.js';

const name = ref('');
const file = ref(null);
const previewUrl = ref('');
const categories = ref([]);
const selectedCategories = ref([]);

onMounted(async () => {
  try {
    const res = await axios.get('/api/categories');
    categories.value = res.data;
  } catch (err) {
    console.error("Neizdevās ielādēt kategorijas", err);
  }
});

const clearFile = () => {
  file.value = null;
  previewUrl.value = '';
};

const onFileChange = (e) => {
  const selectedFile = e.target.files[0];
  
  if (!selectedFile) {
    clearFile();
    return;
  }

  // Strict Validation
  const isSvg = selectedFile.type === 'image/svg+xml' || selectedFile.name.toLowerCase().endsWith('.svg');
  
  if (!isSvg) {
    alert('Kļūda: Lūdzu, izvēlieties derīgu SVG failu!');
    e.target.value = ""; // Clears the actual input DOM element
    clearFile();
    return;
  }

  file.value = selectedFile;

  // Generate Preview
  const reader = new FileReader();
  reader.onload = (f) => {
    // Basic security: ensure the result looks like an SVG tag
    if (f.target.result.includes('<svg')) {
      previewUrl.value = f.target.result;
    } else {
      alert("Fails nav derīgs SVG saturs.");
      clearFile();
    }
  };
  reader.readAsText(selectedFile);
};

const uploadDesign = async () => {
  if (!file.value || !name.value) {
    alert('Lūdzu, aizpildiet visus laukus!');
    return;
  }

  const formData = new FormData();
  formData.append('name', name.value);
  formData.append('image', file.value);
  
  selectedCategories.value.forEach(id => {
    formData.append('category_ids[]', id);
  });

  try {
    await axios.post('/api/dizaini', formData);
    alert('SVG augšupielādēts veiksmīgi!');
    
    // Reset form
    name.value = '';
    clearFile();
    selectedCategories.value = [];
  } catch (err) {
    if (err.response?.status === 422) {
      console.table(err.response.data.errors);
      alert('Servera validācijas kļūda. Pārbaudiet datus.');
    } else {
      alert('Augšupielāde neizdevās.');
    }
  }
};
</script>

<style scoped>
.preview-box {
  border: 1px dashed #ccc;
  padding: 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #f9f9f9;
  min-height: 100px;
}
/* Ensure the injected SVG behaves within the box */
.svg-preview :deep(svg) {
  max-width: 100%;
  max-height: 200px;
  width: auto;
  height: auto;
}
</style>