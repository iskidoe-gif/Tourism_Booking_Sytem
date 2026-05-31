<x-layout>
    <div class="section">
        <h1 class="title">Tourist Dashboard</h1>
        <p class="lead">Use the links below to browse packages or check your reservations.</p>

        <div class="actions">
            <a href="{{ route('packages.index') }}" class="navbtn">Packages</a>
            <a href="{{ route('reservations.index') }}" class="navbtn">Reservations</a>
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
</x-layout>
