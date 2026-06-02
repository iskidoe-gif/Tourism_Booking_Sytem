<x-layout title="Reservation Details">

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-semibold mb-0">Reservation #{{ $booking->booking_number }}</h4>
            <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-semibold">{{ $booking->tourPackage->name }}</h5>
                        <p class="text-muted mb-0">&#128205; {{ $booking->tourPackage->location }}</p>
                    </div>
                    <span class="badge badge-{{ $booking->status }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-white fw-semibold">Booking Information</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Tour Date</dt>
                    <dd class="col-sm-8">{{ $booking->tour_date->format('M d, Y') }}</dd>
                    <dt class="col-sm-4 text-muted">Guests</dt>
                    <dd class="col-sm-8">{{ $booking->num_guests }}</dd>
                    <dt class="col-sm-4 text-muted">Total Price</dt>
                    <dd class="col-sm-8 fw-semibold text-success">₱{{ number_format($booking->total_price, 2) }}</dd>
                    <dt class="col-sm-4 text-muted">Special Requests</dt>
                    <dd class="col-sm-8">{{ $booking->special_requests ?: 'None' }}</dd>
                </dl>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Payment</div>
            <div class="card-body">
                @if($booking->payment)
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Method</dt>
                        <dd class="col-sm-8">{{ ucwords(str_replace('_', ' ', $booking->payment->method)) }}</dd>
                        <dt class="col-sm-4 text-muted">Status</dt>
                        <dd class="col-sm-8">{{ ucfirst($booking->payment->status) }}</dd>
                        <dt class="col-sm-4 text-muted">Reference</dt>
                        <dd class="col-sm-8">{{ $booking->payment->reference_number ?: 'N/A' }}</dd>
                    </dl>
                @else
                    <p class="text-muted mb-0">No payment record yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

</x-layout>
