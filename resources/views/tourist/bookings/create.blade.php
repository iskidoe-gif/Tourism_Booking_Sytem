<x-layout title="Book a Tour">

<div class="row justify-content-center">
    <div class="col-md-7">

        <div class="card mb-3">
            <div class="card-body">
                <h5 class="fw-semibold">{{ $tourPackage->name }}</h5>
                <p class="text-muted small mb-1">
                    &#128205; {{ $tourPackage->location }} &nbsp;&bull;&nbsp;
                    {{ $tourPackage->duration_days }} day(s) &nbsp;&bull;&nbsp;
                    Max {{ $tourPackage->max_guests }} guests
                </p>
                <p class="fw-semibold text-success mb-0">
                    ₱{{ number_format($tourPackage->price, 2) }} / person
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white fw-semibold">Booking Details</div>
            <div class="card-body">
                @if(auth()->user()?->email === 'guest@example.com')
                    <div class="alert alert-warning">
                        Guest accounts cannot confirm bookings. Please register a full account or log in with your own details before continuing.
                    </div>
                @endif

                <form method="POST" action="{{ route('bookings.store') }}">
                    @csrf
                    <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">

                    <div class="mb-3">
                        <label class="form-label">Tour Date</label>
                        <input type="date" name="tour_date"
                               class="form-control @error('tour_date') is-invalid @enderror"
                               value="{{ old('tour_date') }}"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                        @error('tour_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Number of Guests</label>
                        <input type="number" name="num_guests" id="num_guests"
                               class="form-control @error('num_guests') is-invalid @enderror"
                               value="{{ old('num_guests', 1) }}"
                               min="1" max="{{ $tourPackage->max_guests }}">
                        @error('num_guests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Special Requests <span class="text-muted">(optional)</span></label>
                        <textarea name="special_requests" rows="3"
                                  class="form-control @error('special_requests') is-invalid @enderror"
                                  placeholder="e.g. vegetarian meals, wheelchair access">{{ old('special_requests') }}</textarea>
                        @error('special_requests')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-light border d-flex justify-content-between">
                        <span>Estimated Total</span>
                        <strong class="text-success" id="total-display">
                            ₱{{ number_format($tourPackage->price, 2) }}
                        </strong>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">Confirm Booking</button>
                        <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    const price = {{ $tourPackage->price }};
    const guestsInput = document.getElementById('num_guests');
    const totalDisplay = document.getElementById('total-display');

    guestsInput.addEventListener('input', function () {
        const guests = parseInt(this.value) || 1;
        const total = price * guests;
        totalDisplay.textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});
    });
</script>

</x-layout>
