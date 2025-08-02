<template>
  <form @submit.prevent="fAplicarFiltro" class="flex flex-wrap gap-4 mb-6">
    <template v-for="campo in campos" :key="campo.name">
      <!-- Campo com máscara -->
      <IMaskComponent
        v-if="campo.type === 'mask'"
        v-model="form[campo.name]"
        :mask="campo.mask"
        :placeholder="campo.placeholder"
        class="input input-bordered w-full md:w-64"
      />

      <!-- Campo do tipo select -->
      <select
        v-else-if="campo.type === 'select'"
        v-model="form[campo.name]"
        class="select select-bordered w-full md:w-64"
      >
        <option value="" disabled selected>{{ campo.placeholder }}</option>
        <option
          v-for="option in campo.options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>

      <!-- Campo padrão (text/email/etc.) -->
      <input
        v-else
        :type="campo.type || 'text'"
        v-model="form[campo.name]"
        :placeholder="campo.placeholder"
        class="input input-bordered w-full md:w-64"
      />
    </template>

    <button type="submit" class="btn btn-primary">Buscar</button>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { IMaskComponent } from 'vue-imask'

const emit = defineEmits(["submit"]);

const props = defineProps({
  campos: {
    type: Array,
    required: true
  }
});

const form = reactive({});

// Inicializar campos com valor vazio
props.campos.forEach((campo) => {
  form[campo.name] = '';
});

function fAplicarFiltro() {
  emit("submit", { ...form });
}
</script>
