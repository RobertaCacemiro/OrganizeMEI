<template>
    <button
        type="button"
        class="btn btn-square bg-[#3DA700] text-white"
        @click="abrirModal"
    >
        +
    </button>

    <dialog
        id="categories_modal"
        class="modal modal-bottom sm:modal-middle"
        ref="modal"
    >
        <div class="modal-box">
            <h3 class="text-lg font-bold">CATEGORIAS</h3>

            <div class="flex justify-end items-center pb-3">
                <RegisterButton
                    ref="registerButtonRef"
                    :nomenclature="'ADICIONAR'"
                    :form="FormCategoria"
                    :data="registroSelecionado"
                     @created="handleCategoriaCreated"
                />
            </div>

            <Table
                :columnsName="colunas"
                :data="{ data: props.data }"
                :actions="actions"
            />

            <div class="modal-action">
                <form method="dialog">
                    <button class="btn">Close</button>
                </form>
            </div>
        </div>
    </dialog>
</template>

<script setup>
import { ref } from "vue";

import RegisterButton from "../Components/RegisterButton.vue";
import FormCategoria from "../Components/FormCategoria.vue";
import Table from "@/Components/Table.vue";

const props = defineProps({
    data: { type: Object, default: () => ({}) },
});

console.log("DATA NA CATEGORIAS", props.data);

const colunas = [{ label: "IDENTIFICAÇÃO", key: "name" }];

const actions = [
    { icon: "Pencil", color: "blue-800", onClick: fEditar },
    { icon: "Trash2", color: "red-800", onClick: fAbrirConfirmacao },
];

const modal = ref(null);

function abrirModal() {
    modal.value.showModal();
}

function fEditarCategorias() {
    console.log("Edita categorias");
}

function fAbrirConfirmacao() {
    console.log("Abre confirmação");
}

const tableData = ref([...props.data]); // cria cópia reativa da tabela


function handleCategoriaCreated(novaCategoria) {
    // Adiciona categoria na tabela
    tableData.value.push(novaCategoria);
    console.log("Table data", tableData);

    // Fecha modal
    modal.value.close();
}

let registerButtonRef = ref(null);
let registroSelecionado = ref({});

function fEditar(id) {
    const registro = data.value.data.find((item) => item.id === id);
    if (!registro) return;

    registroSelecionado.value = id ? { ...registro } : {};
    registerButtonRef.value.fAbrirModal();
}
</script>
