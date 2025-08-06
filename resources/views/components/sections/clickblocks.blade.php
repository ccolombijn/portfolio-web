@php 
    if(!isset($items) && isset($contentData['clickblocks'])){
        $items = $contentData['clickblocks'];
    }
@endphp
<section class="clickblocks">
    <div class="clickblocks__container">
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}" class="clickblocks__card card" aria-label="{{ isset($item['label'] ?  $item['label'] : $item['description']}}">
                <figure>
                    @if(isset($item['image_url']))
                    <img src="/storage/{{$item['image_url']}}">
                    @endif
                    @if(isset($item['icon']))
                    <i class="{{$item['icon']}}">
                    @endif
                </figure>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['description'] }}</p>
            </a>
        @endforeach
    </div>
</section>