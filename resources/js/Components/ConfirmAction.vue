<script setup>
import { ref, defineExpose } from "vue";

const props = defineProps({
    title: { type: String, default: "Confirmação" },
    message: { type: String, default: "Tem certeza que deseja continuar?" },
    id: { type: [Number, String], required: false },
    cancelButtonText: { type: String, default: "Cancelar" },
    confirmButtonText: { type: String, default: "Confirmar" },
    cancelButtonColor: { type: String, default: "bg-gray-300 text-black" },
    confirmButtonColor: { type: String, default: "bg-red-500 text-white" },
});

const emit = defineEmits(["confirm", "cancel"]);
const modal = ref(null);

function openModal() {
    modal.value.showModal();
}

function closeModal() {
    modal.value.close();
    emit("cancel");
}

function emitConfirm() {
    emit("confirm", props.id);
    closeModal();
}

defineExpose({ openModal, closeModal });
</script>

<template>
    <dialog ref="modal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">{{ title }}</h3>
            <p class="py-4">{{ message }}</p>
            <div class="modal-action">
                <button
                    class="btn"
                    :class="cancelButtonColor"
                    @click="closeModal"
                >
                    {{ cancelButtonText }}
                </button>
                <button
                    class="btn"
                    :class="confirmButtonColor"
                    @click="emitConfirm"
                >
                    {{ confirmButtonText }}
                </button>
            </div>
        </div>
    </dialog>
</template>
