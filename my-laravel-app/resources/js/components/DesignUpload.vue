<template>
  <v-container fluid class="fill-height d-flex align-center justify-center">
    <v-card class="pa-6 pa-sm-6 mx-auto" max-width="600" width="100%">
      <v-card-title class="text-center text-h5 text-sm-h4 font-weight-bold mb-6 text-wrap">
        Augšupielādēt Jaunu Dizainu
      </v-card-title>
      
      <v-form @submit.prevent="uploadDesign">
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

        <div class="mt-6 d-flex flex-column flex-sm-row align-sm-center ga-4">
          <div class="text-subtitle-1 text-sm-subtitle-2 text-medium-emphasis">
            Dizaina tips:
          </div>
          <v-btn-toggle
            v-model="isColor"
            mandatory
            color="primary"
            variant="outlined"
            divided
            class="custom-toggle"
          >
            <v-btn :value="1" prepend-icon="mdi-palette">Krāsains</v-btn>
            <v-btn :value="0" prepend-icon="mdi-format-color-marker-cancel">Melnbalts</v-btn>
          </v-btn-toggle>
        </div>

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
const isColor = ref(1);

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
  name.value = '';
};

const onFileChange = (e) => {
  const selectedFile = e.target.files[0];
  
  if (!selectedFile) {
    clearFile();
    return;
  }

  const isSvg = selectedFile.type === 'image/svg+xml' || selectedFile.name.toLowerCase().endsWith('.svg');
  
  if (!isSvg) {
    alert('Kļūda: Lūdzu, izvēlieties derīgu SVG failu!');
    e.target.value = ""; 
    clearFile();
    return;
  }

  file.value = selectedFile;

  const reader = new FileReader();
  reader.onload = (f) => {
    // checks if file is an svg
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
  formData.append('is_color', isColor.value);
  
  selectedCategories.value.forEach(id => {
    formData.append('category_ids[]', id);
  });

  try {
    await axios.post('/api/dizaini', formData);
    alert('SVG augšupielādēts veiksmīgi!');
    
    name.value = '';
    isColor.value = 1;
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

.custom-toggle {
  height: auto !important; 
  min-height: 44px;
  overflow: hidden; 
  width: 100%;
}

.custom-toggle .v-btn {
  flex: 1 1 auto;
  padding: 8px 16px !important;
  text-transform: none;
  letter-spacing: normal;
  height: 44px !important;
}

.text-wrap {
  white-space: normal;
  line-height: 1.2;
}

</style>