@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Manage Pages</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="border-b border-stone-300">
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Title</th>
                <th class="p-3 text-left">Route</th>
                <th class="p-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
                <tr class="border-b border-stone-300">
                    <td class="p-3">{{ $page['name'] }}</td>
                    <td class="p-3">{{ $page['title'] }}</td>
                    <td class="p-3">{{ isset($page['route']) ? $page['route'] : '/' . $page['name'] }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.pages.edit', $page['name']) }}" class="text-blue-500">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection