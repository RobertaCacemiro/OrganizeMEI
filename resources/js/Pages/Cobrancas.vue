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
            <div class="mt-6">
                <Table
                    :columnsName="colunas"
                    :data="data"
                    :on-edit="editar"
                    :on-delete="fAbrirConfirmacao"
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
import FormCobranca from "../Components/FormCobranca.vue";
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
// console.log("Teste cobranças", data);

const colunas = [
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "client_name" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "data_pagamento" },
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
