<template>
    <button
        :class="['px-4 py-2 rounded-lg font-medium transition', colorClasses]"
        @click="fAbrirModal"
    >
        <slot />
    </button>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    color: { type: String, default: "default" }, // <- cor padrão
    id: { type: [Number, String], required: true },
});

const emit = defineEmits(["open-modal"]);

const colorClasses = computed(() => {
    switch (props.color) {
        case "red":
            return "bg-red-600 text-white hover:bg-red-700";
        case "green":
            return "bg-green-600 text-white hover:bg-green-700";
        case "gray":
            return "bg-gray-200 text-gray-700 hover:bg-gray-300";
        case "default": // nossa nova cor padrão
        default:
            return "bg-[#3DA700] text-white hover:bg-[#339000]";
    }
});

function fAbrirModal() {
    emit("open-modal", props.id);
}
</script>
