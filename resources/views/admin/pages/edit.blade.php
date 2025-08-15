@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit Page: {{ $page['title'] }}</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.pages.update', $page['name']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $page['name']) }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="title" class="block mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $page['title']) }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Route</label>
            <input type="text" name="route" id="route" value="{{ old('route', isset($page['route']) ? $page['route'] : '') }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('route')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

       
        <x-forms.input type="checkbox" name="exclude_nav" value="{{isset($page['exclude_nav'])}}" />
        

        <div class="mb-4 w-1/3" id="controllers" data-controllers="{{json_encode($controllers)}}">
            <label for="controller" class="block mb-2">Controller</label>
            <input type="text" name="controller" id="controller" placeholder="PageController" value="{{ old('controller', isset($page['controller']) ? $page['controller'] : '') }}" class="p-2 border border-stone-300 rounded">@<input type="text" name="method" id="method" placeholder="show" value="{{ old('method', isset($page['method']) ? $page['method'] : '') }}" class="p-2 border border-stone-300 rounded">
            @error('controller')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        @if(empty($page['controller']))
        <div class="mb-4 w-1/3">
            <label for="view" class="block mb-2">View</label>
            <!-- <input type="text" name="view" id="view" placeholder="pages.default" value="{{ old('view', isset($page['view']) ? $page['view'] : '') }}" class="p-2 border border-stone-300 rounded"> -->
            <select name="view" id="view">
            @foreach($views as $view) 
                <option @if(isset($page['view']) || !isset($page['view']) && $view === 'pages.default') selected @endif>{{$view}}</option>
            @endforeach
            </select>
            @error('view')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        @endif
        <div class="mb-4">
            <fieldset id="parts">
                <legend>Parts</legend>
                @foreach($sorted_sections as $section)
                <div class="p-3 rounded shadow cursor-move flex items-center" data-part-name="{{ $section }}">
                    <x-forms.checkbox name="parts[]" value="{{$section}}" label="{{$section}}" :checked=in_array($section,$selected_parts) />
                </div>
                @endforeach
                
               
            </fieldset>
            @error('view')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <input type="hidden" name="parts_order" id="parts-order-input">
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
            <button type="submit" class="bg-gray-800 hover:bg-blue-600 cursor-pointer text-white px-4 py-2 rounded"><i class="bi bi-floppy"></i> Save Changes</button>
            <a onclick="document.forms['delete'].submit();" class="rounded px-4 py-2 text-white bg-red-800 hover:bg-red-700 cursor-pointer"><i class="bi bi-trash"></i> Delete Page</a>
        </div>
    </form>
    <form method="post" action="{{route('admin.pages.destroy', $page['name'])}}" name="delete"> 
        @csrf
        @method('DELETE')
        <!-- <button class="rounded px-4 py-2 text-white bg-red-800 hover:bg-red-700 cursor-pointer" type="submit"><i class="bi bi-trash"></i> Delete Page</button> -->
    </form>
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function () {
            const partsList = document.getElementById('parts');
            const orderInput = document.getElementById('parts-order-input');

            const updateOrder = () => {
                const parts = Array.from(partsList.children)
                    .map(el => el.getAttribute('data-part-name'));
                orderInput.value = parts.join(',');
            };

            new Sortable(partsList, {
                animation: 150,
                onUpdate: function () {
                    updateOrder();
                }
            });
            updateOrder();
        });
    </script> -->
@endsection