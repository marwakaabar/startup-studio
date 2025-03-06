<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Connexion" />

        <!-- Affichage du message de statut -->
        <div v-if="status" class="mb-4 rounded-lg bg-green-100 p-4 text-sm font-medium text-green-700">
            {{ status }}
        </div>

        <!-- Formulaire de connexion dans une carte stylée -->
        <form @submit.prevent="submit" class="w-full max-w-md mx-auto bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Connexion</h2>

            <!-- Champ E-mail -->
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

            <!-- Champ Mot de passe -->
            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Case à cocher 'Se souvenir de moi' -->
            <div class="mt-4 flex items-center justify-between">
                <label class="flex items-center text-sm text-gray-600">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2">Se souvenir de moi</span>
                </label>

                <!-- Lien mot de passe oublié -->
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-indigo-600 hover:underline"
                >
                    Mot de passe oublié ?
                </Link>
            </div>

            <!-- Boutons et lien d'inscription -->
            <div class="mt-6 flex flex-col gap-4">
                <!-- Bouton 'Se connecter' -->
                <PrimaryButton
                    class="w-full py-2 text-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    Connexion
                </PrimaryButton>

                <!-- Lien d'inscription -->
                <p class="text-center text-sm text-gray-600">
                    Pas encore de compte ?
                    <Link
                        :href="route('register')"
                        class="text-indigo-600 font-medium hover:underline"
                    >
                        S'inscrire
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
