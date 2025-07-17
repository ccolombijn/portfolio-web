@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    <section class="header">
        <div class="animated-gradient-bg"></div>
        <div class="animated-clouds"></div>
        <canvas id="dot-wave-canvas" class="absolute inset-0 z-0"></canvas>
        <div class="header__container">
            @include('navigation.nav')
            <div class="header__content">
                {!! $header !!}
            </div>
        </div>
       
    </section>
@endsection
@section('content')
    <section class="content">
        <div class="content__container">
            {!! $content !!}
        </div>
    </section>
@endsection
@section('footer')
    <section class="footer">
        <div class="footer__container">
            {!! $footer !!}
        </div>
    </section>
@endsection