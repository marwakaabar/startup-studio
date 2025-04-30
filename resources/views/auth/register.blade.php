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
    <img src="{{ asset('assets/img/dash/robot_smile.png') }}" alt="" class="circle circletop" style="position: absolute; top: 10px; left: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot_smile_2.png') }}" alt="" class="circle circleright" style="position: absolute; bottom: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot.png') }}" alt="" class="circle circletopright" style="position: absolute; top: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <div class="row d-flex justify-content-center w-100">
        <div class="col-lg-5 col-md-6 col-12">
            <div class="card cardLogin mb-5">
                <div class="card-body">
                    @if (count($errors) > 0)
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                        </button>
                    </div>
                    @endif
                    <form action="{{ route('register') }}" method="post">
                        @csrf

                        <h5 class="mb-5 title">Créer un compte</h5>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/person-gray.png') }}" alt="">
                            </span>
                            <input type="text" class="form-control" placeholder="Nom et prénom du gérant" name="name" value="{{ old('name') }}" required autofocus>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/Mail-2.png') }}" alt="">
                            </span>
                            <input type="email" class="form-control" placeholder="Mail du gérant" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                            </span>
                            <input type="password" class="form-control" placeholder="Mot de passe" name="password" required autocomplete="new-password">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                            </span>
                            <input type="password" class="form-control" placeholder="Confirmer mot de passe"
                                name="password_confirmation" required autocomplete="new-password">
                        </div>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/user-role.png') }}" alt="">
                            </span>
                            <select class="form-select" name="role" required>
                                <option value="" disabled selected>Sélectionner votre rôle</option>
                                <option value="startup" {{ old('role') == 'startup' ? 'selected' : '' }}>Startup</option>
                                <option value="coach" {{ old('role') == 'coach' ? 'selected' : '' }}>Coach</option>
                                <option value="investisseur" {{ old('role') == 'investisseur' ? 'selected' : '' }}>Investisseur</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary fw-bold w-100 mb-4">S'inscrire</button>
                    </form>
                    <div class="text-center mb-4">
                        <p class="text">Vous avez un compte ? <a href="{{ route('login') }}">S'identifier</a></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-center px-3 mb-4">
                        <div class="hr"></div>
                        <p>ou</p>
                        <div class="hr"></div>
                    </div>
                    <div class="card-footer bg-transp border-0 pt-0 pb-0">
                        <div class="d-flex align-items-center justify-content-center">
                            <!-- Bouton Google temporairement désactivé -->
                            <!--
                            <button class="btn btn-rect">
                                <span class="iconify" data-icon="flowbite:google-solid" data-inline="false"></span>
                            </button>
                            -->
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