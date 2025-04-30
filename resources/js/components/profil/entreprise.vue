<template>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card card-1 card-profil">
                <div class="card-header">
                    <h5>Entreprise</h5>
                    <h6>Information de l’entreprise</h6>
                </div>
                <div class="card-body">
                    <div class="row formulaire">
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Nom</label>
                            <input type="text" class="form-control" placeholder="Entrer un nom">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Siret</label>
                            <input type="text" class="form-control" placeholder="Entrer un siret">
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="" class="form-label">E-mail</label>
                            <input type="email" class="form-control" placeholder="Entrer un e-mail">
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="" class="form-label">Numéro</label>
                            <input type="tel" class="form-control" placeholder="Entrer un numéro">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Adresse</label>
                            <input type="text" class="form-control" placeholder="Entrer un adressee">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Code postal</label>
                            <input type="number" class="form-control" placeholder="Entrer un code postal">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Ville</label>
                            <input type="text" class="form-control" placeholder="Entrer une ville">
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Charte graphique</label>
                            <div class="input-group d-flex justify-content-between p-2">
                                <input type="color" class="input-color" placeholder="" name="charte_graphique"
                                    style=" box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.25) !important;"
                                    value="">
                                <button class="input-group-text btn-secondary border-0 rounded" id="basic-addon2">Changer</button>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="" class="form-label">Logo de l’entreprise</label>
                            <div class="jointe">
                                <input type="file" id="logo" class="d-none" @change="updateFileName">
                                <i class="ti ti-cloud-upload"></i>
                                <p>{{ logoName || 'Sélectionner un fichier' }}</p>
                                <label for="logo" class="btn btn-secondary">Parcourir</label>
                            </div>
                        </div>
                        <div class="col-sm-12 mb-3">
                            <label for="" class="form-label">Site web</label>
                            <input type="text" class="form-control" placeholder="Entrer un site web">
                        </div>
                        <div class="col-sm-12 mb-3 mt-3">
                            <h6>Information bancaire</h6>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Banque</label>
                            <input type="number" class="form-control" placeholder="Entrer un nom de banque">
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">Switch</label>
                            <input type="text" class="form-control" placeholder="Entrer un switch">
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                            <label for="" class="form-label">IBAN</label>
                            <input type="number" class="form-control" placeholder="Entrer un IBAN">
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
            logoName: "",
        };
    },
    methods: {
        updateFileName(event) {
            const file = event.target.files[0];
            if (file) {
                this.logoName = file.name;
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