<template>
  <v-dialog
    max-width="600"
    v-model="filtersDialogShown"
    @keydown.esc="cancelFilters"
    @click:outside="cancelFilters"
  >
    <template v-slot:activator="{ on, attrs }">
      <v-badge
        :content="filtersCount.toString()"
        overlap
        offset-x="15"
        offset-y="15"
      >
        <v-btn icon v-bind="attrs" v-on="on">
          <v-icon>mdi-filter</v-icon>
        </v-btn>
      </v-badge>
    </template>
    <v-card>
      <v-card-title>
        <span class="text-h5">Ticket Filters</span>
      </v-card-title>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12" sm="6">
              <v-select
                v-model="filters.devs"
                :items="allDevs"
                :menu-props="{ maxHeight: '400' }"
                label="Assigned To"
                multiple
                clearable
                :disabled="disable('devs')"
              >
                <template v-slot:selection="{ item, index }">
                  <v-chip v-if="index === 0">
                    <span>{{ item.text }}</span>
                  </v-chip>
                  <span v-if="index === 1" class="grey--text text-caption">
                    (+{{ filters.devs.length - 1 }} others)
                  </span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="filters.submitters"
                :items="allSubmitters || []"
                :menu-props="{ maxHeight: '400' }"
                label="Submitter"
                multiple
                clearable
                :disabled="disable('submitters')"
              >
                <template v-slot:selection="{ item, index }">
                  <v-chip v-if="index === 0">
                    <span>{{ item.text }}</span>
                  </v-chip>
                  <span v-if="index === 1" class="grey--text text-caption">
                    (+{{ filters.submitters.length - 1 }} others)
                  </span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="filters.statuses"
                :items="allStatuses || []"
                :menu-props="{ maxHeight: '400' }"
                label="Status"
                multiple
                clearable
                :disabled="disable('statuses')"
              >
                <template v-slot:selection="{ item, index }">
                  <v-chip v-if="index === 0">
                    <span>{{ item }}</span>
                  </v-chip>
                  <span v-if="index === 1" class="grey--text text-caption">
                    (+{{ filters.statuses.length - 1 }} others)
                  </span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="filters.priorities"
                :items="allPriorities || []"
                :menu-props="{ maxHeight: '400' }"
                label="Priority"
                multiple
                clearable
                :disabled="disable('priorities')"
              >
                <template v-slot:selection="{ item, index }">
                  <v-chip v-if="index === 0">
                    <span>{{ item.text }}</span>
                  </v-chip>
                  <span v-if="index === 1" class="grey--text text-caption">
                    (+{{ filters.priorities.length - 1 }} others)
                  </span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6">
              <v-select
                v-model="filters.types"
                :items="allTypes || []"
                :menu-props="{ maxHeight: '400' }"
                label="Type"
                multiple
                clearable
                :disabled="disable('types')"
              >
                <template v-slot:selection="{ item, index }">
                  <v-chip v-if="index === 0">
                    <span>{{ item.text }}</span>
                  </v-chip>
                  <span v-if="index === 1" class="grey--text text-caption">
                    (+{{ filters.types.length - 1 }} others)
                  </span>
                </template>
              </v-select>
            </v-col>
            <v-col cols="12" sm="6" align-self="end">
              <v-select
                v-model="filters.updatedRange"
                :items="allUpdatedRanges || []"
                :menu-props="{ maxHeight: '400' }"
                label="Updated"
                :disabled="disable('updatedRange')"
              ></v-select>
            </v-col>
          </v-row>
          <v-select
            v-model="filters.projects"
            :items="allProjects || []"
            :menu-props="{ maxHeight: '400' }"
            label="Project"
            multiple
            clearable
            :disabled="disable('projects')"
          >
            <template v-slot:selection="{ item, index }">
              <v-chip v-if="index === 0">
                <span>{{ item.text }}</span>
              </v-chip>
              <span v-if="index === 1" class="grey--text text-caption">
                (+{{ filters.projects.length - 1 }} others)
              </span>
            </template></v-select
          >
        </v-container>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="secondary darken-1" text @click="cancelFilters">
          Cancel
        </v-btn>
        <v-btn
          color="primary darken-1"
          @click="applyFilters"
          :loading="loadingIndicator"
          :disabled="loadingIndicator"
          >Apply</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import constants from "@/constants";

export default {
  props: {
    ticketFilters: {
      type: Object,
      required: true,
    },
    dataForFilters: {
      type: Object,
      required: true,
    },
    showFilters: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      filtersDialogShown: false,
      filters: _.cloneDeep(this.ticketFilters),
      previousFilters: _.cloneDeep(this.ticketFilters),
      loadingIndicator: false,
    };
  },
  computed: {
    filtersCount() {
      const {
        devs,
        submitters,
        statuses,
        priorities,
        types,
        updatedRange,
        projects,
      } = this.filters;

      return _.sum([
        _.size(devs) && !this.disable("devs"),
        _.size(submitters) && !this.disable("submitters"),
        _.size(statuses) && !this.disable("statuses"),
        _.size(priorities) && !this.disable("priorities"),
        _.size(types) && !this.disable("types"),
        updatedRange !== constants.DateRange.All &&
          !this.disable("updatedRange"),
        _.size(projects) && !this.disable("projects"),
      ]);
    },
    allDevs() {
      return this.dataForFilters.devs?.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    allSubmitters() {
      return this.dataForFilters.submitters?.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    allStatuses() {
      return Object.values(constants.TicketStatus);
    },
    allPriorities() {
      return this.dataForFilters.priorities?.data.map((tp) => ({
        value: tp.id.toString(),
        text: tp.name,
      }));
    },
    allTypes() {
      return this.dataForFilters.types?.data.map((tt) => ({
        value: tt.id.toString(),
        text: tt.name,
      }));
    },
    allUpdatedRanges() {
      return Object.values(constants.DateRange);
    },
    allProjects() {
      return this.dataForFilters.projectsForFilter?.data.map((p) => ({
        value: p.id.toString(),
        text: p.title,
      }));
    },
  },
  methods: {
    disable(filterKey) {
      return this.showFilters && this.showFilters.indexOf(filterKey) < 0;
    },
    resetFiltersToPrevious() {
      this.filters = _.cloneDeep(this.previousFilters);
    },
    syncFilters() {
      this.previousFilters = _.cloneDeep(this.filters);
    },
    cancelFilters() {
      this.resetFiltersToPrevious();
      this.filtersDialogShown = false;
    },
    applyFilters() {
      this.syncFilters();
      this.loadingIndicator = true;
      this.$emit("filtersApplied", { filters: _.cloneDeep(this.filters) });
    },
    incrementFiltersCount() {
      this.noOfFilters++;
    },
    decrementFiltersCount() {
      this.noOfFilters--;
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
