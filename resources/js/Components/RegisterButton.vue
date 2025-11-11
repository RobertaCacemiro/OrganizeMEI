<script setup>
import { ref, watch } from "vue";

const emit = defineEmits(["close", "refresh"]);

const props = defineProps({
    nomenclature: String,
    form: Object,
    data: Object,
    adicional: Object,
});

function fAbrirModal() {
    modal.value.showModal();
}

function fHandleCancel() {
    form.reset();
    emit("close"); // avisa o pai que quer fechar
}

function fFecharModal() {
    emit("close"); // se quiser notificar o avô
    modal.value.close();
}

const modal = ref(null);
const dataFinal = ref({});

watch(
    () => props.data,
    (editValue) => {
        dataFinal.value = editValue ? { ...editValue } : {};
    },
    { immediate: true }
);

// expor a função para o pai acessar
defineExpose({
    fAbrirModal,
});
</script>

<template>
    <button
        class="btn bg-[#3DA700] text-white rounded-lg collapse-arrow"
        @click="fAbrirModal"
    >
        {{ nomenclature }}
    </button>

    <dialog ref="modal" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <!-- <button
                    class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2"
                >
                    ✕
                </button> -->
            </form>
            <component
                :is="form"
                :data="props.data"
                :adicional="adicional"
                :key="JSON.stringify(dataFinal.value)"
                @close="fFecharModal"
            />
        </div>
    </dialog>
</template>
