<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME')}} - @yield('title')</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body>
    <header>
        @include('layouts.navigation.nav')
        @yield('header')
    </header>
    <main>
        @yield('content')
    </main>
    <footer>
        @yield('footer')
    </footer>
</body>
</html>