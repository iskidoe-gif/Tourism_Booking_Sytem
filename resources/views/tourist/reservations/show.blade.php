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
                            <dt class="col-sm-5 text-muted">Tour Start Date</dt>
                            <dd class="col-sm-7">{{ optional($booking->tour_start_date ?: $booking->tour_date)->format('M d, Y') }}</dd>
                            <dt class="col-sm-5 text-muted">Tour End Date</dt>
                            <dd class="col-sm-7">{{ optional($booking->tour_end_date)->format('M d, Y') ?: 'N/A' }}</dd>
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
                            <dt class="col-sm-5 text-muted">Tour Start</dt>
                            <dd class="col-sm-7">{{ $booking->tour_started_at ? $booking->tour_started_at->format('M d, Y h:i A') : 'Not started yet' }}</dd>
                            <dt class="col-sm-5 text-muted">Tour End</dt>
                            <dd class="col-sm-7">{{ $booking->tour_ended_at ? $booking->tour_ended_at->format('M d, Y h:i A') : 'Not ended yet' }}</dd>
                        </dl>
                    </div>
                </div>

                @if($booking->cancellation_reason)
                    <div class="card {{ $booking->isCancellationPending() ? 'border-warning' : 'border-danger' }} mb-4">
                        <div class="card-header bg-white fw-semibold {{ $booking->isCancellationPending() ? 'text-warning' : 'text-danger' }}">
                            {{ $booking->isCancellationPending() ? 'Cancellation Request Pending' : 'Cancellation Details' }}
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Reason</strong></p>
                            <p class="mb-2">{{ $booking->cancellation_reason }}</p>
                            @if($booking->isCancelled())
                                <p class="mb-0"><strong>Refund</strong> ₱{{ number_format($booking->refund_amount ?? 0, 2) }}</p>
                            @elseif($booking->isCancellationPending())
                                <p class="mb-0 text-muted"><em>Your cancellation request is awaiting admin approval.</em></p>
                            @endif
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
                        <button type="submit" class="btn btn-success">Start Tour</button>
                    </form>
                @endif

                @if($booking->canCheckOut())
                    <form action="{{ route('reservations.check-out', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">End Tour</button>
                    </form>
                @endif

                @if($booking->canBeCancelled())
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Reservation</button>
                @elseif($booking->isCancellationPending())
                    <button type="button" class="btn btn-warning" disabled>Cancellation Pending</button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">Cancel Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reservations.cancel', $booking) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p class="mb-3">Please provide a reason for cancelling this reservation:</p>
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Reason for cancellation <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required placeholder="Please explain why you need to cancel this reservation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-layout>
