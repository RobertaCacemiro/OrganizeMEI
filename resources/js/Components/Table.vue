<script setup>
import { Pencil, Trash2 } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    columnsName: Array,
    data: Object, // paginator
    onEdit: Function,
    onDelete: Function,
});

console.log("Teste cobranças", props.data);


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
        <table class="table table-zebra min-w-[600px] md:min-w-[800px]">
            <thead>
                <tr>
                    <th><input type="checkbox" class="checkbox" /></th>
                    <th
                        v-for="(column, index) in columnsName"
                        :key="index"
                        class="text-[#000000]"
                    >
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, rowIndex) in data.data" :key="rowIndex">
                    <td><input type="checkbox" class="checkbox" /></td>
                    <td
                        v-for="(column, colIndex) in columnsName"
                        :key="colIndex"
                    >
                        {{ formatValue(item[column.key], column.type) }}
                    </td>
                    <td class="flex gap-2">
                        <button
                            @click="onEdit?.(item.id)"
                            class="text-[#000000] hover:text-blue-800"
                        >
                            <Pencil class="w-5 h-5" />
                        </button>
                        <button
                            @click="onDelete?.(item.id)"
                            class="text-[#000000] hover:text-red-800"
                        >
                            <Trash2 class="w-5 h-5" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Paginação -->
    </div>
    <div
        v-if="data.links && data.links.length > 3"
        class="flex justify-center mt-4 gap-2 items-center"
    >
        <template v-for="(link, index) in data.links" :key="index">
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
        </template>
    </div>
</template>
