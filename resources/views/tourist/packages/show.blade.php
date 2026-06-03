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
                <p class="text-muted mb-2">Category: {{ $tourPackage->category_label }}</p>

                <div class="text-warning small mb-3">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($tourPackage->rating) ? '★' : '☆' }}
                    @endfor
                    <span class="text-muted">({{ number_format($tourPackage->rating, 1) }})</span>
                </div>

                <p>{{ $tourPackage->description }}</p>

                <dl class="row small mb-4">
                    <dt class="col-5 text-muted">Destination</dt>
                    <dd class="col-7">{{ $tourPackage->destination?->name ?? 'N/A' }}</dd>
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

<div class="row mt-5">
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-3">Reviews</h5>

                @if($tourPackage->reviews->isEmpty())
                    <p class="text-muted">No reviews yet. Be the first to share your experience.</p>
                @else
                    @foreach($tourPackage->reviews as $review)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $review->user->name }}</strong>
                                    <span class="text-muted small">· {{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-warning small">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <p class="mb-2">{{ $review->comment }}</p>
                            @if(auth()->id() === $review->user_id)
                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="text-end">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->isTourist())
    <div class="row mb-5">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Submit a Review</h5>
                    <form action="{{ route('reviews.store', $tourPackage) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <select name="rating" class="form-control">
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Comment</label>
                            <textarea name="comment" rows="4" class="form-control">{{ old('comment') }}</textarea>
                        </div>
                        <button class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@elseif(auth()->check())
    <div class="row mb-5">
        <div class="col-12 col-lg-8">
            <div class="alert alert-warning">
                Only tourist or guest accounts may submit reviews. Admins cannot rate tour packages.
            </div>
        </div>
    </div>
@endif
</x-layout>
