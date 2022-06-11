<template>
  <div>
    <h3 class="text-center mb-3">Users By Role</h3>
    <pie-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import PieChart from "@/components/reusable/PieChart.vue";
import constants from "@/constants";

export default {
  props: {
    userRoleChartData: {
      type: Object,
      required: true,
    },
  },
  components: { PieChart },
  data() {
    return {
      chartOptions: {
        legend: {
          position: "bottom",
          labels: {
            fontSize: 11,
          },
        },
      },
    };
  },
  computed: {
    chartLabels() {
      return this.userRoleChartData.chartLabels;
    },
    labelToDataMap() {
      return this.userRoleChartData.labelToDataMap;
    },
    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [
          {
            data: this.chartLabels.map((l) => this.labelToDataMap[l]),
            backgroundColor: this.chartLabels.map(
              (l) => constants.RoleColor[l]
            ),
          },
        ],
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
