@extends('coach.layouts.fullwidth')
@section('title')
Changer forfait
@endsection
@section('page_description')
@endsection

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y mt-0 pt-5 auth-container h-100 abonnements">
    <img src="{{ asset('assets/img/dash/circletopLeft.png') }}" alt="" class="circle circletop">
    <img src="{{ asset('assets/img/dash/circleBottomLeft.png') }}" alt="" class="circle circleBottomLeft">
    <img src="{{ asset('assets/img/dash/circleRight2.png') }}" alt="" class="circle circleRight2">
    <div class="row mt-5 mb-5 pb-5">
        <div class="col-12 text-center">
            <h1 class="title">Choisir un abonnement</h1>
        </div>
    </div>
    <div class="row mt-5 mt-lg-5 mt-md-5 d-flex justify-content-center">
        <div class="col-lg-6 col-md-6 col-sm-6 col-12 mb-5 d-flex justify-content-center">
            <div class="card cardPack">
                <div class="card-header">
                    <h4>Gratuit</h4>
                </div>
                <div class="card-body">
                    <p>
                        <i class="bi bi-check2"></i>
                        10 € de crédits
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        1 carte de visite connecté
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        3 comptes réseaux sociaux
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        visuels illimités
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Chatbot IA
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Planification
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Statistiques
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    <a href="#" class="btn btn-secondary">Commencer gratuitement</a>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6 col-12 mb-5 d-flex justify-content-center">
            <div class="card cardPack">
                <div class="card-header">
                    <div class="d-flex justify-content-center">
                        <div class="box">
                            <img src="{{ asset('assets/img/icons/medium.png') }}" alt="">
                        </div>
                    </div>
                    <h4>Standard</h4>
                    <!-- Radio buttons for pricing selection -->
                    <div class="pricing-options d-flex justify-content-center">
                        <div class="form-check me-3">
                            <!--<input class="form-check-input" type="radio" name="prix"
                                    id="mensuel_" value="mensuel" checked>-->
                            <label class="form-check-label" for="mensuel_">
                                29 € <small>/Mois ou </small> 299 € <small>/ An</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        <i class="bi bi-check2"></i>
                        10 € de crédits
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        1 carte de visite connecté
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        3 comptes réseaux sociaux
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        visuels illimités
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Chatbot IA
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Planification
                    </p>
                    <p>
                        <i class="bi bi-check2"></i>
                        Statistiques
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-center">
                    <a href="#" class="btn btn-secondary">Acheter</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/vendor/js/dropdown-hover.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script src="{{ asset('assets/js/charts-apex.js') }}"></script>
@endsection