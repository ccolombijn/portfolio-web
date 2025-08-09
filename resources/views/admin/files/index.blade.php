@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">/{{$path}}</h1>
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white rounded shadow file-manager-table">
        <thead>
            <tr class="border-b border-stone-300">
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Type</th>
                <th class="p-3 text-left">Size</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($files as $file)
                @if(!str_contains($file['name'],'webp'))
                @php
                    $isImage = in_array(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    $previewUrl = $isImage ? asset('storage/' . $file['path']) : '';
                @endphp
                <tr class="border-b border-stone-300 cursor-pointer hover:bg-sky-100 {{ $isImage ? 'has-preview' : '' }}" data-path="/{{$file['path']}}" data-preview-url="{{ $previewUrl }}">
                    <td class="p-3 file"><a href="{{ route('admin.files.view', $file['path']) }}">@if($file['type']==='folder')<i class="bi bi-folder"></i>@else<i class="bi bi-file-earmark"></i>@endif {{ $file['name'] }}</a></td>
                    <td class="p-3">{{ $file['type'] }}</td>
                    <td class="p-3">{{ $file['size'] }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.files.index', $file['name']) }}" class="text-blue-500">Edit</a>
                    </td>
                </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <p class="pt-4 text-right"><a href="{{route('admin.files.upload', ['path' => $path])}}" class="bg-green-800 hover:bg-green-700 cursor-pointer text-white px-4 py-2 rounded"><i class="bi bi-plus"></i> {{__('Add File(s)')}}</a></p>

@endsection