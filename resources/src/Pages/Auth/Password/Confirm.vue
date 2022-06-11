<template>
  <v-sheet elevation="12" rounded="xl" class="pa-6" min-width="350px">
    <div class="text-center">
      <span class="outline-icon">
        <v-avatar size="72">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </span>
      <h2 class="text-center mt-2">Enter Your Password</h2>
    </div>
    <form @submit.prevent="confirmPassword" class="mt-3">
      <v-text-field
        label="Password"
        v-model="confirmPasswordForm.password"
        :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword"
        :error="Boolean(confirmPasswordForm.errors.password)"
        :error-messages="confirmPasswordForm.errors.password"
      ></v-text-field>

      <v-btn
        type="submit"
        block
        large
        color="primary"
        class="mt-2"
        :loading="confirmPasswordForm.processing"
        :disabled="confirmPasswordForm.processing"
      >
        Confirm
      </v-btn>
    </form>
    <p class="lead mt-5 text-center">
      Go back to
      <Link :href="route('home')" method="GET" class="primary--text">
        Home
      </Link>
    </p>
  </v-sheet>
</template>

<script>
import AuthLayout from "@/Layouts/Auth";

export default {
  layout: AuthLayout,
  data() {
    return {
      confirmPasswordForm: this.$inertia.form({
        password: "",
      }),
      showPassword: false,
    };
  },
  methods: {
    confirmPassword() {
      this.confirmPasswordForm.post(route("password.confirm"), {
        onError: () => this.loginForm.reset("password"),
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
