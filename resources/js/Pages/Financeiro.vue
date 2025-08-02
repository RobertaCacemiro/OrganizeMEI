<script setup>
import FiltroTabela from "../Components/FiltroTabela.vue";
import Sidebar from "@/Components/Sidebar.vue";
import Table from "@/Components/Table.vue";

const colunas = [
    { label: "DATA", key: "data" },
    { label: "DESCRIÇÃO", key: "descricao" },
    { label: "CATEGORIA", key: "categoria" },
    { label: "VALOR", key: "valor" },
    { label: "TIPO", key: "tipo" },
];
</script>

<template>
    <Sidebar>
        <FiltroTabela
  :campos="[
    { name: 'name', placeholder: 'Nome:' },
    {
      name: 'cpf_cnpj',
      placeholder: 'CPF/CNPJ:',
      type: 'mask',
      mask: [
        { mask: '000.000.000-00' },
        { mask: '00.000.000/0000-00' }
      ]
    },
    { name: 'email', placeholder: 'E-mail:', type: 'text' },
    {
      name: 'status',
      type: 'select',
      placeholder: 'Status:',
      options: [
        { label: 'Ativo', value: 'ativo' },
        { label: 'Inativo', value: 'inativo' }
      ]
    }
  ]"
  @submit="aplicarFiltros"
/>

        <Table :columnsName="colunas" />
    </Sidebar>
</template>
