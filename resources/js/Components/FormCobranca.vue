<template>
    <form
        @submit.prevent="submit"
        class="p-4 rounded-xl bg-white max-w-lg w-full"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">NOVA COBRANÇA</h1>
        </div>

        <div class="mb-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Cliente
                    <span class="text-red-500 -ml-2">*</span>
                </legend>
                <select
                    v-model="form.client_id"
                    class="w-full rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700] text-base"
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
                <div class="validator-hint hidden">
                    Selecione um cliente que será cobrado.
                </div>
            </fieldset>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Vencimento
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <input
                        v-model="form.due_date"
                        type="date"
                        class="input input-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        required
                    />
                    <div class="validator-hint hidden">
                        Selecione um cliente que será cobrado.
                    </div>
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Valor
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
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
                        :unmask="false"
                        placeholder="R$ 0,00"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        required
                    />
                </fieldset>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Data de Pagamento
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <input
                        v-model="form.payment_date"
                        type="date"
                        class="input input-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        :class="{ 'text-gray-400': !form.payment_date }"
                        placeholder="Selecione uma data"
                    />
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-base">
                        Enviar PIX?
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <div class="flex gap-4 mt-2">
                        <label class="flex items-center gap-2 text-base">
                            <input
                                type="radio"
                                :value="1"
                                v-model="form.ies_send_pix"
                                :disabled="isPixLocked"
                                @change="fVerificaSelcaoPIX"
                                class="radio checked:text-[#3DA700] border-[#3DA700]"
                            />
                            Sim
                        </label>

                        <label class="flex items-center gap-2 text-base">
                            <input
                                type="radio"
                                :value="0"
                                v-model="form.ies_send_pix"
                                :disabled="isPixLocked"
                                class="radio checked:text-[#FF0017] border-[#FF0017]"
                            />
                            Não
                        </label>
                    </div>
                </fieldset>

                <!-- Modal premium -->
                <dialog ref="premiumModal" class="modal">
                    <div class="modal-box max-w-sm">
                        <h3 class="font-bold text-lg text-center">
                            🚀 Recurso Premium
                        </h3>
                        <p class="py-4 text-center">
                            O envio de PIX com cobrança por e-mail está <br />
                            disponível apenas para clientes
                            <span class="font-semibold text-purple-600"
                                >Premium</span
                            >.
                        </p>
                        <div class="flex justify-center gap-4 mt-4">
                            <button
                                class="btn bg-purple-600 hover:bg-purple-700 text-white px-4 py-2"
                                @click="fRedirecionaAssinatura"
                            >
                                Torne-se Premium
                            </button>
                            <button class="btn" @click="fFechaModalAssinatura">
                                Não, obrigada.
                            </button>
                        </div>
                    </div>
                </dialog>
            </div>
        </div>

        <!-- Descrição -->
        <div class="mb-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend text-base">
                    Descrição
                    <span class="text-red-500 -ml-2">*</span>
                </legend>
                <textarea
                    v-model="form.description"
                    class="textarea textarea-bordered w-full border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    rows="3"
                    placeholder="Alguma informação adicional"
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
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";

import { fFormatDate } from "@/utils/helpers";
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

const emit = defineEmits(["close"]);

const clients = props.adicional;

const formData = ref({});
const isEditing = computed(() => !!formData.value?.id);

// Formulário
const form = useForm({
    client_id: "",
    amount: null,
    amount_display: "",
    description: null,
    due_date: new Date().toISOString().split("T")[0],
    payment_date: null,
    ies_send_pix: 0, // 0 ou 1
});

// Guarda o valor original do ies_send_pix
const originalPixValue = ref(0);

const page = usePage();

// Se não existir auth ou user, cai no {}
const user = page.props.auth?.user ?? {};

// Verifica se ele é premium (1 ou 2)
const isPremium = [1, 2].includes(user.access_permission);

const premiumModal = ref(null);

function fVerificaSelcaoPIX() {
    if (form.ies_send_pix === 1 && !isPremium) {
        premiumModal.value.showModal();

        // Reseta para "Não" (false)
        // para garantir que só "Não" fique selecionado
        form.ies_send_pix = false;
    }
}

// Computed que define se o campo deve ficar bloqueado
const isPixLocked = computed(() => {
    return isEditing.value && originalPixValue.value === 1;
});

// Watch para preencher formulário em edição
watch(
    () => props.data,
    (editValue) => {
        formData.value = editValue;

        if (editValue && Object.keys(editValue).length > 0) {
            form.client_id = editValue.client_id;
            form.amount_display = Number(editValue.valor).toFixed(2).replace('.', ',');
            form.due_date = fFormatDate(editValue.data_vencimento);
            form.payment_date = fFormatDate(editValue.data_pagamento);
            form.ies_send_pix = Number(editValue.ies_envia_pix);
            form.description = editValue.descricao;

            originalPixValue.value = Number(editValue.ies_envia_pix); // salva valor original
        } else {
            form.reset();
            originalPixValue.value = 0;
        }
    },
    { immediate: true }
);

function fFechaModalAssinatura() {
    premiumModal.value.close();
}

function fRedirecionaAssinatura() {
    window.location.href = "/upgrade";
}

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

watch(
    () => page.props.errors,
    (errors) => {
        if (errors && Object.keys(errors).length > 0) {
            toastMessage.value = Object.values(errors)[0];
            toastType.value = "error";
            showToast.value = true;

            setTimeout(() => {
                showToast.value = false;
            }, 3000);
        }
    },
    { deep: true }
);

function fHandleCancel() {
    form.reset();
    emit("close");
}

function submit() {
    if (!form.amount_display) return;

    const numericValue = form.amount_display
        .replace(/\./g, "")
        .replace(",", ".");
    form.amount = parseFloat(numericValue);

    if (formData.value.id) {
        form.post(`/cobrancas/${formData.value.id}/update`, {
            method: "put",
            forceFormData: true,
        });
    } else {
        form.post("/cobrancas/store");
    }
}
</script>
