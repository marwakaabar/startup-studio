@extends('coach.layouts.fullwidth')
@section('title')
    Connexion
@endsection
@section('page_description')
@endsection

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y auth-container" style="height:100vh;">
    <img src="{{ asset('assets/img/dash/robot_smile.png') }}" alt="" class="circle circletop" style="position: absolute; top: 10px; left: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot_smile_2.png') }}" alt="" class="circle circleright" style="position: absolute; bottom: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot.png') }}" alt="" class="circle circletopright" style="position: absolute; top: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <div class="row d-flex justify-content-center w-100">
            <div class="col-lg-5 col-md-6 col-12">
                <div class="card cardLogin">
                    <div class="card-body">
                        <span class="salut mb-1">Réinitialiser le mot de passe 🔒</span>
                        <p class="mb-4">Pour <span class="fw-medium">{{ $email }}</span></p>
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            @if (Session::has('error'))
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                    {{ Session::get('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    </button>
                                </div>
                            @endif
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">
                            <div class="input-group mb-4">
                                <span class="input-group-text">
                                    <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                                </span>
                                <input type="password" class="form-control" placeholder="Nouveau mot de passe"
                                    name="password" required autocomplete="new-password">
                            </div>
                            <div class="input-group mb-4">
                                <span class="input-group-text">
                                    <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                                </span>
                                <input type="password" class="form-control" placeholder="Confirmer le mot de passe"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <button class="btn btn-primary w-100 mb-4" type="submit">Définir un nouveau mot de
                                passe</button>
                        </form>
                        <div class="text-center mb-4">
                            <p class="text">Vous n'êtes pas encore client? <a href="{{ route('register') }}">Créer
                                    un compte</a></p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center px-3 mb-4">
                            <div class="hr"></div>
                            <p>ou</p>
                            <div class="hr"></div>
                        </div>
                        <div class="card-footer bg-transp border-0">
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="btn">
                                    <img src="{{ asset('assets/img/icons/gmail.png') }}" alt="">
                                </button>
                                <!--<button class="btn">
                                                                                                    <img src="{{ asset('assets/img/icons/facebook.png') }}" alt="">
                                                                                                </button>-->
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
