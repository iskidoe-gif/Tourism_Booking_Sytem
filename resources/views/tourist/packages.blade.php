<x-layout>
    @php
        $touristUser = auth()->user()?->isTourist() ? auth()->user() : null;
    @endphp

    <section class="packages-hero">
        <div class="packages-hero-card">
            <div class="packages-hero-copy">
                <span class="visual-tag">Explore Bolinao&apos;s Natural Beauty</span>
                <h1 class="visual-headline">Discover your next tour</h1>
                <p class="visual-copy">
                    Search tours by destination, package name, or experience. Choose from beachfront escapes, culture tours, and island adventures.
                </p>
                <form action="{{ route('packages.index') }}" method="GET" class="packages-search">
                    <label for="search" class="sr-only">Search tours by destination or title</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}"
                           placeholder="eg. Patar Beach, Church..." class="form-control" />
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
        </div>
    </section>

    <section class="packages-listing">
        <div class="packages-listing-header">
            <div>
                <h2 class="section-title">Browse Tour Packages</h2>
                <p class="section-copy">Handpicked Bolinao trips in one place.</p>
            </div>
            @if(request('search'))
                <div class="search-summary">Showing results for “{{ request('search') }}”</div>
            @endif
        </div>

        @if($packages->isEmpty())
            <div class="card text-center text-muted py-5">No active packages found.</div>
        @else
            <div class="package-card-grid">
                @foreach($packages as $package)
                    <article class="package-card">
                        @php
                            $imageUrl = $package->image
                                ? (str_starts_with($package->image, 'http') ? $package->image : asset($package->image))
                                : asset('images/package-default.svg');
                        @endphp
                        <div class="package-card-media" style="background-image: url('{{ $imageUrl }}');"></div>
                        <div class="package-card-body">
                            <div class="package-card-meta">
                                <span>{{ $package->duration_days }} Day Tour</span>
                                <span>{{ $package->destination?->name ?? 'Bolinao' }}</span>
                                <span>Max {{ $package->max_guests }} guests</span>
                            </div>
                            <h3 class="package-card-title">{{ $package->name }}</h3>
                            <p class="package-card-description">{{ Str::limit($package->description, 110) }}</p>
                            <div class="package-card-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    {!! $i <= round($package->rating) ? '&#9733;' : '&#9734;' !!}
                                @endfor
                            </div>
                            <div class="package-card-footer">
                                <div class="package-card-price">
                                    <span class="price">&#8369;{{ number_format($package->price) }}</span>
                                    <span class="price-note">/ person</span>
                                </div>
                                <div class="package-card-actions">
                                    <a href="{{ route('packages.show', $package) }}" class="btn btn-secondary">View details</a>
                                    @if($touristUser)
                                        <a href="{{ route('packages.show', $package) }}" class="btn btn-primary">Book now</a>
                                    @else
                                        <a href="{{ route('home', ['auth' => 'signin']) }}" class="btn btn-primary">Login to Book</a>
                                        <a href="{{ route('home', ['auth' => 'register']) }}" class="btn btn-outline-secondary">Register</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="package-pagination">
                {{ $packages->links() }}
            </div>
        @endif
    </section>
</x-layout>
