<template>
    <form
        @submit.prevent="submit"
        class="p-6 rounded-xl bg-white max-w-md w-full border border-blue-500"
    >
        <!-- Título e botão fechar -->
        <div class="relative mb-6 flex justify-between items-center">
            <h1 class="text-2xl font-extrabold text-green-600">Nova Cobrança</h1>
            <button
                type="button"
                class="text-2xl font-bold text-black hover:text-gray-600"
                @click="$emit('close')"
                aria-label="Fechar"
            >
                &times;
            </button>
        </div>

        <!-- Cliente -->
        <div class="mb-4">
            <label class="block text-sm mb-1 font-medium text-gray-700">Cliente:</label>
            <select
                v-model="form.client_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                required
            >
                <option value="" disabled>Selecione</option>
                <option v-for="cliente in clients" :key="cliente.id" :value="cliente.id">
                    {{ cliente.name }}
                </option>
            </select>
        </div>

        <!-- Vencimento e Valor -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Vencimento:</label>
                <input
                    v-model="form.due_date"
                    type="date"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    required
                />
            </div>
            <div>
                <label class="block text-sm mb-1 font-medium text-gray-700">Valor:</label>
                <input
                    v-model="form.amount"
                    type="text"
                    placeholder="R$"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                    @input="formatCurrency"
                    required
                />
            </div>
        </div>

        <!-- Descrição -->
        <div class="mb-6">
            <label class="block text-sm mb-1 font-medium text-gray-700">Descrição:</label>
            <textarea
                v-model="form.description"
                rows="3"
                placeholder=""
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"
            ></textarea>
        </div>

        <!-- Botões -->
        <div class="flex justify-between gap-4">
            <button
                type="button"
                class="flex-grow bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-md"
                @click="$emit('close')"
            >
                Cancelar
            </button>
            <button
                type="submit"
                class="flex-grow bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-md"
            >
                Salvar
            </button>
        </div>
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    clients: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    client_id: null,
    due_date: new Date().toISOString().split("T")[0],
    amount: "",
    description: "",
});

watch(
    () => props.data,
    (val) => {
        if (val && Object.keys(val).length > 0) {
            form.client_id = val.client_id || null;
            form.due_date = val.due_date || new Date().toISOString().split("T")[0];
            form.amount = val.amount ? formatToCurrency(val.amount) : "";
            form.description = val.description || "";
        } else {
            form.reset();
        }
    },
    { immediate: true }
);

function formatToCurrency(value) {
    // Formata número para string R$ 0,00
    if (!value) return "";
    return Number(value)
        .toLocaleString("pt-BR", { style: "currency", currency: "BRL" })
        .replace("R$", "R$ ");
}

function formatCurrency(e) {
    // Permitir apenas números e vírgula na digitação e formatar valor
    let val = e.target.value;
    val = val.replace(/\D/g, "");
    val = (Number(val) / 100).toFixed(2) + "";
    val = val.replace(".", ",");
    val = "R$ " + val;
    form.amount = val;
}

function submit() {
    // Aqui você pode fazer o POST/PUT usando Inertia
    if (props.data && props.data.id) {
        form.post(`/charges/${props.data.id}/update`, {
            method: "put",
            forceFormData: true,
        });
    } else {
        form.post("/charges/store");
    }
}
</script>
