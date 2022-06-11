<template>
  <v-dialog
    v-model="dialogShown"
    max-width="700"
    max-height="500"
    @keydown.esc="closeDialog"
    @click:outside="closeDialog"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="success" v-bind="attrs" v-on="on">
        <v-icon left>mdi-plus-thick</v-icon>
        New
      </v-btn>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Create a new Project Priority
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="newProjectPriorityForm.name"
            label="Name"
            outlined
            clearable
            :error="Boolean(newProjectPriorityForm.errors.name)"
            :error-messages="newProjectPriorityForm.errors.name"
          ></v-text-field>
        </div>
        <div class="d-flex justify-center">
          <div>
            <h3 class="text-center">Color</h3>
            <v-color-picker
              v-model="newProjectPriorityForm.color"
              flat
              mode="hexa"
              hide-mode-switch
            ></v-color-picker>
          </div>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="closeDialog"> Cancel </v-btn>
        <v-btn
          color="primary"
          @click="createNewProjectPriority"
          :loading="newProjectPriorityForm.processing"
          :disabled="newProjectPriorityForm.processing"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      dialogShown: false,
      newProjectPriorityForm: this.$inertia.form({
        name: "",
        color: "#FFFFFF",
      }),
    };
  },
  methods: {
    closeDialog() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.newProjectPriorityForm.reset();
      this.newProjectPriorityForm.clearErrors();
    },
    createNewProjectPriority() {
      this.newProjectPriorityForm.post(route("projectPriorities.store"), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.resetForm();
          this.dialogShown = false;
        },
      });
    },
  },
};
</script>

<style lang="scss">
</style>
