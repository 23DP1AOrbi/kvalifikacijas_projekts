<template>
  <div class="auth-wrapper">
    <v-container fluid class="pa-0">
      <v-row justify="center" align="center" density="compact" class="ma-0">
        <v-col cols="11" sm="8" md="6" lg="4">
          
          <v-card class="auth-card pa-5 pa-sm-8 rounded-lg elevation-12 mx-auto">
            <h1 class="text-h5 font-weight-bold text-center mb-4">Izveidot kontu</h1>
            
            <v-form @submit.prevent="registerUser" v-model="isFormValid">
              <v-text-field
                v-model="form.name"
                label="Lietotājvārds"
                prepend-inner-icon="mdi-account-outline"
                variant="outlined"
                density="compact"
                color="primary"
                hide-details="auto"
                required
                :error-messages="errors.name"
                class="mb-2"
              />

              <v-text-field
                v-model="form.email"
                label="E-pasts"
                type="email"
                prepend-inner-icon="mdi-email-outline"
                variant="outlined"
                density="compact"
                color="primary"
                hide-details="auto"
                required
                :error-messages="errors.email"
                class="mb-2"
              />

              <v-text-field
                v-model="form.password"
                label="Parole"
                :type="showPassword ? 'text' : 'password'"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"
                variant="outlined"
                density="compact"
                color="primary"
                hide-details="auto"
                required
                :error-messages="errors.password"
                class="mb-2"
              />

              <v-text-field
                v-model="form.password_confirmation"
                label="Apstiprināt paroli"
                :type="showConfirmPassword ? 'text' : 'password'"
                prepend-inner-icon="mdi-lock-check-outline"
                :append-inner-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showConfirmPassword = !showConfirmPassword"
                variant="outlined"
                density="compact"
                color="primary"
                hide-details="auto"
                required
                class="mb-4"
              />

              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                rounded="lg"
                :loading="loading"
                :disabled="!isFormValid"
                class="text-none font-weight-bold"
              >
                Reģistrēties
              </v-btn>
            </v-form>

            <v-expand-transition>
              <v-alert
                v-if="successMessage"
                type="success"
                variant="tonal"
                density="compact"
                class="mt-4 rounded-lg text-caption"
              >
                {{ successMessage }}
              </v-alert>
            </v-expand-transition>

            <v-divider class="my-4"></v-divider>

            <div class="text-center">
              <span class="text-body-2 text-medium-emphasis">Jau ir konts? </span>
              <v-btn
                variant="text"
                to="/login"
                color="primary"
                size="small"
                class="text-none font-weight-bold px-1"
              >
                Ienākt šeit
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { ref, reactive } from "vue";
import { register } from "../services/auth";
import { useRouter } from "vue-router";

const router = useRouter();
const isFormValid = ref(false);
const loading = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const errors = ref({});
const successMessage = ref("");

const registerUser = async () => {
  errors.value = {};
  successMessage.value = "";
  loading.value = true;

  try {
    await register(form);
    successMessage.value = "Lietotājs veiksmīgi pievienots!";
    
    // Redirect after a short delay so user sees the success message
    setTimeout(() => {
      router.push("/");
    }, 1500);
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
</script>

<style scoped>
.auth-wrapper {
  /* Using min-height: 100dvh (dynamic viewport height) for better mobile support */
  min-height: calc(100dvh - 64px); 
  display: flex;
  align-items: center; 
  justify-content: center; 
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  width: 100%;
  /* Critical: prevent horizontal scroll */
  overflow-x: hidden;
  margin: auto;
  /* padding: 12px;  */
}

.auth-card {
  width: 100%;
  max-width: 440px; /* Slimmer for better laptop/mobile fit */
}

/* Force elements to wrap if they are somehow wider than the phone */
:deep(.v-card) {
  overflow: hidden !important;
}

@media (max-width: 600px) {
  .auth-card {
    padding: 20px !important; /* Smaller padding on phones */
  }
  h1 {
    font-size: 1.5rem !important; /* Smaller title so it doesn't wrap weirdly */
  }
}
</style>