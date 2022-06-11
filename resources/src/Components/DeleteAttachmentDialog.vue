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
      <div class="text-center text-h6 mx-3">Delete attachment</div>
      <div class="text-center text-h5 mx-3">"{{ attachment.name }}"</div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
        </v-btn>
        <v-btn
          color="error"
          @click="delAttachment"
          :loading="deletingAttachment"
          :disabled="deletingAttachment"
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
    attachment: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      deletingAttachment: false,
    };
  },
  methods: {
    delAttachment() {
      this.$inertia.delete(
        route("attachments.destroy", { attachment: this.attachment }),
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.deletingAttachment = true;
          },
          onSuccess: () => {
            this.deletingAttachment = false;
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
