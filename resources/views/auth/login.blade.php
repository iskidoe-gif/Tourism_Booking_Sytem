@php
    $isAdmin = ($role ?? 'tourist') === 'admin';
@endphp

<x-layout>
    <section class="login-hero" aria-label="{{ $isAdmin ? 'Admin login' : 'Tourist login' }}">
        <div class="login-card">
            <div class="login-photo">
                <a href="{{ route('home') }}" class="login-brand">Bolinao</a>
                <div>
                    <p class="login-kicker">{{ $isAdmin ? 'Admin Access' : 'Plan Your Visit' }}</p>
                    <h1>{{ $isAdmin ? 'Manage trips with care.' : 'Welcome back to Bolinao.' }}</h1>
                    <p>
                        {{ $isAdmin
                            ? 'Review bookings, monitor reservations, and keep every guest experience organized.'
                            : 'Sign in to continue booking heritage stops, beaches, caves, and coastal escapes.' }}
                    </p>
                </div>
            </div>

            <div class="login-panel">
                <div class="login-heading">
                    <p>{{ $isAdmin ? 'Administrator' : 'Tourist Account' }}</p>
                    <h2>{{ $isAdmin ? 'Sign in as admin' : 'Sign in' }}</h2>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Login failed</strong>
                        <div>Please check your credentials and try again.</div>
                    </div>
                @endif

                <form method="POST" action="{{ $isAdmin ? route('admin.login.store') : route('login.store') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="email">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required autofocus />
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="auth-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Password" class="auth-input" required />
                        @error('password')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <label class="remember-row">
                        <input name="remember" type="checkbox" value="1">
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="btn-primary">
                        {{ $isAdmin ? 'Open Admin Dashboard' : 'Sign In to Account' }}
                    </button>
                </form>

                <div class="login-switch">
                    @if($isAdmin)
                        <a href="{{ route('login') }}">Tourist login</a>
                    @else
                        <a href="{{ route('register') }}">Create account</a>
                        <span></span>
                        <a href="{{ route('admin.login') }}">Admin login</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</x-layout>
