@extends('layouts.fullwidth')
@section('title')
Réinitialisation du mot de passe

@endsection

@section('content')
<div id="app">
<reset-password-form token="{{ $token }}" email="{{ $email }}"></reset-password-form>
</div>
@endsection

@section('script')
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection



