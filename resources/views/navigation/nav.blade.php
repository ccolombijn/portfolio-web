<nav>
    <ul class="nav">
    @foreach ($navigationItems as $item)
        @if(!isset($item['exclude_nav']) &&  $item['title'] !== '')
        <li class="nav-item">
            <a href="{{ route($item['routeName']) }}"
                class="nav-link{{ request()->routeIs($item['name'] . '.*') ? ' active' : '' }}">
                {{ $item['title'] }}
            </a>
        </li>
        @endif
    @endforeach
    </ul>
</nav>