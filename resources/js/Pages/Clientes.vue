<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import RegisterButton from "../Components/RegisterButton.vue";
import FormClientes from "../Components/FormClientes.vue";

const colunas = [
    { label: "CNPJ/CPF", key: "cnpjCpf" },
    { label: "NOME", key: "nome" },
    { label: "E-MAIL", key: "email" },
    { label: "TELEFONE", key: "telefone" },
    { label: "ENDEREÇO", key: "endereco" },
];
</script>
<template>
    <Sidebar>
        <RegisterButton :nomenclature="'Adicionar Cliente'" :form="FormClientes" />
        <Table :columnsName="colunas" />
    </Sidebar>
</template>
