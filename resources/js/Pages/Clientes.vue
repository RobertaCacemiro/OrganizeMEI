<template>
    <Sidebar>
        <div class="px-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    :nomenclature="'CADASTRAR CLIENTE'"
                    :form="FormCliente"
                />
            </div>
            <div>
                <FiltroTabela
                    :campos="[
                        { name: 'name', placeholder: 'Nome:' },
                        {
                            name: 'cpf_cnpj',
                            placeholder: 'CPF/CNPJ:',
                            mask: [
                                { mask: '000.000.000-00' }, // CPF
                                { mask: '00.000.000/0000-00' }, // CNPJ
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
                    message="Tem certeza que deseja excluir este registro financeiro?"
                    :id="currentId"
                    @confirm="excluir"
                />
            </div>
        </div>
    </Sidebar>
</template>
<script setup>
import { ref, watch, reactive } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import axios from "axios";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import FiltroTabela from "../Components/FiltroTabela.vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormCliente from "../Components/FormCliente.vue";
import ConfirmAction from "../Components/ConfirmAction.vue";

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
    { icon: "Pencil", color: "blue-800", onClick: fEditar },
    { icon: "Trash2", color: "red-800", onClick: fAbrirConfirmacao },
];

function fEditar (id) {
    axios.get(`/api/clientes/${id}`).then((response) => {
        edicaoForm.value = response.data; // Preenche o formulário
        modalAberto.value = true; // Abre o modal
    });
}

const confirmDelete = ref(null);
const currentId = ref(null);

function fAbrirConfirmacao(id) {
    currentId.value = id;
    confirmDelete.value.openModal();
}

function excluir(id) {
    if (confirm("Tem certeza que deseja excluir este cliente?")) {
        form.delete(`clientes/${id}`);
    }
}

const form = useForm({
    name: props.filters?.name || "",
    cpf_cnpj: props.filters?.cpf_cnpj || "",
    email: props.filters?.email || "",
});

function fAplicarFiltro(filtros) {
    router.get("/clientes", filtros, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
