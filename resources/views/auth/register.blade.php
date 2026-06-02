<x-layout>
    <section class="login-hero" aria-label="Create tourist account">
        <div class="login-card register-card">
            <div class="login-photo">
                <a href="{{ route('home') }}" class="login-brand">Bolinao</a>
                <div>
                    <p class="login-kicker">Start Exploring</p>
                    <h1>Create your Bolinao trip account.</h1>
                    <p>
                        Book local packages, manage reservations, and keep your
                        Pangasinan heritage and coastal plans in one place.
                    </p>
                </div>
            </div>

            <div class="login-panel">
                <div class="login-heading">
                    <p>Tourist Registration</p>
                    <h2>Create account</h2>
                </div>

                @if ($errors->any())
                    <div class="alert alert-error">
                        <strong>Registration error</strong>
                        <div>Please check the errors below and try again.</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="name">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Juan Dela Cruz" class="auth-input" required autofocus />
                        @error('name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="auth-group">
                        <label for="email">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required />
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="auth-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Password" class="auth-input" required />
                        @error('password')<p class="error-text">{{ $message }}</p>@enderror
                    </div>

                    <div class="auth-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm password" class="auth-input" required />
                    </div>

                    <button type="submit" class="btn-primary">Create My Account</button>
                </form>

                <div class="login-switch">
                    <a href="{{ route('login') }}">Sign in</a>
                    <span></span>
                    <a href="{{ route('admin.login') }}">Admin login</a>
                </div>
            </div>
        </div>
    </section>
</x-layout>
