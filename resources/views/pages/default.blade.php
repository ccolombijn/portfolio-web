@extends('layouts.app')

@section('title', $page['title'] ?? '')

@if(in_array('header', $parts))
    @section('header')
        @include('layouts.header', ['header' => $content['header']])
    @endsection
@endif

@if(in_array('footer', $parts))
    @section('footer')
        @include('layouts.footer', ['footer' => $content['footer']])
    @endsection
@endif

@section('content')
    @foreach ($parts as $part)
        @if ($part !== 'header' && $part !== 'footer')
            
            @if(view()->exists('layouts.' . $part))
                @include('layouts.' . $part, [$part => $content[$part]])
            @else
                {!! $content[$part] ?? '' !!}
            @endif

        @endif
    @endforeach
@endsection
