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
                <a href="{{ route($route, [$key => $item['slug']]) }}" class="{{ $name }}__card" aria-label="Bekijk {{ $item['title'] }}">
                    
                        @if(isset($item['image_url']))
                            @include("components.image",[ 'url' => $item['image_url']])
                        @endif
                    
                    <h3>{{ $item['title'] }}</h3>
                </a>
            @endforeach
            {!! $content !!}
        </div>
    </section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection