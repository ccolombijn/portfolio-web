@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{$file['name']}}</h1>
    <p class="text-sm">{{$file['type']}} | Checksum <pre class="inline">{{$file['hash']}}</pre>| {{$file['size']}} | Last modified {{$file['date']}}</p>
    <div class="bg-white p-4 boder border-stone-300 shadow mb-4">
    @if(str_contains($file['type'],'image'))
        @php $image_size = getimagesize($file['path']); @endphp
        
        <img src="data:{{$file['type']}};base64,{{ base64_encode($file['content']) }}">
        <p class="text-sm">Dimensions : {{$image_size[0]}} x {{$image_size[1]}}</p>
    @else
        <pre>{{$file['content']}}</pre>
    @endif
    </div>
    <form method="post" action="{{route('admin.files.destroy', $file['path'])}}"> 
        @csrf
        @method('DELETE')
        <button class="rounded px-4 py-2 text-white bg-red-800 hover:bg-red-700 cursor-pointer" type="submit"><i class="bi bi-trash"></i> Delete File</button>
    </form>
@endsection