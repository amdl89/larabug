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
          <v-btn color="secondary">
            <v-icon left>mdi-file</v-icon>
            Add file
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Add a new File
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="newAttachmentForm.name"
            label="Title"
            outlined
            clearable
            :error="Boolean(newAttachmentForm.errors.name)"
            :error-messages="newAttachmentForm.errors.name"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="newAttachmentForm.notes"
            outlined
            label="Notes"
            :error="Boolean(newAttachmentForm.errors.notes)"
            :error-messages="newAttachmentForm.errors.notes"
          ></v-textarea>
        </div>
        <div>
          <v-file-input
            v-model="newAttachmentForm.attachedFile"
            label="File"
            :error="Boolean(newAttachmentForm.errors.attachedFile)"
            :error-messages="newAttachmentForm.errors.attachedFile"
          ></v-file-input>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <div v-if="newAttachmentForm.progress" class="mr-2">
          Uploading:
          <span class="font-weight-bold">
            {{ Math.floor(newAttachmentForm.progress.percentage) }}
          </span>
          %
        </div>
        <v-btn color="secondary" text @click="closeDialog">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="createNewAttachment"
          :loading="newAttachmentForm.processing"
          :disabled="newAttachmentForm.processing"
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
    storeUrl: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      newAttachmentForm: this.$inertia.form({
        name: "",
        notes: "",
        attachedFile: null,
      }),
    };
  },
  computed: {},
  methods: {
    closeDialog() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.newAttachmentForm.reset();
      this.newAttachmentForm.clearErrors();
    },
    createNewAttachment() {
      this.newAttachmentForm.post(this.storeUrl, {
        preserveState: true,
        preserveScroll: true,
        forceFormData: true,
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
