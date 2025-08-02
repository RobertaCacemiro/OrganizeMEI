<script setup>
import { Pencil, Trash2 } from "lucide-vue-next";

const props = defineProps({
    columnsName: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: false,
    },
    onEdit: {
        type: Function,
        required: false,
    },
    onDelete: {
        type: Function,
        required: false,
    },
});

// console.log("TESTE TABELA", data);
</script>

<template>
    <div
        class="w-full overflow-x-auto rounded-box border border-base-content/5 bg-base-100"
    >
        <table class="table table-zebra min-w-[600px] md:min-w-[800px]">
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <th v-for="(column, index) in columnsName" :key="index" class="text-[#000000]">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item, rowIndex) in data" :key="rowIndex">
                    <td>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </td>
                    <td
                        v-for="(column, colIndex) in columnsName"
                        :key="colIndex"
                    >
                        {{ item[column.key] }}
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
    </div>
</template>
