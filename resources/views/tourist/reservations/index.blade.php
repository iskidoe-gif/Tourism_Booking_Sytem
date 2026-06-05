<x-layout title="My Reservations">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-semibold mb-0">My Reservations</h4>
    <a href="{{ route('packages.index') }}" class="btn btn-sm btn-primary">+ Book New Tour</a>
</div>

@if($bookings->isEmpty())
    <div class="card text-center py-5 text-muted">
        <p>You have no reservations yet.</p>
        <a href="{{ route('packages.index') }}" class="btn btn-primary btn-sm mx-auto" style="width:fit-content">
            Browse Packages
        </a>
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Booking #</th>
                        <th>Package</th>
                        <th>Tour Date</th>
                        <th>Guests</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td class="font-monospace text-muted">#{{ $booking->booking_number }}</td>
                        <td>{{ $booking->tourPackage->name }}</td>
                        <td>{{ $booking->tour_date->format('M d, Y') }}</td>
                        <td>{{ $booking->num_guests }}</td>
                        <td class="text-success fw-semibold">₱{{ number_format($booking->total_price, 2) }}</td>
                        <td>
                            <span class="badge rounded-pill text-white" style="background-color: {{ $booking->status_color }};">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('reservations.show', $booking) }}"
                               class="btn btn-sm btn-outline-secondary">View</a>
                            @if($booking->status === 'pending')
                                <form method="POST" action="{{ route('reservations.cancel', $booking) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Cancel this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $bookings->links() }}</div>
@endif

</x-layout>
