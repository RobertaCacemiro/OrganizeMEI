<template>
  <div
    v-if="open"
    class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4"
  >
    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 w-full max-w-lg">
      <h2 class="text-xl sm:text-2xl font-bold mb-4 text-gray-800">Detalhes do PIX</h2>

      <div v-if="loading" class="text-gray-500 text-center">Carregando...</div>

      <div v-else>
        <p><span class="font-semibold">ID da Cobrança:</span> {{ dados.charge_id }}</p>
        <p><span class="font-semibold">Cliente:</span> {{ dados.cliente_name }}</p>
        <p><span class="font-semibold">Valor:</span> {{ dados.valor }}</p>
        <p><span class="font-semibold">Status:</span> {{ dados.status }}</p>
        <p><span class="font-semibold">Data de Envio:</span> {{ dados.data_envio }}</p>
        <p><span class="font-semibold">Data de Pagamento:</span> {{ dados.data_pagamento }}</p>
      </div>

      <div class="mt-6 flex justify-end">
        <button
          @click="close"
          class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
        >
          Fechar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import axios from "axios";

const props = defineProps({
  id: { type: [Number, String], required: true },
  open: { type: Boolean, default: false },
});

const emit = defineEmits(["update:open"]);

const dados = ref({});
const loading = ref(false);

watch(
  () => props.open,
  async (val) => {
    if (val) {
      await carregarDados();
    }
  }
);

async function carregarDados() {
  loading.value = true;
  try {
    const response = await axios.get(`/boletos/${props.id}/pix`);
    dados.value = response.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
}

function close() {
  emit("update:open", false);
}
</script>
