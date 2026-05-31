<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Tourism Booking System') }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="shell">
    <div class="topbar">
        <div class="frame">
            <a href="{{ route('home') }}" class="brand">Tourism Booking System</a>
            <nav class="menu">
                @php($adminUser = \Illuminate\Support\Facades\Auth::guard('admin')->user())
                @php($touristUser = auth()->user())

                @if($touristUser)
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.index')">Packages</x-nav-link>
                    <x-nav-link :href="route('reservations.index')" :active="request()->routeIs('reservations.index')">Reservations</x-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="navbtn">Logout</button>
                    </form>
                @elseif($adminUser)
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin</x-nav-link>
                    <x-nav-link :href="route('admin.reports.bookings', 'csv')" :active="request()->routeIs('admin.reports.bookings')">Reports</x-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="navbtn">Logout</button>
                    </form>
                @else
                    <x-nav-link :href="route('login')" :active="request()->routeIs('login')">Login</x-nav-link>
                    <x-nav-link :href="route('admin.login')" :active="request()->routeIs('admin.login')">Admin Login</x-nav-link>
                    <x-nav-link :href="route('register')" :active="request()->routeIs('register')">Register</x-nav-link>
                @endif
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
