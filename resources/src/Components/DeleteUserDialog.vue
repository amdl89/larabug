<template>
  <v-dialog v-model="dialogShown" max-width="600">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn icon>
            <v-icon color="error">mdi-delete</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>

    <v-card class="py-6 px-2">
      <div class="text-center text-h6 mx-3">Delete User</div>
      <div class="text-center text-h5 mx-3">"{{ user.profile.name }}"</div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
        </v-btn>
        <v-btn
          color="error"
          @click="delUser"
          :loading="deletingUser"
          :disabled="deletingUser"
        >
          Delete
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
    redirectUrl: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      dialogShown: false,
      deletingUser: false,
    };
  },
  methods: {
    delUser() {
      this.$inertia.delete(
        route(
          "users.destroy",
          _.pickBy({
            user: this.user,
            redirectUrl: this.redirectUrl,
          })
        ),
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.deletingUser = true;
          },
          onSuccess: () => {
            this.deletingUser = false;
            this.dialogShown = false;
          },
        }
      );
    },
  },
};
</script>

<style lang="scss">
</style>
