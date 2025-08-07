@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit project: {{ $project['title'] }}</h1>

    <form action="{{ route('admin.projects.update', $project['name']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $project['name']) }}" class="w-full p-2 border border-stone-300 rounded">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="title" class="block mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $project['title']) }}" class="w-full p-2 border border-stone-300 rounded">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Route</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', isset($project['slug']) ? $project['slug'] : '') }}" class="w-full p-2 border border-stone-300 rounded">
            @error('slug')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Source</label>
            <input type="text" name="source" id="source" value="{{ old('source', isset($project['source']) ? $project['source'] : '') }}" class="w-full p-2 border border-stone-300 rounded">
            @error('source')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="image_url" class="block mb-2">Image</label>
            <div class="image_select p-3 rounded cursor-pointer shadow flex items-center">
                <img src="{{url('storage/' . $project['image_url'])}}" style="height:100px;" class="image_select_thumb" />
                {{$project['image_url']}}
            </div>
            <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url', isset($project['image_url']) ? $project['image_url'] : '') }}" class="w-full p-2 border border-stone-300 rounded">
            @error('image_url')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

       
        <div class="mb-4">
            <label for="header" class="block mb-2">Header</label>
            <textarea class="editor" name="header" id="header">{{$header}}</textarea>
            @error('view')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block mb-2">Content</label>
            <textarea class="editor" name="content" id="content">{{$content}}</textarea>
            @error('view')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        
        <div class="mb-4">
            <label for="footer" class="block mb-2">Footer</label>
            <textarea class="editor" name="footer" id="footer">{{$footer}}</textarea>
            @error('view')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>
@endsection