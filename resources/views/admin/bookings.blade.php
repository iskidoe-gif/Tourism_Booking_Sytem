<x-layout>
    <div class="section">
        <h1 class="title">Booking Requests</h1>
        <p class="lead">Manage all tourist booking requests. Approve or decline bookings here.</p>
    </div>

    <div class="card">
        <div class="stack">
            @php
                $statusLabels = [
                    'pending' => 'Pending approval',
                    'approved' => 'Confirmed',
                    'cancelled' => 'Declined',
                    'completed' => 'Completed',
                ];
                $statusClasses = [
                    'pending' => 'status-pending',
                    'approved' => 'status-approved',
                    'cancelled' => 'status-declined',
                    'completed' => 'status-completed',
                ];
            @endphp

            @forelse($bookings as $booking)
                <div class="mini booking-row">
                    <div class="booking-info">
                        <div>
                            <strong>{{ $booking->booking_number }}</strong>
                            <p class="lead">Tourist: {{ $booking->user?->name ?? 'Guest' }}</p>
                            <p class="lead">{{ $booking->package?->name }} | Tour Date: {{ $booking->tour_date?->format('Y-m-d') }}</p>
                            <p class="lead">
                                📅 Tour start: {{ $booking->tour_start_date?->format('M d, Y') ?? 'Not set' }} 
                                | Tour end: {{ $booking->tour_end_date?->format('M d, Y') ?? 'Not set' }}
                            </p>
                            <p class="lead">Guests: {{ $booking->num_guests }} | Price: PHP {{ number_format((float) $booking->total_price, 2) }}</p>
                            @if($booking->special_requests)
                                <p class="lead"><strong>Requests:</strong> {{ $booking->special_requests }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="booking-meta">
                        <span class="status {{ $statusClasses[$booking->status] ?? '' }}">
                            {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </span>

                        @if($booking->status === 'pending')
                            <div class="booking-actions">
                                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-primary">Confirm</button>
                                </form>

                                <form method="POST" action="{{ route('admin.bookings.status', $booking) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-outline-secondary">Decline</button>
                                </form>
                            </div>
                        @elseif($booking->status === 'approved')
                            <div class="booking-approved-info">
                                <p class="lead">Approved by {{ $booking->approver?->name ?? 'Admin' }}</p>
                                <p class="lead">{{ $booking->approved_at?->format('Y-m-d H:i') }}</p>
                            </div>
                        @elseif($booking->status === 'cancelled')
                            <div class="booking-declined-info">
                                <p class="lead">Declined by {{ $booking->approver?->name ?? 'Admin' }}</p>
                                <p class="lead">{{ $booking->approved_at?->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="lead">No bookings found.</p>
            @endforelse

            @if($bookings->hasPages())
                <div class="pagination-wrapper">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
