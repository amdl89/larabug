<template>
  <v-sheet elevation="12" rounded="xl" class="pa-6" min-width="350px">
    <div class="text-center">
      <span class="outline-icon">
        <v-avatar size="70">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </span>
      <h2 class="text-center mt-2">Login To Your Account</h2>
    </div>
    <form @submit.prevent="login" class="mt-3">
      <v-text-field
        label="Email Or Username"
        v-model="loginForm.email"
        :error="Boolean(loginForm.errors.email)"
        :error-messages="loginForm.errors.email"
      ></v-text-field>

      <v-text-field
        label="Password"
        v-model="loginForm.password"
        :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword"
        :error="Boolean(loginForm.errors.password)"
        :error-messages="loginForm.errors.password"
      ></v-text-field>

      <v-checkbox v-model="loginForm.remember" label="Remember Me"></v-checkbox>

      <v-btn
        type="submit"
        block
        large
        color="primary"
        class="mt-2"
        :loading="loginForm.processing"
        :disabled="loginForm.processing"
      >
        Login
      </v-btn>
    </form>
    <p class="lead mt-5 text-center">
      Don't have an account?
      <Link :href="route('register')" method="GET" class="primary--text">
        Register
      </Link>
    </p>
    <p class="lead mt-5 text-center">
      Forgot your password?
      <Link
        :href="route('password.request')"
        method="GET"
        class="primary--text"
      >
        Reset
      </Link>
    </p>
    <demo-user-login-dialog />
  </v-sheet>
</template>

<script>
import AuthLayout from "@/Layouts/Auth";
import DemoUserLoginDialog from "@/Components/DemoUserLoginDialog.vue";

export default {
  components: { DemoUserLoginDialog },
  layout: AuthLayout,
  data() {
    return {
      loginForm: this.$inertia.form({
        email: "",
        password: "",
        remember: false,
      }),
      showPassword: false,
    };
  },
  methods: {
    login() {
      this.loginForm.post("/login", {
        onError: () => this.loginForm.reset("password"),
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
