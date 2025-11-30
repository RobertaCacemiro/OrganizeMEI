<template>
    <Sidebar>
        <div :class="{ 'blur-sm pointer-events-none select-none': !isPremium }">
            <div class="flex justify-end items-center pb-3">
                <ManipulationButton :id="42" @open-modal="fAbrirModal">
                    GERENCIAR CHAVES
                </ManipulationButton>

                <PixKeys :id="modalId" v-model:open="modalOpen" />
            </div>

            <div>
                <FiltroTabela
                    :campos="[
                        {
                            name: 'codigo',
                            type: 'number',
                            placeholder: 'Código',
                        },
                        {
                            name: 'charge_id',
                            type: 'number',
                            placeholder: 'Nº Cobrança',
                        },
                        {
                            name: 'client_id',
                            type: 'select',
                            placeholder: 'Cliente',
                            options: clientes,
                        },
                        {
                            name: 'status',
                            type: 'select',
                            placeholder: 'Status',
                            options: statusOptions,
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
                    message="Tem certeza que deseja excluir este registro de pagamento?"
                    :id="currentId"
                    @confirm="fExcluir"
                />
            </div>
        </div>

        <!-- Quando o usuário não for assinante, ele mostra uma mensagem -->
        <div
            v-if="!isPremium"
            class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4"
        >
            <div
                class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-sm sm:max-w-md text-center"
            >
                <h2
                    class="font-bold text-center text-lg sm:text-2xl text-gray-800 mb-3 sm:mb-4"
                >
                    🚀 Recurso Premium
                </h2>
                <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6">
                    A tela de pagamentos está disponível apenas para usuários
                    <span class="font-semibold text-purple-600">Premium</span>.
                    Faça um plano para liberar o acesso completo.
                </p>

                <div
                    class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4"
                >
                    <a
                        @click="fRedirecionaAssinatura"
                        class="btn w-full sm:w-auto bg-purple-600 text-white font-medium px-4 sm:px-6 py-2 rounded-lg hover:bg-purple-700 transition text-center"
                    >
                        Torne-se Premium
                    </a>

                    <button
                        @click="fRedirecionaTela"
                        class="w-full sm:w-auto bg-gray-200 text-gray-700 font-medium px-4 sm:px-6 py-2 rounded-lg hover:bg-gray-300 transition"
                    >
                        Não, obrigada
                    </button>
                </div>
            </div>
        </div>

        <PixEmailModal v-model:open="emailModalOpen" :keyPix="emailModalKey" />
    </Sidebar>
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import FiltroTabela from "../Components/FiltroTabela.vue";

import ManipulationButton from "../Components/ManipulationButton.vue";
import PixKeys from "./PixKeys.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";
import PixEmailModal from "../Components/PixEmailModal.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const page = usePage();
const user = page.props.auth?.user ?? {};
const isPremium = [1, 2].includes(user.access);

const data = ref(props.data);
const clientes = ref(props.clients);
const filters = ref(props.filters);

const colunas = [
    { label: "CÓDIGO", key: "id" },
    { label: "Nº COBRANÇA", key: "charge_id" },
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "cliente_name" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "data_pagamento" },
    { label: "DATA DE ENVIO", key: "data_envio" },
];

const actions = [
    {
        icon: "FileText",
        color: "blue-800",
        label: "Visualizar comprovante",
        onClick: fVisualizarComprovante,
    },
    // { icon: "Trash2", color: "red-800", onClick: fAbrirConfirmacao },
];

const statusOptions = [
    { value: 0, label: "Erro de Envio" },
    { value: 1, label: "Pendente Envio" },
    { value: 2, label: "Pendente Pagamento" },
    { value: 3, label: "Pago (Informado pelo Cliente)" },
    { value: 4, label: "Vencido" },
    { value: 5, label: "Envio Cancelado" },
    { value: 6, label: "Pago (Informado pelo Usuário)" },
];

const form = useForm({});

watch(
    () => props.data,
    (newData) => {
        data.value = newData;
    }
);

function fEditar(id) {
    console.log("Editar");
}

const confirmDelete = ref(null);
const currentId = ref(null);

function fAbrirConfirmacao(id) {
    currentId.value = id;
    confirmDelete.value.openModal();
}

function fExcluir(id) {
    fAbrirConfirmacao(id);
    form.delete(`/boletos/${id}`, {
        preserveScroll: true,
    });
}

const modalOpen = ref(false);
const modalId = ref(null);

function fAbrirModal(id) {
    modalId.value = id;
    modalOpen.value = true;
}

const emailModalOpen = ref(false);
const emailModalKey = ref(null);

function fVisualizarComprovante(row) {
    emailModalKey.value = row; // ou row.pix_key
    emailModalOpen.value = true;
}

function fRedirecionaAssinatura() {
    window.location.href = "/upgrade";
}

function fRedirecionaTela() {
    window.history.back();
}

function fAplicarFiltro(filtros) {
    router.get("/pagamentos", filtros, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
