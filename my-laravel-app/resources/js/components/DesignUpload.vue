<template>
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
</script>