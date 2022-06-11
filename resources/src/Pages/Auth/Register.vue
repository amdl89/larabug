<template>
  <v-sheet elevation="12" rounded="xl" class="pt-6 px-6" min-width="400px">
    <div class="text-center">
      <span class="outline-icon">
        <v-avatar size="72">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </span>
      <h2 class="text-center mt-2">Register A New Account</h2>
    </div>
    <form @submit.prevent="register" class="mt-3">
      <v-text-field
        label="Email"
        v-model="registerForm.email"
        :error="Boolean(registerForm.errors.email)"
        :error-messages="registerForm.errors.email"
      ></v-text-field>

      <v-text-field
        label="Username"
        v-model="registerForm.username"
        :error="Boolean(registerForm.errors.username)"
        :error-messages="registerForm.errors.username"
      ></v-text-field>

      <v-text-field
        label="Password"
        v-model="registerForm.password"
        :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword"
        :error="Boolean(registerForm.errors.password)"
        :error-messages="registerForm.errors.password"
      ></v-text-field>

      <v-text-field
        label="Confirm Password"
        v-model="registerForm.password_confirmation"
        type="password"
        :error="Boolean(registerForm.errors.password_confirmation)"
        :error-messages="registerForm.errors.password_confirmation"
      ></v-text-field>

      <v-select
        label="Select Role"
        v-model="registerForm.role"
        :items="allRoles"
        :error="Boolean(registerForm.errors.role)"
        :error-messages="registerForm.errors.role"
      >
      </v-select>

      <v-btn
        type="submit"
        block
        large
        color="primary"
        class="mt-2"
        :loading="registerForm.processing"
        :disabled="registerForm.processing"
      >
        Register
      </v-btn>
    </form>
    <p class="lead mt-5 text-center">
      Already have an account?
      <Link :href="route('login')" method="GET" class="primary--text">
        Log in
      </Link>
    </p>
  </v-sheet>
</template>

<script>
import AuthLayout from "@/Layouts/Auth";
import constants from "@/constants.js";

export default {
  layout: AuthLayout,
  data() {
    return {
      registerForm: this.$inertia.form({
        username: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: null,
      }),
      showPassword: false,
    };
  },
  computed: {
    allRoles() {
      return [constants.Role.Tester, constants.Role.Developer];
    },
  },
  methods: {
    register() {
      this.registerForm.post("/register", {
        onError: () =>
          this.registerForm.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
