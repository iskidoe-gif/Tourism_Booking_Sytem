<x-layout>
    
    <section class="bolinao-hero" aria-label="Bolinao tourism landing page">
        <div class="bolinao-card">
            <header class="bolinao-hero-header">
                <nav class="bolinao-nav" aria-label="Main navigation">
                    <a href="{{ route('home') }}" class="bolinao-brand">Bolinao</a>
                    <div class="bolinao-navlinks">
                        <a href="{{ route('home') }}">Home</a>
                        <a href="{{ route('packages.index') }}">Trips</a>
                        @auth
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('home') }}" data-auth-open data-auth-mode="signin">Login</a>
                            <a href="{{ route('home') }}" class="bolinao-button bolinao-button-outline" data-auth-open data-auth-mode="register">Register</a>
                        @endauth
                    </div>
                </nav>
            </header>
            <div class="bolinao-copy">
                <p class="bolinao-kicker">Heritage</p>
                <h1>Bolinao</h1>
                <p>
                    Visit centuries-old stone landmarks, coastal villages, beaches,
                    caves, and quiet Pangasinan views in one memorable trip.
                </p>

                <div class="bolinao-actions">
                    @auth
                        <a href="{{ route('packages.index') }}" class="bolinao-button bolinao-button-light">Book a Trip Now</a>
                    @else
                        <a href="{{ route('home') }}" class="bolinao-button bolinao-button-light" data-auth-open data-auth-mode="register">Book a Trip Now</a>
                    @endauth
                    <a href="{{ route('packages.index') }}" class="bolinao-button bolinao-button-outline">Browse Tour Packages</a>
                </div>
            </div>
        </div>
    </section>

    <section class="top-destinations main-section" aria-label="Top destinations">
        <div class="top-destinations-inner">
            <div class="section-header">
                <p>Most visited</p>
                <h2>Top destinations in Bolinao</h2>
            </div>

            <div class="destinations-grid">
                <article class="destination-card">
                    <div class="destination-card-copy">
                        <p class="destination-region">Beach escape</p>
                        <h3>Patar White Beach</h3>
                        <p>Soft sand, gentle waves, and a dramatic coastline make Patar one of the most iconic Bolinao beaches.</p>
                    </div>
                    <a href="{{ route('packages.index') }}" class="destination-link">View trips</a>
                </article>

                <article class="destination-card">
                    <div class="destination-card-copy">
                        <p class="destination-region">Heritage & nature</p>
                        <h3>Enchanted Cave</h3>
                        <p>Swim in crystal-clear waters and explore a hidden cave pool beneath the lush coastal cliffs.</p>
                    </div>
                    <a href="{{ route('packages.index') }}" class="destination-link">View trips</a>
                </article>

                <article class="destination-card">
                    <div class="destination-card-copy">
                        <p class="destination-region">Culture</p>
                        <h3>Saint James Church</h3>
                        <p>A centuries-old stone church with stunning views, rich history, and a peaceful atmosphere.</p>
                    </div>
                    <a href="{{ route('packages.index') }}" class="destination-link">View trips</a>
                </article>
            </div>
        </div>
        </div>
    </section>

    <div class="auth-modal" data-auth-modal hidden>
        <div class="auth-modal-backdrop" data-auth-close></div>

        <section class="auth-modal-panel" role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
            <button type="button" class="auth-modal-close" data-auth-close aria-label="Close authentication form">&times;</button>

            <div class="auth-modal-heading">
                <p>Bolinao Account</p>
                <h2 id="auth-modal-title">Log in to your account</h2>
            </div>

            <div class="auth-pane active" data-auth-pane="signin">
                <form method="POST" action="{{ route('login.store') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="modal-login-email">Email Address</label>
                        <input id="modal-login-email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-login-password">Password</label>
                        <input id="modal-login-password" name="password" type="password" placeholder="Password" class="auth-input" required />
                    </div>

                    <label class="remember-row">
                        <input name="remember" type="checkbox" value="1">
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="btn-primary">Sign In</button>
                </form>
            </div>

            <div class="auth-pane" data-auth-pane="register">
                <form method="POST" action="{{ route('register') }}" class="auth-form">
                    @csrf

                    <div class="auth-group">
                        <label for="modal-register-name">Full name</label>
                        <input id="modal-register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Your name" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-email">Email Address</label>
                        <input id="modal-register-email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-password">Password</label>
                        <input id="modal-register-password" name="password" type="password" placeholder="Password" class="auth-input" required />
                    </div>

                    <div class="auth-group">
                        <label for="modal-register-password-confirm">Confirm Password</label>
                        <input id="modal-register-password-confirm" name="password_confirmation" type="password" placeholder="Confirm Password" class="auth-input" required />
                    </div>

                    <button type="submit" class="btn-primary">Create Account</button>
                </form>

                <form method="POST" action="{{ route('guest.login') }}" class="auth-form" style="margin-top:1rem">
                    @csrf
                    <button type="submit" class="btn-secondary">Continue as Guest</button>
                </form>
            </div>
        </section>
    </div>
</x-layout>
