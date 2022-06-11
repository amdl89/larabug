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
        Update Profile
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="updateProfileForm.name"
            label="Name"
            outlined
            clearable
            :error="Boolean(updateProfileForm.errors.name)"
            :error-messages="updateProfileForm.errors.name"
          ></v-text-field>
        </div>
        <div>
          <v-text-field
            v-model="updateProfileForm.title"
            label="Title"
            outlined
            clearable
            :error="Boolean(updateProfileForm.errors.title)"
            :error-messages="updateProfileForm.errors.title"
          ></v-text-field>
        </div>
        <div>
          <v-text-field
            v-model="updateProfileForm.education"
            label="Education"
            outlined
            clearable
            :error="Boolean(updateProfileForm.errors.education)"
            :error-messages="updateProfileForm.errors.education"
          ></v-text-field>
        </div>
        <div>
          <v-text-field
            v-model="updateProfileForm.address"
            label="Address"
            outlined
            clearable
            :error="Boolean(updateProfileForm.errors.address)"
            :error-messages="updateProfileForm.errors.address"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="updateProfileForm.bio"
            outlined
            label="Bio"
            :error="Boolean(updateProfileForm.errors.bio)"
            :error-messages="updateProfileForm.errors.bio"
          ></v-textarea>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="cancelUpdate">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="update"
          :loading="updateProfileForm.processing"
          :disabled="updateProfileForm.processing"
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
    profile: {
      type: Object,
      required: true,
    },
  },
  data() {
    const { name, title, education, address, bio } = this.profile;
    return {
      dialogShown: false,
      updateProfileForm: this.$inertia.form({
        name,
        title,
        education,
        address,
        bio,
      }),
    };
  },
  methods: {
    syncFormValues(newVal) {
      const { name, title, education, address, bio } = newVal;

      this.updateProfileForm.defaults({
        name,
        title,
        education,
        address,
        bio,
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.updateProfileForm.reset();
      this.updateProfileForm.clearErrors();
    },
    update() {
      this.updateProfileForm.put(
        route("profiles.update", { profile: this.profile }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.updateProfileForm.defaults();
            this.dialogShown = false;
          },
        }
      );
    },
  },
  watch: {
    ticket: {
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
