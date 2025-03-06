<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',  // Nom de Startup ou d'Investisseur
    email: '',
    password: '',
    password_confirmation: '',
    role: 'startup',  // Startup est le rôle par défaut
    visibility: '',
    image: null,
    domain_name: '',  // Domaine de la Startup
    specialty: ''  // Spécialité du Coach
});

const onRoleChange = (role) => {
    console.log("Rôle sélectionné :", role);
    form.role = role; // Mettez à jour `form.role` explicitement si nécessaire
    // Si le rôle est "investisseur", effacer le champ `name` et définir `investor_name`
    if (role === 'investisseur') {
        form.name = '';  // On vide le champ `name` pour les investisseurs
    }
};

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Inscription" />
       
        <form @submit.prevent="submit" class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-3xl font-semibold text-center text-gray-800 mb-6">Inscription</h2>

            <div class="mt-4">
                <InputLabel value="Choisissez votre rôle" />
                <div class="flex space-x-6 mt-2">
                    <!-- Startup -->
                    <label class="radio-label flex items-center">
                        <input 
                            type="radio" 
                            name="role" 
                            value="startup" 
                            v-model="form.role" 
                            @change="onRoleChange('startup')" />
                        🚀 Startup
                    </label>
                    <!-- Coach -->
                    <label class="radio-label flex items-center">
                        <input 
                            type="radio" 
                            name="role" 
                            value="coach" 
                            v-model="form.role" 
                            @change="onRoleChange('coach')" />
                        🧑‍🏫 Coach
                    </label>
                    <!-- Investisseur -->
                    <label class="radio-label flex items-center">
                        <input 
                            type="radio" 
                            name="role" 
                            value="investisseur" 
                            v-model="form.role" 
                            @change="onRoleChange('investisseur')" />
                        💼 Investisseur
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <!-- Nom de la startup, du coach ou de l'investisseur -->
            <div class="mt-4">
                <div v-if="form.role == 'coach'">
                    <InputLabel for="name" value="Nom du Coach" />
                    <TextInput
                        id="coach_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div v-if="form.role == 'startup'">
                    <InputLabel for="name" value="Nom de la Startup" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div v-if="form.role === 'investisseur'">
                    <InputLabel for="name" value="Nom de l'Investisseur" />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.investor_name" />
                </div>
            </div>

            <!-- Champ email -->
            <div class="mt-4">
                <InputLabel for="email" value="Adresse e-mail" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Mot de passe -->
            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Champs spécifiques selon le rôle -->
            <div v-if="form.role === 'investisseur'" class="mt-4">
                <InputLabel for="visibility" value="Visibilité" />
                <select v-model="form.visibility" class="mt-1 block w-full">
                    <option value="public">Public</option>
                    <option value="private">Privée</option>
                </select>
                <InputError class="mt-2" :message="form.errors.visibility" />

                <InputLabel for="image" value="Image" />
                <input type="file" id="image" @change="e => form.image = e.target.files[0]" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.image" />
            </div>

            <div v-if="form.role === 'startup'" class="mt-4">
                <InputLabel for="domain_name" value="Nom de Domaine" />
                <TextInput
                    id="domain_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.domain_name"
                />
                <InputError class="mt-2" :message="form.errors.domain_name" />
            </div>

            <div v-if="form.role === 'coach'" class="mt-4">
                <InputLabel for="specialty" value="Spécialité" />
                <TextInput
                    id="specialty"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.specialty"
                />
                <InputError class="mt-2" :message="form.errors.specialty" />
            </div>

            <!-- Bouton de soumission -->
           <div class="mt-6 flex flex-col gap-4">
                <PrimaryButton
                    class="w-full py-2 text-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 ease-in-out"
                    :class="{ 'opacity-50': form.processing }"
                    :disabled="form.processing"
                >
                    Inscription
                </PrimaryButton>

                <p class="text-center text-sm text-gray-600">
                    Déjà inscrit ?
                    <Link
                        :href="route('login')"
                        class="text-indigo-600 font-medium hover:underline"
                    >
                        Se connecter
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
.radio-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 1.1rem;
}

.radio-label input {
    margin-right: 8px;
}
</style>
