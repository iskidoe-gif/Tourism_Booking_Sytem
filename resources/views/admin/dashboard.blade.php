<x-layout title="Admin Dashboard">
<div class="row">

  {{-- Sidebar --}}
  <div class="col-md-2 sidebar rounded-3 py-3">
    <p class="text-white-50 small px-3 mb-2 text-uppercase fw-semibold" style="font-size:11px">Admin Panel</p>
    <ul class="nav flex-column">
      <li><a href="{{ route('admin.dashboard') }}" class="nav-link active"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="{{ route('admin.packages.index') }}" class="nav-link"><i class="bi bi-map"></i> Packages</a></li>
      <li><a href="{{ route('admin.bookings.index') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Bookings</a></li>
      <li><a href="{{ route('admin.reports.index') }}" class="nav-link"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
    </ul>
  </div>

  {{-- Main --}}
  <div class="col-md-10 ps-4">
    <h4 class="fw-bold mb-4">&#128202; Dashboard</h4>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-primary fw-bold fs-2">{{ $stats['total_bookings'] }}</div>
          <div class="small text-muted">Total Bookings</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-warning fw-bold fs-2">{{ $stats['pending_bookings'] }}</div>
          <div class="small text-muted">Pending</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-success fw-bold fs-2">{{ $stats['confirmed'] }}</div>
          <div class="small text-muted">Confirmed</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-info fw-bold fs-2">{{ $stats['total_tourists'] }}</div>
          <div class="small text-muted">Tourists</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-secondary fw-bold fs-2">{{ $stats['total_packages'] }}</div>
          <div class="small text-muted">Packages</div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="text-success fw-bold fs-4">₱{{ number_format($stats['total_revenue'], 0) }}</div>
          <div class="small text-muted">Revenue</div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      {{-- Recent Bookings --}}
      <div class="col-md-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white fw-semibold">Recent Bookings</div>
          <div class="table-responsive">
            <table class="table table-hover mb-0 small">
              <thead class="table-light">
                <tr><th>Booking #</th><th>Tourist</th><th>Package</th><th>Date</th><th>Total</th><th>Status</th></tr>
              </thead>
              <tbody>
                @foreach($recent_bookings as $b)
                <tr>
                  <td class="font-monospace">#{{ $b->booking_number }}</td>
                  <td>{{ $b->user->name }}</td>
                  <td>{{ Str::limit($b->tourPackage->name, 25) }}</td>
                  <td>{{ $b->tour_date->format('M d, Y') }}</td>
                  <td>₱{{ number_format($b->total_price, 0) }}</td>
                  <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Top Packages --}}
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-header bg-white fw-semibold">Top Packages</div>
          <ul class="list-group list-group-flush">
            @foreach($top_packages as $i => $pkg)
            <li class="list-group-item d-flex justify-content-between small">
              <span>{{ $i+1 }}. {{ Str::limit($pkg->name, 28) }}</span>
              <span class="badge bg-primary">{{ $pkg->bookings_count }} bookings</span>
            </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

  </div>
</div>
</x-layout>
