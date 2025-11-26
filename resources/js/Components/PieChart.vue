<template>
    <canvas ref="chartCanvas"></canvas>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import {
    Chart,
    PieController,
    ArcElement,
    Tooltip,
    Legend,
    DoughnutController,
} from "chart.js";

Chart.register(PieController, DoughnutController, ArcElement, Tooltip, Legend);

const props = defineProps({
    chartData: Object,
    chartOptions: Object,
});

const chartCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
    renderChart();
});

watch(
    () => props.chartData,
    (newData) => {
        if (newData) {
            renderChart();
        }
    }
);

watch(
    () => props.chartOptions,
    () => renderChart()
);

function renderChart() {
    if (!chartCanvas.value) return;

    if (!props.chartData || !props.chartData.datasets) return;

    if (chartInstance) {
        chartInstance.destroy();
    }

    chartInstance = new Chart(chartCanvas.value, {
        type: "doughnut",
        data: props.chartData,
        options: props.chartOptions || {},
    });
}
</script>
