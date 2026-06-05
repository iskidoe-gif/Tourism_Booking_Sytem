<x-layout title="Book a Tour">

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="fw-semibold mb-3">{{ $tourPackage->name }}</h4>
                        <p class="text-muted small mb-2">
                            &#128205; {{ $tourPackage->location }} &nbsp;&bull;&nbsp;
                            {{ $tourPackage->duration_days }} day(s) &nbsp;&bull;&nbsp;
                            Up to {{ $tourPackage->max_guests }} guests
                        </p>

                        <div class="mb-3">
                            <span class="badge bg-success">{{ $tourPackage->category_label }}</span>
                            <span class="badge bg-secondary">Real Tour Package</span>
                        </div>

                        <p>{{ $tourPackage->description }}</p>

                        <div class="row text-center mb-3">
                            <div class="col-4">
                                <div class="small text-muted">Price / person</div>
                                <div class="fw-semibold text-success">₱{{ number_format($tourPackage->price, 2) }}</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted">Duration</div>
                                <div class="fw-semibold">{{ $tourPackage->duration_days }} day(s)</div>
                            </div>
                            <div class="col-4">
                                <div class="small text-muted">Rating</div>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= round($tourPackage->rating) ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6>What this booking includes</h6>
                            <ul class="small mb-0">
                                <li>Guided sightseeing and local tour support</li>
                                <li>Pre-approved tour itinerary for easy planning</li>
                                <li>Dedicated booking confirmation and customer support</li>
                                <li>Secure checkout with booking reference</li>
                            </ul>
                        </div>

                        <div class="alert alert-info">
                            <strong>Booking note:</strong> Your reservation request is submitted immediately. A real tour operator will confirm availability and arrange the final invoice.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white fw-semibold">Traveler & Booking Information</div>
                    <div class="card-body">
                        @if(auth()->user()?->isGuest())
                            <div class="alert alert-warning">
                                Guest accounts can only browse tours. Create a tourist account or sign in to complete a booking.
                            </div>

                            <div class="d-flex gap-2 mb-4">
                                <a href="#" class="btn btn-primary flex-grow-1" data-auth-open data-auth-mode="register">Create Tourist Account</a>
                                <a href="#" class="btn btn-outline-secondary flex-grow-1" data-auth-open data-auth-mode="signin">Sign In</a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('bookings.store') }}">
                                @csrf
                                <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">

                                <div class="mb-3">
                                    <label class="form-label">Primary Contact</label>
                                    <input type="text" name="guest_name"
                                           class="form-control @error('guest_name') is-invalid @enderror"
                                           value="{{ old('guest_name', auth()->user()->name) }}"
                                           placeholder="Name of booking contact">
                                    @error('guest_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="guest_email"
                                               class="form-control @error('guest_email') is-invalid @enderror"
                                               value="{{ old('guest_email', auth()->user()->email) }}"
                                               placeholder="contact@example.com">
                                        @error('guest_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="guest_phone"
                                               class="form-control @error('guest_phone') is-invalid @enderror"
                                               value="{{ old('guest_phone', auth()->user()->phone ?? '') }}"
                                               placeholder="0917 123 4567">
                                        @error('guest_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Travel Date</label>
                                    <input type="date" name="tour_date"
                                           class="form-control @error('tour_date') is-invalid @enderror"
                                           value="{{ old('tour_date') }}"
                                           min="{{ now()->addDay()->format('Y-m-d') }}">
                                    @error('tour_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Travelers</label>
                                    <div class="row g-3">
                                        <div class="col-sm-4">
                                            <input type="number" name="num_adults" id="num_adults"
                                                   class="form-control @error('num_adults') is-invalid @enderror"
                                                   value="{{ old('num_adults', 1) }}"
                                                   min="0" max="{{ $tourPackage->max_guests }}"
                                                   placeholder="Adults">
                                            <div class="form-text">Adults</div>
                                            @error('num_adults')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="number" name="num_children" id="num_children"
                                                   class="form-control @error('num_children') is-invalid @enderror"
                                                   value="{{ old('num_children', 0) }}"
                                                   min="0" max="{{ $tourPackage->max_guests }}"
                                                   placeholder="Children">
                                            <div class="form-text">Children</div>
                                            @error('num_children')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="number" name="num_seniors" id="num_seniors"
                                                   class="form-control @error('num_seniors') is-invalid @enderror"
                                                   value="{{ old('num_seniors', 0) }}"
                                                   min="0" max="{{ $tourPackage->max_guests }}"
                                                   placeholder="Seniors">
                                            <div class="form-text">Seniors</div>
                                            @error('num_seniors')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Optional Add-ons</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input booking-service" type="checkbox"
                                               name="services[]" value="airport_transfer"
                                               id="airport_transfer" data-price="1200"
                                               {{ in_array('airport_transfer', old('services', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="airport_transfer">
                                            Airport transfer <span class="text-muted">(₱1,200)</span>
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input booking-service" type="checkbox"
                                               name="services[]" value="travel_insurance"
                                               id="travel_insurance" data-price="450"
                                               {{ in_array('travel_insurance', old('services', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="travel_insurance">
                                            Travel insurance <span class="text-muted">(₱450)</span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input booking-service" type="checkbox"
                                               name="services[]" value="meal_plan"
                                               id="meal_plan" data-price="650"
                                               {{ in_array('meal_plan', old('services', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="meal_plan">
                                            Meal plan <span class="text-muted">(₱650)</span>
                                        </label>
                                    </div>
                                </div>

                                @error('services')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                                <div class="mb-3">
                                    <label class="form-label">Special Requests <span class="text-muted">(optional)</span></label>
                                    <textarea name="special_requests" rows="3"
                                              class="form-control @error('special_requests') is-invalid @enderror"
                                              placeholder="e.g. vegetarian meals, wheelchair access">{{ old('special_requests') }}</textarea>
                                    @error('special_requests')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <input type="hidden" name="num_guests" id="num_guests" value="{{ old('num_guests', 1) }}">

                                <div class="card border-secondary mb-3">
                                    <div class="card-body p-3">
                                        <h6 class="fw-semibold mb-3">Booking Summary</h6>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Base rate</span>
                                            <strong>₱{{ number_format($tourPackage->price, 2) }} x <span id="guest-total">{{ old('num_adults', 1) + old('num_children', 0) + old('num_seniors', 0) }}</span></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Extras</span>
                                            <strong id="extras-total">₱0.00</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-semibold">
                                            <span>Total estimated</span>
                                            <strong id="total-display">₱{{ number_format($tourPackage->price, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1">Submit Reservation</button>
                                    <a href="{{ route('packages.show', $tourPackage) }}" class="btn btn-outline-secondary">Back</a>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const basePrice = {{ $tourPackage->price }};
    const maxGuests = {{ $tourPackage->max_guests }};
    const guestInputs = [
        document.getElementById('num_adults'),
        document.getElementById('num_children'),
        document.getElementById('num_seniors'),
    ];
    const serviceCheckboxes = document.querySelectorAll('.booking-service');
    const guestTotalDisplay = document.getElementById('guest-total');
    const extrasTotalDisplay = document.getElementById('extras-total');
    const totalDisplay = document.getElementById('total-display');
    const hiddenGuestTotal = document.getElementById('num_guests');

    const formatMoney = (value) => {
        return '₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    const calculateTotals = () => {
        const adults = parseInt(guestInputs[0].value) || 0;
        const children = parseInt(guestInputs[1].value) || 0;
        const seniors = parseInt(guestInputs[2].value) || 0;
        const guests = Math.max(0, adults + children + seniors);

        guestTotalDisplay.textContent = guests;
        hiddenGuestTotal.value = guests;

        let serviceTotal = 0;
        serviceCheckboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                serviceTotal += parseFloat(checkbox.dataset.price) || 0;
            }
        });

        const subtotal = guests * basePrice;
        const total = subtotal + serviceTotal;

        extrasTotalDisplay.textContent = formatMoney(serviceTotal);
        totalDisplay.textContent = formatMoney(total);

        guestInputs.forEach((input) => {
            if (parseInt(input.value) < 0) {
                input.value = 0;
            }
            if (parseInt(input.value) > maxGuests) {
                input.value = maxGuests;
            }
        });
    };

    guestInputs.forEach((input) => input.addEventListener('input', calculateTotals));
    serviceCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', calculateTotals));
    calculateTotals();
</script>

</x-layout>
