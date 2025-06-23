<script setup>
const props = defineProps({
    columnsName: {
        type: Array,
        required: true,
    },
    data: {
        type: Array,
        required: false,
    },
});
</script>

<template>
   <div class="w-full overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
        <table class="table table-zebra min-w-[600px] md:min-w-[800px]">
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox" />
                        </label>
                    </th>
                    <th v-for="(column, index) in columnsName" :key="index">
                        {{ column.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <!-- <tr v-for="(item, rowIndex) in data" :key="rowIndex">
          <td>{{ rowIndex + 1 }}</td>
          <td v-for="(column, colIndex) in columnsName" :key="colIndex">
            {{ item[column.key] }}
          </td>
        </tr> -->
            </tbody>
        </table>
    </div>
</template>
