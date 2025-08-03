@extends('layouts.admin')
@section('content')
    @include('components.form', [
        'action' => '/login',
        'fields' => [
            'usernname' => 'text',
            'password' => 'password'
        ],
        'button' => 'inloggen'
    ])
    <a href="{{route('register')}}">Registreren</a>
@endsection