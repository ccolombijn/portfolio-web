@extends('layouts.app')
@section('title', $project->title)
@section('header')
    @include('layouts.header',(array) $project)
@endsection
@section('content')
    <div class="project-detail">
        <a class="btn btn--primary" href="{{ route('portfolio.index') }}">&larr; Terug naar portfolio</a>
        <h1>{{ $project->title }}</h1>
        @if($project->image_url)
            <img src="{{ $project->image_url }}" alt="Afbeelding van {{ $project->title }}" class="project-image" />
        @endif
        <div class="project-description">
            {!! $project->description !!}
        </div>
        <p class="project-details">
            @if(isset($project->created_at))
                <small>Gepubliceerd op: {{ $project->created_at->format('d-m-Y') }}</small>
            @endif
        </p>
    </div>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection