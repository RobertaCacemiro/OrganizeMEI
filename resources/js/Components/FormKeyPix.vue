<template>
    <form @submit.prevent="fSubmit" class="space-y-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">CADASTRAR CHAVE</h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- type -->
            <div>
                <fieldset class="fieldset w-full">
                    <legend class="fieldset-legend text-sm font-semibold">
                        Tipo<span class="text-red-500">*</span>
                    </legend>

                    <select
                        v-model="form.type"
                        class="select select-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        required
                    >
                        <option value="" disabled selected>
                            Selecione o tipo da chave
                        </option>
                        <option value="random">Aleatório</option>
                        <option value="cnpj">CNPJ</option>
                        <option value="cpf">CPF</option>
                        <option value="email">E-mail</option>
                        <option value="phone">Telefone</option>
                    </select>

                    <div v-if="errors.type" class="text-xs text-red-500 mt-1">
                        {{ errors.type }}
                    </div>
                </fieldset>
            </div>

            <!-- Chave -->
            <div>
                <fieldset class="fieldset w-full overflow-hidden">
                    <legend class="fieldset-legend text-sm font-semibold">
                        Chave <span class="text-red-500">*</span>
                    </legend>

                    <IMaskComponent
                        :key="form.type"
                        v-model="form.key_value"
                        :mask="maskForType"
                        :placeholder="
                            placeholders[form.type] || 'Digite a chave Pix'
                        "
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        required
                    />

                    <div
                        v-if="errors.key_value"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ errors.key_value }}
                    </div>
                </fieldset>
            </div>
        </div>

        <div class="flex gap-4 mt-6">
            <button
                type="button"
                class="btn flex-1 bg-[#FF0017] text-white hover:bg-red-700 rounded-xl"
                @click="fHandleCancel"
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
import { ref, reactive, watch, computed } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { IMaskComponent } from "vue-imask";

import { fShowToast } from "@/utils/helpers";
import Toast from "./Toast.vue";

const toastMessage = ref("");
const toastType = ref("success");
const toastVisible = ref(false);
const showToast = ref(false);

const emit = defineEmits(["update:open", "refresh"]); // adiciona 'refresh'

const page = usePage();

const props = defineProps({
    data: Object,
});

const formData = ref({});

const form = useForm({
    type: formData.value?.type || "",
    key_value: formData.value?.key_value || "",
});

watch(
    () => props.data,
    (editValue) => {
        formData.value = editValue;

        if (editValue && Object.keys(editValue).length > 0) {
            form.type = editValue.type || null;
            // form.type = fGetCategoriaId(editValue.type, tiposPix) || null;

            form.key_value = editValue.key_value || null;
        } else {
            form.reset();
        }
    },
    { immediate: true }
);

const errors = reactive({
    type: "",
    key_value: "",
});

const placeholders = {
    random: "Chave aleatória (UUID)",
    cpf: "Ex: 123.456.789-09",
    cnpj: "Ex: 12.345.678/0001-90",
    email: "Ex: nome@dominio.com",
    phone: "Ex: +55 (11) 99999-8888",
};

const maskInput = ref(null);

// Máscaras dinâmicas
const maskForType = computed(() => {
    const type = form.type;

    switch (type) {
        case "cpf":
            return [{ mask: "000.000.000-00", lazy: true }];
        case "cnpj":
            return [{ mask: "00.000.000/0000-00", lazy: true }];
        case "phone":
            return [{ mask: "+55 (00) 00000-0000", lazy: true }];
        default:
            return undefined;
    }
});

watch(
    () => form.type,
    (novoType) => {
        if (maskInput.value) {
            maskInput.value.updateOptions(maskForType.value);
        }
    }
);

// Regex simples de validação
const patterns = {
    email: /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/,
    cpf: /^\d{3}\.\d{3}\.\d{3}-\d{2}$/,
    cnpj: /^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/,
    phone: /^\+55\s?\(\d{2}\)\s?\d{5}-\d{4}$/,
    random: /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-4[0-9a-fA-F]{3}-[89ABab][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/,
};

function validarChave(type, valor) {
    if (!valor) return "Informe a chave Pix.";
    const regex = patterns[type];
    if (!regex) return "Selecione um type válido.";
    if (!regex.test(valor))
        return type === "email"
            ? "Digite um e-mail válido."
            : "Formato inválido para o type selecionado.";
    return "";
}

async function fSubmit() {
    const erro = validarChave(form.type, form.key_value);
    if (erro) {
        form.errors.key_value = erro; // Use form.errors do Inertia
        return;
    }

    form.post(`/pix-keys/store`, {
        onSuccess: () => {
            fShowToast(
                "Chave salva com sucesso",
                toastMessage,
                toastType,
                toastVisible,
                showToast,
                "success",
                3000,
                true,
                fHandleCancel
            );
        },
        onError: (errorsResponse) => {
            const fieldKeys = Object.keys(errorsResponse);

            if (fieldKeys.length > 0) {
                const firstErrorKey = fieldKeys[0];

                const errorArray = errorsResponse[firstErrorKey];

                const errorMessage = Array.isArray(errorArray)
                    ? errorArray[0]
                    : errorArray;

                fShowToast(
                    errorMessage,
                    toastMessage,
                    toastType,
                    toastVisible,
                    showToast,
                    "error",
                    3000,
                    true,
                    () => {
                        fHandleCancel();
                        emit("refresh-table");
                    }
                );
            } else {
                fShowToast(
                    "Ocorreu um erro inesperado no servidor.",
                    toastMessage,
                    toastType,
                    toastVisible,
                    showToast,
                    "error",
                    3000,
                    true,
                    fHandleCancel
                );
            }
        },
    });
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            fShowToast(flash.success, "success", 3000);
        }
    }
);

function fHandleCancel() {
    form.reset();
    emit("close");
}
</script>
