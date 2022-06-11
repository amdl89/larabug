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
        New Project
      </v-btn>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Create a new Project
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="createProjectForm.title"
            label="Title"
            outlined
            clearable
            :error="Boolean(createProjectForm.errors.title)"
            :error-messages="createProjectForm.errors.title"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="createProjectForm.description"
            outlined
            label="Description"
            :error="Boolean(createProjectForm.errors.description)"
            :error-messages="createProjectForm.errors.description"
          ></v-textarea>
        </div>
        <v-row>
          <v-col cols="12" sm="6">
            <v-menu>
              <template v-slot:activator="{ on }">
                <v-text-field
                  v-on="on"
                  label="Deadline"
                  :value="formatDate(createProjectForm.deadline)"
                  :error="Boolean(createProjectForm.errors.deadline)"
                  :error-messages="createProjectForm.errors.deadline"
                  outlined
                >
                </v-text-field>
              </template>
              <v-date-picker
                v-model="createProjectForm.deadline"
                color="success"
                :min="new Date().toISOString()"
              ></v-date-picker>
            </v-menu>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="createProjectForm.priority"
              :items="priorities"
              label="Priority"
              outlined
              :error="Boolean(createProjectForm.errors.priority)"
              :error-messages="createProjectForm.errors.priority"
            ></v-select>
          </v-col>
        </v-row>
        <div>
          <v-select
            v-model="createProjectForm.supervisor"
            :items="supervisors"
            label="Supervisor"
            outlined
            clearable
            :error="Boolean(createProjectForm.errors.supervisor)"
            :error-messages="createProjectForm.errors.supervisor"
          ></v-select>
        </div>
        <div>
          <v-file-input
            v-model="createProjectForm.coverImage"
            label="Cover Image (Optional)"
            :error="Boolean(createProjectForm.errors.coverImage)"
            :error-messages="createProjectForm.errors.coverImage"
            :loading="createProjectForm.processing"
          ></v-file-input>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <div v-if="createProjectForm.progress" class="mr-2">
          Uploading:
          <span class="font-weight-bold">
            {{ Math.floor(createProjectForm.progress.percentage) }}
          </span>
          %
        </div>
        <v-btn color="secondary" text @click="closeDialog">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="createProject"
          :loading="createProjectForm.processing"
          :disabled="createProjectForm.processing"
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
    priorities: {
      type: Array,
      required: true,
    },
    supervisors: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      createProjectForm: this.$inertia.form({
        title: "",
        description: "",
        deadline: null,
        priority: null,
        supervisor: null,
        coverImage: null,
      }),
    };
  },
  methods: {
    closeDialog() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.createProjectForm.reset();
      this.createProjectForm.clearErrors();
    },
    formatDate(date) {
      return date && moment(date).format("MMM DD, YYYY");
    },
    createProject() {
      this.createProjectForm.post(route("projects.store"), {
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
