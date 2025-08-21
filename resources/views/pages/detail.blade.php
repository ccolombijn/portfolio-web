@extends('layouts.app')
@section('title', $item->title)
@section('header')
    @include('layouts.header',(array) $item)
@endsection
@section('content')
    <section class="content {{$name}}">
        <div class="content__container">
            <div class="{{$name}}-detail">
                <a class="btn btn--secondary" href="{{ route($name) }}" aria-label="Terug naar {{$name}}"><i class="fa-solid fa-arrow-left"></i> Terug naar {{$name}}</a>
                <h2>{{ $item->title }}</h2>
                <div class="{{$name}}-description">
                    {!! $item->description !!}
                </div>
                <p class="{{$name}}-details">
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