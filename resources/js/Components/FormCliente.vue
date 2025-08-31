<template>
    <form
        @submit.prevent="submit"
         class="p-4 rounded-xl bg-white w-full max-w-10xl mx-auto"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h1 class="text-2xl font-bold text-[#3DA700]">CADASTRO CLIENTE</h1>
        </div>

        <!-- Dados Pessoais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="label">CPF/CNPJ</label>
                <IMaskComponent
                    v-model:modelValue="form.cpf_cnpj"
                    :mask="[
                        {
                            mask: '000.000.000-00',
                            lazy: true,
                        },
                        {
                            mask: '00.000.000/0000-00',
                            lazy: true,
                        },
                    ]"
                    :dispatch="dispatchCpfCnpj"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    required
                />
            </div>
            <div>
                <label class="label">Identificação</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="input input-bordered w-full min-w-[250px] border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    placeholder=""
                    required
                />
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
            <div>
                <label class="label">Telefone</label>
                <IMaskComponent
                    v-model:modelValue="form.phone"
                    :mask="[
                        {
                            mask: '(00) 0000-0000',
                            lazy: true,
                        },
                        {
                            mask: '(00) 00000-0000',
                            lazy: true,
                        },
                    ]"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    required
                />
            </div>
            <div>
                <label class="label">E-mail</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="input input-bordered w-full min-w-[250px]"
                />
            </div>
        </div>

        <!-- Endereço -->
        <div class="divider">Endereço</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="label">Logradouro</label>
                <input
                    v-model="form.street"
                    type="text"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Número</label>
                <input
                    v-model="form.number"
                    type="text"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Complemento</label>
                <input
                    v-model="form.complement"
                    type="text"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Bairro</label>
                <input
                    v-model="form.district"
                    type="text"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Cidade</label>
                <input
                    v-model="form.city"
                    type="text"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Estado (UF)</label>
                <input
                    v-model="form.state"
                    type="text"
                    class="input input-bordered w-full"
                    maxlength="2"
                />
            </div>
            <div class="md:col-span-2">
                <label class="label">CEP</label>
                <input
                    v-model="form.zip_code"
                    type="text"
                    class="input input-bordered w-full"
                    placeholder="00000-000"
                />
            </div>
        </div>

        <!-- Observações -->
        <div>
            <label class="label">Observações</label>
            <textarea
                v-model="form.notes"
                class="textarea textarea-bordered w-full"
                rows="3"
            ></textarea>
        </div>

        <!-- Botões -->
        <div class="flex gap-4 mt-6">
            <button
                type="button"
                class="btn flex-1 bg-[#FF0017] text-white hover:bg-red-700 rounded-xl"
                @click="$emit('close')"
            >
                CANCELAR
            </button>
            <button
                type="submit"
                class="btn flex-1 bg-[#3DA700] text-white hover:bg-green-700 rounded-xl"
            >
                SALVAR
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { useForm, usePage } from "@inertiajs/vue3";

// Mask
import { IMaskComponent } from "vue-imask";

function dispatchCpfCnpj(appended, dynamicMasked) {
    const number = (dynamicMasked.value + appended).replace(/\D/g, "");

    if (number.length <= 11) {
        return dynamicMasked.compiledMasks[0]; // CPF
    } else {
        return dynamicMasked.compiledMasks[1]; // CNPJ
    }
}

const props = defineProps({
    data: Object,
});

const data = ref(props.data);

let form = useForm({
    cpf_cnpj: data?.cpf_cnpj || "",
    name: data?.name || "",
    email: data?.email || "",
    phone: data?.phone || "",
    street: data?.street || "",
    number: data?.number || "",
    complement: data?.complement || "",
    district: data?.district || "",
    city: data?.city || "",
    state: data?.state || "",
    zip_code: data?.zip_code || "",
    notes: data?.notes || "",
});

const submit = () => {
    form.post("/clientes/store");

    //    router.post("/cadastroCliente", cliente.value);
    // Aqui você pode fazer uma request via axios ou Inertia
};
</script>
