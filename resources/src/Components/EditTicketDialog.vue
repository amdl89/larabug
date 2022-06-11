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
        Update Ticket
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="updateTicketForm.title"
            label="Title"
            outlined
            clearable
            :error="Boolean(updateTicketForm.errors.title)"
            :error-messages="updateTicketForm.errors.title"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="updateTicketForm.description"
            outlined
            label="Description"
            :error="Boolean(updateTicketForm.errors.description)"
            :error-messages="updateTicketForm.errors.description"
          ></v-textarea>
        </div>
        <div>
          <v-select
            v-model="updateTicketForm.status"
            :items="ticketStatuses"
            label="Status"
            outlined
            :error="Boolean(updateTicketForm.errors.status)"
            :error-messages="updateTicketForm.errors.status"
          >
          </v-select>
        </div>
        <v-row>
          <v-col cols="12" sm="6">
            <v-select
              v-model="updateTicketForm.type"
              :items="ticketTypes"
              label="Type"
              outlined
              :error="Boolean(updateTicketForm.errors.type)"
              :error-messages="updateTicketForm.errors.type"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="updateTicketForm.priority"
              :items="ticketPriorities"
              label="Priority"
              outlined
              :error="Boolean(updateTicketForm.errors.priority)"
              :error-messages="updateTicketForm.errors.priority"
            ></v-select>
          </v-col>
        </v-row>
        <div v-if="canAssignTicket">
          <v-select
            v-model="updateTicketForm.assignee"
            :items="ticketDevs"
            label="Developer"
            outlined
            clearable
            :error="Boolean(updateTicketForm.errors.assignee)"
            :error-messages="updateTicketForm.errors.assignee"
          ></v-select>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="cancelUpdate">Cancel</v-btn>
        <v-btn
          color="primary"
          @click="update"
          :loading="updateTicketForm.processing"
          :disabled="updateTicketForm.processing"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import constants from "@/constants";

export default {
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    types: {
      type: Object,
      required: true,
    },
    priorities: {
      type: Object,
      required: true,
    },
    canAssignTicket: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      updateTicketForm: this.$inertia.form({
        title: this.ticket.title,
        description: this.ticket.description,
        status: this.ticket.status,
        type: this.ticket.type.id,
        priority: this.ticket.priority.id,
        assignee: this.ticket.assignee && this.ticket.assignee.id,
      }),
    };
  },
  computed: {
    ticketStatuses() {
      return Object.values(constants.TicketStatus);
    },
    ticketTypes() {
      return this.types.data.map((tt) => ({ value: tt.id, text: tt.name }));
    },
    ticketPriorities() {
      return this.priorities.data.map((tp) => ({
        value: tp.id,
        text: tp.name,
      }));
    },
    ticketDevs() {
      return this.ticket.project.devs.map((u) => ({
        value: u.id,
        text: u.profile.name,
      }));
    },
  },
  methods: {
    syncFormValues(newVal) {
      this.updateTicketForm.defaults({
        title: newVal.title,
        description: newVal.description,
        status: newVal.status,
        type: newVal.type.id,
        priority: newVal.priority.id,
        assignee: newVal.assignee && newVal.assignee.id,
      });
    },
    cancelUpdate() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.updateTicketForm.reset();
      this.updateTicketForm.clearErrors();
    },
    update() {
      if (!this.canAssignTicket) {
        delete this.updateTicketForm.assignee;
      }
      this.updateTicketForm.put(
        route("tickets.update", { ticket: this.ticket.id }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.updateTicketForm.defaults();
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
