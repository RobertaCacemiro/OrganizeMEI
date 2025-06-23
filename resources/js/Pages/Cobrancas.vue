<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";

const colunas = [
    { label: "STATUS", key: "status" },
    { label: "CLIENTE", key: "cliente" },
    { label: "VENCIMENTO", key: "vencimento" },
    { label: "VALOR", key: "valor" },
    { label: "DATA DE PAGAMENTO", key: "dataPagamento" },
];
</script>

<template>
    <Sidebar>
        <Table :columnsName="colunas" />
    </Sidebar>
</template>
