<template>
   <div class="row filters">
        <div
            class="col-lg-8 col-md-8 col-sm-8 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-start align-items-center">
            <div class="input-group me-2 mb-4">
                <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                        <path
                            d="M17.8749 18L12.4244 12.3333M1.52344 7.61111C1.52344 8.47929 1.68792 9.33898 2.00748 10.1411C2.32705 10.9432 2.79544 11.672 3.38592 12.2859C3.9764 12.8998 4.6774 13.3867 5.4489 13.719C6.22039 14.0512 7.04728 14.2222 7.88234 14.2222C8.71741 14.2222 9.54429 14.0512 10.3158 13.719C11.0873 13.3867 11.7883 12.8998 12.3788 12.2859C12.9692 11.672 13.4376 10.9432 13.7572 10.1411C14.0768 9.33898 14.2412 8.47929 14.2412 7.61111C14.2412 6.74293 14.0768 5.88325 13.7572 5.08115C13.4376 4.27905 12.9692 3.55025 12.3788 2.93635C11.7883 2.32245 11.0873 1.83548 10.3158 1.50324C9.54429 1.171 8.71741 1 7.88234 1C7.04728 1 6.22039 1.171 5.4489 1.50324C4.6774 1.83548 3.9764 2.32245 3.38592 2.93635C2.79544 3.55025 2.32705 4.27905 2.00748 5.08115C1.68792 5.88325 1.52344 6.74293 1.52344 7.61111Z"
                            stroke="#C6C6C6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <input type="text" class="form-control" placeholder="Chercher">
            </div>
            <select name="" id="" class="form-select mb-4">
                <option value="" selected disabled>Catégorie</option>
            </select>
        </div>
        <div
            class="col-lg-4 col-md-4 col-sm-4 col-12 d-lg-flex d-md-flex d-sm-flex d-block justify-content-end align-items-center">
            <a :href="'/formation/add'" class="btn btn-primary me-2 mb-4">
                Créer une formation
            </a>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-4 col-md-4 col-sm-6 col-12 d-flex justify-content-center mb-4" v-for="item in formations"
            :key="item.id">
            <div class="card card-1 card-formation">
                <div class="card-header">
                    <img :src="item.image" alt="" class="img-2">
                </div>
                <div class="card-body">
                    <h5>{{ item.price }}€</h5>
                    <p>{{ item.nom }}</p>
                    <div v-if="item.statut === 'oui'" class="statut text-warning d-flex align-items-center justify-content-center">
                        <img src="/assets/img/dash/star2.png" alt="" class="me-2"> Formation certifiante
                    </div>
                    <div v-else-if="item.statut === 'non'" class="statut text-info d-flex align-items-center justify-content-center">
                        <img src="/assets/img/dash/info.png" alt="" class="me-2">Formation non certifiante
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    <a href="/formation/details" class="btn btn-primary fs-sm">Consulter <i class="bi bi-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-12 d-flex justify-content-end mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-md">
                    <li class="page-item prev">
                        <a class="page-link" href="javascript:void(0);"><i class="tf-icon  ti ti-chevron-left"></i></a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">2</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="javascript:void(0);">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">5</a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:void(0);"><i class="tf-icon ti ti-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>
<script>
import 'bootstrap';
import '@popperjs/core';

export default {
    data() {
        return {
            formations: [
                {
                    id: 1,
                    nom: 'Formation en UX/UI',
                    image: 'assets/img/dash/formation1.png',
                    statut: 'non',
                    price: 2000,
                },
                {
                    id: 2,
                    nom: 'Formation en marketing',
                    image: 'assets/img/dash/formation2.png',
                    statut: 'oui',
                    price: 3000,
                },
                {
                    id: 3,
                    nom: 'Formation en Cyber security',
                    image: 'assets/img/dash/formation3.png',
                    statut: 'non',
                    price: 2000,
                },
                {
                    id: 4,
                    nom: 'Formation en python',
                    image: 'assets/img/dash/formation4.png',
                    statut: 'oui',
                    price: 5000,
                },
                {
                    id: 5,
                    nom: 'Azure Fundamentals',
                    image: 'assets/img/dash/formation5.png',
                    statut: 'non',
                    price: 2000,
                },
                {
                    id: 6,
                    nom: 'Apprendre Git et GitHub',
                    image: 'assets/img/dash/formation1.png',
                    statut: 'oui',
                    price: 2000,
                },
            ],
        };
    },
    methods: {
    },

    mounted() {
    },
};
</script>