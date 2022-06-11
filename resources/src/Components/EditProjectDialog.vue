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
        Update Project
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="updateProjectForm.title"
            label="Title"
            outlined
            clearable
            :error="Boolean(updateProjectForm.errors.title)"
            :error-messages="updateProjectForm.errors.title"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="updateProjectForm.description"
            outlined
            label="Description"
            :error="Boolean(updateProjectForm.errors.description)"
            :error-messages="updateProjectForm.errors.description"
          ></v-textarea>
        </div>
        <v-row>
          <v-col cols="12" sm="6">
            <v-menu>
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-on="on"
                  label="Deadline"
                  :value="formatDate(updateProjectForm.deadline)"
                  :error="Boolean(updateProjectForm.errors.deadline)"
                  :error-messages="updateProjectForm.errors.deadline"
                  outlined
                >
                </v-text-field>
              </template>
              <v-date-picker
                v-model="updateProjectForm.deadline"
                color="success"
                :min="formatDateForDatePicker(project.createdAt)"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="updateProjectForm.status"
              :items="statuses"
              label="Status"
              outlined
              :error="Boolean(updateProjectForm.errors.status)"
              :error-messages="updateProjectForm.errors.status"
            ></v-select>
          </v-col>
        </v-row>
        <div v-if="canAssignProject">
          <v-select
            v-model="updateProjectForm.supervisor"
            :items="supervisors"
            label="Supervisor"
            outlined
            clearable
            :error="Boolean(updateProjectForm.errors.supervisor)"
            :error-messages="updateProjectForm.errors.supervisor"
          ></v-select>
        </div>
        <div>
          <v-select
            v-model="updateProjectForm.priority"
            :items="priorities"
            label="Priority"
            outlined
            :error="Boolean(updateProjectForm.errors.priority)"
            :error-messages="updateProjectForm.errors.priority"
          ></v-select>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="cancelUpdate">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="updateProject"
          :loading="updateProjectForm.processing"
          :disabled="updateProjectForm.processing"
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
    project: {
      type: Object,
      required: true,
    },
    statuses: {
      type: Array,
      required: true,
    },
    priorities: {
      type: Array,
      required: true,
    },
    supervisors: {
      type: Array,
      required: true,
    },
    canAssignProject: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      updateProjectForm: this.$inertia.form({
        title: this.project.title,
        description: this.project.description,
        status: this.project.status,
        deadline: this.formatDateForDatePicker(this.project.deadline),
        priority: this.project.priority.id.toString(),
        supervisor: this.project.supervisor?.id?.toString(),
      }),
    };
  },
  methods: {
    syncFormValues(newVal) {
      this.updateProjectForm.defaults({
        title: newVal.title,
        description: newVal.description,
        status: newVal.status,
        deadline: this.formatDateForDatePicker(newVal.deadline),
        priority: newVal.priority.id.toString(),
        supervisor: newVal.supervisor?.id?.toString(),
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.updateProjectForm.reset();
      this.updateProjectForm.clearErrors();
    },
    formatDate(date) {
      return date && moment(date).format("MMM DD, YYYY");
    },
    formatDateForDatePicker(date) {
      return moment(date).toISOString().substr(0, 10);
    },
    updateProject() {
      if (!this.canAssignProject) {
        delete this.updateProjectForm.supervisor;
      }
      this.updateProjectForm.put(
        route("projects.update", { project: this.project }),
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
    project: {
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
