<script setup>
defineProps({
    text: {
        type: String,
        default: 'Salvar'
    },
    loading: {
        type: Boolean,
        default: false
    },
    widthClass: {
        type: String,
        default: 'w-1/1' // Classes de largura do Tailwind
    },
    heightClass: {
        type: String,
        default: 'h-1/1' // Classes de altura do Tailwind
    },
    customClass: {
        type: String,
        default: '' // Classes adicionais
    }
})
</script>

<template>
    <button
        type="submit"
        class="btn bg-[#3DA700] hover:bg-[#388E3C] text-white rounded-lg flex items-center justify-center"
        :class="[widthClass, heightClass, customClass]"
        :disabled="loading"
    >
        <span v-if="loading" class="loading loading-spinner loading-sm mr-2"></span>
        {{ loading ? 'Salvando...' : text }}
    </button>
</template>
