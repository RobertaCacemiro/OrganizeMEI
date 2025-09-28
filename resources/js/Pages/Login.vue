<script setup>
import { ref, reactive, watch } from "vue"; // Reatividade
import { router, usePage } from "@inertiajs/vue3";
import Toast from "@/Components/Toast.vue";

// Icones
import { Mail, KeyRound, Eye, EyeOff } from "lucide-vue-next";

// Mask
import { IMaskComponent } from "vue-imask";

const confirmarSenha = ref("");
const erroSenha = ref("");

const form = reactive({
    email: null,
    password: null,
});

const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const toastMessage = ref("");
const toastType = ref("info");
const showToast = ref(false);

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

/**
 * Função que valida se as senhas informadas coincidem
 */
const validarSenha = () => {
    erroSenha.value = "";

    const senhaValida = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/.test(
        form.password
    );

    if (!senhaValida) {
        erroSenha.value =
            "A senha deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula";
        return false;
    }

    if (form.password !== confirmarSenha._value) {
        erroSenha.value = "As senhas não coincidem.";
        return false;
    }

    return true;
};

/**
 * Função que realiza o chamdado da função de cadastro do usuário na base de dados
 */
function login() {
    const iesSenhaValida = true;

    if (iesSenhaValida) {
        router.post("/login", form);
    }
}
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
        <div
            class="bg-white rounded-2xl shadow-xl p-6 sm:p-8 md:p-12 w-full max-w-md mx-auto"
        >
            <div class="text-center space-y-2">
                <h1 class="text-3xl font-bold tracking-tighter text-[#3DA700]">
                    Entre
                </h1>
                <p class="text-gray-500">
                    Não possui conta?
                    <a
                        class="text-sm underline text-[#3DA700] hover:text-[#388E3C]"
                        href="/register"
                    >
                        Crie agora!</a
                    >
                </p>
            </div>

            <form
                id="formularioLogin"
                class="space-y-2"
                @submit.prevent="login"
            >
                <div class="space-y-2">
                    <span class="text-gray-500 text-sm">E-mail</span>
                    <label
                        class="w-full flex items-center gap-2 border border-gray-300 px-3 py-2 rounded-md focus-within:ring-2 focus-within:ring-[#3DA700] focus-within:border-[#3DA700]"
                    >
                        <Mail class="h-[1em] opacity-50" />
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="email@exemplo.com.br"
                            required
                            class="flex-1 bg-transparent border-0 outline-none p-0"
                        />
                    </label>
                    <div class="validator-hint hidden">
                        Entre com um email válido.
                    </div>
                </div>

                <div class="space-y-2">
                    <span class="text-gray-500 text-sm">Senha</span>
                    <label
                        class="w-full flex items-center gap-2 border border-gray-300 px-3 py-2 rounded-md focus-within:ring-2 focus-within:ring-[#3DA700] focus-within:border-[#3DA700]"
                    >
                        <KeyRound class="h-[1em] opacity-50" />

                        <input
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Password"
                            minlength="8"
                            title="Deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula"
                            required
                            class="flex-1 bg-transparent border-0 outline-none p-0"
                        />

                        <!-- Ícone de olho -->
                        <button
                            type="button"
                            @click="togglePassword"
                            class="ml-2 text-gray-500 hover:text-gray-700"
                        >
                            <component
                                :is="showPassword ? EyeOff : Eye"
                                class="h-5 w-5"
                            />
                        </button>
                    </label>
                </div>

                <div class="text-right italic">
                    <p class="text-gray-500">
                        <a
                            class="text-sm underline text-[#3DA700] hover:text-[#388E3C]"
                            href="/"
                        >
                            Esqueceu a senha?</a
                        >
                    </p>
                </div>

                <div class="flex justify-center p-5">
                    <button
                        type="submit"
                        class="btn bg-[#3DA700] hover:bg-[#388E3C] w-full rounded-xl"
                    >
                        <p class="text-[#ffffff]">ENTRAR</p>
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
