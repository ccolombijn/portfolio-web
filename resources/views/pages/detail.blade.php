@extends('layouts.app')
@section('title', $item->title)
@section('header')
    @include('layouts.header',(array) $item)
@endsection
@section('content')
    <section class="content {{$item->name}}">
        <div class="content__container">
            <div class="project-detail">
                <a class="btn btn--secondary" href="{{ route($item->name . '.index') }}"><i class="fa-solid fa-arrow-left"></i> Terug naar {{$item->name}}</a>
                <h1>{{ $item->title }}</h1>
                
                <div class="project-description">
                    {!! $item->description !!}
                </div>
                <p class="project-details">
                    @if(isset($item->created_at))
                        <small>Gepubliceerd op: {{ $item->created_at->format('d-m-Y') }}</small>
                    @endif
                </p>
            </div>
        </div>
    </section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection