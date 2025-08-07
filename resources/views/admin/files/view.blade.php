@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{$file['name']}}</h1>
    @if(str_contains($file['type'],'image'))
        <img src="data:{{$file['type']}};base64,{{ base64_encode($file['content']) }}">
        @php 
        $image_size = getimagesize($file['path']);
        @endphp
        {{$image_size[0]}} x {{$image_size[1]}}
    @else
        <pre>{{$file['content']}}</pre>
    @endif
    
    <p>{{$file['type']}}</p>
    <pre>{{$file['hash']}}</pre>

@endsection