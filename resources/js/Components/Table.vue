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

const getRelativeUrl = (absoluteUrl) => {
    if (!absoluteUrl) return null;
    try {
        const url = new URL(absoluteUrl);
        return url.pathname + url.search;
    } catch (e) {
        return absoluteUrl;
    }
};
</script>

<template>
    <div class="w-full">
        <div
            class="hidden md:block overflow-x-auto rounded-box border border-base-content/5 bg-base-100"
        >
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th
                            v-for="(column, index) in columnsName"
                            :key="index"
                            class="text-[#000000]"
                        >
                            {{ column.label }}
                        </th>
                        <th>AÇÕES</th>
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
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Cards para telas pequenas -->
        <div class="grid gap-4 md:hidden">
            <div
                v-for="(item, rowIndex) in data.data"
                :key="rowIndex"
                class="rounded-lg border border-base-300 bg-base-100 p-4 shadow-sm"
            >
                <div
                    v-for="(column, colIndex) in columnsName"
                    :key="colIndex"
                    class="flex text-sm mb-1 items-center"
                >
                    <span class="font-semibold text-gray-700 min-w-[100px]">
                        {{ column.label }}:
                    </span>

                    <span class="flex-1 break-words">
                        {{ formatValue(item[column.key], column.type) }}
                    </span>
                </div>

                <!-- Ações -->
                <div class="flex gap-3 mt-10 justify-end">
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
                </div>
            </div>
        </div>
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
                :href="getRelativeUrl(link.url)"
                class="p-1 text-gray-600 hover:text-[#3DA700]"
            >
                <svg
                    xmlns="https://www.w3.org/2000/svg"
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
                    xmlns="https://www.w3.org/2000/svg"
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
                :href="getRelativeUrl(link.url)"
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
                :href="getRelativeUrl(link.url)"
                class="p-1 text-gray-600 hover:text-[#3DA700]"
            >
                <svg
                    xmlns="https://www.w3.org/2000/svg"
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
                    xmlns="https://www.w3.org/2000/svg"
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
