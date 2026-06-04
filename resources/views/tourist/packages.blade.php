<x-layout>
    @php
        $touristUser = auth()->user()?->isTourist() ? auth()->user() : null;
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
            <aside class="packages-sidebar">
                <form action="{{ route('packages.index') }}" method="GET">
                    <div class="sidebar-panel">
                        <h3>Search tour packages</h3>
                        <div class="form-group">
                            <label for="search">Search tours</label>
                            <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search by name, location, or experience" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control">
                                <option value="">All categories</option>
                                @foreach($categoryMap as $key => $cat)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="sidebar-panel sidebar-panel-alt">
                        <h3>Duration filters</h3>
                        <p class="small-copy">Refine your results by trip length.</p>
                        <div class="filter-group">
                            <label class="filter-checkbox"><input type="checkbox" name="dur_all" checked> All</label>
                            <label class="filter-checkbox"><input type="checkbox" name="dur_1" {{ request('dur_1') ? 'checked' : '' }}> 1 Day</label>
                            <label class="filter-checkbox"><input type="checkbox" name="dur_2" {{ request('dur_2') ? 'checked' : '' }}> 2-4 Days</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Search</button>
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

