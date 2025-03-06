<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Réinitialisation du mot de passe" />

        <div class="mb-4 text-sm text-gray-600">
            Vous avez oublié votre mot de passe ? Pas de problème. Indiquez simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe pour vous permettre d'en choisir un nouveau.
        </div>

        <div
            v-if="status"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="w-full max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <!-- Champ Email -->
            <div>
                <InputLabel for="email" value="Adresse e-mail" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Bouton de soumission -->
            <div class="mt-4 flex items-center justify-end">
                <PrimaryButton
                    class="w-full py-2 text-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    Envoyer le lien de réinitialisation
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

