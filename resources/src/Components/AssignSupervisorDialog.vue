<template>
  <v-dialog v-model="dialogShown" max-width="600" max-height="500">
    <template v-slot:activator="{ on, attrs }">
      <v-btn color="primary" depressed rounded v-bind="attrs" v-on="on"
        >Assign Supervisor</v-btn
      >
    </template>
    <v-card class="py-6 px-2 overflow-y-auto">
      <div class="text-center">
        Assign a supervisor for project
        <br />
        <span class="text-h5"> "{{ project.title }}" </span>
      </div>
      <div class="my-5">
        <div
          v-for="supervisor in possibleSupervisors"
          :key="supervisor.id"
          class="
            supervisor-list-item
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
                  supervisor.profile.avatar.thumbnail ||
                  supervisor.profile.avatar.original ||
                  './assets/anonymousUser.jpg'
                "
              />
            </v-avatar>
            <span class="d-inline-block ml-3">
              {{ supervisor.profile.name }}
            </span>
          </div>
          <div>
            <v-btn
              color="primary"
              :loading="
                supervisorBeingAssigned &&
                supervisorBeingAssigned == supervisor.id
              "
              :disabled="
                supervisorBeingAssigned &&
                supervisorBeingAssigned == supervisor.id
              "
              @click="assignSupervisor(supervisor)"
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
    project: {
      type: Object,
      required: true,
    },
    supervisors: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      supervisorBeingAssigned: null,
    };
  },
  computed: {
    possibleSupervisors() {
      return this.supervisors?.data ?? [];
    },
  },
  methods: {
    assignSupervisor(supervisor) {
      this.$inertia.post(
        route("projects.supervisors.store", { project: this.project }),
        { supervisor: supervisor.id },
        {
          preserveState: true,
          preserveScroll: true,
          onStart: () => {
            this.supervisorBeingAssigned = supervisor.id;
          },
          onSuccess: () => {
            this.supervisorBeingAssigned = null;
            this.dialogShown = false;
          },
        }
      );
    },
  },
};
</script>

<style lang="scss">
.supervisor-list-item {
  border-radius: 5px;
}
.supervisor-list-item:hover {
  background-color: #f7edf0;
}
</style>
