<script setup>
import Container from "../../Components/Container.vue";
import Title from "../../Components/Title.vue";
import TextLink from "../../Components/TextLink.vue";
import InputField from "../../Components/InputField.vue";
import PrimaryBtn from "../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../Components/ErrorMessages.vue";
import { useForm } from "@inertiajs/vue3";

// Définir l'état initial du formulaire
const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "startup", // Définir le rôle par défaut
    visibility: "public", // Valeur par défaut de visibilité
    image: null,
    domain_name: "",
    specialty: "",
});

// Fonction de gestion du changement de rôle
const onRoleChange = (role) => {
    form.role = role;
    form.name = ""; // Vider le champ 'name' pour tous les rôles à chaque changement
    form.domain_name = ""; // Vider le champ 'domain_name' pour tous les rôles
    form.specialty = ""; // Vider le champ 'specialty' pour tous les rôles
};

// Fonction de soumission du formulaire
const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="- Inscription"/>
    <Container class="w-1/2">
        <div class="mb-8 text-center">
            <Title>Créer un nouveau compte</Title>
            <p>
                Vous avez déjà un compte ? 
                <TextLink routeName="login" label="Se connecter" />
            </p>
        </div>

        <!-- Messages d'erreur -->
        <ErrorMessages :errors="form.errors"/>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Choisir le rôle -->
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
                    <!-- Mentor -->
                    <label class="radio-label flex items-center">
                        <input 
                            type="radio" 
                            name="role" 
                            value="mentor" 
                            v-model="form.role" 
                            @change="onRoleChange('mentor')" />
                        🧑‍💼 Mentor
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
            </div>

            <!-- Nom -->
            <div class="mt-4">
                <div v-if="form.role === 'mentor'">
                    <InputField label="Nom du Mentor" v-model="form.name" icon="user" />
                </div>
                <div v-if="form.role === 'startup'">
                    <InputField label="Nom de la Startup" v-model="form.name" icon="building" />
                </div>
                <div v-if="form.role === 'investisseur'">
                    <InputField label="Nom de l'Investisseur" v-model="form.name" icon="briefcase" />
                </div>
            </div>

            <!-- Email -->
            <div class="mt-4">
                <InputField label="Email" icon="envelope" v-model="form.email" />
            </div>

            <!-- Mot de passe -->
            <div class="mt-4">
                <InputField label="Mot de passe" type="password" icon="key" v-model="form.password" />
            </div>

            <!-- Confirmation du mot de passe -->
            <div class="mt-4">
                <InputField label="Confirmer le mot de passe" type="password" icon="key" v-model="form.password_confirmation" />
            </div>

            <!-- Visibilité pour Investisseur -->
            <div v-if="form.role === 'investisseur'" class="mt-4">
                        Visibilité

                <InputLabel for="visibility" value="Visibilité" />
                <select v-model="form.visibility" class="mt-1 block w-full px-4 py-2 rounded-md border-2 border-gray-300 focus:border-blue-500 focus:outline-none">
                    <option value="public">Public</option>
                    <option value="private">Privée</option>
                </select>
                <InputError class="mt-2" :message="form.errors.visibility" />
            </div>

            <!-- Image pour Investisseur -->
           <!-- <div v-if="form.role === 'investisseur'" class="mt-4">
            Image
                <InputLabel for="image" value="Image" />
                <input type="file" id="image" @change="e => form.image = e.target.files[0]" class="mt-1 block w-full px-4 py-2 rounded-md border-2 border-gray-300 focus:border-blue-500 focus:outline-none" />
                <InputError class="mt-2" :message="form.errors.image" />
            </div>-->

            <!-- Nom de Domaine pour Startup -->
            <div v-if="form.role === 'startup'" class="mt-4">
                <InputField label="Nom de Domaine" v-model="form.domain_name" icon="globe" />
            </div>

            <!-- Spécialité pour Mentor -->
            <div v-if="form.role === 'mentor'" class="mt-4">
                <InputField label="Spécialité" v-model="form.specialty" icon="star" />
            </div>

            <!-- Conditions d'utilisation -->
            <p class="text-slate-500 text-sm dark:text-slate-400">
                En créant un compte, vous acceptez nos Conditions d'utilisation et notre Politique de confidentialité.
            </p>

            <!-- Bouton d'inscription -->
            <PrimaryBtn :disabled="form.processing">S'inscrire</PrimaryBtn>
        </form>
    </Container>
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

/* Style personnalisé pour les champs de texte */
input[type="text"],
input[type="password"],
input[type="email"],
select {
    border-radius: 8px;
    padding: 0.75rem;
    border: 2px solid #e2e8f0;
    width: 100%;
    transition: border-color 0.2s;
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus,
select:focus {
    border-color: #3b82f6;
    outline: none;
}
</style>
