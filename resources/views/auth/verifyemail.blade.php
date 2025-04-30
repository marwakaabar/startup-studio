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
    <img src="{{ asset('assets/img/dash/circletop.png') }}" alt="" class="circle circletop">
    <img src="{{ asset('assets/img/dash/circleright.png') }}" alt="" class="circle circleright">
    <img src="{{ asset('assets/img/dash/circletopright.png') }}" alt="" class="circle circletopright">
    <div class="row d-flex justify-content-center w-100">
            <div class="col-lg-5 col-md-6 col-12">
                <div class="card cardLogin">
                    <div class="card-body">

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            </div>
                        @endif
                        <p>Un email de verification est envoyer vers {{ $email }}</p>
                        <form method="POST" action="{{ route('verify-email.post', $email) }}">
                            @csrf
                            <input type="text" hidden name="email" value="{{ $email }}" />
                            <button class="btn btn-primary w-100 mb-4" type="submit">Renvoyer</button>
                        </form>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('logout') }}">Essayer un autre
                                compote <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                    fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
