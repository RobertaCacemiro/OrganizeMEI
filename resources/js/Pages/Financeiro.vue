<template>
    <Sidebar>
        <div class="px-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'NOVO LANÇAMENTO'"
                    :form="FormFinanceiro"
                    :data="registroSelecionado"
                    :adicional="categories"
                />
            </div>

            <div class="pb-4">
                <DashboardPages :cards="cards" />
            </div>

            <div>
                <FiltroTabela
                    :campos="[
                        {
                            name: 'descricao',
                            type: 'text',
                            placeholder: 'Descrição',
                        },
                        {
                            name: 'categoria',
                            type: 'select',
                            placeholder: 'Categoria',
                            options: categoriasOptions,
                        },
                        {
                            name: 'tipo',
                            type: 'select',
                            placeholder: 'Tipo',
                            options: [
                                { value: 1, label: 'Despesa' },
                                { value: 2, label: 'Receita' },
                            ],
                        },
                        {
                            name: 'data',
                            type: 'date',
                            placeholder: 'Data',
                        },
                    ]"
                    :definedValues="filters"
                    @submit="fAplicarFiltro"
                />
            </div>

            <div class="mt-6">
                <Table :columnsName="colunas" :data="data" :actions="actions" />

                <ConfirmAction
                    ref="confirmDelete"
                    title="Exclusão de Registro"
                    message="Tem certeza que deseja excluir este registro financeiro?"
                    :id="currentId"
                    @confirm="fExcluir"
                />
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref, watch, reactive } from "vue";
import { useForm, router } from "@inertiajs/vue3";

import FiltroTabela from "../Components/FiltroTabela.vue";
import RegisterButton from "../Components/RegisterButton.vue";
import FormFinanceiro from "../Components/FormFinanceiro.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import DashboardPages from "@/Components/DashboardPages.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
    dashboardValues: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const data = ref(props.data);
const filters = ref(props.filters);
const dashboardValues = reactive(props.dashboardValues);

watch(
    () => props.data,
    (newData) => {
        data.value = newData;
    }
);

const form = useForm({});
const categories = ref(props.categories);

let categoriasOptions = [];

if (Array.isArray(categories.value)) {
    categoriasOptions = categories.value.map((cat) => ({
        value: cat.id,
        label: cat.name,
    }));
} else if (categories.value && Array.isArray(categories.value.data)) {
    categoriasOptions = categories.value.data.map((cat) => ({
        value: cat.id,
        label: cat.name,
    }));
}

const colunas = [
    { label: "DATA", key: "date" },
    { label: "DESCRIÇÃO", key: "descricao" },
    { label: "CATEGORIA", key: "category" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "TIPO", key: "tipo" },
];

const actions = [
    { icon: "Pencil", color: "blue-800", onClick: fEditar },
    { icon: "Trash2", color: "red-800", onClick: fAbrirConfirmacao },
];

const cards = [
    {
        title: "Total de Receitas:",
        titleSize: "text-base",
        description: dashboardValues.total_receitas,
        descriptionSize: "text-2xl",
        icon: "CircleArrowUp",
        iconColor: "text-green-700",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
    },
    {
        title: "Total de Despesas:",
        titleSize: "text-base",
        description: dashboardValues.total_despesas,
        descriptionSize: "text-2xl",
        icon: "CircleArrowDown",
        iconColor: "text-red-600",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
    },
    {
        title: "Saldo Atual:",
        titleSize: "text-base",
        description: dashboardValues.saldo,
        descriptionSize: "text-2xl",
        descriptionWeight: "font-bold",
        titleColor: "text-white",
        descriptionColor: "text-white",
        color: "bg-[#3DA700]",
    },
];

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

function fEditar(id) {
    const registro = data.value.data.find((item) => item.id === id);
    if (!registro) return;

    registroSelecionado.value = id ? { ...registro } : {};
    registerButtonRef.value.fAbrirModal();
}

const confirmDelete = ref(null);
const currentId = ref(null);

function fAbrirConfirmacao(id) {
    currentId.value = id;
    confirmDelete.value.openModal();
}

function fExcluir(id) {
    form.delete(`/financeiro/${id}`, {
        preserveScroll: true,
    });
}

function fAplicarFiltro(filtros) {
    router.get("/financeiro", filtros, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
