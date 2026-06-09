<x-layout>
    <div class="auth-panel">
        <div class="auth-header">
            <div class="auth-badge">
                <svg style="width: 32px; height: 32px;" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2a1 1 0 0 1 1 1v2a1 1 0 1 1-2 0V3a1 1 0 0 1 1-1zm0 14a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm-8-9a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1zm16 0a1 1 0 0 0-1 1v2a1 1 0 1 0 2 0V8a1 1 0 0 0-1-1z" />
                </svg>
            </div>
            <h1 class="auth-title">Create your account</h1>
            <p class="auth-lead">Sign up to start booking your dream travel experiences.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <strong>Registration failed.</strong>
                <div>Please fix the errors below and try again.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf

            <div class="auth-group">
                <label for="name">Full Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Juan Dela Cruz" class="auth-input" required autocomplete="name" />
                @error('name')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <label for="email">Email Address</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required autocomplete="email" />
                @error('email')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <label for="password">Password</label>
                <div class="password-input-wrapper">
                    <input id="password" name="password" type="password" placeholder="••••••••" class="auth-input" required autocomplete="new-password" />
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                        <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                @error('password')<p class="error-text">{{ $message }}</p>@enderror
            </div>

            <div class="auth-group">
                <label for="password_confirmation">Confirm Password</label>
                <div class="password-input-wrapper">
                    <input id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••" class="auth-input" required autocomplete="new-password" />
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                        <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary">Create Account</button>
        </form>

        <div class="divider">
            <div></div>
            <span>Already have an account?</span>
            <div></div>
        </div>

        <div class="auth-links">
            <a href="{{ route('home', ['auth' => 'signin']) }}" class="btn-secondary">Sign In</a>
            <form method="POST" action="{{ route('guest.login') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn-link">Continue as Guest</button>
            </form>
        </div>
    </div>
</x-layout>
