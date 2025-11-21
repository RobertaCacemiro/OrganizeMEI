<template>
    <div
        v-if="open"
        class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4"
    >
        <div
            class="bg-white p-6 shadow-xl rounded-xl w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl max-h-[90vh] flex flex-col"
        >
            <div class="pb-3 mb-3 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">CHAVES PIX</h2>
            </div>

            <div class="overflow-x-auto">
                <div class="flex justify-end items-center pb-3">
                    <RegisterButton
                        ref="registerButtonRef"
                        :nomenclature="'ADICIONAR CHAVE'"
                        :form="FormKeyPix"
                        :data="registroSelecionado"
                        @refresh="fCarregaTable"
                    />
                </div>
            </div>

            <!-- FIM SEÇÃO DO HEADER -->
            <div v-if="loading" class="text-gray-500 text-center py-8">
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

            <div
                v-else-if="dados.data.length && !loading"
                class="overflow-x-auto"
            >
                <!-- Assume-se que 'Table' é o DataTable importado. -->
                <Table
                    :columnsName="colunas"
                    :data="dados"
                    :actions="actions"
                />

                <ConfirmAction
                    ref="confirmAction"
                    :title="title"
                    :message="confirmMessage"
                    :cancel-button-text="textButtonCancel"
                    :confirm-button-text="textButtonConfirm"
                    :confirm-button-color="confirmButtonColor"
                    :cancel-button-color="cancelButtonColor"
                    :id="currentId"
                    @confirm="confirmCallback"
                />
            </div>

            <div
                v-else
                class="text-center text-gray-500 py-8 border border-dashed border-gray-300 rounded-lg"
            >
                Nenhuma chave PIX encontrada.
            </div>

            <div class="mt-6 flex justify-end pt-4 border-t border-gray-100">
                <button
                    @click="close"
                    class="px-6 py-2 bg-[#FF0017] text-white rounded-lg hover:bg-red-700 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <Toast
        v-if="showToast"
        :message="toastMessage"
        :type="toastType"
        position="center"
        size="lg"
    />
</template>

<script setup>
import { ref, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

import axios from "axios";
import Table from "@/Components/Table.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormKeyPix from "../Components/FormKeyPix.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

import { fShowToast } from "@/utils/helpers";
import Toast from "@/Components/Toast.vue";

const colunas = [
    { label: "CÓDIGO", key: "id" },
    { label: "TIPO", key: "type" }, // Recebe string formatada do Controller (E-mail, CPF, etc.)
    { label: "CHAVE", key: "key_value" },
    { label: "ATIVO?", key: "is_active" }, // Recebe string formatada do Controller ("Sim" ou "Não")
];

const actions = [
    {
        icon: "Siren",
        color: "blue-600",
        label: "Ativar chave",
        onClick: fConfirmaAtivacao,
    },
    {
        icon: "Trash2",
        color: "red-600",
        label: "Excluir registro",
        onClick: fConfirmaExclusao,
    },
];

const form = useForm({});

const toastMessage = ref("");
const toastType = ref("success");
const toastVisible = ref(false);
const showToast = ref(false);

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

const props = defineProps({
    id: [Number, String],
    open: Boolean,
});

const currentId = ref(null);
const title = ref(null);
const confirmAction = ref(null);
const confirmMessage = ref("");
const textButtonConfirm = ref(null);
const textButtonCancel = ref(null);
const confirmCallback = ref(null);
const confirmButtonColor = ref("");
const cancelButtonColor = ref("");

function fAbrirConfirmacao(
    id,
    titleParam,
    mensagem,
    textButtonConfirmParam,
    textButtonCancelParam,
    callback,
    confirmColor,
    cancelColor
) {
    currentId.value = id;
    confirmCallback.value = callback;
    confirmAction.value.openModal();
    title.value = titleParam;
    textButtonConfirm.value = textButtonConfirmParam;
    textButtonCancel.value = textButtonCancelParam;
    confirmMessage.value = mensagem;
    confirmButtonColor.value = confirmColor;
    cancelButtonColor.value = cancelColor;
}

function fExcluir(id) {
    form.delete(`/pix-keys/${id}`, {
        onSuccess: () => {
            fShowToast(
                "Registro excluido com sucesso",
                toastMessage,
                toastType,
                toastVisible,
                showToast,
                "success",
                3000,
                true,
                fCarregaTable
            );
        },
        onError: (errorsResponse) => {
            fShowToast(
                errorsResponse,
                toastMessage,
                toastType,
                toastVisible,
                showToast,
                "error",
                3000,
                false
            );
        },
    });
}

function fAtivar(id) {
    form.put(`/pix-keys/${id}/activate`, {
        onSuccess: () => fCarregaTable(),
    });
}

function fConfirmaExclusao(id) {
    fAbrirConfirmacao(
        id,
        "Excluir Registro",
        "Tem certeza que deseja excluir este registro?",
        "EXCLUIR",
        "CANCELAR",
        fExcluir,
        "bg-[#FF0017] text-white", // cor do botão Confirmar
        "bg-[#3DA700] text-white" // cor do botão Cancelar
    );
}

function fConfirmaAtivacao(id) {
    fAbrirConfirmacao(
        id,
        "Deseja ativar a chave PIX?",
        "Chave PIX será ativada. Apenas uma chave pode estar ativa por vez.",
        "ATIVAR",
        "CANCELAR",
        fAtivar,
        "bg-[#3DA700] text-white",
        "bg-[#FF0017] text-white" // cor do botão Confirmar
    );
}

const emit = defineEmits(["update:open"]);

const dados = ref({
    data: [],
    links: [],
});
const loading = ref(false);

watch(
    () => props.open,
    async (val) => {
        if (val) {
            dados.value = { data: [], links: [] };
            await fCarregaTable();
        }
    }
);

async function fCarregaTable() {
    loading.value = true;
    try {
        const url = `/api/pix-keys`;
        const response = await axios.get(url);

        const rawKeys = response.data.keys || [];

        dados.value = {
            data: rawKeys,
            links: [],
        };
    } catch (error) {
        dados.value = { data: [], links: [] }; // Limpa dados em caso de falha
    } finally {
        loading.value = false;
    }
}

function close() {
    emit("update:open", false);
}
</script>
