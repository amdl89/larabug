<template>
  <v-dialog
    v-model="dialogShown"
    max-width="600"
    max-height="500"
    @keydown.esc="closeDialog"
    @click:outside="closeDialog"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn elevation="1" class="rounded-lg">
            <v-icon>mdi-camera</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Change initialAvatarUrl
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-file-input
            v-model="editAvatarForm.avatar"
            label="Avatar"
            :error="Boolean(editAvatarForm.errors.avatar)"
            :error-messages="editAvatarForm.errors.avatar"
            @change="updatePreview"
          ></v-file-input>
        </div>
        <div class="mt-2">
          <v-img :src="url" max-height="400px" contain></v-img>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end mt-2">
        <div v-if="editAvatarForm.progress" class="flex-grow-1 mr-2">
          Uploading:
          <span class="font-weight-bold">
            {{ Math.floor(editAvatarForm.progress.percentage) }}
          </span>
          %
        </div>
        <v-btn color="secondary" text @click="closeDialog">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="editavatar"
          :loading="editAvatarForm.processing"
          :disabled="!editAvatarForm.avatar || editAvatarForm.processing"
        >
          Update
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    initialAvatarUrl: {
      type: String,
      required: true,
    },
    updateUrl: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      url: this.initialAvatarUrl,

      editAvatarForm: this.$inertia.form({
        avatar: null,
        _method: "put",
      }),
    };
  },
  computed: {},
  methods: {
    closeDialog() {
      this.resetForm();
      this.url = this.initialAvatarUrl;
      this.dialogShown = false;
    },
    resetForm() {
      this.editAvatarForm.reset();
      this.editAvatarForm.clearErrors();
    },
    editavatar() {
      this.editAvatarForm.post(this.updateUrl, {
        preserveState: true,
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          this.resetForm();
          this.dialogShown = false;
        },
      });
    },
    updatePreview() {
      if (this.editAvatarForm.avatar) {
        this.url = URL.createObjectURL(this.editAvatarForm.avatar);
      } else {
        this.url = this.initialAvatarUrl;
      }
    },
  },
};
</script>

<style lang="scss">
</style>
