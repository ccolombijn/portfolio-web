<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/scss/admin.scss', 'resources/ts/admin.ts'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/ace-builds@1.43.2/css/ace.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.43.2/ace.min.js" integrity="sha512-sdeAa6bnaA5ZaOGAM7GhwO8ascBi986gL7z7whljQdiISDOEQZKja2yqe7pD+l5HshKzcJtoxJVxVwHcO5SVAQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.43.2/mode-json.min.js" integrity="sha512-K+H+3WTfhwE9fnfv4makUYJxz4kwIadQPOjGHAmOT96FtMSPRnvnuGR/sZjuNB2MmmHQ94Mnc9zoDxTHKMMEww==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <aside class="w-64 bg-linear-to-b from-gray-800 to-sky-800 text-white min-h-screen p-4">
            <img src="{{Vite::asset('resources/images/ccc_logo_wit.png')}}">
            
            <nav class="mt-4">
                <ul>
                    <li><a href="{{ route('admin.dashboard') }}" class="block py-2 {{ request()->routeIs('admin.dashboard') ? ' active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li><a href="{{ route('admin.pages.index') }}" class="block py-2 {{ request()->routeIs('admin.pages*') ? ' active' : '' }}"><i class="bi bi-list"></i> Pages</a></li>
                    <li><a href="{{ route('admin.projects.index') }}" class="block py-2 {{ request()->routeIs('admin.projects*') ? ' active' : '' }}"><i class="bi bi-list"></i> Projects</a></li>
                    <li><a href="{{ route('admin.files.index') }}" class="block py-2 {{ request()->routeIs('admin.files*') ? ' active' : '' }}"><i class="bi bi-folder"></i> Files</a></li>
                    <li><a href="{{ route('admin.settings.index') }}" class="block py-2 {{ request()->routeIs('admin.settings*') ? ' active' : '' }}"><i class="bi bi-gear"></i> Settings</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();"
                               class="block py-2">
                               <i class="bi bi-box-arrow-right"></i> Logout
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
    <script>
        const editors = document.querySelectorAll('.editor').forEach( editor => {
            new EasyMDE({
                element: editor,
            });
        });

    </script>
</body>
</html>