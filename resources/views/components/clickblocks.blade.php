<section class="clickblocks">
    <div class="clickblocks__container">
        @foreach ($items as $item)
            <a href="{{ route($route, [$key => $item['slug']]) }}" class="clickblocks__card card">
                <figure>
                    @if(isset($item['image_url']))
                    <img src="/storage/{{$item['image_url']}}">
                    @endif
                    @if(isset($item['icon']))
                    <i class="{{$icon}}">
                    @endif
                </figure>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['description'] }}</p>
            </a>
        @endforeach
    </div>
</section>