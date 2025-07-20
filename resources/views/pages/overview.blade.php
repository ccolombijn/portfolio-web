@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    @include('layouts.header' ,['header' => $header])
@endsection
@section('content')
    <section class="content">
        <div class="content__container">
            <h1>{{ $page['title'] }}</h1>
            @foreach ($items as $item)
                <a href="{{ route('portfolio.project', ['project' => $item['slug']]) }}">
                    <h2>{{ $item['title'] }}</h2>
                </a>
            @endforeach
        </div>
    </section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection