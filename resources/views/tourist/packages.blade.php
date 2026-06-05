<x-layout>
    @php
        $touristUser = auth()->user()?->isTourist() ? auth()->user() : null;
        $selectedDuration = $selectedDuration ?? request('duration', 'all');
        if (request()->boolean('dur_1') && ! request()->boolean('dur_all')) {
            $selectedDuration = '1';
        } elseif (request()->boolean('dur_2') && ! request()->boolean('dur_all')) {
            $selectedDuration = '2_4';
        }
    @endphp

    <section class="packages-hero bolinao-hero">
        <div class="packages-hero-card bolinao-card">
            <div class="packages-hero-copy bolinao-copy">
                <span class="visual-tag">Explore Bolinao&apos;s Natural Beauty</span>
                <h1 class="visual-headline">Discover your next tour</h1>
                <p class="visual-copy">
                    Search tours by destination, package name, or experience. Choose from beachfront escapes, culture tours, and island adventures.
                </p>
                <div class="packages-hero-cta">Search tours below</div>
            </div>
        </div>
    </section>

    <section class="packages-listing">
        <div class="packages-container">
            <aside class="packages-sidebar packages-search-panel">
                <h3>Search tour packages</h3>
                <form action="{{ route('packages.index') }}" method="GET" class="search-form packages-search-form">
                    <div class="form-group packages-search-field packages-search-field-wide">
                        <label for="search">Search tours</label>
                        <div class="search-input-wrap">
                            <span class="search-input-icon" aria-hidden="true"></span>
                            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search by name, location, or experience" class="form-control" />
                        </div>
                    </div>

                    <div class="form-group packages-search-field">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">All categories</option>
                            @foreach($categoryMap as $key => $cat)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group duration-group packages-search-field">
                        <span class="duration-title">Duration</span>
                        <div class="duration-filter">
                            <label class="duration-option">
                                <input type="radio" name="duration" value="all" @checked(! in_array($selectedDuration, ['1', '2_4'], true))>
                                <span>All</span>
                            </label>
                            <label class="duration-option">
                                <input type="radio" name="duration" value="1" @checked($selectedDuration === '1')>
                                <span>1 Day</span>
                            </label>
                            <label class="duration-option">
                                <input type="radio" name="duration" value="2_4" @checked($selectedDuration === '2_4')>
                                <span>2-4 Days</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="search-btn packages-search-submit">Search</button>
                </form>
            </aside>

            <main class="packages-main">
                <div class="packages-header-row">
                    <div>
                        <h2 class="section-title">Browse Tour Packages</h2>
                        <p class="section-copy">Handpicked Bolinao trips in one place.</p>
                    </div>
                </div>

                @if($packages->isEmpty())
                    <div class="card text-center text-muted py-5">No active packages found.</div>
                @else
                    <div class="package-card-grid">
                        @foreach($packages as $package)
                            <article class="package-card destination-card">
                                <div class="package-card-media" style="background-image: url('{{ $package->image_url }}');">
                                    <div class="badge-rating">{{ number_format($package->rating,1) }} ★</div>
                                </div>
                                <div class="package-card-body">
                                    <div class="package-card-meta">
                                        <span>{{ $package->duration_days }} Day Tour</span>
                                        <span>·</span>
                                        <span>{{ $package->location }}</span>
                                    </div>
                                    <h3 class="package-card-title">{{ $package->name }}</h3>
                                    <p class="package-card-description">{{ Str::limit($package->description, 110) }}</p>
                                    <div style="flex:1"></div>
                                    <div class="package-card-footer">
                                        <div class="package-card-price">
                                            <span class="price">₱{{ number_format($package->price) }}</span>
                                            <span class="price-note">/ person</span>
                                        </div>
                                        <div class="package-card-actions">
                                            <a href="{{ route('packages.show', $package) }}" class="btn btn-secondary">View details</a>
                                            @if($touristUser)
                                                <a href="{{ route('packages.show', $package) }}" class="btn">Book</a>
                                            @else
                                                <a href="#" class="btn" data-auth-open>Book</a>
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
            </main>
        </div>
    </section>
</x-layout>
