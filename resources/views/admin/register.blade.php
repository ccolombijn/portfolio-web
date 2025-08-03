@extends('layouts.admin')
@section('content')
    @include('components.form', [
        'action' => '/register',
        'fields' => [
            'name' => 'name',
            'usernname' => 'text',
            'password' => 'password',
            'password_confirmation' => 'password_confirmation'
        ],
        'button' => 'registreren'
    ])
@endsection