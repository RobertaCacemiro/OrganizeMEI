<template>
    <div
        v-if="visible"
        :class="[
            'fixed z-[9999] px-4 py-2 rounded shadow-lg transition-opacity duration-500',
            bgColor,
            sizeClass,
        ]"
        :style="positionStyle"
    >
        <!-- {{ message }} -->
        <span v-html="message"></span>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";

const visible = ref(false);

const props = defineProps({
    message: String,
    type: { type: String, default: "info" },
    duration: { type: Number, default: 3000 },
    position: { type: String, default: "top-right" },
    size: { type: String, default: "md" },
});

// cores
const bgColor =
    {
        success: "bg-green-600 text-white",
        error: "bg-red-600 text-white",
        info: "bg-blue-600 text-white",
    }[props.type] || "bg-gray-700 text-white";

// tamanho
const sizeClass =
    {
        sm: "text-sm",
        md: "text-base",
        lg: "text-lg",
        xl: "text-xl",
    }[props.size] || "text-base";

// posição
const positionStyle = computed(() => {
    const isMobile = window.innerWidth < 640;

    if (isMobile) {
        return {
            top: "1.25rem",
            left: "50%",
            transform: "translateX(-50%)",
        };
    }

    switch (props.position) {
        case "top-right":
            return { top: "1.25rem", right: "1.25rem" };
        case "top-left":
            return { top: "1.25rem", left: "1.25rem" };
        case "bottom-right":
            return { bottom: "1.25rem", right: "1.25rem" };
        case "bottom-left":
            return { bottom: "1.25rem", left: "1.25rem" };
        case "center":
            return {
                top: "50%",
                left: "50%",
                transform: "translate(-50%, -50%)",
            };
        case "top-center":
            return {
                top: "1.25rem",
                left: "50%",
                transform: "translateX(-50%)",
            };
        case "bottom-center":
            return {
                bottom: "1.25rem",
                left: "50%",
                transform: "translateX(-50%)",
            };
        default:
            return { top: "1.25rem", right: "1.25rem" };
    }
});

onMounted(() => {
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
    }, props.duration);
});
</script>
