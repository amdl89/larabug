<template>
  <div>
    <h4 class="text-center mb-3">Project Members Count</h4>
    <horizontal-bar-chart :data="chartData" :options="chartOptions" />
  </div>
</template>

<script>
import HorizontalBarChart from "@/components/reusable/HorizontalBarChart.vue";

export default {
  props: {
    projectUserChartData: {
      type: Object,
      required: true,
    },
  },
  components: { HorizontalBarChart },
  data() {
    return {
      chartOptions: {
        legend: {
          position: "top",
        },
        scales: {
          yAxes: [
            {
              ticks: {
                display: false,
              },
              stacked: true,
            },
          ],
          xAxes: [
            {
              position: "top",
              stacked: true,
            },
          ],
        },
      },
    };
  },
  computed: {
    chartLabels() {
      return this.projectUserChartData.chartLabels;
    },
    labelToTestersCountMap() {
      return this.projectUserChartData.labelToTestersCountMap;
    },
    labelToDevelopersCountMap() {
      return this.projectUserChartData.labelToDevelopersCountMap;
    },
    chartData() {
      return {
        labels: this.chartLabels,
        datasets: [
          {
            label: "Testers",
            data: this.chartLabels.map((l) => this.labelToTestersCountMap[l]),
            backgroundColor: "#a73060",
          },
          {
            label: "Developers",
            data: this.chartLabels.map(
              (l) => this.labelToDevelopersCountMap[l]
            ),
            backgroundColor: "#607d8b",
          },
        ],
      };
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
