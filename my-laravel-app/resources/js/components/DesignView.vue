<template>
  <v-container>
    <v-btn @click="$router.back()" class="mb-4">Back</v-btn>

    <v-card v-if="svgContent">
      <v-card-title>{{ design?.name }}</v-card-title>

      <v-card-text>
        <!-- ✅ SVG injected -->
        <div v-html="svgContent" class="svg-large"></div>
      </v-card-text>
    </v-card>

    <div v-else>Loading...</div>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "../bootstrap.js";

const route = useRoute();
const design = ref(null);
const svgContent = ref('');

const fetchDesign = async () => {
  try {
    const res = await axios.get(`/api/dizaini/${route.params.id}`);
    design.value = res.data;

    // 🔥 fetch SVG FILE content
    const svgRes = await axios.get(design.value.file_url);
    svgContent.value = svgRes.data;

  } catch (err) {
    console.error(err);
  }
};

onMounted(fetchDesign);
</script>

<style scoped>
.svg-large svg {
  width: 100%;
  max-width: 500px;
}
</style>