<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";
import RegisterButton from "../Components/RegisterButton.vue";
import FormCliente from "../Components/FormCliente.vue";

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
        <div class="flex justify-end items-center pb-3">
            <RegisterButton :nomenclature="'ADICIONAR CLIENTE'" :form="FormCliente" />
        </div>
        <Table :columnsName="colunas" />
    </Sidebar>
</template>
