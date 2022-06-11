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
        Update Ticket Priority
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="updateTicketPriorityForm.name"
            label="Name"
            outlined
            clearable
            :error="Boolean(updateTicketPriorityForm.errors.name)"
            :error-messages="updateTicketPriorityForm.errors.name"
          ></v-text-field>
        </div>
        <div class="d-flex justify-center">
          <div>
            <h3 class="text-center">Color</h3>
            <v-color-picker
              v-model="updateTicketPriorityForm.color"
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
          @click="updateTicketPriority"
          :loading="updateTicketPriorityForm.processing"
          :disabled="updateTicketPriorityForm.processing"
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
    ticketPriority: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      updateTicketPriorityForm: this.$inertia.form({
        name: this.ticketPriority.name,
        color: this.ticketPriority.color,
      }),
    };
  },
  methods: {
    syncFormValues(newVal) {
      this.updateTicketPriorityForm.defaults({
        name: newVal.name,
        color: newVal.color,
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.updateTicketPriorityForm.reset();
      this.updateTicketPriorityForm.clearErrors();
    },
    updateTicketPriority() {
      this.updateTicketPriorityForm.put(
        route("ticketPriorities.update", {
          ticketPriority: this.ticketPriority,
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
    ticketPriority: {
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
