<!-- <template>
  <div>
    <form @submit.prevent="uploadDesign">
      <input type="text" v-model="name" placeholder="Design Name" required />
      <input type="file" @change="onFileChange" accept=".svg" required />
      <button type="submit">Upload</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '../bootstrap.js';

const name = ref('');
const file = ref(null);

const onFileChange = (e) => {
  file.value = e.target.files[0];
};

const uploadDesign = async () => {
  if (!file.value) return;

   // send file directly

  try {
    await axios.get("/sanctum/csrf-cookie");

    const formData = new FormData();
    formData.append('name', name.value);
    formData.append('image', file.value);

    // const res = await axios.post('/api/dizaini', formData, {
    //   headers: { 'Content-Type': 'multipart/form-data' },
    // });

    const res = await axios.post('/dizaini', formData);

    alert('SVG uploaded successfully!');
    name.value = '';
    file.value = null;
    // console.log('Uploaded design:', res.data);
  } catch (err) {
    console.error('Upload failed:', err);
    alert('Upload failed: ' + (err.response?.data?.message || err.message));
  }
};
</script> -->

<template>
  <v-container>
    <v-card class="pa-6" max-width="600">
      <v-card-title>Upload New Design</v-card-title>
      
      <v-form @submit.prevent="uploadDesign">
        <div v-if="previewUrl" class="preview-box mb-4">
          <p class="text-caption">Preview:</p>
          <div v-html="previewUrl" class="svg-preview"></div>
        </div>

        <v-text-field v-model="name" label="Design Name" required />

        <v-file-input
          label="Choose SVG File"
          accept=".svg"
          prepend-icon="mdi-svg"
          @change="onFileChange"
          required
        />

        <v-select
          v-model="selectedCategories"
          :items="categories"
          item-title="name"
          item-value="id"
          label="Select Categories"
          multiple
          chips
          hint="Select as many as apply"
          persistent-hint
          class="mt-4"
        />

        <v-btn type="submit" color="success" block class="mt-6">Upload Design</v-btn>
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
  const res = await axios.get('/api/categories');
  categories.value = res.data;
});

const onFileChange = (e) => {
  const selectedFile = e.target.files[0];
  if (!selectedFile) return;
  
  file.value = selectedFile;

  // Generate Preview
  const reader = new FileReader();
  reader.onload = (f) => {
    previewUrl.value = f.target.result; // For SVGs, we can inject the text directly
  };
  reader.readAsText(selectedFile);
};

const uploadDesign = async () => {
  if (!file.value) return;

  const formData = new FormData();
  formData.append('name', name.value);
  formData.append('image', file.value);
  
  // Laravel expects array inputs as category_ids[]
  selectedCategories.value.forEach(id => {
    formData.append('category_ids[]', id);
  });

  try {
    await axios.post('/dizaini', formData);
    alert('SVG uploaded successfully!');
    // Reset form
    name.value = '';
    file.value = null;
    previewUrl.value = '';
    selectedCategories.value = [];
  } catch (err) {
    alert('Upload failed');
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
}
.svg-preview :deep(svg) {
  max-width: 150px;
  max-height: 150px;
  height: auto;
}
</style>