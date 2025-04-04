<script setup>
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../../Components/ErrorMessages.vue";
import SessionMessages from "../../../Components/SessionMessages.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.put(route("profile.password"), {
        onSuccess: () => form.reset(),
        onError: () => form.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Container class="mb-6">
        <div class="mb-6">
            <Title>Mettre à jour le mot de passe</Title>
            <p>Assurez-vous d'utiliser un mot de passe long et aléatoire pour garantir votre sécurité.</p>
        </div>

        <ErrorMessages :errors="form.errors" />

        <form @submit.prevent="submit" class="space-y-6">
            <InputField
                label="Mot de passe actuel"
                icon="key"
                class="w-1/2"
                type="password"
                v-model="form.current_password"
            />

            <InputField
                label="Nouveau mot de passe"
                icon="key"
                class="w-1/2"
                type="password"
                v-model="form.password"
            />

            <InputField
                label="Confirmer le nouveau mot de passe"
                icon="key"
                class="w-1/2"
                type="password"
                v-model="form.password_confirmation"
            />

            <p v-if="form.recentlySuccessful" class="text-green-500 font-medium">Enregistré !</p>

            <PrimaryBtn :disabled="form.processing">Enregistrer</PrimaryBtn>
        </form>
    </Container>
</template>
