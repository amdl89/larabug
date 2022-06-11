<template>
  <div>
    <h3 class="text-center mb-3">Projects By Priority</h3>
    <pie-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import PieChart from "@/components/reusable/PieChart";

export default {
  props: {
    projectPriorityChartData: {
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
        },
      },
    };
  },
  computed: {
    chartLabels() {
      return this.projectPriorityChartData.chartLabels;
    },
    labelToColorMap() {
      return this.projectPriorityChartData.labelToColorMap;
    },
    labelToDataMap() {
      return this.projectPriorityChartData.labelToDataMap;
    },
    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [
          {
            data: this.chartLabels.map((l) => this.labelToDataMap[l]),
            backgroundColor: this.chartLabels.map(
              (l) => this.labelToColorMap[l]
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
