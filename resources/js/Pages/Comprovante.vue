<!-- <template>
  <div class="min-h-screen bg-[#3DA700]">
    <h1 class="text-white text-center text-2xl font-bold">
      Minha Tela Verde
    </h1>
    <div>
        <h3>TESTE</h3>
    </div>
  </div>
</template> -->


<template>
  <div class="max-w-lg mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-xl font-bold mb-4">Enviar Comprovante</h1>

    <p><strong>Cliente:</strong> {{ payment.cliente }}</p>
    <p><strong>Descrição:</strong> {{ payment.descricao }}</p>
    <p><strong>Valor:</strong> R$ {{ payment.valor }}</p>
    <p><strong>Cidade:</strong> {{ payment.cidade }}</p>

    <form @submit.prevent="submit" class="mt-6">
      <input type="file" @change="onFileChange" class="mb-4" />

      <button
        type="submit"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Enviar Comprovante
      </button>
    </form>
  </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";

const props = defineProps({ payment: Object });

const form = useForm({
  comprovante: null,
});

const onFileChange = (e) => {
  form.comprovante = e.target.files[0];
};

const submit = () => {
  form.post(`/comprovante/${props.payment.key}`);
};
</script>
