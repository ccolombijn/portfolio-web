@extends('layouts.app')
@section('title', $page['title'])
@section('header')
    @include('layouts.header', ['header' => $content['header']])
@endsection
@section('content')
<section class="content">
    <div class="content__container">
    <h1>{{ $page['title'] }}</h1>
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif
        @include('components.form', [
            'action' => route('contact.submit'),
            'fields' => [
                'subject' => 'text',
                'name' => 'text',
                'email' => 'email',
                'message' => 'textarea'
            ],
            'button' => 'Verstuur'
        ])
    </div>
</section>
@endsection
@section('footer')
    @include('layouts.footer' ,['footer' => $content['footer']])
@endsection