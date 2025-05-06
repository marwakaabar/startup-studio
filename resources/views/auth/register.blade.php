@extends('layouts.fullwidth')
@section('title')
S'inscrire

@endsection

@section('content')
<div id="app">
<register-form></register-form>
</div>
@endsection

@section('script')
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection


