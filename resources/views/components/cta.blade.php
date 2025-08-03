<section class="cta">
    <div class="cta__container">
        <div class="cta__image">
            @if(isset($image_url))
                @include('components.image',['src' => $image_url])
            @endif
        </div>
        <div class="cta__text">
            <h2 class="cta__title">{{$title}}</h2>
            @if(isset($subtitle))<h3 class="cta__subtitle">{{$subtitle}}</h3>@endif
            @if(isset($text))<p class="cta__text">{{$text}}</p>@endif
            <div class="cta__buttons">
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
    </div>
</section>
