<template>
    <form
        method="dialog"
        @submit.prevent="fSubmit"
        class="p-4 rounded-xl bg-white max-w-lg w-full"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">NOVO REGISTRO</h1>
        </div>

        <!-- Data e Valor -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Data
                    <span class="text-red-500 -ml-2">*</span>
                </legend>
                <input
                    v-model="form.transaction_date"
                    type="date"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    required
                />
            </fieldset>

            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Valor
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <!-- <input
                        v-model="form.amount"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="R$ 0,00"
                        required
                    /> -->
                    <IMaskComponent
                        v-model="form.amount_display"
                        :mask="{
                            mask: Number,
                            scale: 2,
                            thousandsSeparator: '.',
                            radix: ',',
                            padFractionalZeros: true,
                            normalizeZeros: true,
                        }"
                        placeholder="R$ 0,00"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        required
                    />
                </fieldset>
            </div>
        </div>

        <!-- Tipo -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Tipo
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-base">
                            <input
                                type="radio"
                                value="2"
                                v-model.number="form.type"
                                class="radio checked:text-[#3DA700] border-[#3DA700]"
                            />
                            Receita
                        </label>
                        <label class="flex items-center gap-2 text-base">
                            <input
                                type="radio"
                                value="1"
                                v-model.number="form.type"
                                class="radio checked:text-[#FF0017] border-[#FF0017]"
                            />
                            Despesa
                        </label>
                    </div>
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Categoria
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <div class="flex gap-2">
                        <select
                            v-model="form.category_id"
                            class="select select-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                            required
                        >
                            <option disabled value="0">
                                Selecione uma categoria
                            </option>
                            <option
                                v-for="categoria in filteredCategories"
                                :key="categoria.id"
                                :value="categoria.id"
                            >
                                {{ categoria.name }}
                            </option>
                        </select>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Descrição -->
        <div class="mb-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Descrição
                    <!-- <span class="text-red-500 -ml-2">*</span> -->
                </legend>
                <input
                    v-model="form.description"
                    type="text"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    placeholder="Ex: Concerto do notebook"
                />
            </fieldset>
        </div>

        <!-- Observação -->
        <div class="mb-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Observação
                    <!-- <span class="text-red-500 -ml-2">*</span> -->
                </legend>
                <textarea
                    v-model="form.observation"
                    class="textarea textarea-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    rows="3"
                    placeholder="Observações sobre o registro."
                ></textarea>
            </fieldset>
        </div>

        <!-- Botões -->
        <div class="flex gap-4 mt-6">
            <button
                type="button"
                class="btn flex-1 bg-[#FF0017] text-white hover:bg-red-700 rounded-xl"
                @click="fHandleCancel"
            >
                CANCELAR
            </button>
            <button
                type="submit"
                class="btn flex-1 bg-[#3DA700] text-white hover:bg-green-700 rounded-xl"
            >
                SALVAR
            </button>
        </div>

        <Toast
            v-if="showToast"
            :message="toastMessage"
            :type="toastType"
            position="center"
            size="lg"
        />
    </form>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";

import { fFormatDate, fGetCategoriaId } from "@/utils/helpers";
import { IMaskComponent } from "vue-imask";

import Toast from "@/Components/Toast.vue";

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

const formData = ref({});

const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("error");

// Filtros
const form = useForm({
    transaction_date: new Date().toISOString().split("T")[0],
    amount: null,
    amount_display: "",
    type: 2,
    category_id: 0,
    description: null,
    observation: null,
});

const emit = defineEmits(["close"]);

function fHandleCancel() {
    form.reset();
    emit("close"); // avisa o pai que quer fechar
}

const filteredCategories = computed(() => {
    if (!form.type) return [];
    return categories.filter((c) => Number(c.type) === Number(form.type));
});

watch(
    () => props.data,
    (editValue) => {
        formData.value = editValue;
        console.log("Fomr", formData.value);

        if (editValue && Object.keys(editValue).length > 0) {
            form.transaction_date =
                fFormatDate(editValue.date) ||
                new Date().toISOString().split("T")[0];
            form.amount_display = editValue.valor || null;
            form.type = editValue.type == "Despesa" ? 2 : 1 || null;
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

function fSubmit() {
    if (!form.amount_display) return;

    // converte string para número
    const numericValue = form.amount_display
        .replace(/\./g, "")
        .replace(",", ".");

    if (!form.category_id || form.category_id === 0) {
        toastMessage.value = "Por favor, selecione uma categoria.";
        toastType.value = "error";
        showToast.value = true;
        setTimeout(() => (showToast.value = false), 3000);
        return;
    }

    form.amount = parseFloat(numericValue);
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
