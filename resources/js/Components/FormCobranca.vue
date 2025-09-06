<template>
    <form
        @submit.prevent="submit"
        class="p-4 rounded-xl bg-white max-w-lg w-full"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">Novo Cobrança</h1>
        </div>

        <div class="mb-4">
            <label class="label">Cliente:</label>
            <select
                v-model="form.client_id"
                class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                required
            >
                <option value="" disabled hidden>Selecione</option>
                <option
                    v-for="cliente in clients"
                    :key="cliente.id"
                    :value="cliente.id"
                >
                    {{ cliente.name }}
                </option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="label">Vencimento</label>
                <input
                    v-model="form.due_date"
                    type="date"
                    class="input input-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    required
                />
            </div>
            <div>
                <label class="label">Valor</label>
                <input
                    v-model="form.amount"
                    type="text"
                    class="input input-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    placeholder="R$ 0,00"
                    required
                />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="label">Data de Pagamento</label>
                <input
                    v-model="form.payment_date"
                    type="date"
                    class="input input-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    :class="{ 'text-gray-400': !form.payment_date }"
                    placeholder="Selecione uma data"
                />
            </div>
            <div>
                <label class="label">Enviar PIX?</label>
                <div class="flex gap-4 pt-2">
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            :value="true"
                            v-model="form.ies_send_pix"
                            class="radio radio-success"
                        />
                        Sim
                    </label>
                    <label class="flex items-center gap-2">
                        <input
                            type="radio"
                            :value="false"
                            v-model="form.ies_send_pix"
                            class="radio radio-error"
                        />
                        Não
                    </label>
                </div>
            </div>
        </div>

        <!-- Descrição -->
        <div class="mb-4">
            <label class="label">Descrição</label>
            <textarea
                v-model="form.description"
                class="textarea textarea-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                rows="3"
                placeholder="Alguma informação adicional"
            ></textarea>
        </div>

        <!-- Botões -->
        <div class="flex gap-4 mt-6">
            <button
                type="button"
                class="btn flex-1 bg-[#FF0017] text-white hover:bg-red-700 rounded-xl"
                @click="$emit('close')"
            >
                Cancelar
            </button>
            <button
                type="submit"
                class="btn flex-1 bg-[#3DA700] text-white hover:bg-green-700 rounded-xl"
            >
                Salvar
            </button>
        </div>
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, onMounted } from "vue";

import { fFormatDate, fGetCategoriaId } from "@/utils/helpers";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    adicional: {
        type: Array,
        default: () => [],
    },
});

const clients = props.adicional;

const formData = ref({});

const form = useForm({
    client_id: "",
    amount: null,
    description: null,
    due_date: new Date().toISOString().split("T")[0],
    payment_date: null,
    ies_send_pix: false,
});


onMounted(() => {
    form.reset();
});

watch(
    () => props.data,
    (editValue) => {
        // Here, we update our local formData ref with the new data.
        formData.value = editValue;

        if (editValue && Object.keys(editValue).length > 0) {
            form.transaction_date =
                fFormatDate(editValue.date) ||
                new Date().toISOString().split("T")[0];
            form.amount = editValue.valor || null;
            form.type = editValue.type == "Despesa" ? 1 : 2 || null;
            form.category_id =
                fGetCategoriaId(editValue.category, categories) || null;
            form.description = editValue.descricao || null;
            form.observation = editValue.observation || null;
        } else {
            form.reset();
        }
    },
    { immediate: true }
);

function submit() {
    // if (formData.value.id) {
    //     form.post(`/financeiro/${formData.value.id}/update`, {
    //         method: "put",
    //         forceFormData: true,
    //     });
    // } else {
        form.post("/cobrancas/store");
    // }
}
</script>
