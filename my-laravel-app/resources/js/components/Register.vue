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
import axios from '../bootstrap.js'; // use the same bootstrap.js that sets withCredentials

export default {
  data() {
    return {
      form: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
      },
      errors: {},
      successMessage: "",
    };
  },
  methods: {
    async registerUser() {
      this.errors = {};
      this.successMessage = "";

      try {
        // ✅ Step 1: get CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        // ✅ Step 2: send registration request
        const response = await axios.post('/register', this.form);

        this.successMessage = "Lietotājs veiksmīgi pievienots!";

        // reset form
        this.form = {
          name: "",
          email: "",
          password: "",
          password_confirmation: "",
        };

      } catch (error) {
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors; // validation errors
        } else {
          console.error(error);
        }
      }
    },
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
