@props(['active' => false])

<li class="nav-item">
    <a {{ $attributes }} class="nav-link {{ $active ? 'fw-semibold text-success' : '' }}">
        {{ $slot }}
    </a>
</li>
