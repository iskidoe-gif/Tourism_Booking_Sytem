<x-layout>
    <div class="section">
        <h1 class="title">Packages</h1>
        <p class="lead">Browse active tour packages and book one from here.</p>
    </div>

    <div class="grid2">
        @forelse($availablePackages as $package)
            <div class="card package">
                <div class="package-head">
                    <div>
                        <h2 class="subtitle">{{ $package->title }}</h2>
                        <p class="lead">{{ $package->destination }}</p>
                    </div>
                    <strong class="price">PHP {{ number_format((float) $package->price, 2) }}</strong>
                </div>

                <p class="text">{{ $package->description }}</p>
                <div class="meta">
                    <span>Duration: {{ $package->duration_days }} days</span>
                    <span>Max guests: {{ $package->max_guests }}</span>
                </div>

                <form class="form" method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="tour_package_id" value="{{ $package->id }}">
                    <div class="field">
                        <label class="label">Booking date</label>
                        <input type="date" name="booking_date" value="{{ old('booking_date', now()->toDateString()) }}" class="input">
                    </div>
                    <div class="field">
                        <label class="label">Guests</label>
                        <input type="number" min="1" name="guests" value="{{ old('guests', 1) }}" class="input">
                    </div>
                    <div class="field">
                        <label class="label">Notes</label>
                        <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                    </div>
                    <button class="primary">Book this tour</button>
                </form>
            </div>
        @empty
            <div class="card empty">No active packages are available.</div>
        @endforelse
    </div>
</x-layout>
