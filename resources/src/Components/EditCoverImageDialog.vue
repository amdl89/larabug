<template>
  <v-dialog
    v-model="dialogShown"
    max-width="700"
    max-height="500"
    @keydown.esc="closeDialog"
    @click:outside="closeDialog"
  >
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn elevation="1" class="rounded-lg">
            <v-icon left>mdi-camera</v-icon>
            Edit
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Change Cover Image
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-file-input
            v-model="editCoverImageForm.coverImage"
            label="Cover Image"
            :error="Boolean(editCoverImageForm.errors.coverImage)"
            :error-messages="editCoverImageForm.errors.coverImage"
            @change="updatePreview"
          ></v-file-input>
        </div>
        <div class="mt-2">
          <v-img :src="url" max-height="300px"></v-img>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end mt-2">
        <div v-if="editCoverImageForm.progress" class="flex-grow-1 mr-2">
          Uploading:
          <span class="font-weight-bold">
            {{ Math.floor(editCoverImageForm.progress.percentage) }}
          </span>
          %
        </div>
        <v-btn color="secondary" text @click="closeDialog">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="editCoverImage"
          :loading="editCoverImageForm.processing"
          :disabled="
            !editCoverImageForm.coverImage || editCoverImageForm.processing
          "
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
    initialCoverImageUrl: {
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
      url: this.initialCoverImageUrl,

      editCoverImageForm: this.$inertia.form({
        coverImage: null,
        _method: "put",
      }),
    };
  },
  computed: {},
  methods: {
    closeDialog() {
      this.resetForm();
      this.url = this.initialCoverImageUrl;
      this.dialogShown = false;
    },
    resetForm() {
      this.editCoverImageForm.reset();
      this.editCoverImageForm.clearErrors();
    },
    editCoverImage() {
      this.editCoverImageForm.post(this.updateUrl, {
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
      if (this.editCoverImageForm.coverImage) {
        this.url = URL.createObjectURL(this.editCoverImageForm.coverImage);
      } else {
        this.url = this.initialCoverImageUrl;
      }
    },
  },
};
</script>

<style lang="scss">
</style>
