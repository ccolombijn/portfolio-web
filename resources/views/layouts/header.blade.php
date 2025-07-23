<section class="header">
    <div class="animated-gradient-bg">

        <div class="header__container">
            <h1 class="header__title"><a href="{{ route('home') }}"><span>{{ env('APP_NAME')}}</span></a></h1>
            @include('navigation.nav')
            <div class="header__content">
                {!! $header !!}
            </div>
        </div>
        <div class="animated-clouds"></div>
    </div>
    
    <canvas id="dot-wave-canvas" class="absolute inset-0 z-0"></canvas>
    
</section>