<template>
    <div class="w-full">
        <div
            class="bg-white shadow rounded-xl p-4 md:p-6 h-full relative overflow-hidden"
        >
            <!-- BLOQUEIO QUANDO NÃO É PREMIUM -->
            <div
                v-if="!isPremium"
                class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 z-20"
            >
                <div
                    class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-sm sm:max-w-md text-center"
                >
                    <h2
                        class="font-bold text-lg sm:text-2xl text-gray-800 mb-4"
                    >
                        🚀 Recurso Exclusivo
                    </h2>

                    <p
                        class="text-sm sm:text-base text-gray-600 mb-6 leading-relaxed"
                    >
                        Este gráfico utiliza dados disponíveis somente em planos
                        <span class="font-semibold text-purple-600"
                            >Premium</span
                        >. Faça um upgrade para liberar este conteúdo.
                    </p>

                    <button
                        @click="fRedirecionaAssinatura"
                        class="btn bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition"
                    >
                        Tornar-se Premium
                    </button>
                </div>
            </div>

            <!-- CONTEÚDO NORMAL -->
            <div v-else class="relative z-0">
                <!-- Cabeçalho -->
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h1 class="text-black text-lg md:text-xl font-semibold">
                            Fluxo de Cobranças
                        </h1>

                        <div class="mt-2">
                            <span
                                class="lg:text-3xl md:text-sm font-bold text-black"
                            >
                                {{ totalCharges }}
                            </span>
                            <div class="text-sm text-gray-600">
                                Total do Período
                            </div>
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
            </div>

            <!-- GRÁFICO -->
            <div class="h-80 flex items-center justify-center">
                <BarChart
                    :title="'Fluxo de Cobranças'"
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
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
import BarChart from "@/Components/BarChart.vue";

const page = usePage();
const user = page.props.auth?.user ?? {};
const isPremium = [1, 2].includes(user.access);

const period = ref("month");
const year = ref(new Date().getFullYear());

const years = Array.from(
    { length: 10 },
    (_, i) => new Date().getFullYear() - i
);

const chartData = ref(null);
const chartOptions = ref(null);
const totalCharges = ref(0);

function buildParams() {
    const params = { period: period.value };
    if (period.value !== "week") params.year = year.value;
    return params;
}

async function fetchChart() {
    try {
        const params = buildParams();
        const res = await axios.get("/charts/billing-status", { params });

        chartData.value = JSON.parse(JSON.stringify(res.data.chartData));
        chartOptions.value = { ...res.data.chartOptions };
        totalCharges.value = res.data.totalCharges ?? 0;
    } catch (err) {}
}

watch(period, () => {
    if (period.value === "week") year.value = new Date().getFullYear();
    fetchChart();
});

watch(year, () => {
    if (period.value !== "week") fetchChart();
});

onMounted(fetchChart);
</script>
