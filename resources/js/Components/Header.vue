<script setup>
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const profilePhoto = page.props.auth?.user;

const props = defineProps({
    denTela: String,
});
</script>
<template>
    <div class="flex-1">
        <div class="text-xl font-bold">{{ denTela }}</div>
    </div>
    <div class="flex gap-2 mr-5">
        <a href="/perfil-mei">
            <div
                tabindex="0"
                role="button"
                class="btn btn-ghost btn-circle avatar indicator"
            >
                <div class="w-10 rounded-full">
                    <img
                        alt="Foto de perfil do MEI"
                        :src="
                            profilePhoto.profile_photo
                                ? profilePhoto.profile_photo
                                : 'https://placehold.co/200x200'
                        "
                    />
                </div>
                <span
                    class="badge badge-xs badge-primary indicator-item"
                ></span>
            </div>
            <!-- <ul
                tabindex="0"
                class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow"
            >
                <li>
                    <a class="justify-between">
                        Profile
                        <span class="badge">New</span>
                    </a>
                </li>
                <li><a>Settings</a></li>
                <li><a>Logout</a></li>
            </ul> -->
        </a>
    </div>
</template>
