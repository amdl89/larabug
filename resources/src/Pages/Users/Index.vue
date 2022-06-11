<template>
  <v-container class="my-5 px-5">
    <v-row justify="center" align="stretch">
      <v-col cols="12" sm="3">
        <v-sheet elevation="3" rounded color="accent" class="pa-6 fill-height">
          <div
            class="
              text-center
              white--text
              fill-height
              d-flex
              align-center
              justify-center
            "
          >
            <div>
              <h1 class="text-h1">{{ totalUsersCount }}</h1>
              <span>Total Users</span>
            </div>
          </div>
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="3">
        <v-sheet elevation="3" rounded class="pa-4 fill-height">
          <user-role-chart :userRoleChartData="userRoleChartData" />
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="3">
        <v-sheet elevation="3" rounded color="success" class="pa-6 fill-height">
          <div
            class="
              text-center
              white--text
              fill-height
              d-flex
              align-center
              justify-center
            "
          >
            <div>
              <h1 class="text-h1">{{ usersAddedThisMonthCount }}</h1>
              <span>New Users This Month</span>
            </div>
          </div>
        </v-sheet>
      </v-col>
    </v-row>
    <div class="mt-15">
      <user-data-table
        :users="users"
        :fetchUrl="route('users.index')"
        :auxillaryData="auxillaryData"
        :initialUserFiltersValues="initialUserFiltersValues"
      />
    </div>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import UserRoleChart from "@/Components/UserRoleChart.vue";
import UserDataTable from "@/Components/UserDataTable.vue";

export default {
  components: { UserRoleChart, UserDataTable },
  layout: MainLayout,
  mixins: [ScrollToTopOnCreate],
  props: {
    totalUsersCount: {
      type: Number,
      required: true,
    },
    userRoleChartData: {
      type: Object,
      required: true,
    },
    usersAddedThisMonthCount: {
      type: Number,
      required: true,
    },
    users: {
      type: Object,
      required: true,
    },
    roles: {
      type: Object,
      required: true,
    },
    projects: {
      type: Object,
      required: true,
    },
  },
  computed: {
    auxillaryData() {
      return {
        roles: this.roles,
        projects: this.projects,
      };
    },
    initialUserFiltersValues() {
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
