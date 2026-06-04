<x-layout>
    
    <section class="homepage-slider" aria-label="Homepage slider">
        <div class="slider-view">
            <div class="slider-track">
                <section class="slider-slide slide-hero">
                    <section class="bolinao-hero" aria-label="Bolinao tourism landing page">
                        <div class="bolinao-card">
                            <div class="bolinao-hero-grid">
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
                                            <a href="#" class="bolinao-button bolinao-button-light" data-auth-open>Book a Trip Now</a>
                                        @endauth
                                        <a href="{{ route('packages.index') }}" class="bolinao-button bolinao-button-outline">Browse Tour Packages</a>
                                    </div>
                                </div>

                                <div class="bolinao-hero-visual" aria-hidden="true"></div>
                            </div>
                        </div>
                    </section>
                </section>

                <section class="slider-slide slide-packages featured-packages" aria-label="Featured high-rated packages">
                    <div class="featured-packages-inner">
                        <div class="section-header">
                            <p>Traveler Favorites</p>
                            <h2>Top-Rated Tour Packages</h2>
                            <p>Discover the most popular experiences loved by our guests</p>
                        </div>

                        <div class="package-card-grid">
                            @forelse($topRatedPackages as $package)
                                <article class="package-card">
                                    <div class="package-card-media" style="background-image: url('{{ $package->image_url }}');"></div>
                                    <div class="package-card-body">
                                        <div class="package-card-meta">
                                            <span>{{ $package->duration_days }} Day Tour</span>
                                            <span>{{ $package->location }}</span>
                                            <span>Max {{ $package->max_guests }} guests</span>
                                        </div>
                                        <h3 class="package-card-title">{{ $package->name }}</h3>
                                        <p class="package-card-description">{{ Str::limit($package->description, 110) }}</p>
                                        <div class="package-card-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                {!! $i <= round($package->rating) ? '&#9733;' : '&#9734;' !!}
                                            @endfor
                                            <span class="rating-text">({{ number_format($package->rating, 1) }})</span>
                                        </div>
                                        <div class="package-card-footer">
                                            <div class="package-card-price">
                                                <span class="price">&#8369;{{ number_format($package->price) }}</span>
                                                <span class="price-note">/ person</span>
                                            </div>
                                            <div class="package-card-actions">
                                                <a href="{{ route('packages.show', $package) }}" class="btn btn-secondary">View details</a>
                                                <a href="{{ route('packages.index') }}" class="btn btn-primary">Browse more</a>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <p style="grid-column: 1 / -1; text-align: center; padding: 2rem;">No top-rated packages available yet. Check back soon!</p>
                            @endforelse
                        </div>

                        <div style="text-align: center; margin-top: 2rem;">
                            <a href="{{ route('packages.index') }}" class="btn btn-primary">View All Packages</a>
                        </div>
                    </div>
                </section>

                <section class="slider-slide slide-reviews customer-reviews-section" aria-label="Customer reviews and testimonials">
                    <div class="reviews-container">
                        <div class="reviews-header">
                            <p>What Our Travelers Say</p>
                            <h2>Customer Reviews & Testimonials</h2>
                            <p class="reviews-header-desc">Hear from real travelers who've experienced the beauty and adventure of Bolinao with us</p>
                        </div>

                        <div class="reviews-grid">
                            @forelse($customerReviews as $review)
                                <div class="review-card">
                                    <div class="review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            {!! $i <= round($review->rating) ? '&#9733;' : '&#9734;' !!}
                                        @endfor
                                    </div>
                                    <p class="review-text">"{{ $review->comment }}"</p>
                                    <div class="review-author">
                                        <div class="review-avatar">
                                            {{ strtoupper(substr($review->user->name ?? 'Guest', 0, 1)) }}
                                        </div>
                                        <div class="review-info">
                                            <h4>{{ $review->user->name ?? 'Guest Traveler' }}</h4>
                                            <p>{{ $review->tourPackage->name ?? 'Tour Guest' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div style="grid-column: 1 / -1; text-align: center; padding: 2rem; color: rgba(234, 224, 207, 0.6);">
                                    <p>Reviews coming soon from our travelers!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

</x-layout>
