<x-layout>
    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="title">Tour Packages</h1>
                <p class="lead">Browse active tour packages and book the perfect tour.</p>
            </div>
            <form method="GET" action="{{ route('packages.index') }}" class="d-flex gap-2">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search destinations or titles"
                       class="form-control" style="min-width:280px">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>
    </div>

    @if($packages->isEmpty())
        <div class="card empty">No active packages found.</div>
    @else
        <div class="grid2">
            @foreach($packages as $package)
                <div class="card package">
                    <div class="package-image mb-3 overflow-hidden rounded" style="height:220px;">
                        @php
                            $imageUrl = $package->image
                                ? (str_starts_with($package->image, 'http') ? $package->image : asset($package->image))
                                : asset('images/package-default.svg');
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $package->name }}" class="img-fluid w-100 h-100" style="object-fit:cover;">
                    </div>

                    <div class="package-head d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="subtitle">{{ $package->name }}</h2>
                            <p class="lead mb-0">{{ $package->location }}</p>
                        </div>
                        <strong class="price text-success">PHP {{ number_format((float) $package->price, 2) }}</strong>
                    </div>

                    <p class="text text-truncate" style="max-height:3.6em;overflow:hidden;">{{ $package->description }}</p>

                    <div class="meta d-flex flex-wrap gap-2 mb-3 small text-muted">
                        <span>Duration: {{ $package->duration_days }} day{{ $package->duration_days === 1 ? '' : 's' }}</span>
                        <span>Max guests: {{ $package->max_guests }}</span>
                    </div>

                    <form class="form" method="POST" action="{{ route('bookings.store') }}">
                        @csrf
                        <input type="hidden" name="tour_package_id" value="{{ $package->id }}">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">Tour date</label>
                                <input type="date" name="tour_date" value="{{ old('tour_date', now()->toDateString()) }}" class="form-control">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Guests</label>
                                <input type="number" min="1" name="num_guests" value="{{ old('num_guests', 1) }}" max="{{ $package->max_guests }}" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Special requests</label>
                            <textarea name="special_requests" rows="2" class="form-control">{{ old('special_requests') }}</textarea>
                        </div>
                        <button class="btn btn-primary w-100">Book this tour</button>
                    </form>
                </div>
            @endforeach
        </div>

        @if($packages->hasPages())
            <div class="mt-4">{{ $packages->links() }}</div>
        @endif
    @endif
</x-layout>
