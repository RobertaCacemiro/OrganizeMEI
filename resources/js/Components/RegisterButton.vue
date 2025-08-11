<script setup>
import { ref } from "vue";
import { watch } from "vue";

const emit = defineEmits(["close"]);
const props = defineProps({
    nomenclature: String,
    form: Object,
    data: Object,
    adicional: Object
});


const modal = ref(null);

function abrirModal() {
    modal.value.showModal();
}

function fecharModal() {
    emit("close");
    modal.value.close();
}


const dataFinal = ref({});

watch(
    () => props.data,
    (editValue) => {
        dataFinal.value = editValue ? { ...editValue } : {};
    },
    { immediate: true }
);


// precisa expor a função para o pai acessar
defineExpose({
    abrirModal
});
</script>

<template>
    <button
        class="btn bg-[#3DA700] text-white rounded-lg collapse-arrow"
        @click="abrirModal"
    >
        {{ nomenclature }}
    </button>

    <dialog ref="modal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button
                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                >
                    ✕
                </button>
            </form>
            <component :is="form" :data="props.data" :adicional="adicional"  :key="JSON.stringify(dataFinal.value)"  />

            <!-- <component :is="form" :data="dataFinal.value" :isEdit="false" :adicional="adicional" /> -->
        </div>
    </dialog>
</template>
