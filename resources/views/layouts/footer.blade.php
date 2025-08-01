<section class="footer">
    <div class="footer__container">
        <a href="/" class="footer__brand" aria-label="{{env('APP_NAME')}}"><span>{{env('APP_NAME')}}</span></a>
        <div class="footer__contact">
            {{isset($contactData['name']) ? $contactData['name'] : ''}}<br>
            @if(isset($contactData['email']))<a href="mailto:{{$contactData['email']}}" aria-label="Mail naar {{$contactData['email']}}">{{$contactData['email']}}</a><br>@endif
            @if(isset($contactData['telephone']))<a href="tel:{{$contactData['telephone']}}" aria-label="Bel naar {{$contactData['telephone']}}">{{$contactData['telephone']}}</a><br>@endif
        </div>
        @if(!empty($footer))
        <div class="footer__content">
            {!! $footer !!}
        </div>
        @endif
        <div class="footer__sitemap">
            @include('components.sitemap')
        </div> 
    </div>
</section>