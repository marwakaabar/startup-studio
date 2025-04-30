@extends('coach.layouts.fullwidth')
@section('title')
Vous avez oublié votre mot de passe
@endsection
@section('page_description')
@section('content')
<section class="h-100 auth" style="position: relative;">
    <div class="row justify-content-center h-100 m-0" style="height:100vh !important;">
        <div class="col-md-6 col-sm-6" style="border-radius:0px 30px 30px 0px;background: #E7F2F0;">
            <img src="{{ asset('assets/img/dash/logo.svg') }}" alt="" class="imgLogo">
            <div class="d-flex justify-content-center align-items-center left">
            <div class="card cardLogin">
                <div class="card-header text-start">
                    <a href="{{ route('login') }}" class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M15.75 18.75L9 12L15.75 5.25" stroke="#313131" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Retour à la connexion 
                    </a>
                    <h4>Vous avez oublié votre mot de passe ?</h4>
                    <p>Ne vous inquiétez pas, cela nous arrive à tous. Entrez votre adresse e-mail ci-dessous pour récupérer votre mot de passe</p>
                </div>

                <div class="card-body pb-0">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row">
                            <div class="mb-4">
                                <label for="email" class="form-label text-md-end">{{ __('Email') }}</label>
                                <input id="email" type="email" placeholder="alma.lawson@example.com" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <button type="submit" class="btn btn-secondary w-100">
                                    Envoyer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer pt-0">
                    <div class="col-12 mb-4">
                        <div class="divider">
                            <div class="divider-text">Ou connectez-vous avec</div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <button class="btn btn-white w-100">
                            <img src="{{asset('assets/img/dash/image 157.png')}}" alt="" class="mr-2">
                            Se connecter avec Google
                        </button>
                    </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-6  d-lg-flex d-md-flex d-sm-flex d-none justify-content-start align-items-center h-100" style="background: #fff;">
            <div class="card cardlogin-2">
                <div class="d-flex justify-content-center gifBox">
                    <img src="{{asset('assets/img/dash/Animation - 1734346146429.gif')}}" alt="Description du GIF" class="gif">
                </div>
                <div class="card-body">
                    <h4>Facilitez chaque étape de votre mission</h4>
                    <p>Accédez à un espace intuitif et dynamique,
                        conçu pour simplifier vos tâches et optimiser
                        votre efficacité. Retrouvez tous vos outils en
                        un clic et avancez en toute confiance.
                    </p>
                    <ul class="media">
                        <li><img src="{{asset('assets/img/avatars/1.png')}}" alt=""></li>
                        <li><img src="{{asset('assets/img/avatars/2.png')}}" alt=""></li>
                        <li><img src="{{asset('assets/img/avatars/3.png')}}" alt=""></li>
                        <li><img src="{{asset('assets/img/avatars/4.png')}}" alt=""></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection