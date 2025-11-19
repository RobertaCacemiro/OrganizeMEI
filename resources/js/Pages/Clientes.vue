<template>
    <Sidebar>
        <div class="px-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'CADASTRAR CLIENTE'"
                    :form="FormCliente"
                    :data="registroSelecionado"
                />
            </div>
            <div>
                <FiltroTabela
                    :campos="[
                        { name: 'name', placeholder: 'Nome:' },
                        {
                            name: 'cpf_cnpj',
                            placeholder: 'CPF/CNPJ:',
                            type: 'mask',
                            mask: [
                                { mask: '000.000.000-00' },
                                { mask: '00.000.000/0000-00' },
                            ],
                        },
                        { name: 'email', placeholder: 'E-mail:' },
                    ]"
                    @submit="fAplicarFiltro"
                />
            </div>

            <div class="mt-6">
                <Table
                    :columns-name="colunas"
                    :data="data"
                    :actions="actions"
                />

                <ConfirmAction
                    ref="confirmDelete"
                    title="Exclusão de Registro"
                    message="Tem certeza que deseja excluir este cliente?"
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
import { ref, watch, reactive } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import route from "ziggy-js";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import FiltroTabela from "../Components/FiltroTabela.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormCliente from "../Components/FormCliente.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";
import Toast from "@/Components/Toast.vue";

const props = defineProps({
    data: {
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

// Sempre que os props forem atualizados, atualiza o data também
watch(
    () => props.data,
    (newData) => {
        data.value = newData;
    }
);

const colunas = [
    { label: "CNPJ/CPF", key: "cpf_cnpj" },
    { label: "NOME", key: "name" },
    { label: "E-MAIL", key: "email" },
    { label: "TELEFONE", key: "phone" },
    { label: "ENDEREÇO", key: "address" },
    { label: "OBSERVAÇÃO", key: "observacao" },
];

const actions = [
    {
        icon: "Pencil",
        color: "blue-800",
        label: "Editar registro",
        onClick: fEditar,
    },
    {
        icon: "Trash2",
        color: "red-800",
        label: "Excluir registro",
        onClick: fAbrirConfirmacao,
    },
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

const form = useForm({
    name: props.filters?.name || "",
    cpf_cnpj: props.filters?.cpf_cnpj || "",
    email: props.filters?.email || "",
});

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
    form.delete(`/clientes/${id}`, {
        preserveScroll: true,
        onSuccess: () => {
            fShowToast("Cliente excluido com sucesso!.", "success");
        },
        onError: (errorsResponse) => {
            let errorMessage = "Ocorreu um erro inesperado no servidor.";

            if (errorsResponse.client_delete) {
                errorMessage = errorsResponse.client_delete;
            } else {
                const fieldKeys = Object.keys(errorsResponse);
                if (fieldKeys.length > 0) {
                    const firstErrorKey = fieldKeys[0];
                    errorMessage = Array.isArray(errorsResponse[firstErrorKey])
                        ? errorsResponse[firstErrorKey][0]
                        : errorsResponse[firstErrorKey];
                }
            }

            fShowToast(errorMessage, "error");
        },
    });
}

function fAplicarFiltro(filtros) {
    const clean = Object.fromEntries(
        Object.entries(filtros).filter(([_, v]) => v)
    );

    router.get(route("clientes.index"), clean, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
