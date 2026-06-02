<x-layout>
    <div class="auth-panel">
        <div class="auth-header">
            <div class="auth-badge admin">
                <svg style="width: 32px; height: 32px;" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm-8-9a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1zm16 0a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1z" />
                </svg>
            </div>
            <h1 class="auth-title">Admin Portal</h1>
            <p class="auth-lead">Access the management dashboard to oversee bookings and tour packages</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Admin Login Failed</strong>
                <div>Please verify your credentials and try again.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
            @csrf

            <div class="auth-group">
                <label for="email">Admin Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@example.com" class="auth-input" required autocomplete="email" />
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="••••••••" class="auth-input" required autocomplete="current-password" />
                @error('password')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="btn-primary">Access Dashboard</button>
        </form>

        <div class="divider">
            <div></div>
            <span>Other Users</span>
            <div></div>
        </div>

        <div class="auth-links">
            <a href="{{ route('home') }}" data-auth-open data-auth-mode="signin" class="btn-secondary">Tourist Login</a>
            <a href="{{ route('home') }}" data-auth-open data-auth-mode="register" class="btn-secondary">Create Tourist Account</a>
        </div>
    </div>
</x-layout>
