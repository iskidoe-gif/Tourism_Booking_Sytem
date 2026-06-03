<x-layout>
    <div class="auth-panel admin">
        <div class="auth-header">
            <div class="auth-badge admin">
                <svg style="width: 32px; height: 32px;" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm-8-9a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1zm16 0a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1z" />
                </svg>
            </div>
            <h1 class="auth-title">Admin Login</h1>
            <p class="auth-lead">Sign in with your admin account to manage bookings, packages, and reports.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Admin login failed.</strong>
                <div>Please verify your credentials and try again.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
            @csrf

            <div class="auth-group">
                <label for="email">Admin Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@tourph.com" class="auth-input" required autocomplete="email" />
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

        @if (app()->isLocal())
            <div class="auth-helper">
                <p><strong>Seeded admin credentials:</strong> admin@tourph.com / password123</p>
            </div>
        @endif

        <div class="auth-links">
            <a href="{{ route('home') }}" class="btn-secondary">Back to home</a>
        </div>
    </div>
</x-layout>
