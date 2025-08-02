<script setup>
const props = defineProps({
    nomenclature: String,
    form: Object,
    data: Object, // novo! Cliente a ser editado (opcional)
});
</script>

<template>
    <button
        class="btn bg-[#3DA700] text-white rounded-lg collapse-arrow"
        onclick="my_modal_3.showModal()"
    >
        {{ nomenclature }}
    </button>

    <dialog id="my_modal_3" class="modal">
        <div class="modal-box">
            <!-- Passa data como prop para o componente -->
            <component :is="form" :cliente="data" />
        </div>
    </dialog>
</template>
