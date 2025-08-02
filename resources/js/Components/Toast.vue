<template>
    <div
        v-if="visible"
        :class="[
            'fixed z-50 px-4 py-2 rounded shadow-lg transition-opacity duration-500',
            positionClass,
            bgColor,
            sizeClass,
        ]"
    >
        {{ message }}
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: "info", // 'success', 'error', 'info'
    },
    duration: {
        type: Number,
        default: 3000, // tempo em ms
    },
    position: {
        type: String,
        default: "top-right", // 'top-left', 'bottom-right', etc.
    },
    size: {
        type: String,
        default: "md", // 'sm', 'md', 'lg'
    },
});

const visible = ref(false);

const bgColor =
    {
        success: "bg-green-600 text-white",
        error: "bg-red-600 text-white",
        info: "bg-blue-600 text-white",
    }[props.type] || "bg-gray-700 text-white";

const positionClass =
    {
        "top-right": "top-5 right-5",
        "top-left": "top-5 left-5",
        "bottom-right": "bottom-5 right-5",
        "bottom-left": "bottom-5 left-5",
    }[props.position] || "top-5 right-5";

const sizeClass =
    {
        sm: "text-sm",
        md: "text-base",
        lg: "text-lg",
    }[props.size] || "text-base";

onMounted(() => {
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
    }, props.duration);
});
</script>
