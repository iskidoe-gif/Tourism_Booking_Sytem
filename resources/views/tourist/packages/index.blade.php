<x-layout title="Browse Packages">

{{-- Hero --}}
<div class="rounded-4 p-5 mb-4 text-white text-center"
     style="background: linear-gradient(135deg, #0077B6, #00B4D8); min-height: 180px">
    <h2 class="fw-bold mb-1">&#127754; Discover Bolinao</h2>
    <p class="mb-0 opacity-75">Explore the hidden gems of Bolinao, Pangasinan</p>
</div>

{{-- Filters --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('packages.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="e.g. Patar, lighthouse..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Type</label>
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    @foreach(['beach','island','nature','heritage','adventure'] as $t)
                        <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>
                            {{ ucfirst($t) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Max Price (₱)</label>
                <input type="number" name="max_price" class="form-control form-control-sm"
                       placeholder="e.g. 2000" value="{{ request('max_price') }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-sm btn-primary w-100">Filter</button>
                @if(request()->hasAny(['search','type','max_price']))
                    <a href="{{ route('packages.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Package Cards --}}
@if($packages->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-search fs-1 d-block mb-3"></i>
        No packages found. <a href="{{ route('packages.index') }}">Clear filters</a>
    </div>
@else
<div class="row g-3">
    @foreach($packages as $package)
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            @if($package->image)
                <img src="{{ asset('storage/' . $package->image) }}"
                     class="card-img-top" style="height:170px;object-fit:cover;border-radius:12px 12px 0 0">
            @else
                @php
                    $fallbackType = in_array($package->type, ['beach','island','nature','heritage','adventure'])
                        ? $package->type
                        : 'default';
                @endphp
                <img src="{{ asset('images/package-' . $fallbackType . '.svg') }}"
                     class="card-img-top" style="height:170px;object-fit:cover;border-radius:12px 12px 0 0"
                     alt="{{ ucfirst($fallbackType) }} tour image">
            @endif

            <div class="card-body d-flex flex-column">
                <span class="badge text-bg-info mb-2" style="width:fit-content">{{ ucfirst($package->type) }}</span>
                <h6 class="card-title fw-bold">{{ $package->name }}</h6>
                <p class="text-muted small mb-1">
                    <i class="bi bi-geo-alt"></i> {{ $package->location }}
                    &nbsp;&bull;&nbsp;
                    <i class="bi bi-clock"></i> {{ $package->duration_days }} day(s)
                    &nbsp;&bull;&nbsp;
                    <i class="bi bi-people"></i> Max {{ $package->max_guests }}
                </p>
                <div class="text-warning small mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        {{ $i <= round($package->rating) ? '★' : '☆' }}
                    @endfor
                    <span class="text-muted">({{ number_format($package->rating, 1) }})</span>
                </div>
                <p class="card-text text-muted small flex-grow-1">{{ Str::limit($package->description, 90) }}</p>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="fw-bold text-primary fs-5">₱{{ number_format($package->price, 2) }}</span>
                    <a href="{{ route('packages.show', $package) }}" class="btn btn-sm btn-primary">
                        View &amp; Book <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">{{ $packages->links() }}</div>
@endif

</x-layout>
