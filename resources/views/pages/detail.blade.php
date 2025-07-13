@extends('layouts.app')

@php
    $page['title'] = $project->title;
@endphp

@section('content')
    <div class="project-detail">
        <a href="{{ route('portfolio.index') }}">&larr; Terug naar portfolio</a>
        <h1>{{ $project->title }}</h1>
        @if($project->image_url)
            <img src="{{ $project->image_url }}" alt="Afbeelding van {{ $project->title }}">
        @endif
        <div class="project-description">
            {!! $project->description !!}
        </div>
        <p>
            <small>Gepubliceerd op: {{ $project->created_at->format('d-m-Y') }}</small>
        </p>
    </div>
@endsection