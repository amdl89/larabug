<template>
  <v-row justify="center" align="stretch">
    <v-col cols="12" sm="4">
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
            <h1 class="text-h1">{{ totalProjectCount }}</h1>
            <span>Total Projects</span>
          </div>
        </div>
      </v-sheet>
    </v-col>
    <v-col cols="12" sm="4">
      <v-sheet elevation="3" rounded class="pa-4 fill-height border-warning">
        <div class="text-center fill-height d-flex align-center justify-center">
          <div>
            <v-progress-circular
              :rotate="270"
              :size="90"
              :width="10"
              :value="activeProjectPercent"
              :color="activeProjectColor"
              class="mb-3"
            >
              {{ activeProjectPercent }}%
            </v-progress-circular>
            <h1>
              {{ activeProjectCount }}
              <span
                ><v-chip :color="activeProjectColor" class="white--text"
                  >Active</v-chip
                ></span
              >
            </h1>
          </div>
        </div>
      </v-sheet>
    </v-col>
    <v-col cols="12" sm="4">
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
            <h1 class="text-h1">{{ overdueProjectCount }}</h1>
            <span>Overdue</span>
          </div>
        </div>
      </v-sheet>
    </v-col>
  </v-row>
</template>

<script>
import constants from "@/constants.js";

export default {
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
  },
  computed: {
    activeProjectPercent() {
      return (
        Math.floor((this.activeProjectCount / this.totalProjectCount) * 100) ||
        0
      );
    },
    activeProjectColor() {
      return constants.ProjectStatusColor[constants.ProjectStatus.Active];
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
