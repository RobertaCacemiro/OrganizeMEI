<template>
    <form
        @submit.prevent="fSubmit"
        class="p-4 rounded-xl bg-white w-full max-w-10xl mx-auto"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">CADASTRO CLIENTE</h1>
        </div>

        <!-- Dados Pessoais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        CNPJ/CPF
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <IMaskComponent
                        v-model:modelValue="form.cpf_cnpj"
                        :mask="[
                            {
                                mask: '000.000.000-00',
                                lazy: true,
                            },
                            {
                                mask: '00.000.000/0000-00',
                                lazy: true,
                            },
                        ]"
                        v-bind="isEditing ? { readonly: true } : {}"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="00.000.000/0000-00"
                        @blur="fValidarCpfCnpj"
                        required
                    />

                    <div class="validator-hint hidden">
                        Informe o CNPJ ou CPF do cliente.
                    </div>
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Identificação
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <input
                        v-model="form.name"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="Identificação pessoa juridica ou física."
                        required
                    />
                    <div class="validator-hint hidden">
                        Informe a identificação do cliente.
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Telefone
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <IMaskComponent
                        v-model:modelValue="form.phone"
                        :mask="[
                            {
                                mask: '(00) 0000-0000',
                                lazy: true,
                            },
                            {
                                mask: '(00) 00000-0000',
                                lazy: true,
                            },
                        ]"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="(47) 98888-8888"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o telefone para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        E-mail
                        <span class="text-red-500 -ml-2">*</span>
                    </legend>
                    <input
                        v-model="form.email"
                        type="email"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="exemplo@gmail.com"
                        required
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
        </div>

        <!-- Endereço -->
        <div class="divider">ENDEREÇO</div>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">CEP</legend>
                <div class="flex gap-2 md:col-span-2">
                    <IMaskComponent
                        v-model:modelValue="form.zip_code"
                        :mask="[
                            {
                                mask: '00000-000',
                                lazy: true,
                            },
                        ]"
                        class="input input-bordered flex-grow border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="00000-000"
                    />

                    <button
                        type="button"
                        class="btn btn-primary bg-[#3DA700] border-[#3DA700] hover:bg-[#3DA700] px-4 py-2 flex items-center justify-center"
                        @click="fBuscarCEP"
                        title="Buscar endereço pelo CEP"
                    >
                        <Search class="h-5 w-5 text-white" />
                    </button>
                </div>
            </fieldset>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Logradouro
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.street"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="Rua/Avenida/Travessa"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Número
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.number"
                        type="number"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="10"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Complemento
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.complement"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="Casa/Sala 01"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Bairro
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.district"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="Cidade Nova"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Cidade
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.city"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="Itajaí"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
            <div>
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">
                        Estado (UF)
                        <!-- <span class="text-red-500 -ml-2">*</span> -->
                    </legend>
                    <input
                        v-model="form.state"
                        type="text"
                        class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        placeholder="SC"
                        maxlength="2"
                    />
                    <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
                </fieldset>
            </div>
        </div>

        <!-- Observações -->
        <div class="mb-2">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    Observações
                    <!-- <span class="text-red-500 -ml-2">*</span> -->
                </legend>
                <textarea
                    v-model="form.notes"
                    class="textarea textarea-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    rows="3"
                    placeholder="Anotações pertinentes sobre o cliente."
                ></textarea>
                <!-- <div class="validator-hint hidden">
                        Informe o e-mail para contato do cliente.
                    </div> -->
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
// Vue
import { ref, computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { IMaskComponent } from "vue-imask";

// Icon
import { Search } from "lucide-vue-next";

// Componentes e funções
import Toast from "@/Components/Toast.vue";
import { fValidaCpfCnpj } from "@/utils/validators";
import { useCepService } from "@/utils/cepService";

const props = defineProps({
    data: Object,
    adicional: Object,
});
const emit = defineEmits(["close"]);

const formData = ref(props.data || {});
const isEditing = computed(() => !!formData.value?.id);

const form = useForm({
    cpf_cnpj: formData.value?.cpf_cnpj || "",
    name: formData.value?.name || "",
    email: formData.value?.email || "",
    phone: formData.value?.phone || "",
    street: formData.value?.street || "",
    number: formData.value?.number || "",
    complement: formData.value?.complement || "",
    district: formData.value?.district || "",
    city: formData.value?.city || "",
    state: formData.value?.state || "",
    zip_code: formData.value?.zip_code || "",
    notes: formData.value?.notes || "",
});

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

const { fBuscaEnderecoCEP } = useCepService();

// Valida CNPJ/CPF
function fValidarCpfCnpj() {
    if (!fValidaCpfCnpj(form.cpf_cnpj)) {
        // Mostra toast de erro
        toastMessage.value = "CPF/CNPJ inválido.";
        toastType.value = "error";
        showToast.value = true;

        // Esconde após 3 segundos
        setTimeout(() => {
            showToast.value = false;
        }, 3000);

        return false;
    } else {
        return true;
    }
}

// 🔹 Watch para preencher dados quando editar
watch(
    () => props.data,
    (editValue) => {
        formData.value = editValue;

        if (formData.value?.id) {
            console.log("teste");
        }

        if (editValue && Object.keys(editValue).length > 0) {
            form.cpf_cnpj = editValue.cpf_cnpj;
            form.name = editValue.name;
            form.email = editValue.email;
            form.phone = editValue.phone;
            form.notes = editValue.observacao;

            // Endereço
            form.street = editValue.street;
            form.number = editValue.number;
            form.complement = editValue.complement;
            form.district = editValue.district;
            form.city = editValue.city;
            form.state = editValue.state;
            form.zip_code = editValue.zip_code;
        }
    },
    { immediate: true }
);

const page = usePage();
// Watch para mostrar erros vindos do backend
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

// Busca endereço pelo CEP
const fBuscarCEP = async () => {
    try {
        if (!form.zip_code || form.zip_code.replace(/\D/g, "").length !== 8) {
            throw new Error("CEP inválido ou incompleto");
        }

        const endereco = await fBuscaEnderecoCEP(form.zip_code);

        // Atualiza os campos do formulário
        form.street = endereco.street;
        form.district = endereco.district;
        form.city = endereco.city;
        form.state = endereco.state;
        form.complement = endereco.complement;
    } catch (error) {
        console.error("Erro ao buscar CEP:", error.message);
        // Você pode adicionar uma notificação de erro aqui se quiser
    }
};

// Cancelar formulário
function fHandleCancel() {
    form.reset();
    emit("close");
}

// Submete o formulário
function fSubmit() {
    if (!fValidarCpfCnpj()) return; // Bloqueia o submit se for inválido

    if (formData.value.id) {
        // update
        form.post(`/clientes/${formData.value.id}/update`, {
            method: "put",
            forceFormData: true,
        });
    } else {
        // insert
        form.post("/clientes/store");
    }
}
</script>
