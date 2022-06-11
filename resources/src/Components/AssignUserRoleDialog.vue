<template>
  <v-dialog
    v-model="dialogShown"
    max-width="700"
    max-height="500"
    @keydown.esc="cancelUpdate"
    @click:outside="cancelUpdate"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn color="primary" depressed rounded>Role</v-btn>
        </slot>
      </span>
    </template>
    <v-card class="py-6 overflow-y-auto">
      <div class="text-center text-h6 mx-3">Assign Role To User</div>
      <div class="text-center text-h5 mx-3">"{{ user.profile.name }}"</div>

      <div class="mt-8 px-8">
        <div>
          <v-select
            v-model="assignUserRoleForm.role"
            label="Select Role"
            outlined
            :items="allRoles"
          ></v-select>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="cancelUpdate">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="assignUserRole"
          :loading="assignUserRoleForm.processing"
          :disabled="assignUserRoleForm.processing"
        >
          Ok
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import constants from "@/constants.js";

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      assignUserRoleForm: this.$inertia.form({
        role: this.user.role,
      }),
    };
  },
  computed: {
    allRoles() {
      return Object.values(constants.Role);
    },
  },
  methods: {
    syncFormValues(newVal) {
      this.assignUserRoleForm.defaults({
        role: newVal.role,
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.assignUserRoleForm.reset();
      this.assignUserRoleForm.clearErrors();
    },
    assignUserRole() {
      this.assignUserRoleForm.put(
        route("users.roles.update", { user: this.user }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.resetForm();
            this.dialogShown = false;
          },
        }
      );
    },
  },
  watch: {
    user: {
      deep: true,
      handler(newVal) {
        this.syncFormValues(newVal);
        this.resetForm();
      },
    },
  },
};
</script>

<style lang="scss">
</style>
