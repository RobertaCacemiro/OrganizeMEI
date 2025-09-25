<script setup>
import * as LucideIcons from "lucide-vue-next";
import { computed, ref, watch } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    columnsName: Array,
    data: Object,
    actions: {
        type: Array,
        default: () => [
            {
                icon: Pencil,
                color: "blue-800",
                onClick: (id) => console.log("Editar", id),
            },
            {
                icon: Trash2,
                color: "red-800",
                onClick: (id) => console.log("Excluir", id),
            },
        ],
    },
});

// Armazena os itens selecionados
const selectedItems = ref([]);

// Computed para converter ícones
const actionsWithIcons = computed(() => {
    if (!props.actions) return [];
    return props.actions.map((action) => ({
        ...action,
        icon: action.icon ? LucideIcons[action.icon] : null,
    }));
});

// Se todos estão selecionados
const allSelected = computed({
    get: () =>
        props.data.data?.length > 0 &&
        selectedItems.value.length === props.data.data.length,
    set: (val) => {
        if (val) {
            selectedItems.value = props.data.data.map((item) => item.id);
        } else {
            selectedItems.value = [];
        }
    },
});

function toggleSelection(id) {
    if (selectedItems.value.includes(id)) {
        selectedItems.value = selectedItems.value.filter((x) => x !== id);
    } else {
        selectedItems.value.push(id);
    }
}

function formatValue(value, type) {
    if (type === "money") {
        return `R$ ${Number(value).toLocaleString("pt-BR", {
            minimumFractionDigits: 2,
        })}`;
    }
    return value;
}
</script>

<template>
    <div
        class="w-full overflow-x-auto rounded-box border border-base-content/5 bg-base-100"
    >
        <!-- <table class="table table-zebra min-w-[600px] md:min-w-[800px]"> -->
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <!-- Checkbox principal -->
                    <!-- <th>
                        <input
                            type="checkbox"
                            class="checkbox"
                            v-model="allSelected"
                        />
                    </th> -->
                    <th
                        v-for="(column, index) in columnsName"
                        :key="index"
                        class="text-[#000000]"
                    >
                        {{ column.label }}
                    </th>
                    <!-- <th> // Exclusão em massa
                        <button
                            class="btn bg-[#FF0017] text-white rounded-lg collapse-arrow"
                            @click="fAbrirConfirmacao(selectedItems)"
                        >
                            EXCLUIR
                        </button>
                    </th> -->
                </tr>
            </thead>

            <tbody>
                <tr v-for="(item, rowIndex) in data.data" :key="rowIndex">
                    <!-- Checkbox individual -->
                    <!-- <td>
                        <input
                            type="checkbox"
                            class="checkbox"
                            :checked="selectedItems.includes(item.id)"
                            @change="toggleSelection(item.id)"
                        />
                    </td> -->

                    <td
                        v-for="(column, colIndex) in columnsName"
                        :key="colIndex"
                    >
                        {{ formatValue(item[column.key], column.type) }}
                    </td>

                    <td class="flex gap-2">
                        <template v-if="item.id !== null">
                            <button
                                v-for="(action, aIndex) in actionsWithIcons"
                                :key="aIndex"
                                @click="action.onClick?.(item.id)"
                                class="text-[#000000]"
                                :class="`hover:text-${action.color}`"
                                :title="action.label"
                            >
                                <component
                                    v-if="action.icon"
                                    :is="action.icon"
                                    class="w-5 h-5"
                                />
                            </button>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div
        v-if="data.links && data.links.length > 3"
        class="flex justify-center mt-4 gap-2 items-center"
    >
        <div v-for="(link, index) in data.links" :key="index">
            <!-- Botão Anterior -->
            <Link
                v-if="index === 0 && link.url"
                :href="link.url"
                class="p-1 text-gray-600 hover:text-[#3DA700]"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </Link>
            <span v-else-if="index === 0" class="p-1 text-gray-400">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </span>

            <!-- Páginas numéricas -->
            <Link
                v-else-if="
                    link.url && index !== 0 && index !== data.links.length - 1
                "
                :href="link.url"
                class="btn btn-sm"
                :class="
                    link.active
                        ? 'bg-[#3DA700] text-white border-none'
                        : 'bg-base-200 text-black border-none'
                "
                v-html="link.label"
            />
            <span
                v-else-if="index !== 0 && index !== data.links.length - 1"
                class="btn btn-sm bg-base-200 text-black border-none"
                v-html="link.label"
            ></span>

            <!-- Botão Próximo -->
            <Link
                v-if="index === data.links.length - 1 && link.url"
                :href="link.url"
                class="p-1 text-gray-600 hover:text-[#3DA700]"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </Link>
            <span
                v-else-if="index === data.links.length - 1"
                class="p-1 text-gray-400"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-5 h-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </span>
        </div>
    </div>
</template>
