<template>
  <div>
    <h3 class="text-center mb-3">Tickets By Priority</h3>
    <donut-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import DonutChart from "@/components/reusable/DonutChart";

export default {
  props: {
    ticketPriorityChartData: {
      type: Object,
      required: true,
    },
  },
  components: { DonutChart },
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
      return this.ticketPriorityChartData.chartLabels;
    },
    labelToColorMap() {
      return this.ticketPriorityChartData.labelToColorMap;
    },
    labelToDataMap() {
      return this.ticketPriorityChartData.labelToDataMap;
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
