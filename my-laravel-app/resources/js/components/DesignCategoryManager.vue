<template>
  <v-card variant="outlined" class="pa-4 mt-4">
    <div class="d-flex align-center mb-4">
      <v-icon icon="mdi-tag-multiple" class="mr-2" color="primary"></v-icon>
      <span class="text-subtitle-1 font-weight-bold">Pārvaldīt Kategorijas</span>
    </div>

    <v-select
      v-model="selectedIds"
      :items="allCategories"
      item-title="name"
      item-value="id"
      label="Pievienot/Noņemt kategorijas"
      multiple
      chips
      closable-chips
      :loading="loading"
      variant="filled"
      @update:model-value="updateCategories"
    >
      <template v-slot:no-data>
        <v-list-item>
          <v-list-item-title>Netika atrasta neviena kategorija</v-list-item-title>
        </v-list-item>
      </template>
    </v-select>

    <v-fade-transition>
      <v-alert
        v-if="statusMsg"
        :type="statusType"
        density="compact"
        variant="tonal"
        class="mt-2"
      >
        {{ statusMsg }}
      </v-alert>
    </v-fade-transition>
  </v-card>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from '../bootstrap.js';

const props = defineProps({
  designId: {
    type: [Number, String],
    required: true
  },
  initialCategories: {
    type: Array,
    default: () => []
  }
});

const allCategories = ref([]);
const selectedIds = ref([]);
const loading = ref(false);
const statusMsg = ref('');
const statusType = ref('success');

// Initialize selected IDs from props
onMounted(async () => {
  selectedIds.value = props.initialCategories.map(cat => cat.id);
  fetchGlobalCategories();
});

const fetchGlobalCategories = async () => {
  try {
    const res = await axios.get('/api/categories');
    allCategories.value = res.data;
  } catch (err) {
    console.error("Kļūda ielādējot kategorijas", err);
  }
};

const updateCategories = async (newSelection) => {
  loading.value = true;
  statusMsg.value = '';
  
  try {
    // Assuming your backend has a sync endpoint: /api/dizaini/{id}/categories
    // Or a general update endpoint that accepts category_ids
    await axios.post(`/api/dizaini/${props.designId}/sync-categories`, {
      category_ids: newSelection
    });
    
    statusMsg.value = 'Kategorijas atjaunotas!';
    statusType.value = 'success';
  } catch (err) {
    statusMsg.value = 'Neizdevās saglabāt izmaiņas.';
    statusType.value = 'error';
    console.error(err);
  } finally {
    loading.value = false;
    // Hide message after 3 seconds
    setTimeout(() => statusMsg.value = '', 3000);
  }
};

// Sync if props change externally
watch(() => props.initialCategories, (newVal) => {
  selectedIds.value = newVal.map(cat => cat.id);
});
</script>