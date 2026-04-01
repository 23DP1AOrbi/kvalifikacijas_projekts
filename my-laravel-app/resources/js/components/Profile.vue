<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <h1 class="mb-6">Mans Profils</h1>

        <v-card class="pa-6 mb-6">
          <v-card-title class="px-0 d-flex align-center">
            <span class="text-h5 font-weight-bold">{{ form.name }}</span>
            <v-spacer></v-spacer>
            <v-btn 
              v-if="!isEditing" 
              prepend-icon="mdi-pencil" 
              color="primary" 
              variant="tonal" 
              size="small"
              @click="isEditing = true"
            >
              Labot profilu
            </v-btn>
          </v-card-title>

          <div v-if="!isEditing" class="mt-1 text-body-1 text-medium-emphasis">
            <v-icon start icon="mdi-email" size="small"></v-icon>
            {{ form.email }}
          </div>

          <v-divider v-if="!isEditing" class="my-4"></v-divider>
          
          <v-form v-if="isEditing" @submit.prevent="updateProfile" class="mt-4">
            <v-text-field
              v-model="form.name"
              label="Lietotājvārds"
              :error-messages="errors.name"
              variant="outlined"
              density="comfortable"
            />

            <v-text-field
              v-model="form.email"
              label="E-pasts"
              type="email"
              :error-messages="errors.email"
              variant="outlined"
              density="comfortable"
            />

            <v-expansion-panels variant="accordion" class="mb-4">
              <v-expansion-panel title="Mainīt paroli">
                <v-expansion-panel-text>
                  <v-text-field
                    v-model="form.password"
                    label="Jaunā parole"
                    type="password"
                    :error-messages="errors.password"
                    variant="outlined"
                  />
                  <v-text-field
                    v-model="form.password_confirmation"
                    label="Apstiprināt jauno paroli"
                    type="password"
                    variant="outlined"
                  />
                </v-expansion-panel-text>
              </v-expansion-panel>
            </v-expansion-panels>

            <div class="d-flex gap-4">
              <v-btn 
                type="submit" 
                color="success" 
                :loading="loading" 
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

        <v-card class="pa-6">
          <div class="d-flex gap-4">
            <v-btn variant="text" prepend-icon="mdi-folder-star" color="indigo">Mani Projekti</v-btn>
            <v-btn variant="text" prepend-icon="mdi-heart" color="error">Favorīti</v-btn>
            <v-btn variant="text" prepend-icon="mdi-history">Nesenie</v-btn>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "../bootstrap.js";

const isEditing = ref(false);
const loading = ref(false);
const successMessage = ref("");
const errors = ref({});

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const originalData = ref({});

const fetchUserData = async () => {
  try {
    await axios.get("/sanctum/csrf-cookie");

    const res = await axios.get("/api/user");
    form.value.name = res.data.name;
    form.value.email = res.data.email;
    originalData.value = { ...res.data };
  } catch (err) {
    if (err.response?.status === 401) {
      console.error("Lietotājs nav autorizējies");
      // Optional: window.location.href = "/login";
    } else {
      console.error("Neizdevās ielādēt datus", err);
    }
  }
};

const cancelEdit = () => {
  // Revert form values to original database state
  form.value.name = originalData.value.name;
  form.value.email = originalData.value.email;
  form.value.password = "";
  form.value.password_confirmation = "";
  errors.value = {};
  isEditing.value = false;
};

const updateProfile = async () => {
  errors.value = {};
  successMessage.value = "";
  loading.value = true;

  try {
    await axios.get("/sanctum/csrf-cookie");

    const res = await axios.put("/api/user/update", form.value);
    successMessage.value = "Profils veiksmīgi atjaunināts!";
    
    // Update originalData with the new saved info
    originalData.value = { 
      name: form.value.name, 
      email: form.value.email 
    };
    
    form.value.password = "";
    form.value.password_confirmation = "";
    isEditing.value = false;
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error(error);
    }
  } finally {
    loading.value = false;
  }
};

onMounted(fetchUserData);
</script>

<style scoped>
.gap-4 {
  display: flex;
  gap: 16px;
}
</style>