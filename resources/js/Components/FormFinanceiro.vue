<template>
    <form
        method="dialog"
        @submit.prevent="fSubmit"
        class="p-4 md:p-6 rounded-xl bg-white w-full max-w-4xl mx-auto max-h-[90vh] overflow-y-auto md:max-h-none md:overflow-visible"
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
                    <money3
                        v-model="form.amount"
                        v-bind="moneyConfig"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="0,00"
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
                    <span class="text-red-500 -ml-2">*</span>
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

function fShowToast(message, type) {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;

    setTimeout(() => {
        showToast.value = false;
    }, 3000);
}

// Filtros
const form = useForm({
    transaction_date: new Date().toISOString().split("T")[0],
    amount: 0.0,
    amount_display: "",
    type: 2,
    category_id: 0,
    description: null,
    observation: null,
});

const moneyConfig = {
    prefix: "",
    suffix: "",
    thousands: ".",
    decimal: ",",
    precision: 2,
    masked: false,
};

watch(
    () => form.amount_display,
    (val) => {
        if (!val) form.amount = 0;
        else {
            const num = parseFloat(val.replace(/\./g, "").replace(",", "."));
            form.amount = isNaN(num) ? 0 : num;
        }
    }
);

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

        if (editValue && Object.keys(editValue).length > 0) {
            form.transaction_date =
                fFormatDate(editValue.date) ||
                new Date().toISOString().split("T")[0];
            form.amount = editValue.valor || null;
            form.type = editValue.tipo === "Receita" ? 2 : 1;
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

function fValidaRequestForm() {
    const camposObrigatorios = {
        transaction_date: "Data",
        amount: "Valor",
        category_id: "Categoria",
        description: "Descrição",
    };

    const erros = [];

    for (const [campo, nomeCampo] of Object.entries(camposObrigatorios)) {
        const valor = form[campo];

        if (
            valor === null ||
            valor === undefined ||
            (typeof valor === "string" &&
                (valor.trim() === "" || valor.trim() === "0.00")) ||
            (typeof valor === "number" && valor === 0)
        ) {
            erros.push(`O campo *${nomeCampo} é obrigatório.`);
        }
    }

    if (erros.length > 0) {
        fShowToast(erros[0], "error");
        return false;
    }

    return true;
}

function fSubmit() {
    if (!fValidaRequestForm()) {
        return;
    }

    if (formData.value.id) {
        // Editar
        form.post(`/financeiro/${formData.value.id}/update`, {
            method: "put",
            forceFormData: true,
            onSuccess: () => {
                fShowToast(
                    "Registro financeiro atualizado com sucesso!.",
                    "info"
                );

                setTimeout(() => {
                    fHandleCancel();
                    window.location.href = window.location.href;
                }, 1000);
            },
            onError: (errorsResponse) => {
                const fieldKeys = Object.keys(errorsResponse);
                if (fieldKeys.length > 0) {
                    const firstErrorKey = fieldKeys[0];
                    const errorArray = errorsResponse[firstErrorKey];
                    const errorMessage = Array.isArray(errorArray)
                        ? errorArray[0]
                        : errorArray;
                    fShowToast(errorMessage, "error");
                } else {
                    fShowToast(
                        "Ocorreu um erro inesperado no servidor.",
                        "info"
                    );
                }
            },
        });
    } else {
        // Cadastrar
        form.post("/financeiro/store", {
            onSuccess: () => {
                fShowToast(
                    "Registro financeiro cadastrado com sucesso!.",
                    "info"
                );

                setTimeout(() => {
                    fHandleCancel();
                    window.location.reload();
                }, 1000);
            },
            onError: (errorsResponse) => {
                const fieldKeys = Object.keys(errorsResponse);
                if (fieldKeys.length > 0) {
                    const firstErrorKey = fieldKeys[0];
                    const errorArray = errorsResponse[firstErrorKey];
                    const errorMessage = Array.isArray(errorArray)
                        ? errorArray[0]
                        : errorArray;
                    fShowToast(errorMessage, "error");
                } else {
                    fShowToast(
                        "Ocorreu um erro inesperado no servidor.",
                        "error"
                    );
                }
            },
        });
    }
}
</script>
