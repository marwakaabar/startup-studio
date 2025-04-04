<script setup>
import { switchTheme } from "../theme";
import NavLink from "../Components/NavLink.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});
const show = ref(false);
</script>

<template>
    <header class="bg-white/90 backdrop-blur-sm shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto max-w-screen-lg flex items-center justify-between py-3 px-6">
            <div class="text-2xl font-bold text-[#4A90E2] hover:text-[#357ABD] transition-colors">
                Startup Studio
            </div>
            <div class="flex items-center gap-8">
                <NavLink 
                    routeName="home" 
                    componentName="Home"
                    class="text-gray-700 hover:text-[#4A90E2] font-medium transition-colors duration-200"
                >
                    Accueil
                </NavLink>
            </div>
            <div class="flex items-center gap-6">
                <div v-if="user" class="relative">
                    <div 
                        @click="show = !show" 
                        class="flex items-center gap-3 px-5 py-2.5 rounded-full cursor-pointer border-2 border-[#4A90E2] text-[#4A90E2] hover:bg-[#4A90E2] hover:text-white transition-all duration-200 font-medium"
                    >
                        <p>{{ user.name }}</p>
                        <i class="fa-solid fa-angle-down transition-transform" :class="{ 'rotate-180': show }"></i>
                    </div>
                    <div
                        v-show="show"
                        @click="show = false"
                        class="absolute right-0 mt-3 bg-white shadow-lg rounded-xl border border-gray-100 overflow-hidden w-48 transform origin-top-right transition-all duration-200"
                    >
                        <Link :href="route('profile.edit')" class="flex items-center gap-3 px-6 py-3.5 text-gray-700 hover:bg-gray-50 hover:text-[#4A90E2] transition-colors">
                            <i class="fa-regular fa-user w-4"></i>
                            <span>Profil</span>
                        </Link>
                        <Link 
                            :href="user.role === 'admin' ? route('admin.dashboard') : route('dashboard')" 
                            class="flex items-center gap-3 px-6 py-3.5 text-gray-700 hover:bg-gray-50 hover:text-[#4A90E2] transition-colors"
                        >
                            <i class="fa-regular fa-chart-bar w-4"></i>
                            <span>Tableau de bord</span>
                        </Link>
                        <Link :href="route('logout')" method="post" as="button" class="flex items-center gap-3 w-full px-6 py-3.5 text-gray-700 hover:bg-gray-50 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket w-4"></i>
                            <span>Se déconnecter</span>
                        </Link>
                    </div>
                </div>
                <div v-else class="flex gap-4">
                    <NavLink 
                        routeName="login" 
                        componentName="Auth/Login" 
                        class="px-5 py-2.5 rounded-full bg-[#4A90E2] text-white hover:bg-[#357ABD] transition-colors duration-200 font-medium"
                    >
                        Connexion
                    </NavLink>
                </div>
                <button 
                    @click="switchTheme" 
                    class="w-10 h-10 grid place-items-center rounded-full text-[#4A90E2] hover:bg-[#4A90E2] hover:text-white transition-all duration-200"
                >
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>
        </nav>
    </header>
</template>
