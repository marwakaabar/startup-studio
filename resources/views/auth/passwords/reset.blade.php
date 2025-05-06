@extends('layouts.fullwidth')
@section('title')
Vous avez oublié votre mot de passe
@endsection
@section('page_description')
@section('content')
<section class="h-100 auth" style="position: relative;">
    <div class="row justify-content-center h-100 m-0" style="height:100vh !important;">
        <div class="col-md-7 col-sm-12" style="border-radius:0px 30px 30px 0px;background: #E7F2F0;">
            <img src="{{ asset('assets/img/dash/logo.svg') }}" alt="" class="imgLogo">
            <div class="d-flex justify-content-center align-items-center left">
                <div class="card cardLogin">
                    <div class="card-header">
                        <h4>Vous avez oublié votre mot de passe ?</h4>
                        <p>Ne vous inquiétez pas, cela nous arrive à tous. Entrez votre adresse e-mail ci-dessous pour récupérer votre mot de passe</p>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-sm-5  d-lg-flex d-md-flex d-none justify-content-start align-items-center h-100 bg-white" style="position: relative;">
            <img src="{{asset('assets/img/dash/Vector 37.png')}}" alt="Description du image" class="image" style="width: 107%; height:96%; position:absolute; top:2%; left:-10%;">
        </div>
    </div>
</section>
@endsection