<script setup>
import { ref, defineEmits, defineProps } from 'vue';
import { Search } from 'lucide-vue-next';

const props = defineProps({
  modelValue: String,
  disabled: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'cep-data']);

const isLoading = ref(false);
const error = ref(null);

const buscarCep = async () => {
  const cepNumerico = props.modelValue.replace(/\D/g, '');

  if (cepNumerico.length !== 8) {
    error.value = 'CEP deve conter 8 dígitos';
    return;
  }

  isLoading.value = true;
  error.value = null;

  try {
    const response = await fetch(`https://brasilapi.com.br/api/cep/v2/${cepNumerico}`);

    if (!response.ok) {
      throw new Error('CEP não encontrado');
    }

    const data = await response.json();
    emit('cep-data', {
      rua: data.street || '',
      bairro: data.neighborhood || '',
      cidade: data.city || '',
      estado: data.state || '',
      complemento: data.complement || ''
    });

  } catch (err) {
    error.value = err.message;
    emit('cep-data', null);
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="w-full">
    <label class="label">
      <span class="label-text">CEP</span>
    </label>
    <div class="flex items-center gap-2">
      <input
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
        type="text"
        :disabled="disabled"
        class="input input-bordered flex-1"
        placeholder="00000-000"
        v-mask="'#####-###'"
        @keyup.enter="buscarCep"
      />
      <button
        type="button"
        @click="buscarCep"
        class="btn btn-primary"
        :disabled="isLoading || disabled || !modelValue || modelValue.replace(/\D/g, '').length !== 8"
      >
        <Search v-if="!isLoading" class="w-4 h-4" />
        <span v-else class="loading loading-spinner loading-xs"></span>
      </button>
    </div>
    <div v-if="error" class="text-error text-sm mt-1">{{ error }}</div>
    <div v-else class="text-sm text-gray-500 mt-1">Digite o CEP e clique para buscar</div>
  </div>
</template>
