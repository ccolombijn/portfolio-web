@extends('layouts.app')
@section('title', 'Website is in onderhoud')
@section('header')
    <section class="header @if(isset($name))header--{{$name}}@endif" style="background-image: none;">
        <div class="header__container">
            <h1 class="header__title"><a href="{{ route('home') }}" aria-label="Ga naar homepage"><span>{{ env('APP_NAME')}}</span></a></h1>
           
            <div class="header__content">
                <h2>Website is in onderhoud</h2>
                <p>Hoi!<br><br>

Leuk dat je langskomt. Mijn site is even offline, omdat deze  in ontwikkeling is<br><br>

Achter de schermen werk ik hard aan de inhoud voor de nieuwe site. Je vindt hier dus een hele nieuwe versie vanaf 11/08/2025!<br><br>

Wil je contact opnemen? Leuk! Dan kun je mailen naar <a href="mailto:ccolombijn@gmail.com">ccolombijn@gmail.com</a><br><br>

Tot snel!</p>
            </div>
            <div class="animated-gradient-bg" style="display: none;"></div>
            <div class="animated-clouds" style="display: none;"></div>
            <canvas id="dot-particles-canvas" class="absolute inset-0 z-0"></canvas>
        </div>
    </section>
@endsection