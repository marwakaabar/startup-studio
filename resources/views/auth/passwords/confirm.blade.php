@extends('coach.layouts.fullwidth')
@section('title')
Confirmation de mot de passe
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
                    
                    <form action="{{ route('password.confirm') }}" method="POST">
                        @csrf
                        <h5 class="mb-5 title">Confirmation de mot de passe</h5>
                        <p>Veuillez confirmer votre mot de passe avant de continuer.</p>
                        
                        <div class="input-group mb-4">
                            <span class="input-group-text">
                                <img src="{{ asset('assets/img/icons/lock.png') }}" alt="">
                            </span>
                            <input type="password" class="form-control" placeholder="Mot de passe" name="password" required autocomplete="current-password">
                        </div>

                        <button type="submit" class="btn btn-primary fw-bold w-100 mb-4">Confirmer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@endsection
