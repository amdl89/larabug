<template>
  <div>
    <h3 class="text-center mb-3">Tickets By Type</h3>
    <donut-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import DonutChart from "@/components/reusable/DonutChart";

export default {
  props: {
    ticketTypeChartData: {
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
      return this.ticketTypeChartData.chartLabels;
    },
    labelToColorMap() {
      return this.ticketTypeChartData.labelToColorMap;
    },
    labelToDataMap() {
      return this.ticketTypeChartData.labelToDataMap;
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
