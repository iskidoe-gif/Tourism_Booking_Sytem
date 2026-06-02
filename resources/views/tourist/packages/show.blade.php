<x-layout :title="$tourPackage->name">

<div class="row g-4">
    <div class="col-md-7">
        @if($tourPackage->image)
            @php($imageUrl = str_starts_with($tourPackage->image, 'http') ? $tourPackage->image : asset($tourPackage->image))
            <img src="{{ $imageUrl }}"
                 class="img-fluid rounded border" alt="{{ $tourPackage->name }}">
        @else
            <div class="bg-light border rounded d-flex align-items-center justify-content-center"
                 style="height:360px;font-size:72px">&#127958;</div>
        @endif
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h4 class="fw-semibold">{{ $tourPackage->name }}</h4>
                <p class="text-muted mb-2">&#128205; {{ $tourPackage->location }}</p>

                <div class="text-warning small mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($tourPackage->rating) ? '★' : '☆' }}
                    @endfor
                    <span class="text-muted">({{ number_format($tourPackage->rating, 1) }})</span>
                </div>

                <p>{{ $tourPackage->description }}</p>

                <dl class="row small mb-4">
                    <dt class="col-5 text-muted">Duration</dt>
                    <dd class="col-7">{{ $tourPackage->duration_days }} day(s)</dd>
                    <dt class="col-5 text-muted">Max Guests</dt>
                    <dd class="col-7">{{ $tourPackage->max_guests }}</dd>
                    <dt class="col-5 text-muted">Price</dt>
                    <dd class="col-7 fw-semibold text-success">₱{{ number_format($tourPackage->price, 2) }} / person</dd>
                </dl>

                @auth
                    <a href="{{ route('bookings.create', $tourPackage) }}" class="btn btn-primary w-100">
                        Book This Tour
                    </a>
                @else
                    @if(Route::has('login'))
                        <a href="{{ route('home') }}" class="btn btn-primary w-100" data-auth-open data-auth-mode="signin">
                            Login to Book
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>
</x-layout>
