<template>
  <v-sheet elevation="12" rounded="xl" class="pa-6" min-width="350px">
    <div class="text-center">
      <span class="outline-icon">
        <v-avatar size="72">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </span>
      <h2 class="text-center mt-2">Enter Your New Password</h2>
    </div>
    <form @submit.prevent="resetPassword" class="mt-3">
      <v-text-field label="Email" :value="email" disabled></v-text-field>

      <v-text-field
        label="Password"
        v-model="resetPasswordForm.password"
        :type="showPassword ? 'text' : 'password'"
        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
        @click:append="showPassword = !showPassword"
        :error="Boolean(resetPasswordForm.errors.password)"
        :error-messages="resetPasswordForm.errors.password"
      ></v-text-field>

      <v-text-field
        label="Confirm Password"
        v-model="registerForm.password_confirmation"
        type="password"
        :error="Boolean(resetPasswordForm.errors.password_confirmation)"
        :error-messages="resetPasswordForm.errors.password_confirmation"
      ></v-text-field>

      <v-btn
        type="submit"
        block
        large
        color="primary"
        class="mt-2"
        :loading="resetPasswordForm.processing"
        :disabled="resetPasswordForm.processing"
      >
        Reset
      </v-btn>
    </form>
  </v-sheet>
</template>

<script>
import AuthLayout from "@/Layouts/Auth";

export default {
  props: {
    token: {
      type: String,
      required: true,
    },
    email: {
      type: String,
      required: true,
    },
  },
  layout: AuthLayout,
  data() {
    return {
      resetPasswordForm: this.$inertia.form({
        email: this.email,
        password: "",
        password_confirmation: "",
        token: this.token,
      }),
      showPassword: false,
    };
  },
  methods: {
    resetPassword() {
      this.resetPasswordForm.post(route("password.update"), {
        onError: () =>
          this.loginForm.reset("password", "password_confirmation"),
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
