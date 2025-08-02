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
                                                :src="`/storage/${data.profile_photo}`"
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
                                        @change="handleImageUpload"
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
                    <form class="space-y-3" @submit.prevent="submit()">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">
                                    CNPJ
                                    <span class="text-red-500 -ml-2">*</span>
                                </legend>
                                <IMaskComponent
                                    v-model="form.cnpj"
                                    :mask="'00.000.000/0000-00'"
                                    class="input validator input-lg input-bordered w-full"
                                    :class="{
                                        'input-error': cnpjState === 'invalid',
                                        'input-valid': cnpjState === 'valid',
                                    }"
                                    placeholder="00.000.000/0000-00"
                                    @accept="validateCnpjLive"
                                    required
                                />
                                <div
                                    class="validator-hint"
                                    :class="{ hidden: cnpjState !== 'invalid' }"
                                >
                                    {{ cnpjErrorMessage }}
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
                                    @keyup.enter="buscarCep"
                                />
                                <button
                                    type="button"
                                    class="btn btn-primary h-3/3 input-lg self-center bg-[#3DA700] border-[#3DA700] hover:bg-[#3DA700]"
                                    @click="buscarCep"
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

                        <div class="flex flex-row gap-4 justify-end">
                            <button
                                @click="resetForm"
                                class="btn text-white bg-[#FF0015] hover:bg-[#FF0018] w-1/4 rounded-xl"
                            >
                                CANCELAR
                            </button>

                            <button
                                @click.prevent="submit(isEdit)"
                                class="btn text-white bg-[#3DA700] hover:bg-[#388E3C] w-1/4 rounded-xl"
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
import { ref, reactive } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Sidebar from "@/Components/Sidebar.vue";
import Toast from "@/Components/Toast.vue"; // ajuste o caminho conforme sua estrutura
import { fValidaCNPJ } from "@/utils/validators";
import { useCepService } from "@/utils/cepService"; // Importe o serviço
import { MagnifyingGlassIcon } from "@heroicons/vue/24/outline"; // Importe o ícone de pesquisa

import { IMaskComponent } from "vue-imask";

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

const toast = reactive({
    visible: false,
    message: "",
});

function mostrarToast(
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

const data = props.data;
const isEdit = props.isEdit;

if (isEdit) {
    console.log("CNPJ", data.profile_photo);
}

let form = useForm({
    cnpj: data?.cnpj ?? null,
    identification: data?.identification ?? null,
    email: data?.email ?? null,
    phone: data?.phone ?? null,
    street: data?.street ?? null,
    number: data?.number ?? null,
    complement: data?.complement ?? null,
    district: data?.district ?? null,
    city: data?.city ?? null,
    state: data?.state ?? null,
    zip_code: data?.zip_code ?? null,
    profile_photo: data?.profile_photo ?? null,
});

function resetForm() {
    Object.assign(form, props.data); // Restaura o formulário original
}

function fValidaRequestForm() {
    const camposObrigatorios = {
        identification: "Identificação",
        cnpj: "CNPJ",
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

    // Verifica campos obrigatórios padrão
    for (const [campo, nomeCampo] of Object.entries(camposObrigatorios)) {
        const valor = form[campo];
        if (!valor || typeof valor !== "string" || valor.trim() === "") {
            erros.push(`O campo "${nomeCampo}" é obrigatório.`);
        }
    }

    // Verifica se algum campo de endereço foi preenchido
    const algumEnderecoPreenchido = Object.keys(camposEndereco).some(
        (campo) => {
            const valor = form[campo];
            return valor && valor.toString().trim() !== "";
        }
    );

    // Se um campo de endereço foi preenchido, todos viram obrigatórios
    if (algumEnderecoPreenchido) {
        for (const [campo, nomeCampo] of Object.entries(camposEndereco)) {
            const valor = form[campo];
            if (!valor || valor.toString().trim() === "") {
                erros.push(
                    `O campo "${nomeCampo}" é obrigatório se o endereço for informado.`
                );
            }
        }
    }

    if (erros.length > 0) {
        mostrarToast(erros[0]);
        return false;
    }

    return true;
}

function submit() {
    if (!fValidaRequestForm()) {
        return;
    }

    if (props.isEdit && data.id) {
        form.post(`/profile-mei/${data.id}/update`, {
            method: "put",
            forceFormData: true,
        });
    } else {
        form.post("/profile-mei/store");
    }
}
const previewImage = ref(null);

const handleImageUpload = (event) => {
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

const buscarCep = async () => {
    try {
        if (!form.zip_code || form.zip_code.replace(/\D/g, "").length !== 8) {
            throw new Error("CEP inválido ou incompleto");
        }

        const endereco = await buscarEnderecoPorCep(form.zip_code);

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

// Validação automática quando o CNPJ estiver completo
// Estados do CNPJ
const cnpjState = ref("empty"); // 'empty', 'valid', 'invalid', 'incomplete'
const cnpjErrorMessage = ref("");

// Validação em tempo real
function validateCnpjLive() {
    let cleanCnpj = form.cnpj?.replace(/[^\d]+/g, "") || "";

    if (cleanCnpj.length === 0) {
        cnpjState.value = "empty";
        return;
    }

    if (cleanCnpj.length < 14) {
        cnpjState.value = "incomplete";
        cnpjErrorMessage.value = "CNPJ incompleto";
        return;
    }

    if (fValidaCNPJ(cleanCnpj)) {
        cnpjState.value = "valid";
    } else {
        cnpjState.value = "invalid";
        cnpjErrorMessage.value = "CNPJ inválido";
    }
}
</script>
