<template>
    <form @submit.prevent="submit" class="space-y-4">
        <!-- Botão de fechar -->

        <div class="flex items-center justify-between w-full mb-4">
            <!-- Título centralizado (visualmente) -->
            <h1 class="text-3xl font-bold text-[#3DA700] mx-auto">
                Cadastro de Cliente
            </h1>

            <!-- Botão no canto direito -->
            <button
                type="button"
                class="btn btn-sm btn-circle btn-ghost ml-auto"
            >
                ✕
            </button>
        </div>

        <!-- Dados Pessoais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    class="input input-bordered w-full"
                    placeholder="Digite o CPF ou CNPJ"
                    required
                />
            </div>
            <div>
                <label class="label">Nome</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="input input-bordered w-full"
                    required
                />
            </div>
            <div>
                <label class="label">E-mail</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="input input-bordered w-full"
                />
            </div>
            <div>
                <label class="label">Telefone</label>
                <input
                    v-model="form.phone"
                    type="text"
                    class="input input-bordered w-full"
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

        <!-- Botão de envio -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="btn btn-primary">
                Salvar Cliente
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, watch  } from "vue";
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
    cliente: Object,
});

let form = useForm({
    cpf_cnpj: props.cliente?.cpf_cnpj || "",
    name: props.cliente?.name || "",
    email: props.cliente?.email || "",
    phone: props.cliente?.phone || "",
    street: props.cliente?.street || "",
    number: props.cliente?.number || "",
    complement: props.cliente?.complement || "",
    district: props.cliente?.district || "",
    city: props.cliente?.city || "",
    state: props.cliente?.state || "",
    zip_code: props.cliente?.zip_code || "",
    notes: props.cliente?.notes || "",
});


const submit = () => {
    console.log("Formulário");
    console.log(form);

        form.post("/clientes/store");


    // console.log("Enviando cliente:", cliente.value);
    //    router.post("/cadastroCliente", cliente.value);
    // Aqui você pode fazer uma request via axios ou Inertia
};
</script>
