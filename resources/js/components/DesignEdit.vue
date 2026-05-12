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

        <div class="mt-6">
          <div class="text-subtitle-2 mb-2">Mainīt krāsu tipu</div>
          <v-btn-toggle
            v-model="editIsColor"
            mandatory
            color="primary"
            variant="outlined"
            divided
            class="custom-toggle"
            @update:model-value="updateDesignColor"
          >
            <v-btn :value="1" prepend-icon="mdi-palette">Krāsains</v-btn>
            <v-btn :value="0" prepend-icon="mdi-format-color-marker-cancel">Melnbalts</v-btn>
          </v-btn-toggle>
          <v-progress-linear v-if="savingColor" indeterminate color="primary" class="mt-2" />
        </div>

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

const editIsColor = ref(1);
const savingColor = ref(false);

const fetchDesign = async () => {
  const res = await axios.get(`/api/dizaini/${route.params.id}`);
  design.value = res.data;
  editName.value = res.data.name;
  editIsColor.value = res.data.is_color;
};

const updateDesignColor = async (newValue) => {
  savingColor.value = true;
  try {
    await axios.post(`/api/dizaini/${design.value.id}`, { 
      is_color: newValue,
      _method: 'PATCH'
    });
    design.value.is_color = newValue;
  } catch (err) {
    console.error("Neizdevās saglabāt krāsu tipu", err);
    alert("Kļūda saglabājot!");
  } finally {
    savingColor.value = false;
  }
};

const updateDesignName = async () => {

  if (!editName.value || editName.value === design.value.name) return;
  savingName.value = true;
  try {

   const res = await axios.post(`/api/dizaini/${design.value.id}`, { 
      name: editName.value,
      _method: 'PATCH'
    });

    design.value.name = editName.value;
    alert('Nosaukums atjaunots!');
  } catch (err) {
    if (err.response?.status === 401) {
      console.error("401: Session lost. Try logging out and back in.");
    } else if (err.response?.status === 403) {
      console.error("403: You are logged in, but you are not an ADMIN.");
    }
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
    router.push('/dizaini');
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
</style>