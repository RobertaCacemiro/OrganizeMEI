<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";

import Header from "@/Components/Header.vue";

import { AlignJustify } from "lucide-vue-next";
import { ChartNoAxesCombined } from "lucide-vue-next";
import { Users } from "lucide-vue-next";
import { CircleDollarSign } from "lucide-vue-next";
import { HandCoins } from "lucide-vue-next";
import { ScanBarcode } from "lucide-vue-next";
import { LogOut } from "lucide-vue-next";

const isExpanded = ref(false);
const showMobileSidebar = ref(false);

const toggleSidebar = () => {
    isExpanded.value = !isExpanded.value;
};

const toggleMobileMenu = () => {
    showMobileSidebar.value = !showMobileSidebar.value;
};

const menuItems = [
    { label: "BALANÇO", icon: ChartNoAxesCombined, route: "/" },
    { label: "CLIENTES", icon: Users, route: "/clientes" },
    { label: "FINANCEIRO", icon: CircleDollarSign, route: "/financeiro" },
    { label: "COBRANÇAS", icon: HandCoins, route: "/cobrancas" },
    { label: "BOLETOS", icon: ScanBarcode, route: "/boletos" },
];

// Rotas especiais que não estão no menu mas precisam de título no header
const specialRoutes = {
    "/perfil-mei": "PERFIL DO MEI",
};

const page = usePage();
const telaVisualizacao = computed(() => {
    const menuItem = menuItems.find((item) => item.route === page.url);
    if (menuItem) return menuItem.label;

    return specialRoutes[page.url] || "";
});

function flogout() {
    router.post("/logout");
}
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar Desktop -->
        <div
            :class="[
                'bg-[#3DA700] text-white transition-all duration-300 overflow-hidden hidden lg:flex flex-col',
                isExpanded ? 'w-64' : 'items-center w-20',
            ]"
        >
            <!-- Logo e botão toggle -->
            <div class="p-4 flex items-center justify-between gap-4">
                <div v-if="isExpanded">
                    <a href="/">
                        <img
                            class="w-25 h-auto"
                            src="/resources/img/logo.png"
                            alt="Logo"
                        />
                    </a>
                </div>
                <div>
                    <button
                        @click="toggleSidebar"
                        class="bg-[#3DA700] text-white p-2 border-none rounded-none hover:bg-[#3DA700] hover:text-white focus:outline-none focus:ring-0 active:bg-[#3DA700]"
                    >
                        <AlignJustify />
                    </button>
                </div>
            </div>

            <!-- Menu -->
            <ul class="menu">
                <li v-for="(value, key) in menuItems" :key="key" class="m-1">
                    <a
                        :href="value.route"
                        :class="[
                            'font-bold flex items-center gap-2',
                            $page.url === value.route
                                ? 'bg-[#2d8800] text-black'
                                : 'hover:bg-[#2d8800]',
                        ]"
                    >
                        <component :is="value.icon" />
                        <span v-if="isExpanded">{{ value.label }}</span>
                    </a>
                </li>
            </ul>

            <div class="mt-auto">
                <button
                    @click="flogout"
                    class="flex items-center gap-2 font-bold hover:bg-[#2d8800] p-10 rounded w-full text-left"
                >
                    <LogOut />
                    <span v-if="isExpanded">{{ " SAIR" }}</span>
                </button>
            </div>
        </div>

        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar Top -->
            <div
                class="lg:navbar w-[100vw] bg-base-100 shadow px-5 py-3 flex justify-center items-center gap-2 z-10"
            >
                <div class="lg:hidden">
                    <button
                        @click="toggleMobileMenu"
                        class="btn btn-square btn-ghost"
                    >
                        <AlignJustify />
                    </button>
                </div>
                <Header :denTela="telaVisualizacao" />
            </div>

            <!-- Conteúdo da página via slot -->
            <div class="p-6 flex-1 overflow-auto bg-base-100">
                <slot />
            </div>
        </div>

        <!-- Sidebar Mobile -->
        <transition name="fade">
            <div
                v-if="showMobileSidebar"
                class="fixed inset-0 z-50 flex lg:hidden"
            >
                <!-- Fundo escuro -->
                <div
                    class="fixed inset-0 bg-black bg-opacity-50"
                    @click="toggleMobileMenu"
                ></div>

                <!-- Menu lateral mobile -->
                <div class="bg-[#3DA700] text-white w-64 p-4 z-50">
                    <div class="flex justify-between items-center mb-4">
                        <a href="/">
                            <img src="/resources/img/logo.png" alt="Logo" />
                        </a>
                        <button
                            @click="toggleMobileMenu"
                            class="btn btn-sm btn-ghost text-white"
                        >
                            ✕
                        </button>
                    </div>

                    <ul class="menu">
                        <li
                            v-for="(value, key) in menuItems"
                            :key="key"
                            class="m-1"
                        >
                            <a
                                :href="value.route"
                                :class="[
                                    'font-bold flex items-center gap-2',
                                    $page.url === value.route
                                        ? 'bg-[#2d8800] text-black'
                                        : 'hover:bg-[#2d8800]',
                                ]"
                            >
                                <component :is="value.icon" />
                                <span>{{ value.label }}</span>
                            </a>
                        </li>
                    </ul>

                    <div class="mt-auto">
                        <button
                            @click="flogout"
                            class="flex items-center gap-2 font-bold hover:bg-[#2d8800] p-2 rounded w-full text-left"
                        >
                            <LogOut />
                            <span>SAIR</span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
