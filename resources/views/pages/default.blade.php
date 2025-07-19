@extends('layouts.app')
@section('title', $page['title'])
@foreach ($parts as $part) 
    @section($part)
        @include('layouts.' . $part ,[$part => $content[$part]])
    @endsection
@endforeach