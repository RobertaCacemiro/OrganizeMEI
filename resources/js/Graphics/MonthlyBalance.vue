<template>
    <div class="w-full">
        <div class="bg-white shadow rounded-xl p-4 md:p-6 h-full">
            <!-- Cabeçalho -->
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 class="text-black text-lg md:text-xl font-semibold">
                        Fluxo Financeiro
                    </h1>

                    <div class="mt-2">
                        <span
                            class="lg:text-3xl md:text-sm font-bold text-black"
                        >
                            {{ formattedFinalBalance }}
                        </span>
                    </div>
                </div>

                <!-- Filtros -->
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

            <!-- Gráfico -->
            <div class="h-80 flex items-center justify-center">
                <LineChart
                    :title="'Fluxo Financeiro'"
                    :chart-data="chartData"
                    :chart-options="chartOptions"
                    v-if="chartData"
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
import LineChart from "../Components/LineChart.vue";

const period = ref("month");
const year = ref(new Date().getFullYear());

const years = Array.from(
    { length: 10 },
    (_, i) => new Date().getFullYear() - i
);

const chartData = ref(null);
const chartOptions = ref(null);
const finalBalance = ref(0);

/* -----------------------------
   Monta os parâmetros do request
-------------------------------- */
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
        const res = await axios.get("/charts/monthly-balance", { params });

        chartData.value = res.data.chartData;
        chartOptions.value = res.data.chartOptions;
        finalBalance.value = res.data.final_balance ?? 0;
    } catch (err) {
        console.error("Erro ao buscar dados do gráfico", err);
    }
}

watch([period, year], ([newPeriod, newYear]) => {
    // Se mudar para semana, sempre força ano atual
    if (newPeriod === "week") {
        year.value = new Date().getFullYear();
    }

    fetchChart();
});

onMounted(() => {
    fetchChart();
});

const formattedFinalBalance = computed(() => {
    const v = Number(finalBalance.value) || 0;
    return v.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
});
</script>
