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
    <div class="flex justify-end items-center pb-3">
        <button
            class="btn bg-[#3DA700] text-white rounded-lg collapse-arrow"
            onclick="my_modal_3.showModal()"
        >
            {{ nomenclature }}
        </button>
    </div>
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box">
            <component :is="form" />
        </div>
    </dialog>
</template>
