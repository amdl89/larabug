<template>
  <v-container class="my-5 px-5">
    <v-row justify="center" align="stretch">
      <v-col cols="12" sm="3">
        <v-sheet elevation="3" rounded color="info" class="pa-6 fill-height">
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
              <h2>
                {{ activeProjectsCount }}
              </h2>
              <span>Active Projects</span>
            </div>
          </div>
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="3">
        <v-sheet elevation="3" rounded color="warning" class="pa-6 fill-height">
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
              <h2>
                {{ unassignedTicketsCount }}
              </h2>
              <span>Unassigned Tickets</span>
            </div>
          </div>
        </v-sheet>
      </v-col>
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
              <h2>
                {{ ticketsThisWeekCount }}
              </h2>
              <span>New Tickets This Week</span>
            </div>
          </div>
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
              <h2>
                {{ ticketsClosedThisWeekCount }}
              </h2>
              <span>Tickets Closed This Week</span>
            </div>
          </div>
        </v-sheet>
      </v-col>
    </v-row>
    <v-row align="center">
      <v-col cols="12" md="3">
        <v-row dense>
          <v-col cols="12" sm="4" md="12">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <div class="d-flex align-end">
                <div class="error pa-2 rounded">
                  <v-icon x-large color="white">mdi-ladybug</v-icon>
                </div>
                <div class="ml-2">
                  <span class="text-h4">
                    {{ ticketsCount }}
                  </span>
                  &nbsp;
                  <span>Tickets</span>
                </div>
              </div>
            </v-sheet>
          </v-col>
          <v-col cols="12" sm="4" md="12">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <div class="d-flex align-end">
                <div class="tertiary pa-2 rounded">
                  <v-icon x-large color="white">mdi-account-plus</v-icon>
                </div>
                <div class="ml-2">
                  <span class="text-h4">
                    {{ usersRegisteredThisMonthCount }}
                  </span>
                  &nbsp;
                  <span>New Users</span>
                </div>
              </div>
            </v-sheet>
          </v-col>
          <v-col cols="12" sm="4" md="12">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <div class="d-flex align-end">
                <div class="secondary pa-2 rounded">
                  <v-icon x-large color="white">mdi-account-group</v-icon>
                </div>
                <div class="ml-2">
                  <span class="text-h4">
                    {{ usersCount }}
                  </span>
                  &nbsp;
                  <span>Total Users</span>
                </div>
              </div>
            </v-sheet>
          </v-col>
        </v-row>
      </v-col>
      <v-col cols="12" md="9">
        <v-row>
          <v-col cols="12" sm="4">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <ticket-priority-chart
                :ticketPriorityChartData="ticketPriorityChartData"
              />
            </v-sheet>
          </v-col>
          <v-col cols="12" sm="4">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <ticket-status-chart
                :ticketStatusChartData="ticketStatusChartData"
              />
            </v-sheet>
          </v-col>
          <v-col cols="12" sm="4">
            <v-sheet elevation="6" rounded="lg" class="pa-6">
              <ticket-type-chart :ticketTypeChartData="ticketTypeChartData" />
            </v-sheet>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="4" md="3">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <project-priority-chart
            :projectPriorityChartData="projectPriorityChartData"
          />
        </v-sheet>
      </v-col>
      <v-col sm="8" md="6">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <div class="project-user-chart-container">
            <project-user-chart :projectUserChartData="projectUserChartData" />
          </div>
        </v-sheet>
      </v-col>
      <v-col sm="6" md="3">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <div class="project-ticket-chart-container">
            <project-ticket-chart
              :projectTicketChartData="projectTicketChartData"
            />
          </div>
        </v-sheet>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import TicketPriorityChart from "@/Components/Reusable/TicketPriorityChart";
import TicketStatusChart from "@/Components/Reusable/TicketStatusChart";
import TicketTypeChart from "@/Components/Reusable/TicketTypeChart";
import ProjectPriorityChart from "@/Components/Reusable/ProjectPriorityChart";
import ProjectUserChart from "@/Components/Reusable/ProjectUserChart";
import ProjectTicketChart from "../Components/Reusable/ProjectTicketChart.vue";

export default {
  layout: MainLayout,
  mixins: [ScrollToTopOnCreate],
  components: {
    TicketPriorityChart,
    TicketStatusChart,
    TicketTypeChart,
    ProjectPriorityChart,
    ProjectUserChart,
    ProjectTicketChart,
  },
  props: {
    activeProjectsCount: {
      type: Number,
      required: true,
    },
    unassignedTicketsCount: {
      type: Number,
      required: true,
    },
    ticketsThisWeekCount: {
      type: Number,
      required: true,
    },
    ticketsClosedThisWeekCount: {
      type: Number,
      required: true,
    },
    ticketsCount: {
      type: Number,
      required: true,
    },
    usersRegisteredThisMonthCount: {
      type: Number,
      required: true,
    },
    usersCount: {
      type: Number,
      required: true,
    },
    ticketPriorityChartData: {
      type: Object,
      required: true,
    },
    ticketStatusChartData: {
      type: Object,
      required: true,
    },
    ticketTypeChartData: {
      type: Object,
      required: true,
    },
    projectPriorityChartData: {
      type: Object,
      required: true,
    },
    projectUserChartData: {
      type: Object,
      required: true,
    },
    projectTicketChartData: {
      type: Object,
      required: true,
    },
  },
  created() {
    // console.log(this.projectPriorityChartData);
  },
};
</script>

<style lang="scss" scoped>
.project-user-chart-container,
.project-ticket-chart-container {
  max-height: 260px;
  overflow-y: scroll;
}
.ticket-priority-by-project-chart-container {
  max-height: 500px;
  overflow-y: scroll;
}
</style>
