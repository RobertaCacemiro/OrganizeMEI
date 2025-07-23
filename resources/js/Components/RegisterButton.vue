<script setup>
const props = defineProps({
    nomenclature: {
        type: String,
        required: true,
    },
    form: {
        type: Object,
        required: true,
    },
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
            <component :is="form" />
        </div>
    </dialog>
</template>
