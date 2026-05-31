<x-layout>
    <div class="section">
        <h1 class="title">Admin Dashboard</h1>
        <p class="lead">Manage bookings, review packages, and open reports from one place.</p>

        <div class="actions">
            <a href="{{ route('admin.reports.bookings', 'csv') }}" class="navbtn">CSV Report</a>
            <a href="{{ route('admin.reports.bookings', 'xlsx') }}" class="navbtn">XLSX Report</a>
            <a href="{{ route('admin.reports.bookings', 'pdf') }}" class="navbtn">PDF Report</a>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <p>Packages</p>
            <strong class="value">{{ $stats['packages'] }}</strong>
        </div>
        <div class="card">
            <p>Bookings</p>
            <strong class="value">{{ $stats['bookings'] }}</strong>
        </div>
        <div class="card">
            <p>Pending</p>
            <strong class="value">{{ $stats['pending_bookings'] }}</strong>
        </div>
        <div class="card">
            <p>Revenue</p>
            <strong class="value">PHP {{ number_format((float) $stats['revenue'], 2) }}</strong>
        </div>
    </div>

    <div class="split">
        <div class="card">
            <h2 class="subtitle">Active packages</h2>
            <div class="stack">
                @forelse($availablePackages as $package)
                    <div class="mini">
                        <div>
                            <strong>{{ $package->title }}</strong>
                            <p class="lead">{{ $package->destination }}</p>
                        </div>
                        <span class="price">PHP {{ number_format((float) $package->price, 2) }}</span>
                    </div>
                @empty
                    <p class="lead">No active packages found.</p>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h2 class="subtitle">Recent bookings</h2>
            <div class="stack">
                @forelse($recentBookings as $booking)
                    <div class="mini">
                        <div>
                            <strong>{{ $booking->booking_code }}</strong>
                            <p class="lead">{{ $booking->package?->title }} | {{ $booking->booking_date?->format('Y-m-d') }}</p>
                        </div>
                        <span class="status">{{ $booking->status }}</span>
                    </div>
                @empty
                    <p class="lead">No bookings yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>
