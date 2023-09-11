<script setup>
import { ref, defineProps, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, LineElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, LineElement, CategoryScale, LinearScale);
const { data: data } = defineProps(['data']);


function getLastMonths() {
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
  for (let i = 0; i < 12; i++) {
    const monthIndex = (currentMonth - i + 12) % 12; // Ensure the index is within the range 0-11
    lastSixMonthsLabels.unshift(months[monthIndex]);
  }

  return lastSixMonthsLabels;
}
const chartData = ref({
  labels:getLastMonths(),
  datasets: [{
    label: 'Income per Month',
    backgroundColor: '#11cf95',
    data:data
  }]
})

const chartOptions = ref({
  responsive: true
})
</script>

<template>
    <div class="barChart">
        <Bar id="my-chart-id" :options="chartOptions" :data="chartData" />
    </div>
</template>
<style lang="scss">
.barChart {
    width: 42%;
    height: 60vh;
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 1vw;
    border-radius: 5px;

}
</style>