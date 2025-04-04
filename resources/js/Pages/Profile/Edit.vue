<script setup>
import { Head } from "@inertiajs/vue3";
import UpdateInfo from "./Sections/UpdateInfo.vue";
import UpdatePassword from "./Sections/UpdatePassword.vue";
import DeleteAccount from "./Sections/DeleteAccount.vue";
import CompleterProfile from "./Sections/CompleterProfile.vue";

defineProps({
    user: {
        type: Object,
        required: true,
    },
    status: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <Head title="Profile" />
    
    <div class="container mx-auto px-4 py-8 max-w-3xl">
        <!--  <CompleterProfile :role="user.role" :userInfo="user" /> -->
        <UpdateInfo :user="user" :status="status" />
        <UpdatePassword />
        <DeleteAccount />
    </div>
</template>
