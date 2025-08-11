<template>
  <div class="flex gap-4">
    <div
      v-for="item in data"
      :key="item.id"
      :class="cardClass(item.type)"
      class="rounded-lg p-4 flex-1"
    >
      <div class="text-xs font-semibold mb-1">{{ item.title }}</div>
      <div class="text-lg font-bold mb-1">
        R$ {{ item.amount.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
      </div>
      <div v-if="item.type !== 'saldo'" class="text-green-600 flex items-center gap-1 text-xs">
        <component :is="item.type === 'receita' ? 'ArrowUpIcon' : 'ArrowDownIcon'" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ArrowUpIcon, ArrowDownIcon } from '@heroicons/vue/solid' // Ou qualquer outro ícone que use

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
})

function cardClass(type) {
  if (type === 'saldo') return 'bg-green-600 text-white'
  return 'bg-gray-300 text-black'
}
</script>
