<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@hasSection('description')@yield('description')@else{{$contentData['description']}}@endif">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ env('APP_NAME')}} - @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/favicon.png') }}">
    <script>
      (function(){
        const img = new Image();
        img.onload = function() { document.documentElement.classList.add('webp'); };
        img.onerror = function() { document.documentElement.classList.add('no-webp'); };
        img.src = 'data:image/webp;base64,UklGRhoAAABXRUJQVlA4TA0AAAAvAAAAEAcQERGIiP4HAA==';
      })();
    </script>

    @php
        // 1. Get the correct, hashed URL for the ORIGINAL .png image
        $cloudsPngUrl = Vite::asset('resources/images/CLOUDS.png');
        // 2. Create the .webp URL by simply replacing the extension
        $cloudsWebpUrl = str_replace('.png', '.webp', $cloudsPngUrl);

        // -- Repeat for all other background images --
        $sparklePngUrl = Vite::asset('resources/images/sparkle.png');
        $sparkleWebpUrl = str_replace('.png', '.webp', $sparklePngUrl);

        $dotsPngUrl = Vite::asset('resources/images/dots01.png');
        $dotsWebpUrl = str_replace('.png', '.webp', $dotsPngUrl);
        
        $logoWitPngUrl = Vite::asset('resources/images/ccc_logo_wit.png');
        $logoWitWebpUrl = str_replace('.png', '.webp', $logoWitPngUrl);

        $logoGreyPngUrl = Vite::asset('resources/images/ccc_logo_grey.png');
        $logoGreyWebpUrl = str_replace('.png', '.webp', $logoGreyPngUrl);
    @endphp

    <style>
        /* Fallback for browsers WITHOUT WebP support */
        .header-background { background-image: url('{{ $cloudsPngUrl }}'); }
        .sparkle-element { background-image: url('{{ $sparklePngUrl }}'); }
        .dots-element { background-image: url('{{ $dotsPngUrl }}'); }
        .header__title a { background-image: url('{{ $logoWitPngUrl }}'); }
        .footer__brand { background-image: url('{{ $logoGreyPngUrl }}'); }


        /* Version for browsers WITH WebP support */
        html.webp .header-background { background-image: url('{{ $cloudsWebpUrl }}'); }
        html.webp .sparkle-element { background-image: url('{{ $sparkleWebpUrl }}'); }
        html.webp .dots-element { background-image: url('{{ $dotsWebpUrl }}'); }
        html.webp .header__title a { background-image: url('{{ $logoWitWebpUrl }}'); }
        html.webp .footer__brand { background-image: url('{{ $logoGreyWebpUrl }}'); }
    </style>
    @vite(['resources/scss/app.scss', 'resources/ts/app.ts'])
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>
<body>
    <header>
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