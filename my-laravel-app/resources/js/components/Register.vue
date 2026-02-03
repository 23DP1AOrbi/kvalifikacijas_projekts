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

      <div>
        <label for="role">Lomas</label>
        <select v-model="form.role">
          <option value="user">Lietotājs</option>
          <option value="admin">Admins</option>
        </select>
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
    async registerUser() {
      this.errors = {};
      this.successMessage = "";

      try {
        const response = await axios.post("http://127.0.0.1:8000/api/register", this.form, {
          headers: {
            "Accept": "application/json",
          },
        });

        this.successMessage = "User registered successfully!";
        // Clear the form
        this.form.name = "";
        this.form.email = "";
        this.form.password = "";
        this.form.password_confirmation = "";
        this.form.role = "user";

        console.log(response.data);

      } catch (error) {
        if (error.response && error.response.status === 422) {
          // Validation errors
          this.errors = error.response.data.errors;
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
