<script setup>
import Container from "../../../Components/Container.vue";
import Title from "../../../Components/Title.vue";
import InputField from "../../../Components/InputField.vue";
import PrimaryBtn from "../../../Components/PrimaryBtn.vue";
import ErrorMessages from "../../../Components/ErrorMessages.vue";
import SessionMessages from "../../../Components/SessionMessages.vue";
import { router, useForm } from "@inertiajs/vue3";

const props = defineProps({
  user: Object,
  status: String,
});

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  // Ces champs recevront les objets File
  profile_image: null,
  logo_startup: null,
  visibility: props.user.investisseur?.visibility || '',
  specialty: props.user.coach?.specialty || '',
  phone_number: props.user.startup?.phone_number || '',
  domain_name: props.user.startup?.domain_name || '',
  // Pour l'affichage de l'image existante
  image_url: props.user.investisseur?.profile_image || props.user.coach?.profile_image || '',
  logo_url: props.user.startup?.logo_startup || ''
});

const resendEmail = (e) => {
  router.post(
    route("verification.send"),
    {},
    {
      onStart: () => (e.target.disabled = true),
      onFinish: () => (e.target.disabled = false),
    }
  );
};
</script>

<template>
  <Container class="mb-6">
    <div class="mb-6">
      <Title>Mettre à jour les informations</Title>
      <p>Mettre à jour les informations de votre profil et votre adresse e-mail.</p>
    </div>

    <ErrorMessages :errors="form.errors" />

    <!-- Utilisation de form.post avec { method: 'patch' } pour forcer l'envoi en multipart -->
    <form @submit.prevent="form.post(route('profile.info'), { method: 'patch' })" class="space-y-6" enctype="multipart/form-data">
      <InputField label="Nom" icon="id-badge" class="w-1/2" v-model="form.name" />
      <InputField label="E-mail" icon="at" class="w-1/2" v-model="form.email" />

      <!-- Pour investisseur et coach, gestion de l'image de profil -->
      <div v-if="user.role === 'investisseur' || user.role === 'coach'">
        <label class="block mb-2">Image de Profil</label>
        <input
          type="file"
          class="w-1/2"
          @change="e => form.profile_image = e.target.files[0]"
          accept="image/*"
        />
        <img
          v-if="form.image_url"
          :src="`/storage/images/${form.image_url}`"
          class="mt-2 w-32 h-32 object-cover"
        />
      </div>

      <!-- Pour startup, gestion du logo -->
      <div v-if="user.role === 'startup'">
        <label class="block mb-2">Logo de la Startup</label>
        <input
          type="file"
          class="w-1/2"
          @change="e => form.logo_startup = e.target.files[0]"
          accept="image/*"
        />
        <img
          v-if="form.logo_url"
          :src="`/storage/images/${form.logo_url}`"
          class="mt-2 w-32 h-32 object-cover"
        />
        <InputField label="Nom de Domaine" class="w-1/2" v-model="form.domain_name" />
        <InputField label="Numéro de Téléphone" class="w-1/2" v-model="form.phone_number" />
      </div>

      <div v-if="user.role === 'investisseur'">
        <InputField label="Visibilité" class="w-1/2" v-model="form.visibility" :options="['public', 'private']" />
      </div>

      <div v-if="user.role === 'coach'">
        <InputField label="Spécialité" class="w-1/2" v-model="form.specialty" />
      </div>

      <div v-if="user.email_verified_at === null">
        <SessionMessages :status="status" />
        <p>
          Votre adresse e-mail n'est pas vérifiée.
          <button @click="resendEmail" class="text-indigo-500 font-medium underline dark:text-indigo-400">
            Cliquez ici pour renvoyer l'e-mail.
          </button>
        </p>
      </div>

      <PrimaryBtn :disabled="form.processing">Enregistrer</PrimaryBtn>
    </form>
  </Container>
</template>
