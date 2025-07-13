<nav>
    <ul class="nav">
    {{-- {{dd($navigationItems)}} --}}
    @foreach ($navigationItems as $item)
        @if(!isset($item['exclude_nav']))
        <li class="nav-item">
            <a href="{{ route($item['name']) }}"
                class="nav-link{{ request()->routeIs($item['name']) ? ' active' : '' }}">
                {{ $item['title'] }}
            </a>
        </li>
        @endif
    @endforeach
    </ul>
</nav>