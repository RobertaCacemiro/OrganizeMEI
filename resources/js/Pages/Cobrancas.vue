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
import { ref, reactive, watch} from "vue";
import { useForm, usePage} from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import DashboardPages from "@/Components/DashboardPages.vue";
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
});

const data = ref(props.data);
const clientes = ref(props.clients);
const dashboardValues = reactive(props.dashboardValues);

const colunas = [
    { label: "CÓDIGO", key: "id" },
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "client_name" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "data_pagamento" },
    { label: "DESCRIÇÃO", key: "descricao" },
];

const actions = [
    { icon: "Pencil", color: "blue-800", label: "Editar registro", onClick: fEditar, color: "#FF2C2C" },
    { icon: "Trash2", color: "red-800",  label: "Excluir registro", onClick: fAbrirConfirmacao },
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
    form.delete(`/cobrancas/${id}`, {
        preserveScroll: true,
    });
}

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

const page = usePage();
// Watch para mostrar erros vindos do backend
watch(
    () => page.props.errors,
    (errors) => {
        if (errors && Object.keys(errors).length > 0) {
            toastMessage.value = Object.values(errors)[0];
            toastType.value = "error";
            showToast.value = true;

            setTimeout(() => {
                showToast.value = false;
            }, 3000);
        }
    },
    { deep: true }
);

</script>
