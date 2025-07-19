@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    @include('layouts.header' ,['header' => $header])
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