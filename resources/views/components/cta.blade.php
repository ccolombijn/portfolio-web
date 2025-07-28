<section class="cta">
    <div class="cta__container">
        <div class="cta__image">
            @if(isset($image_url))
            <picture class="cta__picture">
                <img src="storage{{$image_url}}" />
            </picture>
            @endif
        </div>
        <div class="cta__text">
            <h2>{{$title}}</h2>
            @if(isset($subtitle))<h3>{{$subtitle}}</h3>@endif
            @if(isset($text))<p>{{$text}}</p>@endif
            @if(isset($link1) && isset($link1_text))
                @include('components.button',[
                    'link' => route($link1), 
                    'text' => $link1_text
                ])
            @endif
            @if(isset($link2) && isset($link2_text))
                @include('components.button',[
                    'link' => route($link2), 
                    'text' => $link2_text
                ])
            @endif
        </div>
    </div>
</section>
