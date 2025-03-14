<script setup>
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../../Components/ErrorMessages.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const showConfirmPassword = ref(false);

const form = useForm({
    password: "",
});

const submit = () => {
    form.delete(route("profile.destroy"), {
        onFinish: () => form.reset(),
        preserveScroll: true,
    });
};
</script>

<template>
    <Container class="mb-6">
        <div class="mb-6">
            <Title>Supprimer le compte</Title>
            <p>
                Une fois que votre compte est supprimé, toutes ses données seront
                définitivement supprimées. Cette action est irréversible.
            </p>
        </div>

        <ErrorMessages :errors="form.errors" />

        <div v-if="showConfirmPassword">
            <form @submit.prevent="submit" class="flex items-end gap-4">
                <InputField
                    label="Confirmer le mot de passe"
                    icon="key"
                    type="password"
                    class="w-1/2"
                    v-model="form.password"
                />

                <PrimaryBtn :disabled="form.processing">Confirmer</PrimaryBtn>

                <button
                    @click="showConfirmPassword = false"
                    class="text-indigo-500 font-medium underline dark:text-indigo-400"
                >
                    Annuler
                </button>
            </form>
        </div>

        <button
            v-if="!showConfirmPassword"
            @click="showConfirmPassword = true"
            class="px-6 py-2 rounded-lg bg-red-500 text-white"
        >
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            Supprimer le compte
        </button>
    </Container>
</template>
