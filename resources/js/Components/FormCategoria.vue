<template>
    <form
        @submit.prevent="fSubmit"
        class="p-4 rounded-xl bg-white w-full max-w-md"
    >
        <!-- Título e botão de fechar -->
        <div class="relative mb-6 text-center">
            <h3 class="text-2xl font-bold text-[#3DA700]">
                CADASTRO DE CATEGORIAS
            </h3>
        </div>

        <!-- Dados Pessoais -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            <fieldset class="fieldset">
                <legend class="fieldset-legend">
                    Identificação
                    <span class="text-red-500 -ml-2">*</span>
                </legend>
                <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    class="input input-bordered w-full border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#3DA700]"
                    placeholder="Identificação da categoria."
                    required
                />
                <div class="validator-hint hidden">
                    Informe a identificação da categoria.
                </div>
            </fieldset>
        </div>

        <!-- Botões -->
        <div class="flex gap-4 mt-6">
            <button
                type="button"
                class="btn flex-1 bg-[#FF0017] text-white hover:bg-red-700 rounded-xl"
                @click="fHandleCancel"
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

        <Toast
            v-if="showToast"
            :message="toastMessage"
            :type="toastType"
            position="center"
            size="lg"
        />
    </form>
</template>

<script setup>
// Vue
import { ref, computed, watch } from "vue";
import { useForm, usePage } from "@inertiajs/vue3";

// Componentes e funções
import Toast from "@/Components/Toast.vue";

const props = defineProps({
    data: Object,
    adicional: Object,
});
const emit = defineEmits(["close"]);

const formData = ref(props.data || {});
const isEditing = computed(() => !!formData.value?.id);

const form = useForm({
    name: formData.value?.name || "",
});

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

// 🔹 Watch para preencher dados quando editar
watch(
    () => props.data,
    (editValue) => {
        formData.value = editValue;

        if (editValue && Object.keys(editValue).length > 0) {
            form.name = editValue.name;
        }
    },
    { immediate: true }
);

const page = usePage();
// Watch para mostrar erros vindos do backend
watch(
    () => page.props.errors,
    (errors) => {
        if (errors && Object.keys(errors).length > 0) {
            toastMessage.value = Object.values(errors)[0];
            toastType.value = "error";
            showToast.value = true;

            setTimeout(() => {
                showToast.value = false;
            }, 3000);
        }
    },
    { deep: true }
);

// Cancelar formulário
function fHandleCancel() {
    form.reset();
    emit("close");
}

// Submete o formulário
// function fSubmit() {
//     console.log("FORM categoria");

//     // if (formData.value.id) {
//     //     // update
//     //     form.post(`/clientes/${formData.value.id}/update`, {
//     //         method: "put",
//     //         forceFormData: true,
//     //     });
//     // } else {
//     //     // insert
//         form.post("/categorias/store");
//     // }

// }

// function fSubmit() {
//     console.log("FORM categoria");

//     form.post("/categorias/store", {
//         onSuccess: (page) => {
//             console.log("Categoria criada com sucesso!");

//             fHandleCancel();

//              const novaCategoria = page.props.flash.newCategory;
//             if (novaCategoria) {
//                 // Adiciona no array de dados da tabela
//                 props.data.data.push(novaCategoria);
//             }
//             // 2️⃣ Atualizar tabela
//             // Se você está usando Inertia para a tabela, pode dar reload:
//             // Inertia.reload({ only: ['categories'] }); // só atualiza os dados necessários
//             // ou, se você mantém a lista local:
//             // categories.value.push(page.props.newCategory);
//         },
//         onError: (errors) => {
//             console.log("Erro ao criar categoria", errors);
//         }
//     });
// }

function fSubmit() {
    console.log("FORM categoria");

    form.post("/categorias/store", {
        onSuccess: (response) => {
            console.log("Categoria criada com sucesso!");

            // Emite evento para o componente pai
            const novaCategoria = page.props.flash.newCategory;
            console.log("Teste page props", page.props);
            console.log("Teste page props flash", page.props.flash);
            console.log("Teste page props flash cateorgia", page.props.flash.newCategory);


            if (novaCategoria) {
                console.log("NOva categoria", novaCategoria);
                emit("created", novaCategoria); // ⚡️ evento 'created'
            } else {
                console.log("Não foi criado");
            }

            // Fecha o formulário
            fHandleCancel();
        },
        onError: (errors) => {
            console.log("Erro ao criar categoria", errors);
        }
    });
}


</script>
