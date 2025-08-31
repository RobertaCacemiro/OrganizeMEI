<template>
    <Sidebar>
        <div class="px-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'NOVO LANÇAMENTO'"
                    :form="FormCobranca"
                    :data="registroSelecionado"
                    :adicional="clientes"
                />
            </div>

            <div class="pb-4">
                <DashboardPages :cards="cards" />
            </div>

            <div class="mt-6">
                <Table
                    :columnsName="colunas"
                    :data="data"
                    :actions="[
                        { icon: Pencil, color: '#3B82F6', onClick: editar },
                        {
                            icon: Trash2,
                            color: '#EF4444',
                            onClick: fAbrirConfirmacao,
                        },
                        { icon: Eye, color: '#10B981', onClick: visualizar },
                    ]"
                />

                <ConfirmAction
                    ref="confirmDelete"
                    title="Exclusão de Registro"
                    message="Tem certeza que deseja excluir este registro de cobrança?"
                    :id="currentId"
                    @confirm="excluir"
                />
            </div>
        </div>
    </Sidebar>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useForm } from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import DashboardPages from "@/Components/DashboardPages.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormCobranca from "../Components/FormCobranca.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Object,
        default: () => ({}),
    },
    dashboardValues: {
        type: Object,
        default: () => ({}),
    },
});

console.log(props);

const data = ref(props.data);
const clientes = ref(props.clients);
const dashboardValues = reactive(props.dashboardValues);

const colunas = [
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "client_name" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "data_pagamento" },
];

const cards = [
    {
        title: "Total de Cobranças:",
        titleSize: "text-base",
        description: dashboardValues.total_cobrancas,
        descriptionSize: "text-3xl",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
    },
    {
        title: "Pagos:",
        titleSize: "text-base",
        description: dashboardValues.total_pagos,
        descriptionSize: "text-3xl",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
        titleColor: "text-white",
        descriptionColor: "text-white",
        color: "bg-[#3DA700]",
    },
    {
        title: "Pendentes:",
        titleSize: "text-base",
        description: dashboardValues.total_pendentes,
        descriptionSize: "text-3xl",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
    },
    {
        title: "Atrasados:",
        titleSize: "text-base",
        description: dashboardValues.total_atrasados,
        descriptionSize: "text-3xl",
        descriptionColor: "text-black",
        descriptionWeight: "font-bold",
    },
];

const form = useForm({});

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

function editar(id) {
    console.log("Editar");
}

const confirmDelete = ref(null);
const currentId = ref(null);

function fAbrirConfirmacao(id) {
    currentId.value = id;
    confirmDelete.value.openModal();
}

function excluir(id) {
    form.delete(`/cobrancas/${id}`, {
        preserveScroll: true,
    });
}
</script>
