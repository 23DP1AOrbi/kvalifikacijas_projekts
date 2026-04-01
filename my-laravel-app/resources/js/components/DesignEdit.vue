<template>
  <v-container>
    <v-btn @click="$router.push('/dizaini')" prepend-icon="mdi-arrow-left" variant="text" class="mb-4">
      Atpakaļ uz galeriju
    </v-btn>

    <v-row v-if="design">
      <v-col cols="12" md="7">
        <v-card class="pa-4">
          <v-card-title>Rediģēt Dizainu</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="updateDesignName">
              <v-text-field
                v-model="editName"
                label="Dizaina Nosaukums"
                variant="outlined"
                append-inner-icon="mdi-content-save"
                @click:append-inner="updateDesignName"
                :loading="savingName"
              />
            </v-form>

            <div class="preview-container mt-4">
              <img :src="design.file_url" class="design-img" />
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="5">
        <DesignCategoryManager 
          :design-id="design.id" 
          :initial-categories="design.categories" 
        />

        <v-card class="mt-4 border-error" variant="outlined">
          <v-card-text class="d-flex align-center justify-space-between">
            <div>
              <div class="text-subtitle-1 text-error font-weight-bold">Bīstamā zona</div>
              <div class="text-caption">Šī darbība ir neatgriezeniska</div>
            </div>
            <v-btn color="error" prepend-icon="mdi-delete" @click="deleteDesign">
              Dzēst dizainu
            </v-btn>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <ConfirmAlert
                ref="confirmAlert" 
                :item-name="design?.name" 
                @confirm="executeDelete" 
            />
    

    <!-- <v-loader v-else /> -->
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '../bootstrap.js';
import ConfirmAlert from './ConfirmAlert.vue';
import DesignCategoryManager from './DesignCategoryManager.vue';

const route = useRoute();
const router = useRouter();
const design = ref(null);
const editName = ref('');
const savingName = ref(false);
const confirmAlert = ref(null);

const fetchDesign = async () => {
  const res = await axios.get(`/api/dizaini/${route.params.id}`);
  design.value = res.data;
  editName.value = res.data.name;
};

const updateDesignName = async () => {
  if (!editName.value || editName.value === design.value.name) return;
  savingName.value = true;
  try {
    await axios.patch(`/api/dizaini/${design.value.id}`, { name: editName.value });
    design.value.name = editName.value;
    alert('Nosaukums atjaunots!');
  } catch (err) {
    console.error(err);
  } finally {
    savingName.value = false;
  }
};

const deleteDesign = async () => {
  confirmAlert.value.open();
};

const executeDelete = async () => {
  try {
    await axios.delete(`/api/dizaini/${design.value.id}`);
    router.push('/dizaini'); // Redirect after success
  } catch (err) {
    console.error("Dzēšana neizdevās:", err);
  }
};

onMounted(fetchDesign);
</script>

<style scoped>
.preview-container {
  background: #f5f5f5;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  justify-content: center;
}
.design-img {
  max-width: 100%;
  max-height: 400px;
  object-fit: contain;
}
.border-error {
  border: 1px solid rgb(var(--v-theme-error)) !important;
}
</style>