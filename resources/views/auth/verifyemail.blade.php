@extends('layouts.fullwidth')
@section('title')
Vérification de l'adresse email

@endsection

@section('content')
<div id="app">
<verify-email-link id="{{ $id }}" hash="{{ $hash }}" expires="{{ $expires }}" signature="{{ $signature }}"></verify-email-link>
</div>
@endsection

@section('script')
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection



