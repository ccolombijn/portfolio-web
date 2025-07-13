<nav>
    <ul>
    @foreach ($navigationItems as $item)
        <li>
            <a href="{{ route($item['name']) }}"
                class="nav-link {{ request()->routeIs($item['name']) ? 'active' : '' }}">
                {{ $item['title'] }}
            </a>
        </li>
    @endforeach
    </ul>
</nav>