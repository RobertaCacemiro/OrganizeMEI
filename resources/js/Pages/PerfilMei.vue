<template>
    <Sidebar>
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row gap-10">
                <div class="w-full md:w-1/3 lg:w-1/3">
                    <div class="card bg-base-100 shadow-xl w-full">
                        <div class="card-body">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    FOTO DE PERFIL
                                </legend>
                                <div class="flex flex-col items-center">
                                    <div class="avatar mb-5">
                                        <div class="w-64 rounded-full">
                                            <img
                                                v-if="previewImage"
                                                :src="previewImage"
                                                class="w-full h-full object-cover"
                                            />
                                            <img
                                                v-else-if="data?.profile_photo"
                                                :src="data.profile_photo"
                                                class="w-full h-full object-cover"
                                            />
                                            <img
                                                v-else
                                                src="https://placehold.co/200x200"
                                                class="w-full h-full object-cover"
                                            />
                                        </div>
                                    </div>

                                    <input
                                        type="file"
                                        @change="fHandleImageUpload"
                                        class="file-input file-input-bordered w-full"
                                        accept="image/*"
                                        required
                                    />

                                    <p
                                        class="text-sm text-red-600 mt-2 text-center"
                                    >
                                        Formatos suportados: JPG, PNG (Max. 2MB)
                                    </p>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <!-- Seção do Formulário -->
                <div class="w-full md:w-2/3 lg:w-3/4">
                    <form class="space-y-3" @submit.prevent="fSubmit()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    CNPJ
                                    <span class="text-red-500 -ml-2">*</span>
                                </legend>
                                <IMaskComponent
                                    v-model="form.cnpj"
                                    v-bind="isEdit ? { readonly: true } : {}"
                                    :mask="mask"
                                    placeholder="00.000.000/0000-00"
                                    @accept="fValidateCNPJLivre"
                                    class="input validator input-lg input-bordered w-full transition-colors"
                                    :class="{
                                        '!border-red-500 !focus:border-red-500':
                                            cnpjState === 'invalid',
                                        '!border-green-500 !focus:border-green-500':
                                            cnpjState === 'valid',
                                    }"
                                />
                                <div class="mt-1 h-5">
                                    <p
                                        v-if="cnpjState === 'invalid'"
                                        class="text-red-600 text-sm flex items-center gap-1"
                                    >
                                        CNPJ inválido
                                    </p>
                                </div>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    IDENTIFICAÇÃO
                                    <span class="text-red-500 -ml-2">*</span>
                                </legend>
                                <input
                                    v-model="form.identification"
                                    type="text"
                                    class="input validator input-lg input-bordered w-full"
                                    placeholder="Nome Fantasia ou Razão Social"
                                    minlength="3"
                                    maxlength="200"
                                    pattern=".*\S.*"
                                    required
                                />
                                <div class="validator-hint hidden">
                                    Informe a identificação da empresa.
                                </div>
                            </fieldset>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    E-MAIL
                                    <span class="text-red-500 -ml-2">*</span>
                                </legend>
                                <input
                                    v-model="form.email"
                                    type="email"
                                    class="input validator input-lg input-bordered w-full"
                                    placeholder="email@exemplo.com.br"
                                    required
                                />
                                <div class="validator-hint hidden">
                                    Informe com um e-mail válido.
                                </div>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    TELEFONE
                                    <span class="text-red-500 -ml-2">*</span>
                                </legend>
                                <IMaskComponent
                                    v-model="form.phone"
                                    :mask="[
                                        { mask: '(00) 0000-0000' }, // telefone fixo
                                        { mask: '(00) 00000-0000' }, // telefone cotando com o digito 9 na frente celular
                                    ]"
                                    type="text"
                                    class="input validator input-lg input-bordered w-full"
                                    placeholder="(00) 00000-0000"
                                    pattern="^\(\d{2}\)\s?\d{4,5}-\d{4}$"
                                    required
                                />
                                <div class="validator-hint hidden">
                                    Informe com um telefone válido.
                                </div>
                            </fieldset>
                        </div>

                        <!-- Divisor  -->
                        <div class="divider text-md">ENDEREÇO</div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">CEP</legend>
                            <div class="flex gap-2">
                                <IMaskComponent
                                    v-model="form.zip_code"
                                    :mask="'00000-000'"
                                    type="text"
                                    class="input input-lg input-bordered w-1/3"
                                    placeholder="00000-000"
                                    minlength="9"
                                    maxlength="9"
                                    @keyup.enter="fBuscarCEP"
                                />
                                <button
                                    type="button"
                                    class="btn btn-primary h-3/3 input-lg self-center bg-[#3DA700] border-[#3DA700] hover:bg-[#3DA700]"
                                    @click="fBuscarCEP"
                                    title="Buscar endereço pelo CEP"
                                >
                                    <MagnifyingGlassIcon
                                        class="h-5 w-5 text-white"
                                    />
                                </button>
                            </div>
                            <p class="label">Opcional</p>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">LOGRADOURO</legend>
                            <input
                                v-model="form.street"
                                type="text"
                                class="input input-lg input-bordered w-full"
                                placeholder="Nome da rua"
                            />
                            <p class="label">Opcional</p>
                        </fieldset>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">NÚMERO</legend>
                                <input
                                    v-model="form.number"
                                    type="number"
                                    class="input input-lg input-bordered w-full"
                                    placeholder="Número"
                                />
                                <p class="label">Opcional</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    COMPLEMENTO
                                </legend>
                                <input
                                    v-model="form.complement"
                                    type="text"
                                    class="input input-lg input-bordered w-full"
                                    placeholder="Complemento"
                                />
                                <p class="label">Opcional</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">BAIRRO</legend>
                                <input
                                    v-model="form.district"
                                    type="text"
                                    class="input input-lg input-bordered w-full"
                                    placeholder="Bairro"
                                />
                                <p class="label">Opcional</p>
                            </fieldset>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">CIDADE</legend>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="input input-lg input-bordered w-full"
                                    placeholder="Cidade"
                                />
                                <p class="label">Opcional</p>
                            </fieldset>

                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">ESTADO</legend>
                                <input
                                    v-model="form.state"
                                    type="text"
                                    class="input input-lg input-bordered w-full"
                                    placeholder="Estado"
                                />
                                <p class="label">Opcional</p>
                            </fieldset>
                        </div>

                        <div
                            class="flex flex-col md:flex-row gap-4 justify-end"
                        >
                            <button
                                type="button"
                                @click="fResetForm"
                                :disabled="!formModified && isEdit"
                                class="btn text-white bg-[#FF0015] hover:bg-[#FF0020] w-full md:w-1/4 rounded-xl"
                            >
                                CANCELAR
                            </button>

                            <button
                                type="submit"
                                @click.prevent="fSubmit(isEdit)"
                                :disabled="!formModified && isEdit"
                                class="btn text-white bg-[#3DA700] hover:bg-[#388E3C] w-full md:w-1/4 rounded-xl"
                            >
                                SALVAR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <!-- Exemplo de chamada do toast -->
            <Toast
                v-if="toast.visible"
                :message="toast.message"
                :type="toast.type"
                :position="toast.position"
                :duration="toast.duration"
                :size="toast.size"
            />
        </div>
    </Sidebar>
</template>

<script setup>
import { ref, reactive, watch, nextTick } from "vue";
import { useForm, usePage, router } from "@inertiajs/vue3";
import { onMounted, onBeforeUnmount } from "vue";
import { IMaskComponent } from "vue-imask";

import Sidebar from "@/Components/Sidebar.vue";
import Toast from "@/Components/Toast.vue";

import { fValidaCnpj } from "@/utils/validators";
import { useCepService } from "@/utils/cepService";
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline";

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
    },
    isEdit: {
        type: Boolean,
        default: false,
    },
});

let removeBeforeHook = null;

function fVerificaCadastro(event) {
    const meiId = usePage().props.auth?.user?.mei_id;
    const targetUrl = event.detail.visit.url.pathname;
    const rotasLiberadas = [
        "/logout",
        "/login",
        "/register",
        "/perfil-mei",
        "/profile-mei/store",
    ];

    if (rotasLiberadas.some((r) => targetUrl.startsWith(r))) {
        return true;
    }

    if (!meiId) {
        fShowToast(
            "Você precisa cadastrar o perfil MEI para continuar!",
            "info",
            "top-center",
            4000,
            "xl"
        );
        return false;
    }
}

onMounted(() => {
    removeBeforeHook = router.on("before", fVerificaCadastro);
});

onBeforeUnmount(() => {
    if (typeof removeBeforeHook === "function") {
        removeBeforeHook();
    }
});

const toast = reactive({
    visible: false,
    message: "",
});

function fShowToast(
    msg,
    tipo = "error",
    posicao = "bottom-right",
    duracao = 3000,
    tamanho = "md"
) {
    toast.message = msg;
    toast.type = tipo;
    toast.position = posicao;
    toast.duration = duracao;
    toast.size = tamanho;
    toast.visible = true;

    setTimeout(() => {
        toast.visible = false;
    }, duracao);
}

const data = props.data ?? {};
const isEdit = ref(props.isEdit);

const form = useForm({
    cnpj: data?.cnpj ?? "",
    phone: data?.phone ?? "",
    zip_code: data?.zip_code ?? "",
    identification: data?.identification ?? "",
    email: data?.email ?? "",
    street: data?.street ?? "",
    number: data?.number ?? null,
    complement: data?.complement ?? "",
    district: data?.district ?? "",
    city: data?.city ?? "",
    state: data?.state ?? "",
    profile_photo: data?.profile_photo ?? null,
});

let originalData = {};
const formModified = ref(false);
const isEditInternal = ref(props.isEdit || false);

onMounted(() => {
    if (isEditInternal.value) {
        originalData = JSON.parse(JSON.stringify(form.data()));
    }
});

watch(
    form,
    (newVal) => {
        if (!isEdit.value || !originalData) {
            formModified.value = false;
            return;
        }

        formModified.value = Object.keys(newVal).some(
            (key) => newVal[key] !== originalData[key]
        );
    },
    { deep: true }
);

function fResetForm() {
    if (isEdit.value && originalData) {
        Object.keys(originalData).forEach((key) => {
            form[key] = originalData[key] ?? "";
        });
    } else {
        form.reset();
    }

    formModified.value = false;
}

function fValidaRequestForm() {
    const camposObrigatorios = {
        cnpj: "CNPJ",
        identification: "Identificação",
        email: "E-mail",
        phone: "Telefone",
    };

    const camposEndereco = {
        street: "Rua",
        number: "Número",
        complement: "Complemento",
        district: "Bairro",
        city: "Cidade",
        state: "Estado",
        zip_code: "CEP",
    };

    const erros = [];

    for (const [campo, nomeCampo] of Object.entries(camposObrigatorios)) {
        const valor = form[campo];
        if (!valor || typeof valor !== "string" || valor.trim() === "") {
            erros.push(`O campo ${nomeCampo} é obrigatório.`);
        } else if (campo === "cnpj") {
            const cleanCnpj = valor.replace(/[^\d]/g, "");
            if (!fValidaCnpj(cleanCnpj)) {
                erros.push(`O CNPJ informado é inválido.`);
            }
        }
    }

    const algumEnderecoPreenchido = Object.keys(camposEndereco).some(
        (campo) => {
            const valor = form[campo];
            return valor && valor.toString().trim() !== "";
        }
    );

    if (algumEnderecoPreenchido) {
        for (const [campo, nomeCampo] of Object.entries(camposEndereco)) {
            const valor = form[campo];
            if (!valor || valor.toString().trim() === "") {
                erros.push(
                    `O campo ${nomeCampo} é obrigatório para completar o endereço.`
                );
            }
        }
    }

    if (erros.length > 0) {
        fShowToast(erros[0], "error", "top-center", 4000, "xl");
        return false;
    }

    return true;
}

const previewImage = ref(null);

const fHandleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.profile_photo = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const { buscarEnderecoPorCep } = useCepService();

const fBuscarCEP = async () => {
    fShowToast(
        "Realizando a busca de endereço por CEP!",
        "info",
        "center",
        10000,
        "xl"
    );

    try {
        if (!form.zip_code || form.zip_code.replace(/\D/g, "").length !== 8) {
            throw new Error("CEP inválido ou incompleto");
        }

        const endereco = await buscarEnderecoPorCep(form.zip_code);

        form.street = endereco.street;
        form.district = endereco.district;
        form.city = endereco.city;
        form.state = endereco.state;
        form.complement = endereco.complement;

        fShowToast(
            "Endereço encontrado com sucesso!",
            "success",
            "center",
            1000
        );
    } catch (error) {
        fShowToast(error.message, "error", "center", 4000, "xl");
    }
};

const mask = "00.000.000/0000-00";
const cnpjState = ref("incomplete");

async function fValidateCNPJLivre() {
    await nextTick();

    const cleanCnpj = form.cnpj?.replace(/[^\d]+/g, "") || "";

    if (!cleanCnpj) {
        cnpjState.value = "incomplete";
        return;
    }

    if (cleanCnpj.length < 14) {
        cnpjState.value = "incomplete";
        return;
    }

    cnpjState.value = fValidaCnpj(cleanCnpj) ? "valid" : "invalid";
}

function fSubmit() {
    if (!fValidaRequestForm()) {
        return;
    }

    if (props.isEdit && data.id) {
        form.post(`/profile-mei/${data.id}/update`, {
            method: "put",
            forceFormData: true,
            onSuccess: () => {
                fShowToast(
                    "Perfil MEI atualizado com sucesso!.",
                    "info",
                    "center",
                    1000,
                    "xl"
                );
                setTimeout(() => {
                    router.visit("/perfil-mei");
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
                    fShowToast(errorMessage, "error", "center", 1000, "xl");
                } else {
                    fShowToast(
                        "Ocorreu um erro inesperado no servidor.",
                        "info",
                        "center",
                        1000,
                        "xl"
                    );
                }
            },
        });
    } else {
        form.post(`/profile-mei/store`, {
            onSuccess: () => {
                fShowToast(
                    "Perfil MEI salvo com sucesso!.",
                    "info",
                    "center",
                    1000,
                    "xl"
                );
                setTimeout(() => {
                    router.visit("/perfil-mei");
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
                    fShowToast(errorMessage, "error", "center", 1000, "xl");
                } else {
                    fShowToast(
                        "Ocorreu um erro inesperado no servidor.",
                        "info",
                        "center",
                        1000,
                        "xl"
                    );
                }
            },
        });
    }
}
</script>
