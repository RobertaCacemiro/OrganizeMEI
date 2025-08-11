<template>
    <form @submit.prevent="fAplicarFiltro" class="w-full flex flex-wrap gap-4">
        <template v-for="campo in campos" :key="campo.name">
            <div class="flex-grow min-w-[150px]">
                <!-- Campo com máscara -->
                <IMaskComponent
                    v-if="campo.type === 'mask'"
                    v-model="form[campo.name]"
                    :mask="campo.mask"
                    :placeholder="campo.placeholder"
                    class="input input-bordered w-full"
                />

                <!-- Campo do tipo select -->
                <select
                    v-else-if="campo.type === 'select'"
                    v-model="form[campo.name]"
                    class="select select-bordered w-full"
                >
                    <option value="" disabled selected>
                        {{ campo.placeholder }}
                    </option>
                    <option
                        v-for="option in campo.options"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>

                <!-- Campo padrão -->
                <input
                    v-else
                    :type="campo.type || 'text'"
                    v-model="form[campo.name]"
                    :placeholder="campo.placeholder"
                    class="input input-bordered w-full"
                />
            </div>
        </template>

        <!-- Botão submit -->
        <div class="w-full md:w-auto">
            <button
                type="submit"
                class="btn bg-[#3DA700] text-white min-w-[120px] w-full md:w-auto rounded-lg collapse-arrow"
            >
                BUSCAR
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive } from "vue";
import { IMaskComponent } from "vue-imask";

const emit = defineEmits(["submit"]);

const props = defineProps({
    campos: {
        type: Array,
        required: true,
    },
});

const form = reactive({});

props.campos.forEach((campo) => {
    form[campo.name] = "";
});

function fAplicarFiltro() {
    emit("submit", { ...form });
}

</script>
