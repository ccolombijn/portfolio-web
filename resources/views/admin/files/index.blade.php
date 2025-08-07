@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Manage Files</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white rounded shadow">
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
                <tr class="border-b border-stone-300">
                    <td class="p-3"><a href="{{ route('admin.files.view', $file['path']) }}">@if($file['type']==='folder')<i class="bi bi-folder"></i>@else<i class="bi bi-file-earmark"></i>@endif {{ $file['name'] }}</a></td>
                    <td class="p-3">{{ $file['type'] }}</td>
                    <td class="p-3">{{ $file['size'] }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.files.index', $file['name']) }}" class="text-blue-500">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection