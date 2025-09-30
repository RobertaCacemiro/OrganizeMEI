<script setup>
import { ref, reactive, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { User, Mail, Smartphone, KeyRound, Eye, EyeOff } from "lucide-vue-next";
import { IMaskComponent } from "vue-imask";
import Toast from "@/Components/Toast.vue";

const confirmarSenha = ref("");
const showPassword = ref(false);

function fMostraSenha() {
    showPassword.value = !showPassword.value;
}

const form = reactive({
    name: null,
    phone: null,
    email: null,
    password: null,
});

const showToast = ref(false);
const toastMessage = ref("");
const toastType = ref("error");

function fMensagemErro(message, type = "error") {
    toastMessage.value = message;
    toastType.value = type;
    showToast.value = true;
}

function fValidaSenha() {
    const senha = form.password ?? "";
    const senhaValida = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(senha);

    if (!senhaValida) {
        fMensagemErro(
            "A senha deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula",
            "error"
        );
        return false;
    }

    if (senha !== confirmarSenha.value) {
        fMensagemErro("As senhas não coincidem.", "error");
        return false;
    }

    return true;
}

function register() {
    if (!fValidaSenha()) return;

    router.post("/register", form);
}

// feedback imediato ao alterar senha
watch([() => form.password, () => confirmarSenha.value], () => {
    if (showToast.value) {
        fValidaSenha();
    }
});

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
</script>

<template>
    <div
        class="bg-[#3DA700] justify-center flex-col flex items-center p-4 min-h-screen w-full"
    >
        <!-- Logo -->
        <div class="w-full max-w-xs mx-auto">
            <img
                class="w-full h-auto"
                src="/resources/img/logo.png"
                alt="Logo"
            />
        </div>
        <!-- Card com o login -->
        <div class="bg-white rounded-2xl shadow-xl p-12 space-y-4">
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-bold tracking-tighter text-[#3DA700]">
                    Cadastre-se
                </h1>
                <p class="text-gray-500">
                    Já possui uma conta?
                    <a
                        class="text-sm underline text-[#3DA700] hover:text-[#388E3C]"
                        href="/login"
                    >
                        Entrar</a
                    >
                </p>
            </div>

            <form
                id="formularioRegister"
                class="space-y-4"
                @submit.prevent="register"
            >
                <div class="space-y-2">
                    <span class="text-gray-500 text-sm">Nome</span>
                    <label
                        class="input validator w-full flex items-center gap-2"
                    >
                        <User class="h-[1em] opacity-50" />
                        <input
                            v-model="form.name"
                            type="text"
                            class="rounded-md w-full"
                            name="user"
                            placeholder="Nome Completo"
                            pattern="[A-Za-zÀ-ÿ\s]{3,50}"
                            title="Apenas letras e espaços, entre 3 e 50 caracteres"
                            required
                        />
                    </label>
                    <!-- <p class="validator-hint">
                        Deve ter de 3 a 50 caracteres
                        contendo apenas letras,números ou hífens
                    </p> -->
                </div>

                <div class="space-y-1">
                    <span class="text-gray-500 text-sm">E-mail</span>
                    <label
                        class="input validator w-full flex items-center gap-2"
                    >
                        <Mail class="h-[1em] opacity-50" />
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="email@exemplo.com.br"
                            required
                        />
                    </label>
                    <div class="validator-hint hidden">
                        Entre com um email válido.
                    </div>
                </div>

                <div class="space-y-1">
                    <span class="text-gray-500 text-sm">Telefone</span>
                    <label
                        class="input validator w-full flex items-center gap-2"
                    >
                        <Smartphone class="h-[1em] opacity-50" />
                        <IMaskComponent
                            v-model="form.phone"
                            :mask="[
                                { mask: '(00) 0000-0000' }, // telefone fixo
                                { mask: '(00) 00000-0000' }, // telefone cotando com o digito 9 na frente celular
                            ]"
                            type="text"
                            class="tabular-nums"
                            placeholder="(77) 97777-7777"
                            title="Digite um número de telefone válido com DDD"
                            required
                        />
                    </label>
                    <!-- <p class="validator-hint">Deve ter 10 dígitos</p> -->
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Campo Senha -->
                    <div class="space-y-1">
                        <span class="text-gray-500 text-sm">Senha</span>
                        <label
                            class="input validator w-full flex items-center gap-2"
                        >
                            <KeyRound class="h-[1em] opacity-50" />
                            <input
                                v-model="form.password"
                                class="w-full"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Password"
                                minlength="8"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula"
                                required
                            />
                            <button
                                type="button"
                                @click="fMostraSenha"
                                class="ml-2 text-gray-500 hover:text-gray-700"
                            >
                                <component
                                    :is="showPassword ? EyeOff : Eye"
                                    class="h-5 w-5"
                                />
                            </button>
                        </label>
                    </div>

                    <!-- Campo Confirmar Senha -->
                    <div class="space-y-1">
                        <span class="text-gray-500 text-sm"
                            >Confirmar Senha</span
                        >
                        <label
                            class="input validator w-full flex items-center gap-2"
                        >
                            <KeyRound class="h-[1em] opacity-50" />
                            <input
                                v-model="confirmarSenha"
                                class="w-full"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Password"
                                minlength="8"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula"
                                required
                            />
                            <button
                                type="button"
                                @click="fMostraSenha"
                                class="ml-2 text-gray-500 hover:text-gray-700"
                            >
                                <component
                                    :is="showPassword ? EyeOff : Eye"
                                    class="h-5 w-5"
                                />
                            </button>
                        </label>
                    </div>
                </div>

                <div class="flex justify-center p-5 w-full">
                    <button
                        type="submit"
                        class="btn bg-[#3DA700] hover:bg-[#388E3C] w-full rounded-xl"
                    >
                        <p class="text-[#ffffff]">CADASTRAR</p>
                    </button>
                </div>
            </form>
            <Toast
                v-if="showToast"
                :message="toastMessage"
                :type="toastType"
                position="center"
                size="lg"
            />
        </div>
    </div>
</template>
