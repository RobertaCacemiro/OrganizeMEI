<template>
    <form
        @submit.prevent="submit"
        class="p-4 rounded-xl bg-white max-w-lg w-full"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">Novo Lançamento</h1>
        </div>

        <!-- Data e Valor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="label">Data</label>
                <input
                    v-model="form.transaction_date"
                    type="date"
                    class="input input-bordered w-full"
                    required
                />
            </div>
            <div>
                <label class="label">Valor</label>
                <input
                    v-model="form.amount"
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="R$ 0,00"
                    required
                />
            </div>
        </div>

        <!-- Tipo -->
        <div class="mb-4">
            <label class="label">Tipo</label>
            <div class="flex gap-4">
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        value="1"
                        v-model="form.type"
                        class="radio radio-error"
                    />
                    Despesa
                </label>
                <label class="flex items-center gap-2">
                    <input
                        type="radio"
                        value="2"
                        v-model="form.type"
                        class="radio radio-success"
                    />
                    Receita
                </label>
            </div>
        </div>

        <!-- Categoria + botão -->
        <div class="mb-4">
            <label class="label">Categoria</label>
            <div class="flex gap-2">
                <select
                    v-model="form.category_id"
                    class="select select-bordered w-full"
                    required
                >
                    <option disabled value="">Selecione uma categoria</option>
                    <option
                        v-for="categoria in categories"
                        :key="categoria.id"
                        :value="categoria.id"
                    >
                        {{ categoria.name }}
                    </option>
                </select>
                <button
                    type="button"
                    class="btn btn-success btn-square text-white"
                >
                    +
                </button>
            </div>
        </div>

        <!-- Descrição -->
        <div class="mb-4">
            <label class="label">Descrição</label>
            <input
                v-model="form.description"
                type="text"
                class="input input-bordered w-full"
                placeholder="Ex: Concerto do notebook"
            />
        </div>

        <!-- Observação -->
        <div class="mb-4">
            <label class="label">Observação</label>
            <textarea
                v-model="form.observation"
                class="textarea textarea-bordered w-full"
                rows="3"
                placeholder="Alguma informação adicional"
            ></textarea>
        </div>

        <!-- Botões -->
        <div class="flex justify-between mt-6">
            <button
                type="button"
                class="btn bg-red-600 text-white hover:bg-red-700"
                @click="$emit('close')"
            >
                Cancelar
            </button>
            <button
                type="submit"
                class="btn bg-green-600 text-white hover:bg-green-700"
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

const categories = props.adicional;

// Use a ref to store the data locally.
// We will update this ref when props.data changes.
const formData = ref({});

const form = useForm({
    transaction_date: new Date().toISOString().split("T")[0],
    amount: null,
    type: null,
    category_id: null,
    description: null,
    observation: null,
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
    if (formData.value.id) {
        form.post(`/financeiro/${formData.value.id}/update`, {
            method: "put",
            forceFormData: true,
        });
    } else {
        form.post("/financeiro/store");
    }
}
</script>
