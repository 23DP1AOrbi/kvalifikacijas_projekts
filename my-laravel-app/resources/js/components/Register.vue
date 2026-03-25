<!-- <template>
  <div class="register-form">
    <h1>Reģistrācija</h1>
    <form @submit.prevent="registerUser">
      <div>
        <label for="name">Lietotājvārds</label>
        <input v-model="form.name" type="text" id="name" required />
        <span class="error" v-if="errors.name">{{ errors.name[0] }}</span>
      </div>

      <div>
        <label for="email">E-pasts</label>
        <input v-model="form.email" type="email" id="email" required />
        <span class="error" v-if="errors.email">{{ errors.email[0] }}</span>
      </div>

      <div>
        <label for="password">Parole</label>
        <input v-model="form.password" type="password" id="password" required />
        <span class="error" v-if="errors.password">{{ errors.password[0] }}</span>
      </div>

      <div>
        <label for="password_confirmation">Apstiprināt Paroli</label>
        <input v-model="form.password_confirmation" type="password" id="password_confirmation" required />
      </div>

      <button type="submit">Reģistrēties</button>
    </form>
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
  </div>
</template>

<script>
import axios from "axios";



export default {
  data() {
    return {
      form: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "user",
      },
      errors: {},
      successMessage: "",
    };
  },
  methods: {
     async getCsrfToken() {
      try {
          const res = await axios.get('/csrf-token'); // hits the Laravel route
          axios.defaults.headers.common['X-CSRF-TOKEN'] = res.data.csrf_token;
          axios.defaults.withCredentials = true; // include session cookie
      } catch (error) {
          console.error("Failed to get CSRF token", error);
      }
    },

      async registerUser() {
        this.errors = {};
        this.successMessage = "";

        await this.getCsrfToken();

        try {
          const response = await axios.post("/register", this.form);

          this.successMessage = "Lietotājs veiksmīgi pievienots!";

          this.form = {
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            role: "user",
          };

        } catch (error) {
          if (error.response?.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.error(error);
          }
        }
    }

  },
};
</script>

<style scoped>
.error {
  color: red;
  font-size: 0.9em;
}
.success {
  color: green;
  margin-top: 10px;
}
</style> -->

<template>
  <div class="register-form">
    <h1>Reģistrācija</h1>
    <form @submit.prevent="registerUser">
      <div>
        <label for="name">Lietotājvārds</label>
        <input v-model="form.name" type="text" id="name" required />
        <span class="error" v-if="errors.name">{{ errors.name[0] }}</span>
      </div>

      <div>
        <label for="email">E-pasts</label>
        <input v-model="form.email" type="email" id="email" required />
        <span class="error" v-if="errors.email">{{ errors.email[0] }}</span>
      </div>

      <div>
        <label for="password">Parole</label>
        <input v-model="form.password" type="password" id="password" required />
        <span class="error" v-if="errors.password">{{ errors.password[0] }}</span>
      </div>

      <div>
        <label for="password_confirmation">Apstiprināt Paroli</label>
        <input v-model="form.password_confirmation" type="password" id="password_confirmation" required />
      </div>

      <button type="submit">Reģistrēties</button>
    </form>
    <div v-if="successMessage" class="success">{{ successMessage }}</div>
  </div>
</template>

<script>
import { ref } from "vue";
import { register } from "../services/auth";

export default {
  setup() {
    const form = ref({
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

      try {
        await register(form.value);
        successMessage.value = "Lietotājs veiksmīgi pievienots!";
        form.value = { name: "", email: "", password: "", password_confirmation: "" };
        window.location.href = "/";
      } catch (error) {
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors;
        } else {
          console.error(error);
        }
      }
    };

    return { form, errors, successMessage, registerUser };
  },
};
</script>


<style scoped>
.error {
  color: red;
  font-size: 0.9em;
}
.success {
  color: green;
  margin-top: 10px;
}
</style>
