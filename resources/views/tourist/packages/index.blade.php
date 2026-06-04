<x-layout title="Tour Packages">

@php
    $touristUser = auth()->user()?->isTourist() ? auth()->user() : null;
@endphp

<section class="packages-hero bolinao-hero">
    <div class="packages-hero-card bolinao-card">
        <div class="packages-hero-copy bolinao-copy">
            <span class="visual-tag">Explore Bolinao&apos;s Natural Beauty</span>
            <h1 class="visual-headline">Find your next tour in Bolinao</h1>
            <p class="visual-copy">
                Discover curated packages, unique experiences, and local adventures across Bolinao.
            </p>
            <div class="packages-hero-cta">Explore tours below</div>
        </div>
    </div>
</section>

<section class="packages-listing">
    <div class="packages-container">
        <aside class="packages-sidebar">
            <h3>Search</h3>
            <form action="{{ route('packages.index') }}" method="GET">
                <div style="display:flex;flex-direction:column;gap:0.6rem;">
                    <label for="search">Search tours</label>
                    <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search tours by name, location, or experience" class="form-control" />

                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control">
                        <option value="">All categories</option>
                        @foreach($categoryMap as $key => $cat)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                        @endforeach
                    </select>

                    <div class="search-cta">
                        <button type="submit" class="btn" style="width:100%;">Search</button>
                    </div>
                </div>
            </form>

            <hr style="margin:1rem 0; border:none; border-top:1px solid rgba(6,20,12,0.04)">
            <h3>Advanced Search</h3>
            <div style="font-size:0.9rem;color:#335a45">

                <div style="margin-top:0.6rem">Duration</div>
                <label><input type="checkbox" name="dur_all" checked> All</label>
                <label><input type="checkbox" name="dur_1"> 1 Day</label>
                <label><input type="checkbox" name="dur_2"> 2-4 Days</label>
            </div>
        </aside>

        <main class="packages-main">
            <div class="packages-header-row">
                <div>
                    <h2 class="section-title">Browse Tour Packages</h2>
                    <p class="section-copy">Choose from curated Bolinao packages designed for couples, families, and small groups.</p>
                </div>
            </div>

            @if($packages->isEmpty())
                <div class="card text-center text-muted py-5">No packages found.</div>
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

                <div class="package-pagination" style="margin-top:1rem">
                    {{ $packages->links() }}
                </div>
            @endif
        </main>
    </div>
    </section>

</x-layout>

