<template>
  <div>
    <h3 class="text-center mb-3">Tickets By Status</h3>
    <pie-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import PieChart from "@/components/reusable/PieChart.vue";
import constants from "@/constants";

export default {
  props: {
    ticketStatusChartData: {
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
      return this.ticketStatusChartData.chartLabels;
    },
    labelToDataMap() {
      return this.ticketStatusChartData.labelToDataMap;
    },
    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [
          {
            data: this.chartLabels.map((l) => this.labelToDataMap[l]),
            backgroundColor: this.chartLabels.map(
              (l) => constants.TicketStatusColor[l]
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
