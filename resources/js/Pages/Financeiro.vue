<script setup>

import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";

const colunas = [
    { label: "DATA",      key: "data" },
    { label: "DESCRIÇÃO", key: "descricao" },
    { label: "CATEGORIA", key: "categoria" },
    { label: "VALOR",     key: "valor" },
    { label: "TIPO",      key: "tipo" },

];

</script>

<template>
    <Sidebar>
        <Table :columnsName="colunas" />
    </Sidebar>
</template>
