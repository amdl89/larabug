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
      <div class="text-center text-h6 mx-3">Delete ticket</div>
      <div class="text-center text-h5 mx-3">"{{ ticket.title }}"</div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
        </v-btn>
        <v-btn
          color="error"
          @click="delTicket"
          :loading="deletingTicket"
          :disabled="deletingTicket"
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
    ticket: {
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
      deletingTicket: false,
    };
  },
  methods: {
    delTicket() {
      this.$inertia.delete(
        route(
          "tickets.destroy",
          _.pickBy({
            ticket: this.ticket.id,
            redirectUrl: this.redirectUrl,
          })
        ),
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.deletingTicket = true;
          },
          onSuccess: () => {
            this.deletingTicket = false;
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
