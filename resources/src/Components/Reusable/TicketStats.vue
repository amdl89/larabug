<template>
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
            <h1 class="text-h1">{{ totalTicketCount }}</h1>
            <span>Total Tickets</span>
          </div>
        </div>
      </v-sheet>
    </v-col>
    <v-col cols="12" sm="3">
      <v-sheet elevation="3" rounded class="pa-4 fill-height border-warning">
        <div class="text-center fill-height d-flex align-center justify-center">
          <div>
            <v-progress-circular
              :rotate="270"
              :size="90"
              :width="10"
              :value="openOrReopendedTicketPercent"
              color="warning"
              class="mb-3"
            >
              {{ openOrReopendedTicketPercent }}%
            </v-progress-circular>
            <h1>
              {{ openOrReopendedTicketCount }}
              <span><v-chip color="warning">Open</v-chip></span>
              <small class="h6">/</small>
              <span><v-chip color="error">Reopened</v-chip></span>
            </h1>
          </div>
        </div>
      </v-sheet>
    </v-col>
    <v-col cols="12" sm="3">
      <v-sheet elevation="3" rounded class="pa-4 fill-height border-primary">
        <div class="text-center fill-height d-flex align-center justify-center">
          <div>
            <v-progress-circular
              :rotate="270"
              :size="90"
              :width="10"
              :value="unassignedTicketPercent"
              color="tertiary"
              class="mb-3"
            >
              {{ unassignedTicketPercent }}%
            </v-progress-circular>
            <h1>
              {{ unassignedTicketCount }}
              <span
                ><v-chip color="tertiary" class="white--text"
                  >Not assigned</v-chip
                ></span
              >
            </h1>
          </div>
        </div>
      </v-sheet>
    </v-col>
    <v-col cols="12" sm="3">
      <v-sheet elevation="3" rounded class="pa-4 fill-height border-success">
        <div class="text-center fill-height d-flex align-center justify-center">
          <div>
            <v-progress-circular
              :rotate="270"
              :size="90"
              :width="10"
              :value="closedTicketPercent"
              color="success"
              class="mb-3"
            >
              {{ closedTicketPercent }}%
            </v-progress-circular>
            <h1>
              {{ closedTicketCount }}
              <span><v-chip color="success">Closed</v-chip></span>
            </h1>
          </div>
        </div>
      </v-sheet>
    </v-col>
  </v-row>
</template>

<script>
export default {
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
  },
  computed: {
    openOrReopendedTicketPercent() {
      return (
        Math.floor(
          (this.openOrReopendedTicketCount / this.totalTicketCount) * 100
        ) || 0
      );
    },
    unassignedTicketPercent() {
      return (
        Math.floor(
          (this.unassignedTicketCount / this.totalTicketCount) * 100
        ) || 0
      );
    },
    closedTicketPercent() {
      return (
        Math.floor((this.closedTicketCount / this.totalTicketCount) * 100) || 0
      );
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
