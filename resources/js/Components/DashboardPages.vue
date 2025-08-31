<template>
    <div class="flex gap-4 flex-wrap">
        <div
            v-if="cardsWithIcons.length"
            v-for="(card, index) in cardsWithIcons"
            :key="index"
            :class="[
                card.color ? card.color : 'bg-gray-200',
                'rounded-xl p-5 flex-1',
            ]"
        >
            <div class="flex justify-between items-center mb-2">
                <h2
                    :class="[
                        card.titleColor ? card.titleColor : 'text-black',
                        card.titleSize ? card.titleSize : 'text-xl',
                        card.titleWeight ? card.titleWeight : 'font-bold',
                    ]"
                >
                    {{ card.title }}
                </h2>
                <component
                    v-if="card.icon"
                    :is="card.icon"
                    :class="card.iconColor ? card.iconColor : 'text-gray-700'"
                    class="w-6 h-6"
                />
            </div>
            <p
                :class="[
                    card.descriptionColor ? card.descriptionColor : 'text-gray-600',
                    card.descriptionSize ? card.descriptionSize : 'text-base',
                    card.descriptionWeight ? card.descriptionWeight : 'font-normal'
                ]"
            >
                {{ card.description }}
            </p>
        </div>
    </div>
</template>

<script setup>
import * as LucideIcons from "lucide-vue-next";
import { computed } from "vue";

const props = defineProps({
    cards: Array,
});

// Computed para transformar string do ícone em componente Lucide
const cardsWithIcons = computed(() => {
    if (!props.cards) return [];
    return props.cards.map((card) => ({
        ...card,
        icon: card.icon ? LucideIcons[card.icon] : null,
    }));
});
</script>
