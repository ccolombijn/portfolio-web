@php
$webp = Vite::asset('resources/' . str_replace('.png','.webp',$url));
$png = Vite::asset('resources/' . $url );
$path = resource_path($url);
$dimensions = '';
if (file_exists($path)) {
    $size = getimagesize($path);
    if ($size) {
        $width = $size[0];
        $height = $size[1];
        $dimensions = " width=\"{$width}\" height=\"{$height}\"";
    }
}
@endphp
<figure>
    <source srcset="{{ $webp }}" type="image/webp">
    <source srcset="{{ $png }}" type="image/png">
    <img src="{{ $png }}" alt="{{ isset($alt) ? $alt : $url }}"{{ $dimensions }}>
</figure>