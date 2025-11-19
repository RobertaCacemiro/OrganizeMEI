<template>
    <div
        class="min-h-screen flex flex-col items-center justify-center bg-gray-100 p-6 text-center"
    >
        <div class="bg-white shadow-xl rounded-2xl p-8 max-w-md w-full">
            <div class="flex justify-center mb-6">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-20 w-20 text-purple-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-3">
                🚀 Funcionalidade em Desenvolvimento
            </h1>

            <p class="text-gray-600 mb-6 leading-relaxed">
                A página de
                <span class="text-purple-600 font-semibold"
                    >Upgrade para Premium</span
                >
                está sendo construída. Em breve você poderá conhecer todos os
                benefícios exclusivos e planos disponíveis.
            </p>

            <button
                @click="voltar"
                class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-6 rounded-lg font-medium transition"
            >
                Voltar
            </button>
        </div>
    </div>
</template>

<script setup>
function voltar() {
    window.history.back();
}
</script>

<style scoped></style>
