<template>
    <Sidebar>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 w-full h-full">
            <MonthlyBalance
                :chart-data="monthlyBalance.chartData"
                :chart-options="monthlyBalance.chartOptions"
            />

            <BillingStatus
                :chart-data="billingStatus.chartData"
                :chart-options="billingStatus.chartOptions"
            />

            <RecipesCategories
                :chart-data="incomeCategories.chartData"
                :chart-options="incomeCategories.chartOptions"
            />

            <ExpensesCategories
                :chart-data="expenseCategories.chartData"
                :chart-options="expenseCategories.chartOptions"
            />
        </div>
    </Sidebar>
</template>

<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import MonthlyBalance from "@/Graphics/MonthlyBalance.vue";
import BillingStatus from "@/Graphics/BillingStatus.vue";
import ExpensesCategories from "@/Graphics/ExpensesCategories.vue";
import RecipesCategories from "@/Graphics/RecipesCategories.vue";

import { defineProps } from "vue";

const props = defineProps({
    monthlyBalance: {
        type: Object,
        default: () => ({
            chartData: {},
            chartOptions: {},
        }),
    },
    billingStatus: {
        type: Object,
        default: () => ({
            chartData: {},
            chartOptions: {},
        }),
    },
    incomeCategories: {
        type: Object,
        default: () => ({
            chartData: {},
            chartOptions: {},
        }),
    },
    expenseCategories: {
        type: Object,
        default: () => ({
            chartData: {},
            chartOptions: {},
        }),
    },
});

const monthlyBalance = props.monthlyBalance;
const billingStatus = props.billingStatus;
const incomeCategories = props.incomeCategories;
const expenseCategories = props.expenseCategories;
</script>
