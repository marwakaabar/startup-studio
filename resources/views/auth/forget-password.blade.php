@extends('coach.layouts.fullwidth')
@section('title')
Connexion
@endsection
@section('page_description')
@endsection

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y auth-container d-flex justify-content-center align-items-center" style="height:100vh;">
    <img src="{{ asset('assets/img/dash/robot_smile.png') }}" alt="" class="circle circletop" style="position: absolute; top: 10px; left: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot_smile_2.png') }}" alt="" class="circle circleright" style="position: absolute; bottom: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <img src="{{ asset('assets/img/dash/robot.png') }}" alt="" class="circle circletopright" style="position: absolute; top: 10px; right: 10px; opacity: 0.1; width: 150px;">
    <div class="row d-flex justify-content-center align-items-center w-100">
        <div class="col-lg-5 col-md-6 col-12">
            <div class="card cardLogin">
                <div class="card-body">
                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf
                        @if (Session::has('message'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                        @endif
                        @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ Session::get('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                        @endif
                        @if ($errors->has('email'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $errors->first('email') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </button>
                        </div>
                        @endif
                        <h5 class="mb-5 title">Mot de passe oublié</h5>
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/Mail-2.png') }}" alt="">
                            </span>
                            <input type="email" class="form-control" placeholder="Exemple@gmail.com" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        </div>


                        <button class="btn btn-primary w-100 mb-4" type="submit">Envoyer le lien de
                            réinitialisation</button>
                    </form>
                    <div class="text-center mb-4">
                        <p class="text">Vous n'êtes pas encore client ? <a href="{{ route('register') }}">Créer
                                un compte</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection