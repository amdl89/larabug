<template>
  <v-sheet elevation="12" rounded="xl" class="pa-6" min-width="350px">
    <div class="text-center">
      <span class="outline-icon">
        <v-avatar size="72">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </span>
      <h2 class="text-center mt-2">Enter Your Email</h2>
    </div>
    <form @submit.prevent="requestPasswordReset" class="mt-3">
      <v-text-field
        label="Email"
        v-model="requestPasswordResetForm.email"
        :error="Boolean(requestPasswordResetForm.errors.email)"
        :error-messages="requestPasswordResetForm.errors.email"
      ></v-text-field>

      <v-btn
        type="submit"
        block
        large
        color="primary"
        class="mt-2"
        :loading="requestPasswordResetForm.processing"
        :disabled="requestPasswordResetForm.processing"
      >
        Send Reset Link
      </v-btn>
    </form>
    <p class="lead mt-5 text-center">
      Go back to
      <Link :href="route('login')" method="GET" class="primary--text">
        Login
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
      requestPasswordResetForm: this.$inertia.form({
        email: "",
      }),
    };
  },
  methods: {
    requestPasswordReset() {
      this.requestPasswordResetForm.post(route("password.request"), {
        onSuccess: () => {
          this.requestPasswordResetForm.reset("email");
          this.$toasted.success(
            "Password reset link will be sent shortly. Please check your email",
            {
              duration: 5000,
              icon: "check-bold",
            }
          );
        },
      });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
