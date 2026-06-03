<x-layout>
    <div class="section">
        <h1 class="title">Welcome back, {{ $user->name ?? 'Traveler' }}!</h1>
        <p class="lead">Quick access to your bookings, recommendations, and upcoming trips.</p>

        <div class="actions">
            <a href="{{ route('packages.index') }}" class="navbtn">Browse Packages</a>
            <a href="{{ route('reservations.index') }}" class="navbtn">My Reservations</a>
            <a href="{{ route('packages.index') }}" class="navbtn">Recommended Tours</a>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <p>Available Packages</p>
            <strong class="value">{{ $stats['packages'] }}</strong>
        </div>
        <div class="card">
            <p>Total Bookings</p>
            <strong class="value">{{ $stats['bookings'] }}</strong>
        </div>
        <div class="card">
            <p>Pending Trips</p>
            <strong class="value">{{ $stats['pending_bookings'] }}</strong>
        </div>
        <div class="card">
            <p>Paid Bookings</p>
            <strong class="value">{{ $stats['paid_payments'] }}</strong>
        </div>
    </div>

    <div class="grid2">
        <div class="card">
            <h2 class="section-title">Upcoming Trips</h2>
            <p class="lead">See your next reservations and manage them from one place.</p>

            @if($recentBookings->isEmpty())
                <p class="muted">You have no upcoming bookings yet. Start exploring tours to book your next adventure.</p>
            @else
                <div class="stack">
                    @foreach($recentBookings->take(4) as $booking)
                        <div class="card card-sm">
                            <p class="text-sm text-secondary">{{ $booking->package->name }}</p>
                            <p><strong>{{ $booking->tour_date->format('F j, Y') }}</strong> · {{ $booking->num_guests }} guest{{ $booking->num_guests > 1 ? 's' : '' }}</p>
                            <p>Status: <span class="badge badge-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'approved' ? 'success' : 'danger') }}">{{ ucfirst($booking->status) }}</span></p>
                        </div>
                    @endforeach
                </div>
                <div class="actions" style="margin-top: 1rem;">
                    <a href="{{ route('reservations.index') }}" class="navbtn">View all reservations</a>
                </div>
            @endif
        </div>

        <div class="card">
            <h2 class="section-title">Suggested Tours</h2>
            <p class="lead">Top packages picked for you based on availability and ratings.</p>

            @if($availablePackages->isEmpty())
                <p class="muted">There are no active tours available right now. Please check again later.</p>
            @else
                <div class="stack">
                    @foreach($availablePackages->take(4) as $package)
                        <div class="card card-sm">
                            <p class="text-sm text-secondary">{{ $package->location }}</p>
                            <p><strong>{{ $package->name }}</strong></p>
                            <p>PHP {{ number_format((float) $package->price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="actions" style="margin-top: 1rem;">
                    <a href="{{ route('packages.index') }}" class="navbtn">Browse all tours</a>
                </div>
            @endif
        </div>
    </div>
</x-layout>
