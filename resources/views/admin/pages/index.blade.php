@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{__('Manage Pages')}}</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="border-b border-stone-300">
                <th class="p-3 text-left">{{__('Name')}}</th>
                <th class="p-3 text-left">{{__('Title')}}</th>
                <th class="p-3 text-left">{{__('Route')}}</th>
                <th class="p-3 text-left">{{__('Actions')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr class="border-b border-stone-300">
                    <td class="p-3">{{ $page['name'] }}</td>
                    <td class="p-3">{{ $page['title'] }}</td>
                    <td class="p-3"><pre>{{ isset($page['route']) ? $page['route'] : '/' . $page['name'] }}</pre></td>
                    <td class="p-3">
                        @php $pageLink = isset($page['method']) ? $page['name'] . '.' . $page['method'] : $page['name'] @endphp
                        <a href="{{ route('admin.pages.edit', $pageLink) }}" class="text-blue-800"><i class="bi bi-pencil-fill"></i> {{__('Edit')}}</a>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="pt-4 text-right"><a href="{{route('admin.pages.create')}}" class="bg-green-800 text-white px-4 py-2 rounded"><i class="bi bi-plus"></i> {{__('Add Page')}}</a></p>
@endsection