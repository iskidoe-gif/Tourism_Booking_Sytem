<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tourism Booking System') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
@php
    $webUser = auth()->user();
    $adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user() ?: ($webUser?->isAdmin() ? $webUser : null);
    $touristUser = $webUser?->isTourist() ? $webUser : null;
    $isAuthPage = request()->routeIs(['admin.login']);
@endphp
<body class="shell {{ request()->routeIs('home') ? 'home-shell' : ($isAuthPage ? 'auth-shell' : 'page-shell') }}">

    <div class="topbar {{ request()->routeIs('home') ? 'home-topbar' : ($isAuthPage ? 'auth-topbar' : 'page-topbar') }}">
        <div class="frame">
            <nav class="bolinao-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="bolinao-brand">Bolinao</a>
                <div class="bolinao-navlinks">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">Home</x-nav-link>
                    <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.index')">Trips</x-nav-link>
                    @if($touristUser)
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                        <a href="{{ route('dashboard') }}" class="profile-link" title="Go to profile">
                            <span class="profile-icon" aria-hidden="true">{{ strtoupper(substr($touristUser->name, 0, 1)) }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="nav-form">
                            @csrf
                            <button class="navbtn">Logout</button>
                        </form>
                    @elseif($adminUser)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin</x-nav-link>
                        <x-nav-link :href="route('admin.packages.index')" :active="request()->routeIs('admin.packages.*')">Packages</x-nav-link>
                        <x-nav-link :href="route('admin.reports.bookings', 'csv')" :active="request()->routeIs('admin.reports.bookings')">Reports</x-nav-link>
                        <form method="POST" action="{{ route('logout') }}" class="nav-form">
                            @csrf
                            <button class="navbtn">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('home') }}" data-auth-open data-auth-mode="signin">Login</a>
                    @endif
                </div>
            </nav>
        </div>
    </div>

    <main class="content">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{ $slot }}
    </main>
</body>
</html>
