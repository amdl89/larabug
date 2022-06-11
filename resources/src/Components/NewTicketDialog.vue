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
        New Ticket
      </v-btn>
    </template>
    <v-card class="pb-6 overflow-y-auto">
      <div class="py-2 text-center secondary white--text">
        Create a new Ticket
        <br />
      </div>
      <div class="mt-8 px-8">
        <div>
          <v-text-field
            v-model="newTicketForm.title"
            label="Title"
            outlined
            clearable
            :error="Boolean(newTicketForm.errors.title)"
            :error-messages="newTicketForm.errors.title"
          ></v-text-field>
        </div>
        <div>
          <v-textarea
            v-model="newTicketForm.description"
            outlined
            label="Description"
            :error="Boolean(newTicketForm.errors.description)"
            :error-messages="newTicketForm.errors.description"
          ></v-textarea>
        </div>
        <div>
          <v-select
            v-model="newTicketForm.project"
            :items="allProjects"
            label="Project"
            outlined
            :error="Boolean(newTicketForm.errors.project)"
            :error-messages="newTicketForm.errors.project"
            :disabled="
              allProjects.length == 1 &&
              this.initialValues.project == allProjects[0].value
            "
          >
          </v-select>
        </div>
        <v-row>
          <v-col cols="12" sm="6">
            <v-select
              v-model="newTicketForm.type"
              :items="allTicketTypes"
              label="Type"
              outlined
              :error="Boolean(newTicketForm.errors.type)"
              :error-messages="newTicketForm.errors.type"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6">
            <v-select
              v-model="newTicketForm.priority"
              :items="allTicketPriorities"
              label="Priority"
              outlined
              :error="Boolean(newTicketForm.errors.priority)"
              :error-messages="newTicketForm.errors.priority"
            ></v-select>
          </v-col>
        </v-row>
        <div v-if="canAssignTicket">
          <div
            v-if="currFetchState == fetchState.error"
            class="text-center error--text"
          >
            Error Loading Devs
            <v-icon @click="loadDevs" class="cur-point">mdi-restart</v-icon>
          </div>
          <v-select
            v-else
            :loading="currFetchState == fetchState.loading"
            v-model="newTicketForm.assignee"
            :items="devsForProject"
            label="Developer"
            :disabled="
              !newTicketForm.project || currFetchState == fetchState.loading
            "
            clearable
            outlined
            :error="Boolean(newTicketForm.errors.assignee)"
            :error-messages="newTicketForm.errors.assignee"
          ></v-select>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="closeDialog"> Cancel </v-btn>
        <v-btn
          color="primary"
          @click="createNewTicket"
          :loading="newTicketForm.processing"
          :disabled="newTicketForm.processing"
        >
          Save
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import fetchStates from "@/Mixins/fetchStatesMixin.js";

export default {
  mixins: [fetchStates],
  props: {
    projects: {
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
    initialValues: {
      type: Object,
      default: () => ({}),
    },
    canAssignTicket: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      newTicketForm: this.$inertia.form({
        title: "",
        description: "",
        project: null,
        type: null,
        priority: null,
        assignee: null,
        ...this.initialValues,
      }),
      devsForProject: [],
    };
  },
  async created() {
    if (this.canAssignTicket) {
      await this.loadDevs();
    }
  },
  computed: {
    allProjects() {
      return this.projects.data.map((p) => ({
        value: p.id.toString(),
        text: p.title,
      }));
    },
    allTicketTypes() {
      return this.types.data.map((tt) => ({
        value: tt.id.toString(),
        text: tt.name,
      }));
    },
    allTicketPriorities() {
      return this.priorities.data.map((tp) => ({
        value: tp.id.toString(),
        text: tp.name,
      }));
    },
  },
  methods: {
    closeDialog() {
      this.resetForm();
      this.dialogShown = false;
    },
    resetForm() {
      this.newTicketForm.reset();
      this.newTicketForm.clearErrors();
    },
    createNewTicket() {
      if (!this.canAssignTicket) {
        delete this.newTicketForm.assignee;
      }
      this.newTicketForm.post(route("tickets.store"), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
          this.resetForm();
          this.dialogShown = false;
        },
      });
    },
    async loadDevs() {
      if (!this.newTicketForm.project) {
        return;
      }
      try {
        this.newTicketForm.assignee = null;
        this.setCurrFetchState(this.fetchState.loading);

        const res = await axios.get(
          route("projects.devs.index", { project: this.newTicketForm.project })
        );

        this.devsForProject = res.data.data.map((u) => ({
          value: u.id,
          text: u.profile.name,
        }));

        this.setCurrFetchState(this.fetchState.loaded);
      } catch (error) {
        this.setCurrFetchState(this.fetchState.error);
      }
    },
  },
  watch: {
    "newTicketForm.project": async function () {
      if (this.canAssignTicket) {
        await this.loadDevs();
      }
    },
  },
};
</script>

<style lang="scss">
</style>
