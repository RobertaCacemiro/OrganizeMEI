<script setup>
import { ref, reactive } from "vue"; // Reatividade
import { router } from "@inertiajs/vue3";

// Icones
import { Mail } from "lucide-vue-next";
import { KeyRound } from "lucide-vue-next";

// Mask
import { IMaskComponent } from "vue-imask";

const confirmarSenha = ref("");
const erroSenha = ref("");

const form = reactive({
    email: null,
    password: null,
});

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

                <div class="space-y-2">
                    <span class="text-gray-500 text-sm">Senha</span>
                    <label class="input validator w-full flex items-center">
                        <KeyRound class="h-[1em] opacity-50" />
                        <input
                            v-model="form.password"
                            class="w-full"
                            type="password"
                            placeholder="Password"
                            minlength="8"
                            title="Deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula"
                            required
                        />
                    </label>
                    <!-- <p class="validator-hint hidden">
                        Deve ter mais de 8 caracteres, incluindo
                            <br />Pelo menos um número   <br />
                        Pelo menos uma letra minúscula <br />Pelo menos uma letra maiúscula
                    </p> -->
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
        </div>
    </div>
</template>
