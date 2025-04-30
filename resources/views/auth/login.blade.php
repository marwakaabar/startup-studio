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
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-5 col-md-6 col-12">
                <div class="card cardLogin">
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
                        @if ($message = Session::get('message'))
                            <div class="form-group">
                                <div class="alert alert-success dark alert-dismissible fade show" role="alert">
                                    <span>{{ $message }}</span>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        @if ($message = Session::get('token_expire'))
                            <div class="form-group">
                                <div class="alert alert-danger dark alert-dismissible fade show" role="alert">
                                    <span>{{ $message }}</span>
                                    <button class="btn-close" type="button" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </div>
                        @endif
                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <h5 class="mb-5 title">Accéder à mon compte</h5>
                            <div class="input-group mb-4">
                                <span class="input-group-text">
                                    <img src="{{ asset('assets/img/icons/Mail-2.png') }}" alt="">
                                </span>
                                <input type="email" class="form-control" placeholder="Exemple@gmail.com" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                                </span>
                                <input type="password" class="form-control" placeholder="Mot de passe" name="password" required autocomplete="current-password">
                            </div>
                            <div class="mb-4 d-flex justify-content-between">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        Se souvenir de moi
                                    </label>
                                </div>
                                <a href="{{ route('password.request') }}">
                                    <small>Mot de passe oublié?</small>
                                </a>
                            </div>
                            <button class="btn btn-primary w-100 mb-4" type="submit">Se connecter</button>
                        </form>
                        <div class="text-center mb-4">
                            <p class="text">Vous n'êtes pas encore client ? <a href="{{ route('register') }}">Créer
                                    un compte</a></p>
                        </div>
                        <div class="d-flex align-items-center justify-content-center px-3 mb-4">
                            <div class="hr"></div>
                            <p>ou</p>
                            <div class="hr"></div>
                        </div>
                        <div class="card-footer bg-transp border-0 pb-0">
                            <div class="d-flex align-items-center justify-content-center">
                                <!-- Uncomment the Google authentication when it's implemented 
                                <a href="#" class="btn btn-rect">
                                    <span class="iconify" data-icon="flowbite:google-solid" data-inline="false"></span>
                                </a>
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
