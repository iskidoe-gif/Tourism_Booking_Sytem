<!DOCTYPE html>
<html lang="ENG">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="shell">
        <header class="topbar">
            <div class="frame">
                <a href="{{ route('home') }}" class="brand">Tourism Booking System</a>
                @if (Route::has('login'))
                    <nav class="menu menu-right">
                    @auth
                        <a
                            href="{{ route('dashboard') }}"
                            class="navbtn">
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="navbtn"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="navbtn">
                                Register
                            </a>
                        @endif
                    @endauth
                    </nav>
                @endif
            </div>
        </header>
        <main class="frame">
            <div class="hero">
                <p class="eyebrow">Tourism Booking System</p>
                <h1 class="hero-title">Plan, book, and manage trips.</h1>
                <p class="hero-copy">A simple travel platform for tourists and admins.</p>
            </div>
        </main>
    </body>
</html>
