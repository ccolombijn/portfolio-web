@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    <section class="header">
        <div class="header__container">
            {!! $header !!}
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