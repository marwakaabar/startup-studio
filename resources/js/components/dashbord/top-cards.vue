<template>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
        <div class="card cardTop">
            <div class="card-header">
                <img src="/assets/img/dash/calendar.png" alt="">
            </div>
            <div class="card-body">
                <h5>Nombre de rendez-vous réalisés</h5>
            </div>
            <div class="card-footer">
                <p>{{nb_rdv}} rendez-vous</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
        <div class="card cardTop">
            <div class="card-header">
                <img src="/assets/img/dash/cart.png" alt="">
            </div>
            <div class="card-body">
                <h5>Nombre de formation achetées</h5>
            </div>
            <div class="card-footer">
                <p>{{nb_formation}} formations</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4">
        <div class="card cardTop">
            <div class="card-header">
                <img src="/assets/img/dash/star.png" alt="">
            </div>
            <div class="card-body">
                <h5>Taux de recommandation sur le site web</h5>
            </div>
            <div class="card-footer">
                <p>{{taux_recommandation}}%</p>
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
            nb_rdv: 25,
            nb_formation:150,
            taux_recommandation:67.4,
        };
    },
    methods: {
    },

    mounted() {

    }


};
</script>