@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">{{__('Applications')}}</h1>

    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-3 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <table class="w-full bg-white rounded shadow">
        <thead>
            <tr class="border-b border-stone-300">
                <th class="p-3 text-left">{{__('Company')}}</th>
                <th class="p-3 text-left">{{__('URL')}}</th>
                <th class="p-3 text-left">{{__('Status')}}</th>
                <th class="p-3 text-left"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($applications as $application)
                <tr class="border-b border-stone-300 cursor-pointer hover:bg-sky-100">
                    <td class="p-3">{{ $application['company_name'] }}</td>
                    <td class="p-3">{{ $application['url'] }}</td>
                    <td class="p-3"><pre>{{ $application['name'] }}</pre></td>
                    <td class="p-3">
                        <a href="{{ route('admin.applications.edit', $application['name']) }}" class="text-blue-500"></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="pt-4 text-right"><a href="{{route('admin.applications.create')}}" class="bg-green-800 hover:bg-green-700 text-white px-4 py-2 rounded"><i class="bi bi-plus"></i> {{__('Add Project')}}</a></p>

@endsection