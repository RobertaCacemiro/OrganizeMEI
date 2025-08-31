<template>
    <Sidebar>
        <div class="px-4">
            <!-- <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'NOVO LANÇAMENTO'"
                    :form="FormBoleto"
                    :data="registroSelecionado"
                />
            </div> -->
            <div class="mt-6">
                <Table
                    :columnsName="colunas"
                    :data="data"
                    :actions="actions"
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

console.log("Data", data);

const colunas = [
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "cliente" },
    { label: "VENCIMENTO", key: "data_vencimento" },
    { label: "VALOR", key: "valor", type: "money" },
    { label: "DATA DE PAGAMENTO", key: "dataPagamento" },
    { label: "DATA DE ENVIO", key: "data_envio" },
    { label: "ANEXOS", key: "dataPagamento" }
];

const actions = [
    { icon: "Pencil", color: "blue-800", onClick: fEditar },
    { icon: "Trash2", color: "red-800", onClick: fAbrirConfirmacao },
];

const form = useForm({});

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

function fEditar(id) {
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
