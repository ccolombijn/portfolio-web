@extends('layouts.app')
@section('content')
    <h1>{{ $page['title'] }}</h1>
    @foreach ($items as $item)
        <a href="{{ route('portfolio.show', $project) }}">
            <h2>{{ $project->title }}</h2>
        </a>
    @endforeach
@endsection