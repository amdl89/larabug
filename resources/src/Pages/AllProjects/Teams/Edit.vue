<template>
  <v-container class="my-5 px-5">
    <v-row>
      <v-col cols="12" sm="6">
        <v-select
          v-model="project"
          :items="allProjects"
          :menu-props="{ maxHeight: '400' }"
          label="Project"
          background-color="#fff"
          outlined
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-select
          v-model="supervisorForm.supervisor"
          :items="allSupervisors"
          :menu-props="{ maxHeight: '400' }"
          label="Supervisor"
          background-color="#fff"
          outlined
          :disabled="!project || supervisorForm.processing"
          :loading="supervisorForm.processing"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-row>
          <v-col cols="12">
            <div class="d-flex justify-space-between align-center">
              <h2>DEVS</h2>
              <div>
                <v-btn
                  color="secondary"
                  :disabled="!project || devsForm.processing"
                  @click="resetDevs"
                >
                  Reset
                  <v-icon right>mdi-restart</v-icon>
                </v-btn>
                <v-btn
                  color="primary"
                  :disabled="!project || devsForm.processing"
                  :loading="devsForm.processing"
                  @click="updateProjectDevs"
                >
                  Update
                  <v-icon right>mdi-pencil</v-icon>
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" sm="6">
            <move-container
              :items="devsLeft"
              containerTitle="Other devs"
              @allMoved="moveAllDevsRight"
              @itemMoved="moveOneDevRight"
            >
              <template slot="item" slot-scope="{ item }">
                {{ item.text }}
                <v-icon color="secondary">mdi-chevron-right</v-icon>
              </template>
            </move-container>
          </v-col>
          <v-col cols="12" sm="6">
            <move-container
              :items="devsRight"
              containerTitle="Project devs"
              @allMoved="moveAllDevsLeft"
              @itemMoved="moveOneDevLeft"
            >
              <template slot="moveAll:activator" slot-scope="{ bind, on }">
                <v-btn
                  block
                  color="secondary"
                  class="white--text rounded-0"
                  depressed
                  v-bind="bind"
                  v-on="on"
                >
                  <v-icon left>mdi-chevron-double-left</v-icon>
                  Move All
                </v-btn>
              </template>
              <template slot="item" slot-scope="{ item }">
                <v-icon color="secondary">mdi-chevron-left</v-icon>
                {{ item.text }}
              </template>
            </move-container>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" sm="6">
        <v-row>
          <v-col cols="12">
            <div class="d-flex justify-space-between align-center">
              <h2>TESTERS</h2>
              <div>
                <v-btn
                  color="secondary"
                  :disabled="!project || testersForm.processing"
                  @click="resetTesters"
                >
                  Reset
                  <v-icon right>mdi-restart</v-icon>
                </v-btn>
                <v-btn
                  color="primary"
                  :disabled="!project || testersForm.processing"
                  :loading="testersForm.processing"
                  @click="updateProjectTesters"
                >
                  Update
                  <v-icon right>mdi-pencil</v-icon>
                </v-btn>
              </div>
            </div>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" sm="6">
            <move-container
              :items="testersLeft"
              containerTitle="Other Testers"
              @allMoved="moveAllTestersRight"
              @itemMoved="moveOneTesterRight"
            >
              <template slot="item" slot-scope="{ item }">
                {{ item.text }}
                <v-icon color="secondary">mdi-chevron-right</v-icon>
              </template>
            </move-container>
          </v-col>
          <v-col cols="12" sm="6">
            <move-container
              :items="testersRight"
              containerTitle="Project Testers"
              @allMoved="moveAllTestersLeft"
              @itemMoved="moveOneTesterLeft"
            >
              <template slot="moveAll:activator" slot-scope="{ bind, on }">
                <v-btn
                  block
                  color="secondary"
                  class="white--text rounded-0"
                  depressed
                  v-bind="bind"
                  v-on="on"
                >
                  <v-icon left>mdi-chevron-double-left</v-icon>
                  Move All
                </v-btn>
              </template>
              <template slot="item" slot-scope="{ item }">
                <v-icon color="secondary">mdi-chevron-left</v-icon>
                {{ item.text }}
              </template>
            </move-container>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import MoveContainer from "@/Components/Reusable/MoveContainer.vue";

export default {
  components: { MoveContainer },
  mixins: [ScrollToTopOnCreate],
  layout: MainLayout,
  props: {
    projects: {
      type: Object,
      required: true,
    },
    supervisors: {
      type: Object,
      required: true,
    },
    devs: {
      type: Object,
      required: true,
    },
    testers: {
      type: Object,
      required: true,
    },
  },
  data() {
    const { initialProject } = route().params;

    return {
      project: initialProject ?? null,
      supervisorForm: this.$inertia.form({
        supervisor: null,
      }),
      devsForm: this.$inertia.form({
        devs: [],
      }),
      testersForm: this.$inertia.form({
        testers: [],
      }),
    };
  },
  computed: {
    allProjects() {
      return this.projects.data.map((p) => ({
        value: p.id.toString(),
        text: p.title,
      }));
    },
    allSupervisors() {
      return this.supervisors.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    currProject() {
      return this.projects.data.find((p) => p.id == this.project);
    },
    currProjectSupervisor() {
      return this.currProject?.supervisor?.id?.toString();
    },
    currProjectDevs() {
      return this.currProject
        ? this.currProject.devs.map((u) => u.id.toString())
        : [];
    },
    currProjectTesters() {
      return this.currProject
        ? this.currProject.testers.map((u) => u.id.toString())
        : [];
    },
    devsLeft() {
      if (!this.currProject) {
        return [];
      }
      const devsSet = new Set(this.devsForm.devs);
      return this.devs.data
        .filter((u) => !devsSet.has(u.id.toString()))
        .map((u) => ({
          value: u.id.toString(),
          text: u.profile.name,
        }));
    },
    devsRight() {
      if (!this.currProject) {
        return [];
      }
      const devsSet = new Set(this.devsForm.devs);
      return this.devs.data
        .filter((u) => devsSet.has(u.id.toString()))
        .map((u) => ({
          value: u.id.toString(),
          text: u.profile.name,
        }));
    },
    testersLeft() {
      if (!this.currProject) {
        return [];
      }
      const testersSet = new Set(this.testersForm.testers);
      return this.testers.data
        .filter((u) => !testersSet.has(u.id.toString()))
        .map((u) => ({
          value: u.id.toString(),
          text: u.profile.name,
        }));
    },
    testersRight() {
      if (!this.currProject) {
        return [];
      }
      const testersSet = new Set(this.testersForm.testers);
      return this.testers.data
        .filter((u) => testersSet.has(u.id.toString()))
        .map((u) => ({
          value: u.id.toString(),
          text: u.profile.name,
        }));
    },
  },
  methods: {
    moveAllDevsRight({ items }) {
      this.devsForm.devs = [
        ...this.devsForm.devs,
        ...items.map((i) => i.value),
      ];
    },
    moveAllDevsLeft() {
      this.devsForm.devs = [];
    },
    moveAllTestersRight({ items }) {
      this.testersForm.testers = [
        ...this.testersForm.testers,
        ...items.map((i) => i.value),
      ];
    },
    moveAllTestersLeft() {
      this.testersForm.testers = [];
    },
    moveOneDevRight({ item }) {
      this.devsForm.devs = [...this.devsForm.devs, item.value];
    },
    moveOneDevLeft({ item }) {
      this.devsForm.devs = this.devsForm.devs.filter((i) => i !== item.value);
    },
    moveOneTesterRight({ item }) {
      this.testersForm.testers = [...this.testersForm.testers, item.value];
    },
    moveOneTesterLeft({ item }) {
      this.testersForm.testers = this.testersForm.testers.filter(
        (i) => i !== item.value
      );
    },
    resetTesters() {
      this.testersForm.testers = this.currProjectTesters;
    },
    resetDevs() {
      this.devsForm.devs = this.currProjectDevs;
    },
    updateProjectTesters() {
      this.testersForm.put(
        route("projects.testers.update", { project: this.project }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.testersForm.defaults();
          },
          onError: () => {
            console.log(this.supervisorForm.errors);
          },
        }
      );
    },
    updateProjectDevs() {
      this.devsForm.put(
        route("projects.devs.update", { project: this.project }),
        {
          preserveState: true,
          preserveScroll: true,
          onSuccess: () => {
            this.devsForm.defaults();
          },
        }
      );
    },
  },
  watch: {
    project: {
      immediate: true,
      handler() {
        this.supervisorForm.supervisor = this.currProjectSupervisor;
        this.devsForm.devs = this.currProjectDevs;
        this.testersForm.testers = this.currProjectTesters;
      },
    },
    "supervisorForm.supervisor": {
      handler(newVal) {
        if (newVal == this.currProjectSupervisor) {
          return;
        }
        this.supervisorForm.post(
          route("projects.supervisors.store", { project: this.project }),
          {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
              this.supervisorForm.defaults();
            },
          }
        );
      },
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
