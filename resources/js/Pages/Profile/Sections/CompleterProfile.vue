<script setup>
import { ref, watch } from "vue";
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../../Components/ErrorMessages.vue";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
    role: String,
    userInfo: Object,
});

// Références pour la gestion des fichiers
const profileImage = ref(null);
const videoPresentation = ref(null);
const logoStartup = ref(null);

const form = useForm({
    phone_number: props.userInfo.phone_number || "",
    diploma: props.userInfo.diploma || "",
    competence: props.userInfo.competence || "",
    description: props.userInfo.description || "",
    website_link: props.userInfo.website_link || "",
    social_links: props.userInfo.social_links || "",
    profile_image: null,
    video_presentation: null,
    logo_startup: null,
});

// Surveillance des fichiers pour mise à jour du formulaire
watch(profileImage, (newFile) => {
    form.profile_image = newFile ? newFile[0] : null;
});

watch(videoPresentation, (newFile) => {
    form.video_presentation = newFile ? newFile[0] : null;
});

watch(logoStartup, (newFile) => {
    form.logo_startup = newFile ? newFile[0] : null;
});
</script>

<template>
    <Container class="mb-6">
        <div class="mb-6">
            <Title>Complétez votre profil</Title>
            <p>Ajoutez vos informations personnelles pour compléter votre profil.</p>
        </div>

        <ErrorMessages :errors="form.errors" />

        <form @submit.prevent="form.put(route('profile.updateProfile'))" class="space-y-6">
            <!-- Section coach -->
            <template v-if="role === 'coach'">
                <div class="space-y-4">
                    <InputField label="Numéro de téléphone" icon="phone" v-model="form.phone_number" />
                    <InputField label="Diplôme" icon="graduation-cap" v-model="form.diploma" />
                    <InputField label="Compétences" icon="tools" v-model="form.competence" />
                    <InputField label="Description" icon="file-text" v-model="form.description" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                        <input type="file" @change="e => profileImage = e.target.files" class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm" />
                    </div>
                </div>
            </template>

            <!-- Section Investisseur -->
            <template v-if="role === 'investisseur'">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Vidéo de présentation</label>
                        <input type="file" @change="e => videoPresentation = e.target.files" class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <InputField label="Description" icon="file-text" v-model="form.description" />
                    <InputField label="Lien du site web" icon="link" v-model="form.website_link" />
                    <InputField label="Liens sociaux (JSON)" icon="share" v-model="form.social_links" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                        <input type="file" @change="e => profileImage = e.target.files" class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm" />
                    </div>
                </div>
            </template>

            <!-- Section Startup -->
            <template v-if="role === 'startup'">
                <div class="space-y-4">
                    <InputField label="Numéro de téléphone" icon="phone" v-model="form.phone_number" />
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Logo de la startup</label>
                        <input type="file" @change="e => logoStartup = e.target.files" class="mt-1 block w-full text-sm text-gray-700 border border-gray-300 rounded-md shadow-sm" />
                    </div>
                    <InputField label="Description" icon="file-text" v-model="form.description" />
                    <InputField label="Lien du site web" icon="link" v-model="form.website_link" />
                    <InputField label="Liens sociaux" icon="share" v-model="form.social_links" />
                </div>
            </template>

            <PrimaryBtn :disabled="form.processing">Enregistrer</PrimaryBtn>
        </form>
    </Container>
</template>

<style scoped>
input[type="file"] {
    display: block;
    margin-top: 8px;
}
</style>
