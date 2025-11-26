<template>
    <canvas ref="canvas"></canvas>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Chart } from "chart.js/auto";

// Props recebidas do pai
const props = defineProps({
    chartData: Object,
    chartOptions: Object,
});

// Informação do usuário
const page = usePage();
const user = page.props.auth?.user ?? {};

// Canvas
const canvas = ref(null);
let chartInstance = null;

// Renderização do gráfico
const renderChart = () => {
    if (chartInstance) chartInstance.destroy();

    chartInstance = new Chart(canvas.value, {
        type: "bar",
        data: props.chartData,
        options: {
            ...props.chartOptions,
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: true },
                y: { stacked: true, beginAtZero: true },
            },
            plugins: {
                legend: {
                    position: "bottom",
                    labels: {
                        usePointStyle: true, // 👈 usa bolinhas no lugar do quadrado
                        pointStyle: "circle", // 👈 formato arredondado
                        boxWidth: 12, // opcional — tamanho
                        padding: 16, // opcional — espaçamento
                    },
                },
            },
        },
    });
};

onMounted(() => {
    if (props.chartData) {
        renderChart();
    }
});

onBeforeUnmount(() => {
    if (chartInstance) chartInstance.destroy();
});

// Atualizar quando os dados mudarem
watch(
    () => props.chartData,
    () => {
        if (!props.chartData) return;

        if (!chartInstance) {
            // Primeira criação do gráfico
            renderChart();
        } else {
            // Atualização
            chartInstance.data = props.chartData;
            chartInstance.update();
        }
    },
    { deep: true }
);

watch(
    () => props.chartOptions,
    () => {
        if (!props.chartOptions) return;

        renderChart();
    },
    { deep: true }
);
</script>
