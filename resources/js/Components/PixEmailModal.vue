<template>
    <div
        v-if="open"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
    >
        <div
            class="relative bg-white rounded-xl shadow-xl w-full max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-4xl max-h-[90vh] flex flex-col"
        >
            <!-- HEADER -->
            <div class="p-4 border-b flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">
                    COMPROVANTE DE PAGAMENTO
                </h2>

                <div class="flex items-center gap-4 text-gray-600">
                    <button
                        @click="zoomIn"
                        class="hover:text-[#3DA700] relative group"
                    >
                        <ZoomIn />
                        <span class="tooltip">Dar zoom</span>
                    </button>

                    <button
                        @click="zoomOut"
                        class="hover:text-[#3DA700] relative group"
                    >
                        <ZoomOut />
                        <span class="tooltip">Reduzir zoom</span>
                    </button>

                    <button
                        @click="rotate"
                        class="hover:text-[#3DA700] relative group"
                    >
                        <RotateCcw />
                        <span class="tooltip">Rotacionar</span>
                    </button>

                    <button
                        @click="downloadFile"
                        class="hover:text-[#3DA700] relative group"
                    >
                        <Download />
                        <span class="tooltip">Baixar arquivo</span>
                    </button>
                </div>
            </div>

            <!-- CONTENT -->
            <div
                class="flex-1 overflow-auto p-4 flex justify-center items-center bg-gray-50"
            >
                <div v-if="loading" class="text-gray-500 text-center py-8">
                    <svg
                        class="animate-spin h-5 w-5 text-[#3DA700] mx-auto mb-2"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2z"
                        ></path>
                    </svg>
                    <p>Carregando...</p>
                </div>

                <div
                    v-if="error"
                    class="text-center py-10 border border-[#3DA700] border-dashed text-[#3DA700]"
                >
                    {{ error }}
                </div>

                <img
                    v-if="imageUrl"
                    :src="imageUrl"
                    class="rounded shadow max-w-full max-h-[70vh] transition-transform duration-300"
                    :style="{
                        transform:
                            'scale(' + zoom + ') rotate(' + rotation + 'deg)',
                    }"
                />
            </div>

            <!-- FOOTER -->
            <div class="mt-4 p-4 border-t flex justify-end">
                <button
                    @click="$emit('update:open', false)"
                    class="px-6 py-2 bg-[#FF0017] text-white rounded-lg hover:bg-red-700 transition shadow-md hover:shadow-lg"
                >
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <Toast
        v-if="showToast"
        :message="toastMessage"
        :type="toastType"
        position="center"
        size="lg"
    />
</template>

<script setup>
import { ref, watch } from "vue";
import { ZoomIn, ZoomOut, RotateCcw, Download } from "lucide-vue-next";
import Toast from "@/Components/Toast.vue";

const props = defineProps({
    open: Boolean,
    keyPix: [String, Number],
});

const emit = defineEmits(["update:open"]);

const imageUrl = ref(null);
const loading = ref(false);
const error = ref(null);

const zoom = ref(1);
const rotation = ref(0);

function zoomIn() {
    zoom.value += 0.1;
}

function zoomOut() {
    if (zoom.value > 0.2) zoom.value -= 0.1;
}

function rotate() {
    rotation.value += 90;
}

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

function fShowToast(message, type) {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;

    setTimeout(() => {
        showToast.value = false;
    }, 3000); // 3 segundos
}

async function downloadFile() {
    try {
        const res = await fetch(
            `/comprovante/${props.keyPix}/arquivo/download`,
            {
                method: "GET",
            }
        );

        if (!res.ok) throw new Error("Erro ao baixar arquivo");

        const blob = await res.blob();
        const url = URL.createObjectURL(blob);

        const a = document.createElement("a");
        a.href = url;
        a.download = `comprovante_${props.keyPix}`;
        a.click();

        URL.revokeObjectURL(url);
    } catch (e) {
        fShowToast(e, "error");
    }
}

watch(
    () => props.open,
    async (open) => {
        if (!open) return;

        loading.value = true;
        zoom.value = 1;
        rotation.value = 0;
        error.value = null;
        imageUrl.value = null;

        try {
            const res = await fetch(`/comprovante/${props.keyPix}/arquivo`);
            if (!res.ok) throw new Error("Nenhum registro encontrado.");

            const data = await res.json();
            imageUrl.value = data.url;
        } catch (e) {
            error.value = e.message;
            // fShowToast(e, "error");
        } finally {
            loading.value = false;
        }
    }
);
</script>

<style scoped>
.tooltip {
    @apply absolute bottom-[-28px] left-1/2 -translate-x-1/2 bg-black text-white text-xs px-2 py-1 rounded opacity-0 pointer-events-none transition-opacity whitespace-nowrap;
}

.group:hover .tooltip {
    @apply opacity-100;
}

button {
    user-select: none;
}
</style>
