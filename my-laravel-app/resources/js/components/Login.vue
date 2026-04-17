<template>
  <div class="login-page-wrapper">
    <v-container>
      <v-row justify="center" align="center" no-gutters>
        <v-col cols="12" sm="10" md="6" lg="5">
          
          <v-card class="pa-10 rounded-lg elevation-12">
            <h1 class="text-h4 font-weight-bold text-center mb-8">Pieslēgšanās</h1>
            
            <v-form @submit.prevent="loginUser" v-model="isFormValid">
              <v-text-field
                v-model="form.email"
                label="E-pasts"
                placeholder="piemērs@pasts.lv"
                type="email"
                prepend-inner-icon="mdi-email-outline"
                variant="filled"
                color="primary"
                required
                :rules="[v => !!v || 'E-pasts ir obligāts']"
                class="mb-2"
              />

              <v-text-field
                v-model="form.password"
                label="Parole"
                placeholder="********"
                :type="showPassword ? 'text' : 'password'"
                prepend-inner-icon="mdi-lock-outline"
                :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                @click:append-inner="showPassword = !showPassword"
                variant="filled"
                color="primary"
                required
                :rules="[v => !!v || 'Parole ir obligāta']"
                class="mb-4"
              />

              <v-btn
                type="submit"
                color="primary"
                size="large"
                block
                rounded="lg"
                elevation="2"
                :loading="loading"
                :disabled="!isFormValid"
                class="text-none"
              >
                Pieslēgties
              </v-btn>
            </v-form>

            <v-expand-transition>
              <v-alert
                v-if="error"
                type="error"
                variant="tonal"
                density="comfortable"
                class="mt-6 rounded-lg"
                closable
              >
                {{ error }}
              </v-alert>
            </v-expand-transition>

            <v-divider class="my-8"></v-divider>

            <div class="text-center">
              <span class="text-body-2 text-medium-emphasis">Nav konta? </span>
              <v-btn
                variant="text"
                to="/register"
                color="primary"
                class="text-none font-weight-bold px-1"
              >
                Reģistrēties šeit
              </v-btn>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { login } from "../services/auth";
import { useRouter } from "vue-router";

const router = useRouter();

const form = reactive({
  email: "",
  password: ""
});

const error = ref("");
const successMessage = ref("");
const loading = ref(false);
const showPassword = ref(false);
const isFormValid = ref(false);

const loginUser = async () => {
  if (!isFormValid.value) return;
  
  error.value = "";
  successMessage.value = "";
  loading.value = true;

  try {
    await login(form);
    successMessage.value = "Veiksmīgi pieslēdzāties!";
    
    setTimeout(() => {
      router.push("/");
    }, 1000);
  } catch (err) {
    error.value = err.response?.data?.message || "Nepareizs e-pasts vai parole";
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
  padding: 12px; 
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