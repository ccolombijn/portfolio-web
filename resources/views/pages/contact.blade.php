@extends('layouts.app')
@section('content')
    <h1>{{ $page['title'] }}</h1>

    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('contact.submit') }}">
        @csrf
        <button type="submit">Verstuur</button>
    </form>
@endsection