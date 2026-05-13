<template>
  <v-card class="pa-6 mb-6">
    <v-card-title class="px-0 d-flex align-center">
      <span class="text-h5 font-weight-bold">{{ props.user.name }}</span>
      <v-spacer></v-spacer>
      <v-btn 
        v-if="!isEditing" 
        prepend-icon="mdi-pencil" 
        color="primary" 
        variant="tonal" 
        size="small"
        @click="startEditing"
      >
        Labot profilu
      </v-btn>
    </v-card-title>

    <div v-if="!isEditing" class="mt-1 text-body-1 text-medium-emphasis">
      <v-icon start icon="mdi-email" size="small"></v-icon>
      {{ props.user.email }}
    </div>

    <v-divider v-if="!isEditing" class="my-4"></v-divider>
    
    <v-form 
      v-if="isEditing" 
      @submit.prevent="updateProfile" 
      v-model="isFormValid"
      class="mt-4"
    >
      <v-text-field
        v-model="tempForm.name"
        label="Lietotājvārds"
        variant="filled"
        density="comfortable"
        :rules="[
          v => !!v || 'Lietotājvārds ir obligāts',
          v => (v && v.length <= 255) || 'Vārds nevar pārsniegt 255 simbolus'
        ]"
        :error-messages="errors.name"
        @input="errors.name = []"
        class="mb-1"
      />

      <v-text-field
        v-model="tempForm.email"
        label="E-pasts"
        type="email"
        variant="filled"
        density="comfortable"
        :rules="[
          v => !!v || 'E-pasts ir obligāts',
          v => /.+@.+\..+/.test(v) || 'E-pastam jābūt derīgam'
        ]"
        :error-messages="errors.email"
        @input="errors.email = []"
        class="mb-1"
      />

      <v-expansion-panels variant="accordion" class="mb-4">
        <v-expansion-panel title="Mainīt paroli">
          <v-expansion-panel-text>
            <v-text-field
              v-model="tempForm.password"
              label="Jaunā parole (atstājiet tukšu, ja nemaināt)"
              type="password"
              variant="filled"
              :rules="[
                v => !v || v.length >= 8 || 'Jābūt vismaz 8 simboliem'
              ]"
              :error-messages="errors.password"
              @input="errors.password = []"
            />
            <v-text-field
              v-model="tempForm.password_confirmation"
              label="Apstiprināt jauno paroli"
              type="password"
              variant="filled"
              :rules="[
                v => v === tempForm.password || 'Paroles nesakrīt'
              ]"
            />
          </v-expansion-panel-text>
        </v-expansion-panel>
      </v-expansion-panels>

      <div class="d-flex gap-4">
        <v-btn 
          type="submit" 
          color="success" 
          :loading="loading" 
          :disabled="!isFormValid"
          class="flex-grow-1"
        >
          Saglabāt
        </v-btn>
        <v-btn 
          variant="outlined" 
          color="secondary" 
          @click="cancelEdit"
        >
          Atcelt
        </v-btn>
      </div>
    </v-form>

    <v-alert v-if="successMessage" type="success" variant="tonal" class="mt-4" closable>
      {{ successMessage }}
    </v-alert>
  </v-card>
</template>

<script setup>
import { ref, reactive } from "vue";
import axios from "../bootstrap.js";

const props = defineProps(['user']);
const emit = defineEmits(['updated']);

const isEditing = ref(false);
const isFormValid = ref(false);
const loading = ref(false);
const successMessage = ref("");

const tempForm = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: ""
});

const errors = ref({
  name: [],
  email: [],
  password: []
});

const startEditing = () => {
  tempForm.name = props.user.name;
  tempForm.email = props.user.email;
  tempForm.password = "";
  tempForm.password_confirmation = "";
  isEditing.value = true;
};

const updateProfile = async () => {
  if (!isFormValid.value) return;

  loading.value = true;
  errors.value = { name: [], email: [], password: [] };
  
  try {
    await axios.get('/sanctum/csrf-cookie');
    
    await axios.put("/api/user/update", tempForm);
    successMessage.value = "Profils veiksmīgi atjaunināts!";
    isEditing.value = false;
    emit('updated'); 
    
    setTimeout(() => {
      successMessage.value = "";
    }, 3000);
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
    }
  } finally {
    loading.value = false;
  }
};

const cancelEdit = () => {
  isEditing.value = false;
  errors.value = { name: [], email: [], password: [] };
};
</script>

<style scoped>
.gap-4 {
  gap: 16px;
}
</style>