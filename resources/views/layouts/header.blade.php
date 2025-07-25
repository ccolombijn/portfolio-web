<section class="header @if(isset($name))header--{{$name}}@endif">
    <div class="header__container">
        <h1 class="header__title"><a href="{{ route('home') }}"><span>{{ env('APP_NAME')}}</span></a></h1>
        @include('navigation.nav')
        <div class="header__content">
            @if(isset($title))
                <h2>{{$title}}</h2>
            @endif
            @if(isset($image_url))
                <img src="/storage{{ $image_url }}" />
            @endif
            {!! $header !!}
        </div>
    </div>
    <div class="animated-gradient-bg"></div>
    <div class="animated-clouds"></div>
    <canvas id="dot-particles-canvas" class="absolute inset-0 z-0"></canvas>
</section>