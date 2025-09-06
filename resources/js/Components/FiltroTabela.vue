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
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                />

                <!-- Campo do tipo select -->
                <select
                    v-else-if="campo.type === 'select'"
                    v-model="form[campo.name]"
                    class="select select-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
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

                <!-- Campo do tipo date (placeholder visível) -->
                <input
                    v-else-if="campo.type === 'date'"
                    type="text"
                    v-model="form[campo.name]"
                    :placeholder="campo.placeholder"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    @focus="(e) => (e.target.type = 'date')"
                    @blur="
                        (e) => {
                            if (!e.target.value) e.target.type = 'text';
                        }
                    "
                />

                <!-- Campo padrão -->
                <input
                    v-else
                    :type="campo.type || 'text'"
                    v-model="form[campo.name]"
                    :placeholder="campo.placeholder"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                />
            </div>
        </template>

        <!-- Botão submit -->
        <div class="w-full md:w-auto flex gap-2">
            <button
                type="submit"
                class="btn bg-[#3DA700] text-white min-w-[120px] w-full md:w-auto rounded-lg collapse-arrow"
            >
                BUSCAR
            </button>

            <button
                type="button"
                class="btn min-w-[120px] w-full md:w-auto rounded-lg"
                @click="fLimpaFiltros"
            >
                LIMPAR
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
    definedValues: {
        type: Object,
        required: false,
    },
});

const form = reactive({});

props.campos.forEach((campo) => {
    form[campo.name] = props.definedValues?.[campo.name] || "";
});

function fAplicarFiltro() {
    emit("submit", { ...form });
}

function fLimpaFiltros() {
    props.campos.forEach((campo) => {
        form[campo.name] = "";
    });

    emit("submit", { ...form });
}
</script>
