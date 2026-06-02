<x-layout>
    <section class="bolinao-hero" aria-label="Bolinao tourism landing page">
        <div class="bolinao-card">
            <nav class="bolinao-nav" aria-label="Main navigation">
                <a href="{{ route('home') }}" class="bolinao-brand">Bolinao</a>
                <div class="bolinao-navlinks">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('packages.index') }}">Trips</a>
                    <a href="{{ route('login') }}">Sign in</a>
                    <a href="{{ route('register') }}">Register</a>
                </div>
            </nav>

            <div class="bolinao-copy">
                <p class="bolinao-kicker">Heritage</p>
                <h1>Bolinao</h1>
                <p>
                    Visit centuries-old stone landmarks, coastal villages, beaches,
                    caves, and quiet Pangasinan views in one memorable trip.
                </p>

                <div class="bolinao-actions">
                    <a href="{{ route('register') }}" class="bolinao-button bolinao-button-light">Explore all trips</a>
                    <a href="{{ route('login') }}" class="bolinao-button bolinao-button-outline">Explore packages</a>
                </div>
            </div>

            <div class="bolinao-socials" aria-label="Social links">
                <span>f</span>
                <span>x</span>
                <span>ig</span>
            </div>

            <div class="bolinao-progress">
                <span class="bolinao-play">▶</span>
                <span class="bolinao-line"></span>
            </div>

            <div class="bolinao-dots" aria-hidden="true">
                <span class="active"></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </section>
</x-layout>
