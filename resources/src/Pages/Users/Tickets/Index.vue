<template>
  <v-container class="my-5 px-5">
    <ticket-stats
      :totalTicketCount="totalTicketCount"
      :openOrReopendedTicketCount="openOrReopendedTicketCount"
      :unassignedTicketCount="unassignedTicketCount"
      :closedTicketCount="closedTicketCount"
    />
    <div class="mt-15">
      <ticket-data-table
        :ticketData="tickets"
        :auxillaryData="auxillaryData"
        :initialTicketFilterValues="initialTicketFilterValues"
        :showFilters="showFilters"
        :fetchUrl="route('users.tickets.index', { user: authUser })"
        title="MY TICKETS"
        :canAssignTicket="canAssignTicket"
      />
    </div>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import TicketDataTable from "@/components/TicketDataTable.vue";
import TicketStats from "@/Components/Reusable/TicketStats.vue";

export default {
  components: { TicketDataTable, TicketStats },
  layout: MainLayout,
  mixins: [ScrollToTopOnCreate],
  props: {
    totalTicketCount: {
      type: Number,
      required: true,
    },
    openOrReopendedTicketCount: {
      type: Number,
      required: true,
    },
    unassignedTicketCount: {
      type: Number,
      required: true,
    },
    closedTicketCount: {
      type: Number,
      required: true,
    },
    tickets: {
      type: Object,
      required: true,
    },
    devs: {
      type: Object,
      required: true,
    },
    submitters: {
      type: Object,
      required: true,
    },
    priorities: {
      type: Object,
      required: true,
    },
    types: {
      type: Object,
      required: true,
    },
    projects: {
      type: Object,
      required: true,
    },
    projectsForFilter: {
      type: Object,
      default: null,
    },
    canAssignTicket: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    auxillaryData() {
      return {
        devs: this.devs,
        submitters: this.submitters,
        priorities: this.priorities,
        types: this.types,
        projects: this.projects,
        projectsForFilter: this.projectsForFilter ?? this.projects,
      };
    },
    initialTicketFilterValues() {
      const { searchQuery, sortBy, filters, page } = route().params;

      return {
        pageNo: parseInt(page) || null,
        searchQuery: searchQuery || null,
        sortBy: sortBy || null,
        filters: (filters && {
          ...filters,
          submitters: [this.authUser.id.toString()],
        }) || { submitters: [this.authUser.id.toString()] },
      };
    },
    showFilters() {
      return [
        "devs",
        "priorities",
        "types",
        "statuses",
        "updatedRange",
        "projects",
      ];
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
