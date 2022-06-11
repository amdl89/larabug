<template>
  <v-dialog v-model="dialogShown" max-width="600" max-height="500">
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="primary" depressed rounded v-bind="attrs" v-on="on"
        >Assign Dev</v-btn
      >
    </template>
    <v-card class="py-6 px-2 overflow-y-auto">
      <div class="text-center">
        Assign a developer for ticket
        <br />
        <span class="text-h5"> "{{ ticket.title }}" </span>
      </div>
      <div class="my-5">
        <div
          v-for="dev in avaliableDevsForTicket"
          :key="dev.id"
          class="
            dev-list-item
            px-6
            py-5
            d-flex
            justify-space-between
            align-center
          "
        >
          <div>
            <v-avatar size="36px" class="ml-3">
              <img
                :src="
                  dev.profile.avatar.thumbnail ||
                  dev.profile.avatar.original ||
                  './assets/anonymousUser.jpg'
                "
              />
            </v-avatar>
            <span class="d-inline-block ml-3">
              {{ dev.profile.name }}
            </span>
          </div>
          <div>
            <v-btn
              color="primary"
              :loading="devBeingAssigned && devBeingAssigned == dev.id"
              :disabled="devBeingAssigned && devBeingAssigned == dev.id"
              @click="assignDev(dev)"
            >
              Assign
            </v-btn>
          </div>
        </div>
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
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
  },
  data() {
    return {
      dialogShown: false,
      devBeingAssigned: null,
    };
  },
  computed: {
    avaliableDevsForTicket() {
      return this.ticket.project.devs;
    },
  },
  methods: {
    assignDev(dev) {
      this.$inertia.post(
        route("tickets.assignees.store", { ticket: this.ticket.id }),
        { assignee: dev.id },
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.devBeingAssigned = dev.id;
          },
          onSuccess: () => {
            this.devBeingAssigned = null;
            this.dialogShown = false;
          },
        }
      );
    },
  },
};
</script>

<style lang="scss">
.dev-list-item {
  border-radius: 5px;
}
.dev-list-item:hover {
  background-color: #f7edf0;
}
</style>
