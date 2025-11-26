<template>
    <div class="w-full">
        <div class="bg-white shadow rounded-xl p-4 md:p-6 h-full">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 class="text-black text-lg md:text-xl font-semibold">
                        Receita X Categoria
                    </h1>

                    <div class="mt-2">
                        <span
                            class="lg:text-3xl md:text-sm font-bold text-black"
                        >
                            {{ formattedQuantityRevenues }}
                        </span>
                        <div class="text-sm text-gray-600">
                            Total do Período
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 items-center">
                    <select
                        v-model="period"
                        class="border rounded px-3 py-1 text-black"
                    >
                        <option value="week">Semana</option>
                        <option value="month">Mês</option>
                        <option value="year">Ano</option>
                    </select>

                    <select
                        v-model="year"
                        class="border rounded px-3 py-1 text-black"
                        :disabled="period === 'week'"
                    >
                        <option v-for="y in years" :key="y" :value="y">
                            {{ y }}
                        </option>
                    </select>
                </div>
            </div>

            <div class="h-80 flex items-center justify-center">
                <div
                    v-if="
                        chartData &&
                        chartData.datasets &&
                        chartData.datasets[0].data.length === 0
                    "
                    class="p-6 text-center text-gray-500"
                >
                    Nenhum dado encontrado para o período selecionado.
                </div>
                <PieChart
                    v-if="chartData"
                    :key="period + '-' + year"
                    title="Receitas X Categorias"
                    :chart-data="chartData"
                    :chart-options="chartOptions"
                />

                <div v-else class="p-6 text-center text-gray-500">
                    <svg
                        class="animate-spin h-5 w-5 text-[#3DA700] mx-auto"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z"
                        ></path>
                    </svg>
                    <p class="mt-2 text-sm">Carregando..</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";
import PieChart from "@/Components/PieChart.vue";

const period = ref("month");
const year = ref(new Date().getFullYear());

const years = Array.from(
    { length: 10 },
    (_, i) => new Date().getFullYear() - i
);

const chartData = ref(null);
const chartOptions = ref(null);
const quantityRevenues = ref(0);

function buildParams() {
    const params = { period: period.value };
    if (period.value !== "week") {
        params.year = year.value;
    }
    return params;
}

async function fetchChart() {
    try {
        const params = buildParams();
        const res = await axios.get("/charts/income-categories", { params });

        const options = res.data.chartOptions || {};
        if (!options.plugins) options.plugins = {};
        if (!options.plugins.tooltip) options.plugins.tooltip = {};

        options.plugins.tooltip.callbacks = {
            label: function (context) {
                return context.label + ": " + context.formattedValue;
            },
        };

        chartOptions.value = options;
        chartData.value = res.data.chartData;
        quantityRevenues.value = res.data.final_revenues ?? 0;
    } catch (err) {
        console.error("Erro ao buscar dados do gráfico", err);
    }
}

watch(period, (newPeriod) => {
    if (newPeriod === "week") year.value = new Date().getFullYear();
    fetchChart();
});

watch(year, () => {
    if (period.value !== "week") fetchChart();
});

onMounted(() => {
    fetchChart();
});

const formattedQuantityRevenues = computed(() => {
    const v = Number(quantityRevenues.value) || 0;
    return v.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
});
</script>
