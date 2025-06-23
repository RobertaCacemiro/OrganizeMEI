<script setup>
import { ref, reactive } from "vue"; // Reatividade
import { router } from "@inertiajs/vue3";

// Icones
import { User } from "lucide-vue-next";
import { Mail } from "lucide-vue-next";
import { Smartphone } from "lucide-vue-next";
import { KeyRound } from "lucide-vue-next";

// Mask
import { IMaskComponent } from "vue-imask";

const confirmarSenha = ref("");
const erroSenha = ref("");

const form = reactive({
    name: null,
    phone: null,
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

    // console.log(senhaValida);
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
function register() {
    const iesSenhaValida = true;

    if (iesSenhaValida) {
        console.log("FORM", form);
        router.post("/register", form);
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
                    <span class="text-gray-500 text-sm">name</span>
                    <label
                        class="input validator w-full flex items-center gap-2"
                    >
                        <User class="h-[1em] opacity-50" />
                        <input
                            v-model="form.name"
                            type="text"
                            class="rounded-md w-full"
                            name="user"
                            placeholder="name Completo"
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
                    <span class="text-gray-500 text-sm">phone</span>
                    <label
                        class="input validator w-full flex items-center gap-2"
                    >
                        <Smartphone class="h-[1em] opacity-50" />
                        <IMaskComponent
                            v-model="form.phone"
                            :mask="[
                                { mask: '(00) 0000-0000' }, // phone fixo
                                { mask: '(00) 00000-0000' }, // phone cotando com o digito 9 na frente celular
                            ]"
                            type="text"
                            class="tabular-nums"
                            placeholder="(77) 97777-7777"
                            title="Digite um número de phone válido com DDD"
                            required
                        />
                    </label>
                    <!-- <p class="validator-hint">Deve ter 10 dígitos</p> -->
                </div>

                <div class="grid grid-cols-2 content-start gap-2">
                    <div class="space-y-1">
                        <span class="text-gray-500 text-sm">Senha</span>
                        <label
                            class="input validator w-fit flex items-center gap-2"
                        >
                            <KeyRound class="h-[1em] opacity-50" />
                            <input
                                v-model="form.password"
                                class="w-1/2"
                                type="password"
                                placeholder="Password"
                                minlength="8"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
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

                    <div class="space-y-1">
                        <span class="text-gray-500 text-sm"
                            >Confirmar Senha</span
                        >
                        <label
                            class="input validator w-fit flex items-center gap-2"
                        >
                            <KeyRound class="h-[1em] opacity-50" />
                            <input
                                v-model="confirmarSenha"
                                class="w-1/2"
                                type="password"
                                placeholder="Password"
                                minlength="8"
                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                title="Deve ter mais de 8 caracteres, incluindo número, letra minúscula e letra maiúscula"
                                required
                            />
                        </label>
                    </div>
                    <div>
                        <p v-if="erroSenha" class="text-red-300 text-sm mt-1">
                            {{ erroSenha }}
                        </p>
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
        </div>
    </div>
</template>
