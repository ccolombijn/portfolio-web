@props([
    'src',
    'alt' => '',
    'source' => 'resources',
    'lazy' => true,
])

@php
    $fallbackUrl = '';
    $webpUrl = '';
    $dimensions = '';
    $mimeType = 'image/png';
    if(str_contains($src,'storage')) $source = 'storage';
    // resources
    if ($source === 'resources') {
        $fullResourcePath = str_contains($src,'resources') ? ltrim($src, '/') : 'resources/' . ltrim($src, '/');
        $physicalPath = resource_path(ltrim($src, '/'));
        
        $fallbackUrl = Vite::asset($fullResourcePath);
    
    // storage
    } elseif ($source === 'storage') {
        $pathInStorage = ltrim($src, '/');
        $physicalPath = storage_path('app/public/' . $pathInStorage);
        
        $fallbackUrl = asset($pathInStorage);
    }

    if (!empty($fallbackUrl)) {
        $webpUrl = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $fallbackUrl);


        if (isset($physicalPath) && file_exists($physicalPath)) {
            $imageSize = getimagesize($physicalPath);
            if ($imageSize) {
                $dimensions = "width=\"{$imageSize[0]}\" height=\"{$imageSize[1]}\"";
                $mimeType = $imageSize['mime'];
            }
        }
    }
@endphp

@if ($fallbackUrl)
    <picture>
        <source srcset="{{ $webpUrl }}" type="image/webp">
        <source srcset="{{ $fallbackUrl }}" type="{{ $mimeType }}">
        <img src="{{ $fallbackUrl }}" alt="{{ $alt }}" {!! $dimensions !!} @if($lazy) loading="lazy" @endif>
    </picture>
@endif