@extends('layouts.app')
@section('content')
    <h1>{{ $page['title'] }}</h1>
    @foreach ($items as $item)
        </a><a href="{{ route('portfolio.show', ['project' => $item['slug']]) }}">
            <h2>{{ $item['title'] }}</h2>
        </a>
    @endforeach
@endsection