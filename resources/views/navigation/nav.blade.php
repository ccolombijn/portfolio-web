<nav>
    <div class="nav-mobile-open"><i class="fa-solid fa-bars"></i></div>
    <div class="nav-mobile-close"><i class="fa-solid fa-xmark"></i></div>
    <ul class="nav">
    @foreach ($navigationItems as $item)
        @if(!isset($item['exclude_nav']) &&  $item['title'] !== '')
        <li class="nav-item">
            <a href="{{ route($item['routeName']) }}"
                class="nav-link{{ request()->routeIs($item['name'] . '*') ? ' active' : '' }}" aria-label="Ga naar {{ $item['title'] }}">
                {{ $item['title'] }}
            </a>
        </li>
        @endif
    @endforeach
    </ul>
</nav>