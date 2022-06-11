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
          <v-btn icon>
            <v-icon color="primary">mdi-pencil</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Update Project Priority
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="updateProjectPriorityForm.name"
            label="Name"
            outlined
            clearable
            :error="Boolean(updateProjectPriorityForm.errors.name)"
            :error-messages="updateProjectPriorityForm.errors.name"
          ></v-text-field>
        </div>
        <div class="d-flex justify-center">
          <div>
            <h3 class="text-center">Color</h3>
            <v-color-picker
              v-model="updateProjectPriorityForm.color"
              flat
              mode="hexa"
              hide-mode-switch
            ></v-color-picker>
          </div>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="cancelUpdate"> Cancel </v-btn>
        <v-btn
          color="primary"
          @click="updateProjectPriority"
          :loading="updateProjectPriorityForm.processing"
          :disabled="updateProjectPriorityForm.processing"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    projectPriority: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      updateProjectPriorityForm: this.$inertia.form({
        name: this.projectPriority.name,
        color: this.projectPriority.color,
      }),
    };
  },
  methods: {
    syncFormValues(newVal) {
      this.updateProjectPriorityForm.defaults({
        name: newVal.name,
        color: newVal.color,
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.updateProjectPriorityForm.reset();
      this.updateProjectPriorityForm.clearErrors();
    },
    updateProjectPriority() {
      this.updateProjectPriorityForm.put(
        route("projectPriorities.update", {
          projectPriority: this.projectPriority,
        }),
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
    projectPriority: {
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
