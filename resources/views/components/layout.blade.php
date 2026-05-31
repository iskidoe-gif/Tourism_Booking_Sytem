<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TourPH - {{ $title ?? 'Explore the Philippines' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f7f7f4; color: #171713; }
        .app-shell { max-width: 1100px; }
        .navbar { border: 1px solid #d8d8d1; border-radius: 10px; }
        .navbar-brand { font-weight: 700; color: #171713 !important; }
        .brand-mark { color: #078263; font-size: 1.2rem; }
        .nav-button { border: 1px solid #bdbdb6; border-radius: 8px; color: #171713; font-weight: 600; padding: .55rem 1.15rem; }
        .nav-button:hover, .nav-button.active { background-color: #ffffff; border-color: #727269; color: #171713; }
        .btn-primary { background-color: #ffffff; border-color: #aeadA6; color: #171713; font-weight: 600; }
        .btn-primary:hover { background-color: #f0f0eb; border-color: #77776f; color: #171713; }
        .btn-outline-secondary { border-color: #aeadA6; color: #171713; font-weight: 600; }
        .card { border: 1px solid #d8d8d1; border-radius: 8px; }
        .section-title { color: #74746c; font-size: .78rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .package-art { height: 104px; display: grid; place-items: center; font-size: 2.25rem; }
        .package-art-0 { background: #dcefe8; color: #08745d; }
        .package-art-1 { background: #e3eff9; color: #1266ad; }
        .package-art-2 { background: #eaf4df; color: #467d25; }
        .price-text { color: #08745d; font-weight: 800; }
        .badge-pending { background-color: #FAEEDA; color: #854F0B; }
        .badge-confirmed { background-color: #E1F5EE; color: #0F6E56; }
        .badge-cancelled { background-color: #FCEBEB; color: #A32D2D; }
        .table { --bs-table-bg: #ffffff; --bs-table-border-color: #d8d8d1; }
        .table thead th { background: #f4f3ee; font-size: .78rem; }
        .avatar { width: 36px; height: 36px; border-radius: 50%; display: grid; place-items: center; background: #d4f3e9; color: #08745d; font-weight: 700; font-size: .8rem; }
    </style>
</head>
<body>
<div class="container app-shell py-4">
    <nav class="navbar navbar-expand-lg navbar-light bg-white px-3">
        <div class="container-fluid px-0">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                <span class="brand-mark">&#9878;</span> TourPH
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav mx-auto gap-2">
                    <li class="nav-item">
                        <a class="nav-link nav-button {{ request()->routeIs('packages.*') ? 'active' : '' }}" href="{{ route('packages.index') }}">Packages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-button" href="{{ route('packages.index') }}#book-tour">Book a tour</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-button {{ request()->routeIs('reservations.*') ? 'active' : '' }}" href="{{ route('packages.index') }}#my-reservations">My reservations</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item d-flex align-items-center gap-2">
                        <span class="avatar">
                            {{ Auth::check() ? Str::of(Auth::user()->name)->explode(' ')->map(fn($part) => Str::substr($part, 0, 1))->take(2)->implode('') : 'JD' }}
                        </span>
                        <span class="small">{{ Auth::check() ? Auth::user()->name : 'Juan D.' }}</span>
                    </li>
                    @auth
                        @if(Route::has('logout'))
                            <li class="nav-item ms-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                                </form>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{ $slot }}
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
