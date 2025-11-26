<template>
    <canvas ref="canvas"></canvas>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { Chart } from "chart.js/auto";

const props = defineProps({
    chartData: Object,
});

const canvas = ref(null);
let chartInstance = null;

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                usePointStyle: true,
                pointStyle: "circle",
                boxWidth: 12,
                padding: 16,
            },
        },

        tooltip: {
            callbacks: {
                label: function (context) {
                    let value = context.raw;
                    return value.toLocaleString("pt-BR", {
                        style: "currency",
                        currency: "BRL",
                    });
                },
            },
        },
    },
};

const renderChart = () => {
    if (chartInstance) chartInstance.destroy();
    chartInstance = new Chart(canvas.value, {
        type: "line",
        data: props.chartData,
        options: chartOptions,
    });
};

onMounted(renderChart);
onBeforeUnmount(() => {
    if (chartInstance) chartInstance.destroy();
});

watch(
    () => props.chartData,
    (newData) => {
        if (chartInstance) {
            chartInstance.data = newData;
            chartInstance.update();
        }
    },
    { deep: true }
);
</script>
