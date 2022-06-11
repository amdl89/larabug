<template>
  <v-container class="my-5 px-5">
    <project-stats
      :totalProjectCount="totalProjectCount"
      :activeProjectCount="activeProjectCount"
      :overdueProjectCount="overdueProjectCount"
    />
    <div class="mt-15">
      <project-data-table
        :projects="projects"
        :fetchUrl="route('projects.index')"
        :initialProjectFiltersValues="initialProjectFiltersValues"
        :auxillaryData="auxillaryData"
        :canCreateProjects="canCreateProjects"
      />
    </div>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import ProjectStats from "@/Components/Reusable/ProjectStats.vue";
import ProjectDataTable from "@/Components/ProjectDataTable.vue";

export default {
  components: { ProjectStats, ProjectDataTable },
  layout: MainLayout,
  mixins: [ScrollToTopOnCreate],
  props: {
    totalProjectCount: {
      type: Number,
      required: true,
    },
    activeProjectCount: {
      type: Number,
      required: true,
    },
    overdueProjectCount: {
      type: Number,
      required: true,
    },
    projects: {
      type: Object,
      required: true,
    },
    priorities: {
      type: Object,
      required: true,
    },
    supervisors: {
      type: Object,
      required: true,
    },
    canCreateProjects: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    auxillaryData() {
      return {
        priorities: this.priorities,
        supervisors: this.supervisors,
      };
    },
    initialProjectFiltersValues() {
      const { searchQuery, sortBy, filters, page } = route().params;

      return {
        page: parseInt(page) || null,
        searchQuery: searchQuery || null,
        sortBy: sortBy || null,
        filters: filters || {},
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
