@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Edit project: {{ $project['title'] }}</h1>

    <form action="{{ route('admin.projects.update', $project['name']) }}" method="POST" class="bg-white p-6 rounded shadow">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $project['name']) }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <div class="mb-4">
            <label for="title" class="block mb-2">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $project['title']) }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('title')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Route</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', isset($project['slug']) ? $project['slug'] : '') }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('slug')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="route" class="block mb-2">Source</label>
            <input type="text" name="source" id="source" value="{{ old('source', isset($project['source']) ? $project['source'] : 'storage') }}" class="w-full p-2 border border-stone-300 rounded shadow" disabled>
            @error('source')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="image_url" class="block mb-2">Image</label>
            <div class="image_select p-3 rounded cursor-pointer shadow flex items-center border border-stone-300 hover:bg-sky-100" data-storage-url="{{url('storage/')}}">
                <img src="{{url('storage/' . $project['image_url'])}}" style="height:100px;" class="image_select__thumb pr-4" />
                <span class="image_select__label">{{$project['image_url']}}</span>
            </div>
            <input type="hidden" name="image_url" id="image_url" value="{{ old('image_url', isset($project['image_url']) ? $project['image_url'] : '') }}" class="w-full p-2 border border-stone-300 rounded shadow">
            @error('image_url')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        <p class="pt-4 text-right"><a href="{{route('admin.files.upload', ['path' => 'images/projects'])}}" class="bg-green-800 text-white px-4 py-2 rounded"><i class="bi bi-plus"></i> {{__('Add File(s)')}}</a></p>
       
        <div class="mb-4">
            <label for="header" class="block mb-2">Header</label>
            <textarea class="editor shadow" name="header" id="header">{{$header}}</textarea>
            @error('header')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label for="content" class="block mb-2">Content</label>
            <textarea class="editor shadow" name="content" id="content">{{$content}}</textarea>
            @error('content')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>
        
        <div class="mb-4">
            <label for="footer" class="block mb-2">Footer</label>
            <textarea class="editor shadow" name="footer" id="footer">{{$footer}}</textarea>
            @error('footer')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
        </div>

        <div>
            <button type="submit" class="bg-gray-800 hover:bg-blue-600 cursor-pointer text-white px-4 py-2 rounded"><i class="bi bi-floppy"></i> Save Changes</button>
            <a onclick="document.forms['delete'].submit();" class="rounded px-4 py-2 text-white bg-red-800 hover:bg-red-700 cursor-pointer"><i class="bi bi-trash"></i> Delete Project</a>
        </div>
    </form>
    <form method="post" action="{{route('admin.projects.destroy', $project['name'])}}" name="delete"> 
        @csrf
        @method('DELETE')
        <!-- <button class="rounded px-4 py-2 text-white bg-red-800 hover:bg-red-700 cursor-pointer" type="submit"><i class="bi bi-trash"></i> Delete Page</button> -->
    </form>
    <!-- <script>
        const imgSelect = document.querySelector('.image_select');
        if(imgSelect){
            let table
            document.querySelector('.image_select').addEventListener('click', event => {
                const imgLabel = event.target.querySelector('span');
                const imgThumb = event.target.querySelector('img');
                const imgThumbSrc = imgThumb.src;
                const imgInput = document.getElementById('image_url');
                const imgPath = imgLabel.textContent;
                const imgPathArr = imgPath.split('/');
                const imgFile = imgPathArr[imgPathArr.length-1];
                //const imgBasePath = imgPath.replace(imgFile,'');
                //const imgBasePath = imgThumbSrc.replace(imgPath,'');
                const imgBasePath = event.target.dataset.storageUrl;
                if(!table){
                    fetch('/admin/files/images/projects').then(res => res.text()).then(txt => {
                        const parser = new DOMParser();
                        const page = parser.parseFromString(txt,'text/html');
                        table = page.querySelector('table');
                        const rows = table.querySelectorAll('tbody tr');
                        rows.forEach( row => {
                            console.log(row);
                            row.classList.add('hover:bg-sky-100','cursor-pointer');
                            row.querySelector('a').addEventListener('click', event => event.preventDefault())
                            row.addEventListener('click',event =>{
                                const file = row.querySelector('.file').textContent.replace(' ','');
                                //const newFilePath = imgPath.replace(imgFile,file);
                                const newFilePath = row.dataset.path;

                                imgLabel.innerText = newFilePath;
                                //imgThumb.src = imgThumb.src.replace(imgFile,file);
                                imgThumb.src = imgBasePath + newFilePath;
                                imgInput.value = newFilePath;
                                table.setAttribute('style', 'display:none;');
                            });
                        })
                        event.target.after(table);
                    });
                } else {
                    table.removeAttribute('style');
                }
            });
        }
        
    </script> -->
@endsection
