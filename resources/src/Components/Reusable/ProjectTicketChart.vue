<template>
  <div>
    <h4 class="text-center mb-3">Project Tickets Count</h4>
    <horizontal-bar-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import HorizontalBarChart from "@/components/reusable/HorizontalBarChart.vue";

export default {
  props: {
    projectTicketChartData: {
      type: Object,
      required: true,
    },
  },
  components: { HorizontalBarChart },
  data() {
    return {
      chartOptions: {
        legend: {
          display: false,
        },
        scales: {
          yAxes: [
            {
              ticks: {
                display: false,
              },
            },
          ],
          xAxes: [
            {
              position: "top",
              ticks: {
                beginAtZero: true,
              },
            },
          ],
        },
      },
    };
  },
  computed: {
    chartLabels() {
      return this.projectTicketChartData.chartLabels;
    },
    labelToDataMap() {
      return this.projectTicketChartData.labelToDataMap;
    },
    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [
          {
            label: "Tickets",
            data: this.chartLabels.map((l) => this.labelToDataMap[l]),
            backgroundColor: "#04AEC4",
          },
        ],
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
