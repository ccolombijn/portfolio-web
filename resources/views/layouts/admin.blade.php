<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/scss/admin.scss', 'resources/ts/admin.ts'])
</head>
<body class="bg-gray-100">
    <div class="flex">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
            <img src="{{Vite::asset('resources/images/ccc_logo_wit.png')}}">
            
            <nav class="mt-4">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}" class="block py-2">Dashboard</a></li>
                    <li><a href="{{ route('admin.pages.index') }}" class="block py-2">Manage Pages</a></li>
                    {{-- <li><a href="{{ route('admin.projects.index') }}" class="block py-2">Manage Projects</a></li> --}}
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block py-2">
                                Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-10">
            @yield('content')
        </main>
    </div>
</body>
</html>