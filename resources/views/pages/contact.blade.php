@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    @include('layouts.header', ['header' => $header])
@endsection
@section('content')
<section class="content">
    <div class="content__container">
    <h1>{{ $page['title'] }}</h1>
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif
        <form method="POST" class="form" action="{{ route('contact.submit') }}">
            @csrf
            <p class="form__subject">
                <label for="subject">Onderwerp</label>
                <input type="text" name="subject" id="subject">
            </p>
            <p class="form__name">
                <label for="name">Naam</label>
                <input type="text" name="name" id="name">
            </p>
            <p class="form__email">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email">
            </p>
            <p class="form__message">
                <label for="message">Bericht</label>
                <textarea name="message" id="message"></textarea>
            </p>
            <button class="btn btn--primary form__submit" type="submit">Verstuur</button>
        </form>
    </div>
</section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $footer])
@endsection