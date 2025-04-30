<template>

    <div class="mt-2  formulaire">
        <div v-if="currentStep === 1" class="row">
            <div class="col-12 mb-4">
                <h4 class="">Informations générales</h4>
                <p class="text-orange fw-bold">*Représente un champ obligatoire</p>
            </div>
            <div class="col-12 mb-4">
                <label for="" class="form-label">Titre de la formation *</label>
                <input type="text" class="form-control" placeholder="Exemple : Formation en UX/UI">
            </div>
            <div class="col-12 mb-4">
                <label for="" class="form-label">Domaine de la formation *</label>
                <input type="text" class="form-control" placeholder="Exemple : Formation en Informatique">
            </div>
            <div class="col-lg-6 col-md-6 col-12 mb-4">
                <label for="" class="form-label">Prix de la formation (en €)*</label>
                <input type="number" class="form-control" placeholder="0.00 €">
            </div>
            <div class="col-lg-6 col-md-6 col-12 mb-4">
                <label for="" class="form-label">Type de formation*</label>
                <div class="row">
                    <div class="col-6">
                        <input type="radio" id="oui" name="type" class="d-none">
                        <label for="oui" class="btn checkBtn w-100">Certifiante</label>
                    </div>
                    <div class="col-6">
                        <input type="radio" id="non" name="type" class="d-none">
                        <label for="non" class="btn checkBtn w-100">Certifiante</label>
                    </div>
                </div>
            </div>
            <div class="col-12 mb-4">
                <label for="" class="form-label">Image de couverture</label>
                <div class="jointe text-center">
                    <input type="file" id="file" class="d-none" @change="updateFileName">

                    <label for="file" class="">
                        <svg class="mb-2" xmlns="http://www.w3.org/2000/svg" width="71" height="53" viewBox="0 0 71 53"
                            fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M20.917 11.6665C18.5963 11.6665 16.3708 12.5884 14.7298 14.2293C13.0889 15.8703 12.167 18.0959 12.167 20.4165C12.167 22.7371 13.0889 24.9627 14.7298 26.6037C16.3708 28.2446 18.5963 29.1665 20.917 29.1665C23.2376 29.1665 25.4632 28.2446 27.1042 26.6037C28.7451 24.9627 29.667 22.7371 29.667 20.4165C29.667 18.0959 28.7451 15.8703 27.1042 14.2293C25.4632 12.5884 23.2376 11.6665 20.917 11.6665ZM18.0003 20.4165C18.0003 19.643 18.3076 18.9011 18.8546 18.3541C19.4016 17.8071 20.1434 17.4998 20.917 17.4998C21.6905 17.4998 22.4324 17.8071 22.9794 18.3541C23.5264 18.9011 23.8337 19.643 23.8337 20.4165C23.8337 21.1901 23.5264 21.9319 22.9794 22.4789C22.4324 23.0259 21.6905 23.3332 20.917 23.3332C20.1434 23.3332 19.4016 23.0259 18.8546 22.4789C18.3076 21.9319 18.0003 21.1901 18.0003 20.4165Z"
                                fill="#0068A9" />
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M9.25 0C6.92936 0 4.70376 0.921873 3.06282 2.56282C1.42187 4.20376 0.5 6.42936 0.5 8.75L0.5 43.75C0.5 46.0706 1.42187 48.2962 3.06282 49.9372C4.70376 51.5781 6.92936 52.5 9.25 52.5H61.75C64.0706 52.5 66.2962 51.5781 67.9372 49.9372C69.5781 48.2962 70.5 46.0706 70.5 43.75V8.75C70.5 6.42936 69.5781 4.20376 67.9372 2.56282C66.2962 0.921873 64.0706 0 61.75 0H9.25ZM61.75 5.83333H9.25C8.47645 5.83333 7.73459 6.14062 7.18761 6.68761C6.64062 7.23459 6.33333 7.97645 6.33333 8.75V43.75C6.33333 44.5235 6.64062 45.2654 7.18761 45.8124C7.73459 46.3594 8.47645 46.6667 9.25 46.6667H21.8325L41.8933 26.6029C42.7059 25.7902 43.6705 25.1456 44.7322 24.7058C45.7939 24.266 46.9319 24.0396 48.081 24.0396C49.2302 24.0396 50.3682 24.266 51.4299 24.7058C52.4916 25.1456 53.4562 25.7902 54.2687 26.6029L64.6667 37.0008V8.75C64.6667 7.97645 64.3594 7.23459 63.8124 6.68761C63.2654 6.14062 62.5235 5.83333 61.75 5.83333ZM61.75 46.6667H30.0808L46.0204 30.73C46.5674 30.1832 47.3091 29.876 48.0825 29.876C48.8559 29.876 49.5976 30.1832 50.1446 30.73L64.3954 44.9808C64.1609 45.4847 63.7873 45.911 63.3186 46.2097C62.85 46.5083 62.3057 46.6669 61.75 46.6667Z"
                                fill="#0068A9" />
                        </svg><br>
                        Cliquez pour sélectionner une image
                    </label>
                    <small>( JPG, PNG, mas 5MB)</small>
                    <p>{{ fileName || '' }}</p>
                </div>
            </div>
        </div>
        <div v-if="currentStep === 2" class="row">
            <div class="col-12 mb-4">
                <h4>Module et contenu</h4>
            </div>
            <moduleComponent />
        </div>
        <div class="col-12 mb-4 mt-4 d-flex"
            :class="{ 'justify-content-between': currentStep > 1, 'justify-content-end': currentStep === 1 }">
            <button v-if="currentStep > 1" class="btn btn-primary fs-sm px-3 py-2" @click="prevStep">
                <i class="bi bi-chevron-left"></i> Précédent
            </button>
            <button class="btn btn-primary fs-sm px-3 py-2" @click="nextStep">
                {{ currentStep === 2 ? 'Enregistrer' : 'Suivant' }}
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
 
</template>
<script>
import 'bootstrap';
import '@popperjs/core';
import moduleComponent from './module.vue'
export default {
    components: {
        moduleComponent
    },
    data() {
        return {
            currentStep: 1, // Étape initiale
            fileName: "",
            imageUrl: "/assets/img/icons/upload.svg", // Image par défaut
            isDefaultImage: true, // Vérifie si c'est l'image par défaut
            modules: [
                {
                    selectedLevel: "", // Niveau d'expérience
                    moduleName: "", // Nom de la société
                    moduleOrdre: "", // Date de début
                    moduleDuree: "", // Date de fin
                    moduleDescription: "", // Description du poste
                },
            ],
        };
    },
    mounted() {

    },
    methods: {
        nextStep() {
            if (this.currentStep < 2) {
                this.currentStep += 1;
            }
        },
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep -= 1;
            }
        },
        updateFileName(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
            }
        },
        addmodule() {
            this.modules.push({
                selectedLevel: "",
                moduleName: "",
                moduleOrdre: "",
                moduleDuree: "",
                moduleDescription: "",
            });
        },
    },


};
</script>