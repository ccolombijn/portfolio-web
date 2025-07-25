@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    @include('layouts.header' ,['header' => $header])
@endsection
@section('content')
    <section class="content {{ $name }}">
        <div class="content__container">
            <h2>{{ $page['title'] }}</h2>
            @foreach ($items as $item)
                <a href="{{ route($route, [$key => $item['slug']]) }}" class="{{ $name }}__card">
                    <figure>
                        @if(isset($item['image_url']))
                        <img src="/storage/{{$item['image_url']}}">
                        @endif
                    </figure>
                    <h3>{{ $item['title'] }}</h3>
                </a>
            @endforeach
        </div>
    </section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection