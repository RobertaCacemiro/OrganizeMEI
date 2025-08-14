<template>
    <Sidebar>
        <div class="px-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'NOVO LANÇAMENTO'"
                    :form="FormBoleto"
                    :data="registroSelecionado"
                />
            </div>
            <div class="mt-6">
                <Table
                    :columnsName="colunas"
                    :data="data"
                    :on-edit="editar"
                    :on-delete="excluir"
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
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormBoleto from "../Components/FormBoleto.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    clientes: {
        type: Object,
        default: () => ({}),
    },
});

const data = ref(props.data);

const colunas = [
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "cliente" },
    { label: "VENCIMENTO", key: "vencimento" },
    { label: "VALOR", key: "valor" },
    { label: "DATA DE PAGAMENTO", key: "dataPagamento" },
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

    fAbrirConfirmacao(id);
    form.delete(`/boletos/${id}`, {
        preserveScroll: true,
    });
}
</script>
