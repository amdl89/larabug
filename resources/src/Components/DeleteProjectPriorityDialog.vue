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
      <div class="text-center text-h6 mx-3">Delete project Priority</div>
      <div class="text-center text-h5 mx-3">"{{ projectPriority.name }}"</div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
        </v-btn>
        <v-btn
          color="error"
          @click="delProjectPriority"
          :loading="deletingProjectPriority"
          :disabled="deletingProjectPriority"
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
    projectPriority: {
      type: Object,
      required: true,
    },
    redirectUrl: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      dialogShown: false,
      deletingProjectPriority: false,
    };
  },
  methods: {
    delProjectPriority() {
      this.$inertia.delete(
        route(
          "projectPriorities.destroy",
          _.pickBy({
            projectPriority: this.projectPriority,
            redirectUrl: this.redirectUrl,
          })
        ),
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.deletingProjectPriority = true;
          },
          onSuccess: () => {
            this.deletingProjectPriority = false;
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
