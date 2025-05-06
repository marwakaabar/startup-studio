@extends('layouts.general')
@section('title')
Forum
@endsection

@section('page_description')

@endsection

@section('css')

@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y mt-0">
  <show-topic 
    :topic-id="{{ $topic->id }}" 
    :initial-topic='@json($topic)'
    :initial-posts='@json($posts)'
    :current-user='@json($currentUser)'
  ></show-topic>
</div>
@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection