<x-layout>
    <div class="section">
        <h1 class="title">Reservations</h1>
        <p class="lead">See your bookings and their current status.</p>
    </div>

    <div class="tablewrap">
        <table class="table">
            <thead>
                <tr>
                    <th class="th">Code</th>
                    <th class="th">Package</th>
                    <th class="th">Date</th>
                    <th class="th">Guests</th>
                    <th class="th">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                    <tr>
                        <td class="td">{{ $booking->booking_code }}</td>
                        <td class="td">{{ $booking->package?->title }}</td>
                        <td class="td">{{ $booking->booking_date?->format('Y-m-d') }}</td>
                        <td class="td">{{ $booking->guests }}</td>
                        <td class="td">{{ $booking->status }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="td empty">No reservations yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layout>
