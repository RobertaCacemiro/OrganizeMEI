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

            <div>
                <FiltroTabela
                    :campos="[
                        {
                            name: 'description',
                            type: 'text',
                            placeholder: 'Descrição',
                        },
                        {
                            name: 'category_id',
                            type: 'select',
                            placeholder: 'Categoria',
                            options: categoriasOptions,
                        },
                        {
                            name: 'type',
                            type: 'select',
                            placeholder: 'Tipo',
                            options: [
                                { value: 1, label: 'Despesa' },
                                { value: 2, label: 'Receita' },
                            ],
                        },
                        {
                            name: 'transaction_date',
                            type: 'date',
                            placeholder: 'Data',
                        },
                    ]"
                    @submit="fAplicarFiltro"
                />
            </div>

            <div class="mt-6">
                <Table
                    :columnsName="colunas"
                    :data="data"
                    :on-edit="editar"
                    :on-delete="abrirConfirmacao"
                />

                <ConfirmAction
                    ref="confirmDelete"
                    title="Exclusão de Registro"
                    message="Tem certeza que deseja excluir este registro financeiro?"
                    :id="currentId"
                    @confirm="excluir"
                />
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";

import FiltroTabela from "../Components/FiltroTabela.vue";
import RegisterButton from "../Components/RegisterButton.vue";
import FormFinanceiro from "../Components/FormFinanceiro.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
});

const data = ref(props.data);
const categories = ref(props.categories);

let categoriasOptions = [];

if (Array.isArray(categories.value)) {
  categoriasOptions = categories.value.map(cat => ({
    value: cat.id,
    label: cat.name,
  }));
} else if (categories.value && Array.isArray(categories.value.data)) {
  categoriasOptions = categories.value.data.map(cat => ({
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

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

function abrirNovo() {
    registroSelecionado.value = {}; // novo objeto vazio
    registerButtonRef.value.abrirModal();
}

function editar(id) {
    const registro = data.value.data.find((item) => item.id === id);
    if (!registro) return;

    registroSelecionado.value = id ? { ...registro } : {};
    registerButtonRef.value.abrirModal();
}

const confirmDelete = ref(null);
const currentId = ref(null);

const form = useForm({});

function abrirConfirmacao(id) {
    currentId.value = id;
    confirmDelete.value.openModal();
}

function excluir(id) {
    form.delete(`/financeiro/${id}`, {
        preserveScroll: true,
    });
}

function fAplicarFiltro(filtros) {
    form.get(
        "/financeiro",
        filtros,
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
}
</script>
