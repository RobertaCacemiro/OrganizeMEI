<template>
    <Sidebar>
        <div class="p-4">
            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    :nomenclature="'CADASTRAR'"
                    :form="FormCliente"
                />
            </div>
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
                @submit="aplicarFiltros"
            />

            <Table
                :columns-name="colunas"
                :data="data"
                :on-edit="editar"
                :on-delete="excluir"
            />
        </div>
    </Sidebar>
</template>
<script setup>
import { ref, watch } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import axios from "axios";

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import RegisterButton from "../Components/RegisterButton.vue";
import FiltroTabela from "../Components/FiltroTabela.vue";

import FormCliente from "../Components/FormCliente.vue";

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

// 🔄 Torna os dados reativos
const data = ref(props.data);

// 🔍 Sempre que os props forem atualizados, atualiza o data também
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
];

function editar(id) {
    axios.get(`/api/clientes/${id}`).then((response) => {
        const dados = response.data;

        form.cpf_cnpj = dados.cpf_cnpj;
        form.name = dados.name;
        form.email = dados.email;
        form.phone = dados.phone;
        form.street = dados.street;
        form.number = dados.number;
        form.complement = dados.complement;
        form.district = dados.district;
        form.city = dados.city;
        form.state = dados.state;
        form.zip_code = dados.zip_code;
        form.notes = dados.notes;

        // Exibe o modal
        document.getElementById("my_modal_3").showModal();
    });
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

function fAplicarFiltro() {
    form.get(
        "/clientes",
        {
            name: form.name,
            cpf_cnpj: form.cpf_cnpj,
            email: form.email,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
}
</script>
