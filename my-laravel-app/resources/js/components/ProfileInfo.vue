<template>
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
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "../bootstrap.js";

const props = defineProps(['user']);
const emit = defineEmits(['updated']);

const isEditing = ref(false);
const loading = ref(false);
const errors = ref({});
const successMessage = ref("");
const form = ref({ ...props.user });

// Update local form if the parent's user data changes
watch(() => props.user, (newVal) => {
  form.value = { ...newVal };
}, { deep: true });

const updateProfile = async () => {
  loading.value = true;
  try {
    await axios.put("/api/user/update", form.value);
    isEditing.value = false;
    emit('updated'); // Tell parent to refresh user data
  } catch (err) {
    if (err.response?.status === 422) errors.value = err.response.data.errors;
  } finally {
    loading.value = false;
  }
};

const cancelEdit = () => {
  form.value = { ...props.user };
  isEditing.value = false;
};
</script>