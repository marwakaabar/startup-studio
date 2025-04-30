<template>
    <div class="row filters">
        <div class="col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-end align-items-center">
            <a href="/abonnements" class="btn btn-primary me-2 mb-4">Acheter un abonnement</a>
        </div>
    </div>
    <div class="mt-4">
        <div class="col-12 table-responsive">
            <table id="abonnementsTable" class="table table-1 w-100">
                <thead>
                    <tr>
                        <th>Type d’abonnement</th>
                        <th>Durée</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in abonnements" :key="item.id">
                        <td><span>{{ item.type }}</span></td>
                        <td><span>{{ item.duree }}</span></td>
                        <td><span>{{ item.price }} €</span></td>
                        <td>
                            <div class="d-flex justify-content-end align-items-center">
                                <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#renouvelerModal">Renouveler
                                    <svg class="" xmlns="http://www.w3.org/2000/svg" width="16" height="14"
                                        viewBox="0 0 22 23" fill="none">
                                        <path d="M20.9987 1.5V13.5H14.332" stroke="#7E57C2" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M1 21.4996C1 16.7257 2.05357 12.1474 3.92893 8.77171C5.8043 5.39606 8.34784 3.49963 11 3.49963C13.461 3.50414 15.8346 5.14195 17.6667 8.09963L21 13.4996"
                                            stroke="#7E57C2" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- delete Modal -->
    <div class="modal fade modal-1" id="renouvelerModal" tabindex="-1" aria-labelledby="renouvelerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-2">
                <div class="modal-body modal-Quest">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="col-12 deleteBox d-flex justify-content-center mb-2">
                        <div class="box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="67" viewBox="0 0 12 67"
                                fill="none">
                                <path
                                    d="M0 60.75C0 62.3413 0.632142 63.8674 1.75736 64.9926C2.88258 66.1179 4.4087 66.75 6 66.75C7.5913 66.75 9.11742 66.1179 10.2426 64.9926C11.3679 63.8674 12 62.3413 12 60.75C12 59.1587 11.3679 57.6326 10.2426 56.5074C9.11742 55.3821 7.5913 54.75 6 54.75C4.4087 54.75 2.88258 55.3821 1.75736 56.5074C0.632142 57.6326 0 59.1587 0 60.75ZM3 45H9C9.4125 45 9.75 44.6625 9.75 44.25V0.75C9.75 0.3375 9.4125 0 9 0H3C2.5875 0 2.25 0.3375 2.25 0.75V44.25C2.25 44.6625 2.5875 45 3 45Z"
                                    fill="#94D196" />
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-2 mb-4">
                        <h5 class="question text-dark">Veuillez introoduire la durée de votre abonnement</h5>
                    </div>
                    <div class="row formulaire">
                        <div class="col-12 mb-3">
                            <label for="" class="form-label text-primary">Durée de l’abonnement</label>
                            <select name="" id="" class="form-select">
                                <option value="" selected disabled>Choisir une durée</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary"  data-bs-toggle="modal"
                    data-bs-target="#confirmModal">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <!-- confirmation  Modal -->
    <div class="modal fade modal-1" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content  p-3  pt-3">
                <div class="modal-body modal-Quest px-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <div class="col-12 deleteBox d-flex justify-content-center mb-2">
                        <div class="box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="67" viewBox="0 0 12 67"
                                fill="none">
                                <path
                                    d="M0 60.75C0 62.3413 0.632142 63.8674 1.75736 64.9926C2.88258 66.1179 4.4087 66.75 6 66.75C7.5913 66.75 9.11742 66.1179 10.2426 64.9926C11.3679 63.8674 12 62.3413 12 60.75C12 59.1587 11.3679 57.6326 10.2426 56.5074C9.11742 55.3821 7.5913 54.75 6 54.75C4.4087 54.75 2.88258 55.3821 1.75736 56.5074C0.632142 57.6326 0 59.1587 0 60.75ZM3 45H9C9.4125 45 9.75 44.6625 9.75 44.25V0.75C9.75 0.3375 9.4125 0 9 0H3C2.5875 0 2.25 0.3375 2.25 0.75V44.25C2.25 44.6625 2.5875 45 3 45Z"
                                    fill="#94D196" />
                            </svg>
                        </div>
                    </div>
                    <div class="col-12 text-center mt-2 mb-4">
                        <p><strong>Voulez-vous vraiment renouveler cette abonnement?</strong></p>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div class="col-lg-8 col-md-8 col-12 mb-3">
                            <table class="table-2 table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                    viewBox="0 0 20 19" fill="none">
                                                    <path
                                                        d="M3.69829 17.1968H16.3008M9.60527 1.23301C9.64412 1.16244 9.70121 1.10359 9.77056 1.06261C9.83991 1.02162 9.91899 1 9.99955 1C10.0801 1 10.1592 1.02162 10.2285 1.06261C10.2979 1.10359 10.355 1.16244 10.3938 1.23301L13.0512 6.27762C13.1145 6.39443 13.203 6.49576 13.3102 6.57433C13.4173 6.65291 13.5406 6.70678 13.671 6.73208C13.8015 6.75738 13.9359 6.75348 14.0647 6.72067C14.1935 6.68785 14.3134 6.62693 14.4158 6.54227L18.2659 3.24401C18.3398 3.1839 18.4309 3.14879 18.526 3.14373C18.6212 3.13867 18.7154 3.16394 18.7953 3.21588C18.8752 3.26782 18.9365 3.34376 18.9704 3.43277C19.0044 3.52178 19.0092 3.61927 18.9843 3.7112L16.4331 12.9345C16.3811 13.1232 16.2689 13.2898 16.1136 13.409C15.9583 13.5283 15.7683 13.5936 15.5726 13.5952H4.42743C4.23149 13.5938 4.04135 13.5286 3.88588 13.4093C3.7304 13.2901 3.61808 13.1233 3.56596 12.9345L1.01575 3.7121C0.990768 3.62017 0.995604 3.52268 1.02956 3.43367C1.06351 3.34466 1.12483 3.26872 1.2047 3.21678C1.28456 3.16484 1.37884 3.13958 1.47398 3.14463C1.56911 3.14969 1.66018 3.1848 1.73409 3.24491L5.58326 6.54317C5.6857 6.62783 5.80562 6.68875 5.93439 6.72157C6.06317 6.75439 6.19761 6.75828 6.32807 6.73298C6.45853 6.70768 6.58178 6.65381 6.68895 6.57523C6.79612 6.49666 6.88457 6.39533 6.94794 6.27852L9.60527 1.23301Z"
                                                        stroke="#474747" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <strong>Type d’abonnement</strong>
                                            </span>
                                        </td>
                                        <td><span>{{ abonnement_actuel.type }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M1 10C1 11.1819 1.23279 12.3522 1.68508 13.4442C2.13738 14.5361 2.80031 15.5282 3.63604 16.364C4.47177 17.1997 5.46392 17.8626 6.55585 18.3149C7.64778 18.7672 8.8181 19 10 19C11.1819 19 12.3522 18.7672 13.4442 18.3149C14.5361 17.8626 15.5282 17.1997 16.364 16.364C17.1997 15.5282 17.8626 14.5361 18.3149 13.4442C18.7672 12.3522 19 11.1819 19 10C19 7.61305 18.0518 5.32387 16.364 3.63604C14.6761 1.94821 12.3869 1 10 1C7.61305 1 5.32387 1.94821 3.63604 3.63604C1.94821 5.32387 1 7.61305 1 10Z"
                                                        stroke="#474747" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path d="M10 5V10L13 13" stroke="#474747" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <strong>Durée</strong>
                                            </span>
                                        </td>
                                        <td><span>{{ abonnement_actuel.duree }}</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                    viewBox="0 0 20 20" fill="none">
                                                    <path
                                                        d="M1 10C1 11.1819 1.23279 12.3522 1.68508 13.4442C2.13738 14.5361 2.80031 15.5282 3.63604 16.364C4.47177 17.1997 5.46392 17.8626 6.55585 18.3149C7.64778 18.7672 8.8181 19 10 19C11.1819 19 12.3522 18.7672 13.4442 18.3149C14.5361 17.8626 15.5282 17.1997 16.364 16.364C17.1997 15.5282 17.8626 14.5361 18.3149 13.4442C18.7672 12.3522 19 11.1819 19 10C19 8.8181 18.7672 7.64778 18.3149 6.55585C17.8626 5.46392 17.1997 4.47177 16.364 3.63604C15.5282 2.80031 14.5361 2.13738 13.4442 1.68508C12.3522 1.23279 11.1819 1 10 1C8.8181 1 7.64778 1.23279 6.55585 1.68508C5.46392 2.13738 4.47177 2.80031 3.63604 3.63604C2.80031 4.47177 2.13738 5.46392 1.68508 6.55585C1.23279 7.64778 1 8.8181 1 10Z"
                                                        stroke="#474747" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M12.401 6C11.732 5.372 10.901 5 10 5C7.79 5 6 7.239 6 10C6 12.761 7.79 15 10 15C10.9 15 11.731 14.628 12.4 14M5 8.5H9M5 11.5H9"
                                                        stroke="#474747" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                                <strong>Prix</strong>
                                            </span>
                                        </td>
                                        <td><span>{{ abonnement_actuel.price }} €</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button class="btn btn-primary mx-2" data-bs-dismiss="modal">Confirmer</button>
                    <button class="btn btn-danger  mx-2" data-bs-dismiss="modal">Annuler</button>
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
            abonnement_actuel: {
                id: 1,
                type: 'Abonnement Premium',
                duree: '1 ans',
                price: 999,
            },
            abonnements: [
                {
                    id: 1,
                    type: 'Abonnement Premium',
                    duree: '1 ans',
                    price: 999,
                },
                {
                    id: 2,
                    type: 'Abonnement Premium',
                    duree: '1 ans',
                    price: 999,
                },
                {
                    id: 3,
                    type: 'Abonnement Premium',
                    duree: '1 ans',
                    price: 999,
                },
                {
                    id: 4,
                    type: 'Abonnement Premium',
                    duree: '1 ans',
                    price: 999,
                },
            ],
        };
    },
    methods: {
    },

    mounted() {
        this.$nextTick(() => {
            $("#abonnementsTable").DataTable({
                searching: false, // Supprime la barre de recherche
                ordering: false,  // Désactive le tri
                paging: true,     // Active la pagination
                info: false,      // Masque "Showing X to Y of Z entries"
                language: {
                    paginate: {
                        previous: "", // Supprime "Précédent"
                        next: "",     // Supprime "Suivant"
                    },
                    lengthMenu: "Afficher _MENU_ entrées",
                    zeroRecords: "Aucun enregistrement trouvé",
                    infoEmpty: "",
                    infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
                },
                dom: "tp", // Supprime les boutons inutiles (t = tableau, p = pagination)
            });
        });

        this.$nextTick(() => {
            $("#linksTable").DataTable({
                searching: false, // Supprime la barre de recherche
                ordering: false,  // Désactive le tri
                paging: true,     // Active la pagination
                info: false,      // Masque "Showing X to Y of Z entries"
                language: {
                    paginate: {
                        previous: "", // Supprime "Précédent"
                        next: "",     // Supprime "Suivant"
                    },
                    lengthMenu: "Afficher _MENU_ entrées",
                    zeroRecords: "Aucun enregistrement trouvé",
                    infoEmpty: "",
                    infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
                },
                dom: "tp", // Supprime les boutons inutiles (t = tableau, p = pagination)
            });
        });
    }


};
</script>