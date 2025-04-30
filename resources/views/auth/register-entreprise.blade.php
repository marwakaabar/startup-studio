@extends('coach.layouts.fullwidth')
@section('title')
S'inscrire
@endsection
@section('page_description')
@endsection

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="{{ asset('assets/img/dash/circletop.png') }}" alt="" class="circle circletop">
    <img src="{{ asset('assets/img/dash/circleright.png') }}" alt="" class="circle circleright">
    <img src="{{ asset('assets/img/dash/circletopright.png') }}" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-lg-5 col-md-6 col-12">
            <div class="card cardLogin mb-5">
                <div class="card-body">
                    <form action="" method="post">
                        @csrf
                        @csrf
                        <h5 class="mb-5 title">Créer un compte</h5>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="18"
                                    viewBox="0 0 20 18" fill="none">
                                    <path
                                        d="M16 12H14V14H16M16 8H14V10H16M18 16H10V14H12V12H10V10H12V8H10V6H18M8 4H6V2H8M8 8H6V6H8M8 12H6V10H8M8 16H6V14H8M4 4H2V2H4M4 8H2V6H4M4 12H2V10H4M4 16H2V14H4M10 4V0H0V18H20V4H10Z"
                                        fill="#B5B5B5" />
                                </svg>
                            </span>
                            <input type="text" class="form-control" placeholder="Nom de l’entreprise" name="nom">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18"
                                    viewBox="0 0 22 18" fill="none">
                                    <path
                                        d="M19.0769 1H2.92308C1.86099 1 1 1.86099 1 2.92308V14.4615C1 15.5236 1.86099 16.3846 2.92308 16.3846H19.0769C20.139 16.3846 21 15.5236 21 14.4615V2.92308C21 1.86099 20.139 1 19.0769 1Z"
                                        stroke="#B5B5B5" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path d="M4.07812 4.07666L11.0012 9.46128L17.9243 4.07666" stroke="#B5B5B5"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <input type="email" class="form-control" placeholder="Mail du gérant" name="mail">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="23"
                                    viewBox="0 0 13 23" fill="none">
                                    <path
                                        d="M12.0727 5.57267C11.5589 5.57267 11.1453 5.98627 11.1453 6.5V15.7863C11.1453 18.3506 9.06865 20.4317 6.5 20.4317C3.93135 20.4317 1.85466 18.3506 1.85466 15.7863V4.64099C1.85466 3.10415 3.0998 1.85466 4.64099 1.85466C6.18218 1.85466 7.42733 3.10415 7.42733 4.64099V13.9317C7.42733 14.4454 7.00938 14.859 6.5 14.859C5.99062 14.859 5.57267 14.4454 5.57267 13.9317V6.5C5.57267 5.98627 5.15472 5.57267 4.64535 5.57267C4.13161 5.57267 3.71802 5.98627 3.71802 6.5V13.9317C3.71802 15.4729 4.96316 16.718 6.50435 16.718C8.04555 16.718 9.29069 15.4729 9.29069 13.9317V4.64535C9.28634 2.08104 7.20529 0 4.64099 0C2.07669 0 0 2.08104 0 4.64535V15.7907C0 19.3825 2.91259 22.2907 6.5 22.2907C10.0874 22.2907 13 19.3781 13 15.7907V6.5C13 5.98627 12.5864 5.57267 12.0727 5.57267Z"
                                        fill="#B5B5B5" />
                                </svg>
                            </span>
                            <input type="number" class="form-control" placeholder="Siret" name="Siret">
                        </div>
                        <a href="{{ route('abonnements') }}"
                            class="btn btn-secondary fw-bold w-100 mb-4">Connexion</a>
                    </form>
                    <div class="text-center mb-4">
                        <p class="text">Vous avez un compte ? <a href="{{ route('login') }}">S’identifier</a></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center px-3 mb-4">
                        <div class="hr"></div>
                        <p>ou</p>
                        <div class="hr"></div>
                    </div>
                    <div class="card-footer bg-transp border-0 pt-0 pb-0">
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-rect">
                                <span class="iconify" data-icon="flowbite:google-solid" data-inline="false"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection