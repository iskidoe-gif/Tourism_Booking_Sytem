<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bolinao Tourism' }} — Bolinao, Pangasinan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bolinao-blue:  #0077B6;
            --bolinao-teal:  #00B4D8;
            --bolinao-sand:  #F4A261;
            --bolinao-light: #CAF0F8;
        }
        body { background-color: #f8fdff; font-family: 'Segoe UI', sans-serif; }

        /* Navbar */
        .navbar { background: var(--bolinao-blue) !important; }
        .navbar-brand { font-weight: 700; color: #fff !important; font-size: 1.25rem; letter-spacing: .5px; }
        .navbar-brand span { color: var(--bolinao-sand); }
        .nav-link { color: rgba(255,255,255,.85) !important; font-size: 14px; }
        .nav-link:hover, .nav-link.active { color: #fff !important; }

        /* Buttons */
        .btn-primary   { background: var(--bolinao-blue); border-color: var(--bolinao-blue); }
        .btn-primary:hover { background: #005f8e; border-color: #005f8e; }
        .btn-teal      { background: var(--bolinao-teal); border-color: var(--bolinao-teal); color: #fff; }
        .btn-teal:hover{ background: #009bbf; color: #fff; }

        /* Status badges */
        .badge-pending   { background: #fff3cd; color: #856404; }
        .badge-confirmed { background: #d1e7dd; color: #0f5132; }
        .badge-cancelled { background: #f8d7da; color: #842029; }
        .badge-paid      { background: #d1e7dd; color: #0f5132; }
        .badge-unpaid    { background: #fff3cd; color: #856404; }

        /* Cards */
        .card { border: 1px solid #ddeeff; border-radius: 12px; }
        .card:hover { box-shadow: 0 4px 18px rgba(0,119,182,.12); }

        /* Sidebar (admin) */
        .sidebar { min-height: calc(100vh - 56px); background: #023e8a; padding-top: 1rem; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); padding: 10px 20px; border-radius: 8px; margin: 2px 8px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,.15); color: #fff; }
        .sidebar .nav-link i { margin-right: 8px; }

        footer { background: var(--bolinao-blue); color: rgba(255,255,255,.7); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            &#127754; Bolinao <span>Tourism</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto ms-3 gap-1">
                <x-nav-link :href="route('packages.index')" :active="request()->routeIs('packages.*')">
                    <i class="bi bi-compass"></i> Packages
                </x-nav-link>
                @auth
                <x-nav-link :href="route('reservations.index')" :active="request()->routeIs('reservations.*')">
                    <i class="bi bi-calendar-check"></i> My Reservations
                </x-nav-link>
                @if(Auth::user()->isAdmin())
                <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    <i class="bi bi-shield-check"></i> Admin
                </x-nav-link>
                @endif
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto align-items-center gap-2">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-teal px-3" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                            <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center fw-bold"
                                 style="width:32px;height:32px;font-size:13px">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text text-muted small">{{ Auth::user()->email }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid px-4 py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{ $slot }}
</main>

<footer class="py-3 mt-5 text-center small">
    &copy; {{ date('Y') }} Bolinao Tourism Booking System &mdash; Bolinao, Pangasinan, Philippines
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
