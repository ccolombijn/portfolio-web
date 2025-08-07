@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{env('APP_NAME')}} {{__('Admin Dashboard')}}</h1>
    <p>{{__('Select an option from the menu to get started.')}}</p>
@endsection