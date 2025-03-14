<script setup>
import { useForm } from "@inertiajs/vue3";
import Container from "../../Components/Container.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import SessionMessages from "../../Components/SessionMessages.vue";

const form = useForm({});

defineProps({
    status: String,
});

const submit = () => {
    form.post(route("verification.send"));
};
</script>

<template>
    <Head title="- Vérification de l'email" />

    <Container class="w-1/2">
        <div class="mb-8">
            <p>
                Merci de vous être inscrit ! Avant de commencer, veuillez vérifier
                votre adresse email en cliquant sur le lien que nous venons de vous
                envoyer. Si vous n'avez pas reçu l'email, nous serons heureux de
                vous en envoyer un autre.
            </p>
        </div>

        <SessionMessages :status="status"/>

        <form @submit.prevent="submit">
            <PrimaryBtn :disabled="form.processing">
                Renvoyer l'email de vérification
            </PrimaryBtn>
        </form>
    </Container>
</template>
