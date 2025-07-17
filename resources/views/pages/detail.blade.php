@extends('layouts.app')
@section('title', $project->title)
@section('header')
    <section class="header">
        <div class="header__container">
            @include('navigation.nav')
            <div class="header__content">
                <div class="project-intro">
                    {!! $project->intro !!}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('content')
    <div class="project-detail">
        <a href="{{ route('portfolio.index') }}">&larr; Terug naar portfolio</a>
        <h1>{{ $project->title }}</h1>
        @if($project->image_url)
            <img src="{{ $project->image_url }}" alt="Afbeelding van {{ $project->title }}" class="project-image" />
        @endif
        <div class="project-description">
            {!! $project->description !!}
        </div>
        <p class="project-details">
            <small>Gepubliceerd op: {{ $project->created_at->format('d-m-Y') }}</small>
        </p>
    </div>
@endsection
@section('footer')
    <section class="footer">
        <div class="footer__container">
            {!! $footer !!}
        </div>
    </section>
@endsection