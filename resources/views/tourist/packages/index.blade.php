<x-layout title="Tour Packages">

<section id="packages">
    <div class="section-title mb-3">Browse Tour Packages</div>

    @if($packages->isEmpty())
        <div class="card text-center text-muted py-5">No packages found.</div>
    @else
        <div class="row g-3">
            @foreach($packages->take(3) as $package)
                <div class="col-md-4">
                    <div class="card h-100 overflow-hidden">
                        <div class="package-art package-art-{{ $loop->index % 3 }}">
                            @if($loop->index % 3 === 0)
                                &#127946;
                            @elseif($loop->index % 3 === 1)
                                &#127970;
                            @else
                                &#9968;
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-1">{{ $package->name }}</h6>
                            <div class="text-muted small mb-2">
                                &#128336; {{ $package->duration_days }} {{ Str::plural('day', $package->duration_days) }}
                                &nbsp;&middot;&nbsp;
                                &#128101; Max {{ $package->max_guests }}
                            </div>
                            <div class="text-warning small lh-1 mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    {!! $i <= round($package->rating) ? '&#9733;' : '&#9734;' !!}
                                @endfor
                            </div>
                            <div class="mb-3">
                                <span class="price-text">&#8369;{{ number_format($package->price) }}</span>
                                <span class="small">/ person</span>
                            </div>
                            <a href="{{ route('packages.show', $package) }}" class="btn btn-primary w-100">
                                View &amp; book
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

<hr class="my-4">

<section id="book-tour">
    <div class="section-title mb-3">Book A Tour</div>

    @if($packages->isEmpty())
        <div class="card text-center text-muted py-5">Add tour packages before accepting bookings.</div>
    @else
        @php($selectedPackage = $packages->first())
        <div class="card">
            <div class="card-body">
                <form id="quick-booking-form" method="POST" action="{{ route('bookings.store', $selectedPackage) }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tour package</label>
                            <select id="tour-package-select" class="form-select" data-action-template="{{ url('/book') }}/__PACKAGE_ID__">
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}"
                                            data-price="{{ $package->price }}"
                                            data-max-guests="{{ $package->max_guests }}">
                                        {{ $package->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Number of guests</label>
                            <input type="number" name="num_guests" id="num-guests"
                                   class="form-control @error('num_guests') is-invalid @enderror"
                                   value="{{ old('num_guests', 2) }}" min="1" max="{{ $selectedPackage->max_guests }}">
                            @error('num_guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tour date</label>
                            <input type="date" name="tour_date"
                                   class="form-control @error('tour_date') is-invalid @enderror"
                                   value="{{ old('tour_date') }}"
                                   min="{{ now()->addDay()->format('Y-m-d') }}">
                            @error('tour_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Special requests</label>
                            <input type="text" name="special_requests"
                                   class="form-control @error('special_requests') is-invalid @enderror"
                                   value="{{ old('special_requests') }}"
                                   placeholder="e.g. vegetarian meals">
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <span class="small">Total: </span>
                            <strong id="total-display">&#8369;{{ number_format($selectedPackage->price * old('num_guests', 2)) }}</strong>
                            <span class="small" id="guest-display">({{ old('num_guests', 2) }} guests)</span>
                        </div>

                        <div class="col-md-6 text-md-end">
                            <button type="submit" class="btn btn-primary px-5">Confirm booking</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</section>

<script>
    const bookingForm = document.getElementById('quick-booking-form');
    const packageSelect = document.getElementById('tour-package-select');
    const guestsInput = document.getElementById('num-guests');
    const totalDisplay = document.getElementById('total-display');
    const guestDisplay = document.getElementById('guest-display');

    function updateBookingSummary() {
        const selected = packageSelect.options[packageSelect.selectedIndex];
        const price = Number(selected.dataset.price || 0);
        const maxGuests = Number(selected.dataset.maxGuests || 1);
        const guests = Math.max(1, Number(guestsInput.value || 1));

        guestsInput.max = maxGuests;
        if (guests > maxGuests) {
            guestsInput.value = maxGuests;
        }

        const currentGuests = Number(guestsInput.value || 1);
        totalDisplay.textContent = '\u20b1' + (price * currentGuests).toLocaleString('en-PH');
        guestDisplay.textContent = '(' + currentGuests + (currentGuests === 1 ? ' guest)' : ' guests)');
        bookingForm.action = packageSelect.dataset.actionTemplate.replace('__PACKAGE_ID__', selected.value);
    }

    if (bookingForm && packageSelect && guestsInput) {
        packageSelect.addEventListener('change', updateBookingSummary);
        guestsInput.addEventListener('input', updateBookingSummary);
        updateBookingSummary();
    }
</script>

</x-layout>
