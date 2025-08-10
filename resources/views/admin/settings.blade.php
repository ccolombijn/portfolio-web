@extends('layouts.admin')

@section('content')
    <h1 class="text-4xl mb-4">Settings</h1>
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    @php
        //  human-readable titles
        $titleMap = [
            'sections.cta' => 'Call-to-action (global)',
            'header_img' => 'Header image (global)',
        ];
    @endphp

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        <h2 class="text-3xl font-bold mb-4">Content</h2>
        
        @foreach($content as $key => $value)
            @if(is_array($value))

                <h3 class="text-2xl font-bold my-4">{{ $titleMap[$key] ?? ucfirst($key) }}</h3>
                
                @foreach($value as $subKey => $subValue)
                    <x-forms.input 
                        name="content[{{ $key }}][{{ $subKey }}]" 
                        :value="$subValue" 
                        label="{{ ucfirst($subKey) }}"
                    />
                @endforeach
            @else
                <x-forms.input 
                    label="{{ $titleMap[$key] ?? ucfirst($key) }}" 
                    name="content[{{ $key }}]" 
                    :value="$value" 
                />
            @endif
        @endforeach
        
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Save Changes</button>
    </form>
@endsection