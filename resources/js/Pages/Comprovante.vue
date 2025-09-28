<template>
    <div
        class="flex flex-col items-center justify-center min-h-screen bg-[#3DA700] p-4 space-y-6"
    >
        <!-- Logo -->
        <div class="w-full max-w-xs mx-auto">
            <img
                class="w-full h-auto"
                src="/resources/img/logo.png"
                alt="Logo"
            />
        </div>

        <!-- Se já foi pago -->
        <div
            v-if="infoData.ies_paid"
            class="card w-full max-w-xl bg-base-100 shadow-2xl rounded-xl"
        >
            <div class="card-body text-center space-y-4">
                <h1
                    class="text-2xl font-extrabold text-gray-800 flex items-center justify-center gap-2"
                >
                    Pagamento confirmado
                    <BadgeCheck class="w-6 h-6 text-green-500" />
                </h1>
                <p class="text-gray-700">
                    Obrigado, <strong>{{ infoData.client_name }}</strong
                    >!
                </p>
                <p class="text-gray-600">
                    Recebemos o comprovante referente à cobrança
                    <strong>#{{ infoData.charge_id }}</strong
                    >.
                </p>
                <p class="text-green-600 font-semibold">
                    Agradecemos pela sua confiança.
                </p>
            </div>
        </div>

        <!-- Se ainda não foi pago -->
        <div
            v-else
            class="card w-full max-w-xl bg-base-100 shadow-2xl rounded-xl"
        >
            <div class="card-body space-y-4">
                <!-- Título -->
                <div class="text-center space-y-2">
                    <h1 class="text-2xl font-extrabold text-gray-800">
                        COBRANÇA - {{ infoData.charge_id }}
                    </h1>
                    <p class="text-sm text-gray-500">
                        Confira os detalhes abaixo para realizar o pagamento
                    </p>
                </div>

                <!-- Mensagem ao cliente -->
                <div class="text-black">
                    <p>
                        Olá, <strong>{{ infoData.client_name }}</strong
                        >!
                    </p>
                    <p>
                        Você recebeu uma cobrança emitida por
                        <strong>{{ infoData.mei_identification }}</strong>
                        -
                        <span class="text-black">
                            <strong>{{ infoData.mei_cnpj }}</strong> </span
                        >.
                    </p>
                </div>

                <!-- Detalhes da cobrança -->
                <ul class="ml-2 list-disc list-inside">
                    <li>
                        <strong>Valor:</strong>
                        {{ formatCurrency(infoData.amount) }}
                    </li>
                    <li>
                        <strong>Descrição:</strong>
                        {{ infoData.description }}
                    </li>
                </ul>

                <!-- Caixa PIX com botão copiar -->
                <div class="space-y-3">
                    <p class="text-sm text-gray-600">
                        Para realizar o pagamento, você pode:
                    </p>
                    <div class="flex flex-col items-center space-y-3">
                        <p class="text-sm text-gray-700">
                            Escanear o QR Code abaixo
                        </p>
                        <qrcode-vue :value="pixCode" :size="200" level="M" />
                        <p class="text-sm text-gray-700">
                            Ou copiar o código PIX para pagamento no seu banco
                        </p>

                        <div class="relative w-full">
                            <div
                                class="bg-gray-100 rounded-lg p-3 pr-12 text-sm font-mono break-words max-h-32 overflow-y-auto border-2 border-solid border-[#3DA700]"
                            >
                                {{ pixCode }}
                            </div>
                            <button
                                @click="copyToClipboard"
                                class="absolute top-1/2 right-2 -translate-y-1/2 p-2 rounded-md hover:bg-gray-200 transition"
                                type="button"
                            >
                                <Copy class="w-5 h-5 text-gray-600" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Formulário de envio -->
                <form @submit.prevent="submit" class="space-y-4">
                    <p class="text-sm text-center text-gray-700">
                        Após o pagamento, envie o comprovante.
                    </p>

                    <div class="form-control">
                        <label class="label mb-2">
                            <span class="label-text">
                                Anexar comprovante
                                <p class="text-red-500 text-xs mt-1">
                                    (apenas arquivos PDF, JPG e JPEG serão
                                    aceitos)
                                </p>
                            </span>
                        </label>
                        <input
                            type="file"
                            @change="handleFileChange"
                            accept=".pdf,.jpg,.jpeg"
                            class="file-input file-input-bordered w-full border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                        />
                    </div>

                    <button
                        type="submit"
                        class="btn w-full text-white rounded-xl"
                        :class="
                            form.processing
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-[#3DA700] hover:bg-[#388E3C]'
                        "
                        :disabled="form.processing"
                    >
                        <span
                            v-if="form.processing"
                            class="loading loading-spinner mr-2"
                        ></span>
                        ENVIAR
                    </button>
                </form>

                <!-- Alerta de aviso -->
                <div
                    role="alert"
                    class="alert alert-warning alert-soft mt-4 shadow-md flex items-start gap-2"
                >
                    <TriangleAlert class="w-6 h-6 text-red-500 flex-shrink-0" />
                    <div class="text-sm leading-relaxed text-gray-600">
                        <strong class="text-red-500">ATENÇÃO:</strong> Antes de
                        realizar o pagamento, confirme que você reconhece esta
                        cobrança e que ela corresponde a um serviço ou produto
                        contratado com
                        <strong>{{ infoData.mei_identification }}</strong
                        >.<br />
                        Em caso de dúvidas, entre em contato diretamente com o
                        emissor antes de prosseguir.<br /><br />

                        <div
                            v-if="infoData.mei_email"
                            class="flex items-center gap-2 text-gray-700 mt-1"
                        >
                            <Mail class="w-4 h-4 text-gray-600" />
                            <a
                                :href="`mailto:${infoData.mei_email}`"
                                class="hover:underline text-blue-600"
                            >
                                {{ infoData.mei_email }}
                            </a>
                        </div>

                        <div
                            v-if="infoData.mei_phone"
                            class="flex items-center gap-2 text-gray-700 mt-1"
                        >
                            <PhoneCall class="w-4 h-4 text-gray-600" />
                            <a
                                :href="`https://wa.me/${infoData.mei_phone.replace(
                                    /\D/g,
                                    ''
                                )}`"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="hover:underline text-blue-600"
                            >
                                {{ infoData.mei_phone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <Toast
                v-if="showToast"
                :message="toastMessage"
                :type="toastType"
                position="center"
                size="lg"
                :duration="1000"
            />
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Toast from "@/Components/Toast.vue";

import QrcodeVue from "qrcode.vue";
import {
    Copy,
    TriangleAlert,
    Mail,
    PhoneCall,
    BadgeCheck,
} from "lucide-vue-next";

const props = defineProps({
    data: Object,
});

const infoData = ref(props.data || {});

const form = useForm({
    comprovante: null,
});

const formatCurrency = (value) => {
    return parseFloat(value).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL",
    });
};

const pixCode = ref(infoData.value.pix_key);

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

const handleFileChange = (e) => {
    const file = e.target.files[0];

    if (!file) return;

    const allowedTypes = ["application/pdf", "image/jpeg"];
    if (!allowedTypes.includes(file.type)) {
        toastMessage.value = "Apenas arquivos PDF, JPG e JPEG são aceitos.";
        toastType.value = "error";
        showToast.value = true;

        form.comprovante = null;
        e.target.value = ""; // limpa o campo input file

        return;
    }

    form.comprovante = file;
};

const showToastMessage = (message, type = "info") => {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;

    setTimeout(() => {
        showToast.value = false;
    }, 2000);
};

const copyToClipboard = () => {
    navigator.clipboard
        .writeText(pixCode.value)
        .then(() => {
            showToastMessage("Código copiado com sucesso!.", "success");
        })
        .catch(() => {
            showToastMessage("Falha ao copiar o código PIX.", "error");
        });
};

const submit = () => {
    // Verifica se anexou um arquivo
    if (!form.comprovante) {
        showToastMessage(
            "Por favor, anexe um comprovante antes de enviar.",
            "error"
        );
        return;
    }

    form.post(`/comprovante/${infoData.value.key}`, {
        forceFormData: true,
        onSuccess: () => {
            window.location.reload();
        },
        onError: (errors) => {
            if (errors.comprovante)
                showToastMessage(errors.comprovante, "error");
        },
    });
};
</script>
