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

            <div>
                <FiltroTabela
                    :campos="[
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
                        {
                            name: 'ies_send_pix',
                            type: 'select',
                            placeholder: 'Envio PIX?',
                            options: sendPixOptions,
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
                    message="Tem certeza que deseja excluir este registro de cobrança?"
                    :id="currentId"
                    @confirm="fExcluir"
                />
            </div>
        </div>

        <Toast
            v-if="showToast"
            :message="toastMessage"
            :type="toastType"
            position="center"
            size="lg"
        />
    </Sidebar>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import DashboardPages from "@/Components/DashboardPages.vue";
import FiltroTabela from "../Components/FiltroTabela.vue";
import Toast from "@/Components/Toast.vue";

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
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const data = ref(props.data);
const clientes = ref(props.clients);
const dashboardValues = reactive(props.dashboardValues);
const filters = ref(props.filters);

const colunas = [
    { label: "CÓDIGO", key: "id" },
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "client_name" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "data_pagamento" },
    { label: "ENVIO PIX?", key: "link_acesso_pix" },
    { label: "DESCRIÇÃO", key: "descricao" },
];

const actions = [
    {
        icon: "Pencil",
        color: "blue-800",
        label: "Editar registro",
        onClick: fEditar,
        color: "#FF2C2C",
    },
    {
        icon: "Trash2",
        color: "red-800",
        label: "Excluir registro",
        onClick: fAbrirConfirmacao,
    },
];

const cards = [
    {
        title: "Total de Cobranças (Mensal):",
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

const statusOptions = [
    { value: 1, label: "Pendente Envio" },
    { value: 2, label: "Pendente Pagamento" },
    { value: 3, label: "Pago" },
    { value: 4, label: "Vencido" },
];

const sendPixOptions = [
    { value: 1, label: "Sim" },
    { value: 0, label: "Não" },
];

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

function fShowToast(message, type) {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;

    setTimeout(() => {
        showToast.value = false;
    }, 3000); // 3 segundos
}

const form = useForm({});

watch(
    () => props.data,
    (newData) => {
        data.value = newData;
    }
);

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
    // form.delete(`/cobrancas/${id}`, {
    //     preserveScroll: true,
    // });

    form.delete(`/cobrancas/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            fShowToast("Cobrança excluido com sucesso!.", "success");
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        },
        onError: (errorsResponse) => {
            const fieldKeys = Object.keys(errorsResponse);

            if (fieldKeys.length > 0) {
                const firstErrorKey = fieldKeys[0];
                let errorMessage = "Ocorreu um erro no processamento.";

                const errorArray = errorsResponse[firstErrorKey];

                if (Array.isArray(errorArray) && errorArray.length > 0) {
                    errorMessage = errorArray[0];
                } else if (
                    typeof errorsResponse[firstErrorKey] === "object" &&
                    errorsResponse[firstErrorKey] !== null
                ) {
                    errorMessage = firstErrorKey;
                } else if (typeof errorArray === "string") {
                    errorMessage = errorArray;
                }

                // Garante que o que vai para o fShowToast é uma String
                if (
                    typeof errorMessage === "string" &&
                    errorMessage.length > 0
                ) {
                    fShowToast(errorMessage, "error");
                } else {
                    // Último recurso
                    fShowToast(
                        "Ocorreu um erro inesperado no servidor.",
                        "error"
                    );
                }
            } else {
                fShowToast("Ocorreu um erro inesperado no servidor.", "error");
            }
        },
    });
}

function fAplicarFiltro(filtros) {
    router.get("/cobrancas", filtros, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
