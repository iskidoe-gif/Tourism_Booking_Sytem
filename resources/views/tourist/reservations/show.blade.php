<x-layout title="Reservation Details">

<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-semibold mb-1">Reservation #{{ $booking->booking_number }}</h4>
                <p class="text-muted mb-0">{{ $booking->tourPackage->name }} &middot; {{ $booking->tourPackage->location }}</p>
            </div>
            <div class="text-end">
                <span class="badge rounded-pill" style="background-color: {{ $booking->status_color }}; color: #fff;">{{ $booking->status_label }}</span>
                <div class="text-muted small mt-1">{{ optional($booking->tour_date)->format('M d, Y') }}</div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <div class="small text-muted">Contact</div>
                        <div>{{ $booking->guest_details['contact_name'] ?? $booking->user->name }}</div>
                        <div class="text-muted">{{ $booking->guest_details['contact_email'] ?? $booking->user->email }}</div>
                        <div class="text-muted">{{ $booking->guest_details['contact_phone'] ?? 'Not provided' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Guests</div>
                        <div>{{ $booking->num_guests }} total</div>
                        <div class="text-muted">Adults: {{ $booking->num_adults ?? 0 }}</div>
                        <div class="text-muted">Children: {{ $booking->num_children ?? 0 }}, Seniors: {{ $booking->num_seniors ?? 0 }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="small text-muted">Reference</div>
                        <div>{{ $booking->reference_code ?? 'Pending' }}</div>
                        <div class="small text-muted mt-2">Confirmation</div>
                        <div>{{ $booking->confirmation_code ?? 'Pending' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header bg-white fw-semibold">Reservation Summary</div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-5 text-muted">Check-in Date</dt>
                            <dd class="col-sm-7">{{ optional($booking->check_in_date ?: $booking->tour_date)->format('M d, Y') }}</dd>
                            <dt class="col-sm-5 text-muted">Check-out Date</dt>
                            <dd class="col-sm-7">{{ optional($booking->check_out_date)->format('M d, Y') ?: 'N/A' }}</dd>
                            <dt class="col-sm-5 text-muted">Duration</dt>
                            <dd class="col-sm-7">{{ $booking->tourPackage->duration_days }} day(s)</dd>
                            <dt class="col-sm-5 text-muted">Base price</dt>
                            <dd class="col-sm-7">₱{{ number_format($booking->base_price ?? ($booking->tourPackage->price * $booking->num_guests), 2) }}</dd>
                            <dt class="col-sm-5 text-muted">Add-ons</dt>
                            <dd class="col-sm-7">
                                @if($booking->services && $booking->services->isNotEmpty())
                                    <ul class="mb-0 small">
                                        @foreach($booking->services as $service)
                                            <li>{{ $service['name'] }} (₱{{ number_format($service['price'], 2) }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No add-ons selected</span>
                                @endif
                            </dd>
                            <dt class="col-sm-5 text-muted">Special requests</dt>
                            <dd class="col-sm-7">{{ $booking->special_requests ?: 'None' }}</dd>
                            <dt class="col-sm-5 text-muted">Tour operator status</dt>
                            <dd class="col-sm-7">{{ ucfirst($booking->status) }}</dd>
                            <dt class="col-sm-5 text-muted">Check-in</dt>
                            <dd class="col-sm-7">{{ $booking->check_in_at ? $booking->check_in_at->format('M d, Y h:i A') : 'Not checked in yet' }}</dd>
                            <dt class="col-sm-5 text-muted">Check-out</dt>
                            <dd class="col-sm-7">{{ $booking->check_out_at ? $booking->check_out_at->format('M d, Y h:i A') : 'Not checked out yet' }}</dd>
                        </dl>
                    </div>
                </div>

                @if($booking->cancellation_reason)
                    <div class="card border-danger mb-4">
                        <div class="card-header bg-white fw-semibold text-danger">Cancellation Details</div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Reason</strong></p>
                            <p class="mb-2">{{ $booking->cancellation_reason }}</p>
                            <p class="mb-0"><strong>Refund</strong> ₱{{ number_format($booking->refund_amount ?? 0, 2) }}</p>
                        </div>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header bg-white fw-semibold">Booking Timeline</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3">
                                <strong>Request submitted</strong>
                                <div class="text-muted">Your request was received and is awaiting operator verification.</div>
                            </li>
                            <li class="mb-3">
                                <strong>Confirmation pending</strong>
                                <div class="text-muted">A local operator will confirm availability and payment details.</div>
                            </li>
                            <li>
                                <strong>Final payment</strong>
                                <div class="text-muted">Once confirmed, you will receive a payment invoice with a booking reference.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header bg-white fw-semibold">Price Breakdown</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Package rate</span>
                            <span>₱{{ number_format($booking->tourPackage->price, 2) }} x {{ $booking->num_guests }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <strong>₱{{ number_format($booking->base_price ?? ($booking->tourPackage->price * $booking->num_guests), 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Add-ons</span>
                            <strong>₱{{ number_format($booking->additional_fees ?? 0, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold mb-3">
                            <span>Total</span>
                            <strong>₱{{ number_format($booking->total_price, 2) }}</strong>
                        </div>
                        <div class="text-muted small">
                            Your reservation is pending confirmation from the tour operator. Final invoice and payment instructions will be sent once approved.
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white fw-semibold">Payment Record</div>
                    <div class="card-body">
                        @if($booking->payment)
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted">Method</dt>
                                <dd class="col-7">{{ ucwords(str_replace('_', ' ', $booking->payment->method)) }}</dd>
                                <dt class="col-5 text-muted">Status</dt>
                                <dd class="col-7">{{ ucfirst($booking->payment->status) }}</dd>
                                <dt class="col-5 text-muted">Amount</dt>
                                <dd class="col-7">₱{{ number_format($booking->payment->amount, 2) }}</dd>
                                <dt class="col-5 text-muted">Reference</dt>
                                <dd class="col-7">{{ $booking->payment->reference_number ?: 'N/A' }}</dd>
                            </dl>
                        @else
                            <p class="text-muted mb-0">No payment recorded yet. The operator will send payment instructions after confirmation.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
            <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Back to reservations</a>

            <div class="d-flex flex-wrap gap-2">
                @if($booking->canCheckIn())
                    <form action="{{ route('reservations.check-in', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Check In</button>
                    </form>
                @endif

                @if($booking->canCheckOut())
                    <form action="{{ route('reservations.check-out', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Check Out</button>
                    </form>
                @endif

                @if($booking->canBeCancelled())
                    <form action="{{ route('reservations.cancel', $booking) }}" method="POST" onsubmit="return confirm('Confirm cancellation of this reservation?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Cancel Reservation</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

</x-layout>
