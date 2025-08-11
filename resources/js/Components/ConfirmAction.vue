<template>
  <dialog ref="modal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg">{{ title }}</h3>
      <p class="py-4">{{ message }}</p>
      <div class="modal-action">
        <button class="btn bg-[#3DA700] text-white" @click="closeModal">Cancelar</button>
        <button class="btn btn-error bg-[#FF0017] text-white" @click="emitConfirm">Excluir</button>
      </div>
    </div>
  </dialog>
</template>

<script setup>
import { ref, defineExpose } from "vue";

const props = defineProps({
  title: { type: String, default: "Confirmação" },
  message: { type: String, default: "Tem certeza que deseja continuar?" },
  id: { type: [Number, String], required: false }
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
