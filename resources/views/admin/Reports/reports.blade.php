@extends('layouts.general')
@section('title')
Modération Forum
@endsection

@section('content')
<div id="app">
    <div class="container-xxl flex-grow-1 container-p-y mt-0">
        <liste-reports :initial-reports='@json($reports)'></liste-reports>
    </div>
</div>
@endsection

@section('script')
@vite(['resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

