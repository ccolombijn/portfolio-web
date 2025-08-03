@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Page: {{ $page['title'] }}</h1>

    <form action="{{ route('admin.pages.update', $page['name']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="title" class="block mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $page['title']) }}" class="w-full p-2 border border-stone-300 rounded">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Route</label>
            <input type="text" name="route" id="route" value="{{ old('route', isset($page['route']) ? $page['route'] : '') }}" class="w-full p-2 border border-stone-300 rounded">
            @error('route')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Exclude in nav</label>
            <input type="checkbox" name="exclude_nav" id="exclude_nav"@if(isset($page['exclude_nav'])) checked="{{ old('exclude_nav', isset($page['exclude_nav']) ? 'true' : 'false' )}}"@endif>
            @error('route')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Controller</label>
            <input type="text" name="controller" id="controller" placeholder="PageController" value="{{ old('controller', isset($page['controller']) ? $page['controller'] : '') }}" class="w-1/2 p-2 border border-stone-300 rounded">@<input type="text" name="method" id="method" placeholder="show" value="{{ old('method', isset($page['method']) ? $page['method'] : '') }}" class="w-1/2 p-2 border border-stone-300 rounded">
            @error('route')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>
@endsection