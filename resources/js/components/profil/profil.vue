<template>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-1 card-profil">
                <div class="card-header">
                    <h5>Profil</h5>
                    <p>Modifier votre profil admin</p>
                </div>
                <div class="card-body">
                    <div class="row formulaire">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Nom</label>
                            <input type="text" class="form-control" placeholder="Entrer un nom">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Prénom</label>
                            <input type="text" class="form-control" placeholder="Entrer un prénom">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">E-mail</label>
                            <input type="email" class="form-control" placeholder="Entrer un e-mail">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Ville</label>
                            <input type="text" class="form-control" placeholder="Entrer un ville">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="" class="form-label">Photo de profil</label>
                            <div class="jointe">
                                <input type="file" id="file" class="d-none" @change="updateFileName">
                                <i class="ti ti-cloud-upload"></i>
                                <p>{{ fileName || 'Sélectionner un fichier' }}</p>
                                <label for="file" class="btn btn-secondary">Parcourir</label>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Numéro</label>
                            <input type="tel" class="form-control" placeholder="Entrer un numéro">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" placeholder="Entrer un mot de passe">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Adresse</label>
                            <input type="text" class="form-control" placeholder="Entrer un adressee">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Code postal</label>
                            <input type="number" class="form-control" placeholder="Entrer un code postal">
                        </div>
                        <div class="col-12 mb-3 mt-5 d-flex justify-content-center">
                            <button class="btn btn-secondary mx-2">Modifier</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import 'bootstrap';
import '@popperjs/core';


export default {
    data() {
        return {
            fileName: "",
        };
    },
    methods: {
        updateFileName(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name;
            }
        },
        previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                this.imageUrl = URL.createObjectURL(file);
                this.isDefaultImage = false; // Enlève la classe quand une image est sélectionnée
            }
        },
    },

    mounted() {

    }


};
</script>