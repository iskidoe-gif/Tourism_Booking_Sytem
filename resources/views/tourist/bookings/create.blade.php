<x-layout title="Book a Tour">

<section class="package-detail-page">
    <div class="package-detail-hero">
        <div class="package-detail-grid">
            <div class="package-detail-media">
                @if($tourPackage->image)
                    <img src="{{ $tourPackage->image_url }}" alt="{{ $tourPackage->name }}">
                @else
                    <div class="package-detail-placeholder" aria-hidden="true">Bolinao</div>
                @endif
                <span class="package-detail-badge">{{ $tourPackage->category_label }}</span>
            </div>

            <aside class="package-detail-summary">
                <p class="package-detail-kicker">Reserve your tour</p>
                <h1>{{ $tourPackage->name }}</h1>

                <div class="package-detail-rating" aria-label="Rated {{ number_format($tourPackage->rating, 1) }} out of 5">
                    @for($i = 1; $i <= 5; $i++)
                        <span>{!! $i <= round($tourPackage->rating) ? '&#9733;' : '&#9734;' !!}</span>
                    @endfor
                    <strong>{{ number_format($tourPackage->rating, 1) }}</strong>
                </div>

                <p class="package-detail-location">
                    {{ $tourPackage->location }} &nbsp;&bull;&nbsp; {{ $tourPackage->duration_days }} day{{ $tourPackage->duration_days === 1 ? '' : 's' }} &nbsp;&bull;&nbsp; Up to {{ $tourPackage->max_guests }} guests
                </p>

                <p class="package-detail-description">{{ $tourPackage->description }}</p>

                <div class="package-detail-price">
                    <span>Price per person</span>
                    <strong>₱{{ number_format($tourPackage->price, 2) }}</strong>
                </div>

                <div class="package-detail-actions">
                    <a href="{{ route('packages.show', $tourPackage) }}" class="package-detail-primary">View package details</a>
                    <p>Reservation requests are reviewed by the tour operator and confirmed shortly after submission.</p>
                </div>
            </aside>
        </div>
    </div>

    <div class="row gx-4 gy-4 package-detail-content">
        <div class="col-lg-7">
            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Traveler information</p>
                    <h2>Complete your reservation</h2>
                </div>

                @if(auth()->user()?->isGuest())
                    <div class="alert alert-warning">
                        Guest accounts can only browse tours. Create a tourist account or sign in to complete a booking.
                    </div>

                    <div class="d-flex gap-2 mb-4 flex-wrap">
                        <a href="#" class="package-detail-primary flex-grow-1" data-auth-open data-auth-mode="register">Create Tourist Account</a>
                        <a href="#" class="package-detail-primary flex-grow-1" data-auth-open data-auth-mode="signin">Sign In</a>
                    </div>
                @else
                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf
                        <input type="hidden" name="tour_package_id" value="{{ $tourPackage->id }}">

                        <div class="mb-3">
                            <label class="form-label">Primary contact</label>
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

                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label">Tour start</label>
                                <input type="date" name="tour_start_date"
                                       class="form-control @error('tour_start_date') is-invalid @enderror"
                                       value="{{ old('tour_start_date') }}"
                                       min="{{ now()->format('Y-m-d') }}">
                                @error('tour_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Tour end</label>
                                <input type="date" name="tour_end_date"
                                       id="tour_end_date"
                                       class="form-control @error('tour_end_date') is-invalid @enderror"
                                       value="{{ old('tour_end_date', \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d')) }}"
                                       min="{{ \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::parse(old('tour_start_date', now()))->addDays($tourPackage->duration_days)->format('Y-m-d') }}">
                                <div class="form-text text-muted">This package is {{ $tourPackage->duration_days }} day(s); tour end is fixed to match the duration.</div>
                                @error('tour_end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
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
                            <label class="form-label">Optional add-ons</label>
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
                            <label class="form-label">Special requests <span class="text-muted">(optional)</span></label>
                            <textarea name="special_requests" rows="3"
                                      class="form-control @error('special_requests') is-invalid @enderror"
                                      placeholder="e.g. vegetarian meals, wheelchair access">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <input type="hidden" name="num_guests" id="num_guests" value="{{ old('num_guests', 1) }}">

                        <div class="d-flex flex-column gap-3">
                            <button type="submit" class="package-detail-primary">Submit Reservation</button>
                            <a href="{{ route('packages.show', $tourPackage) }}" class="text-muted" style="text-decoration: underline;">Back to package</a>
                        </div>
                    </form>
                @endif
            </section>
        </div>

        <div class="col-lg-5">
            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Package details</p>
                    <h2>Your tour summary</h2>
                </div>

                <div class="package-detail-stats">
                    <div>
                        <span>Destination</span>
                        <strong>{{ $tourPackage->destination?->name ?? 'Bolinao' }}</strong>
                    </div>
                    <div>
                        <span>Duration</span>
                        <strong>{{ $tourPackage->duration_days }} day{{ $tourPackage->duration_days === 1 ? '' : 's' }}</strong>
                    </div>
                    <div>
                        <span>Max guests</span>
                        <strong>{{ $tourPackage->max_guests }}</strong>
                    </div>
                    <div>
                        <span>Category</span>
                        <strong>{{ $tourPackage->category_label }}</strong>
                    </div>
                </div>

                <div class="mt-4">
                    <h6 class="mb-2">What’s included</h6>
                    <ul class="small mb-0 text-muted">
                        <li>Guided sightseeing and local tour support</li>
                        <li>Pre-approved itinerary for easy planning</li>
                        <li>Booking confirmation and customer support</li>
                        <li>Secure checkout with reservation tracking</li>
                    </ul>
                </div>
            </section>

            <section class="package-detail-panel">
                <div class="package-detail-section-heading">
                    <p>Estimated cost</p>
                    <h2>Booking total</h2>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span>Base rate</span>
                    <strong>₱{{ number_format($tourPackage->price, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Guests</span>
                    <strong id="guest-total">{{ old('num_adults', 1) + old('num_children', 0) + old('num_seniors', 0) }}</strong>
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

                <div class="alert alert-info mt-4 mb-0">
                    <strong>Note:</strong> This is an estimate. Final charges may vary after tour operator confirmation.
                </div>
            </section>
        </div>
    </div>
</section>

<script>
    const basePrice = {{ $tourPackage->price }};
    const maxGuests = {{ $tourPackage->max_guests }};
    const bookingDurationDays = {{ $tourPackage->duration_days }};
    const checkInInput = document.querySelector('input[name="tour_start_date"]');
    const checkOutInput = document.getElementById('tour_end_date');
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

    const updateCheckOutConstraints = () => {
        if (!checkInInput || !checkOutInput) {
            return;
        }

        const checkInDate = checkInInput.value ? new Date(checkInInput.value) : null;
        if (!checkInDate) {
            return;
        }

        const expectedCheckOut = new Date(checkInDate);
        expectedCheckOut.setDate(expectedCheckOut.getDate() + bookingDurationDays);
        const formatted = expectedCheckOut.toISOString().slice(0, 10);

        checkOutInput.min = formatted;
        checkOutInput.max = formatted;
        if (checkOutInput.value !== formatted) {
            checkOutInput.value = formatted;
        }
    };

    guestInputs.forEach((input) => input.addEventListener('input', calculateTotals));
    serviceCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', calculateTotals));
    if (checkInInput) {
        checkInInput.addEventListener('change', updateCheckOutConstraints);
    }
    updateCheckOutConstraints();
    calculateTotals();
</script>

</x-layout>
