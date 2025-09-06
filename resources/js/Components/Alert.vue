<template>
    <div
        v-if="visible"
        :class="[
            'fixed z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300',
            positionClass,
            bgColor,
            sizeClass
        ]"
    >
        <div class="flex items-center justify-between gap-4 alert-soft">
            <span>{{ message }}</span>
            <div class="flex gap-2">
                <button
                    v-if="showConfirm"
                    @click="confirm"
                    class="px-3 py-1 rounded bg-white/20 hover:bg-white/30"
                >
                    {{ confirmText }}
                </button>
                <button
                    v-if="dismissible"
                    @click="close"
                    class="px-3 py-1 rounded bg-white/20 hover:bg-white/30"
                >
                    {{ closeText }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    message: String,
    type: {
        type: String,
        default: "info", // 'success', 'error', 'warning', 'info'
    },
    position: {
        type: String,
        default: "top-right",
    },
    size: {
        type: String,
        default: "md",
    },
    dismissible: {
        type: Boolean,
        default: true,
    },
    closeText: {
        type: String,
        default: "Fechar",
    },
    showConfirm: {
        type: Boolean,
        default: false,
    },
    confirmText: {
        type: String,
        default: "OK",
    },
});

const emit = defineEmits(["close", "confirm"]);

const visible = ref(true);

function close() {
    visible.value = false;
    emit("close");
}

function confirm() {
    visible.value = false;
    emit("confirm");
}

// cores por tipo
const bgColor =
    {
        success: "bg-green-600 text-white",
        error: "bg-red-600 text-white",
        warning: "bg-yellow-500 text-black",
        info: "bg-blue-600 text-white",
    }[props.type] || "bg-gray-700 text-white";

// posição
const positionClass =
    {
        "top-right": "top-5 right-5",
        "top-left": "top-5 left-5",
        "bottom-right": "bottom-5 right-5",
        "bottom-left": "bottom-5 left-5",
        center: "top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2",
    }[props.position] || "top-5 right-5";

// tamanho
const sizeClass =
    {
        sm: "text-sm",
        md: "text-base",
        lg: "text-lg",
    }[props.size] || "text-base";
</script>
