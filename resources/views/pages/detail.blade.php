@extends('layouts.app')
@section('title', $project->title)
@section('header')
    @include('layouts.header',(array) $project)
@endsection
@section('content')
    <section class="content">
        <div class="content__container">
            <div class="project-detail">
                <a class="btn btn--secondary" href="{{ route('portfolio.index') }}"><i class="fa-solid fa-arrow-left"></i> Terug naar portfolio</a>
                <h1>{{ $project->title }}</h1>
                @if($project->image_url)
                    <img src="/storage{{ $project->image_url }}" alt="Afbeelding van {{ $project->title }}" class="project-image" />
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
        </div>
    </section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection