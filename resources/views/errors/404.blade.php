@extends('layouts.app')
@section('title', 'Pagina niet gevonden')
@section('header')
    <section class="header @if(isset($name))header--{{$name}}@endif">
        <div class="header__container">
            <h1 class="header__title"><a href="{{ route('home') }}" aria-label="Ga naar homepage"><span>{{ env('APP_NAME')}}</span></a></h1>
            @include('navigation.nav')
            <div class="header__content">
                <h2>Pagina niet gevonden </h2>
                <p>De pagina bestaat niet (meer) en kan daarom niet weergegeven worden</p>
            </div>
            <div class="animated-gradient-bg"  style="display: none;"></div>
            <div class="animated-clouds" style="display: none;"></div>
            <canvas id="dot-particles-canvas" class="absolute inset-0 z-0"></canvas>
        </div>
    </section>
@endsection