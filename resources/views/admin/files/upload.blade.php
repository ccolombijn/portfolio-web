@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Add File(s) to: {{ $path === '.' ? 'Root' : $path }}</h1>

    <form id="upload-form" action="{{ route('admin.files.upload.store', ['path' => $path]) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
        @csrf
        
        <div class="mb-4">
            <label for="files_to_upload" class="block mb-2 font-bold">Select files to upload:</label>
            <input type="file" name="files_to_upload[]" id="files_to_upload" multiple class="w-full p-2 border border-stone-300 rounded">
        </div>
        <div id="file-preview-container" class="space-y-2 mb-4"></div>
        <div id="progress-container" class="w-full bg-gray-200 rounded-full h-4 my-4 hidden">
            <div id="progress-bar" class="bg-gray-800 h-6 rounded-full text-xs text-white text-center leading-none" style="width: 0%">0%</div>
        </div>
        
        <div id="upload-status" class="text-green-800 mb-4"></div>

        <div>
            <button type="submit" id="upload-button" disabled class="bg-gray-800 hover:bg-blue-700 text-white px-4 py-2 rounded"><i class="bi bi-upload"></i> Upload</button>
        </div>
    </form>
@endsection