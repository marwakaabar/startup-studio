<script setup>
import Container from "../../Components/Container.vue";
import InputField from "../../Components/InputField.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../Components/ErrorMessages.vue";
import SessionMessages from "../../Components/SessionMessages.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    email: "",
});

defineProps({status: String})

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head title="- Mot de passe oublié" />
    <Container class="w-1/2">
        <div class="mb-8 text-center">
            <p>
                Vous avez oublié votre mot de passe ? Pas de problème. Indiquez-nous simplement votre adresse email et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra de choisir un nouveau mot de passe.
            </p>
        </div>

        <!-- Messages d'erreur -->
        <ErrorMessages :errors="form.errors" />

        <SessionMessages :status="status"/>

        <form @submit.prevent="submit" class="space-y-6">
            <InputField label="Email" icon="at" v-model="form.email" />

            <PrimaryBtn :disabled="form.processing">
                Envoyer le lien de réinitialisation du mot de passe
            </PrimaryBtn>
        </form>
    </Container>
</template>
