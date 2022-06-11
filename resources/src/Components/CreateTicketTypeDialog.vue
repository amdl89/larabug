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
        New
      </v-btn>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Create a new Ticket Type
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="newTicketTypeForm.name"
            label="Name"
            outlined
            clearable
            :error="Boolean(newTicketTypeForm.errors.name)"
            :error-messages="newTicketTypeForm.errors.name"
          ></v-text-field>
        </div>
        <div class="d-flex justify-center">
          <div>
            <h3 class="text-center">Color</h3>
            <v-color-picker
              v-model="newTicketTypeForm.color"
              flat
              mode="hexa"
              hide-mode-switch
            ></v-color-picker>
          </div>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="closeDialog"> Cancel </v-btn>
        <v-btn
          color="primary"
          @click="createNewTicketType"
          :loading="newTicketTypeForm.processing"
          :disabled="newTicketTypeForm.processing"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      dialogShown: false,
      newTicketTypeForm: this.$inertia.form({
        name: "",
        color: "#FFFFFF",
      }),
    };
  },
  methods: {
    closeDialog() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.newTicketTypeForm.reset();
      this.newTicketTypeForm.clearErrors();
    },
    createNewTicketType() {
      this.newTicketTypeForm.post(route("ticketTypes.store"), {
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
