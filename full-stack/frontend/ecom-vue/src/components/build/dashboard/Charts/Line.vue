<script setup>
import { Line } from 'vue-chartjs'

function getLastSixMonthsLabels() {
  const months = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
  ];

  const today = new Date();
  const currentMonth = today.getMonth(); // 0-indexed, where January is 0 and December is 11
  // Calculate the month names for the last 6 months
  const lastSixMonthsLabels = [];
  for (let i = 0; i < 6; i++) {
    const monthIndex = (currentMonth - i + 12) % 12; // Ensure the index is within the range 0-11
    lastSixMonthsLabels.unshift(months[monthIndex]);
  }

  return lastSixMonthsLabels;
}


import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
// Define the props you are expecting
const { data: chartData, label: chartLabel } = defineProps(['data', 'label']);

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

const data = {
  labels: getLastSixMonthsLabels(),
  datasets: [
    {
      label: chartLabel,
      backgroundColor: '#3da5f4',
      data: chartData,
    },
  ],
};
const options = {
  responsive: true,
  maintainAspectRatio: false,
};
</script>

<template lang="">
    <div class="lineChart">
        <Line :data="data" :options="options" />
    </div>
</template>

<style lang="scss">
.lineChart {
  width: 100%;
  height: 48%;
  display: flex;
  background-color: #fff;
  padding: 1vw;
  border-radius: 5px;


}

</style>