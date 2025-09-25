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
                    message="Tem certeza que deseja excluir este cliente?"
                    :id="currentId"
                    @confirm="fExcluir"
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
    { icon: "Pencil", color: "blue-800", label: "Editar registro", onClick: fEditar },
    { icon: "Trash2", color: "red-800", label: "Excluir registro", onClick: fAbrirConfirmacao },
];

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
    });
}

function fAplicarFiltro(filtros) {
    router.get("/clientes", filtros, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}
</script>
