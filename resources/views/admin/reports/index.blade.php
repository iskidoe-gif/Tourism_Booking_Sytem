<x-layout title="Reports">
<div class="row">
  <div class="col-md-2 sidebar rounded-3 py-3">
    <p class="text-white-50 small px-3 mb-2 text-uppercase fw-semibold" style="font-size:11px">Admin Panel</p>
    <ul class="nav flex-column">
      <li><a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
      <li><a href="{{ route('admin.packages.index') }}" class="nav-link"><i class="bi bi-map"></i> Packages</a></li>
      <li><a href="{{ route('admin.bookings.index') }}" class="nav-link"><i class="bi bi-calendar2-check"></i> Bookings</a></li>
      <li><a href="{{ route('admin.reports.index') }}" class="nav-link active"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
    </ul>
  </div>

  <div class="col-md-10 ps-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="fw-bold mb-0">&#128202; Booking Reports</h4>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.reports.pdf',  request()->only('from','to')) }}"
           class="btn btn-sm btn-danger"><i class="bi bi-filetype-pdf"></i> PDF</a>
        <a href="{{ route('admin.reports.csv',  request()->only('from','to')) }}"
           class="btn btn-sm btn-success"><i class="bi bi-filetype-csv"></i> CSV</a>
        <a href="{{ route('admin.reports.xlsx', request()->only('from','to')) }}"
           class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel"></i> XLSX</a>
        <a href="{{ route('admin.reports.json', request()->only('from','to')) }}"
           class="btn btn-sm btn-secondary"><i class="bi bi-filetype-json"></i> JSON</a>
      </div>
    </div>

    {{-- Date Filter --}}
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-4">
            <label class="form-label small fw-semibold">From</label>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-semibold">To</label>
            <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
          </div>
          <div class="col-md-2">
            <button class="btn btn-sm btn-primary w-100">Filter</button>
          </div>
        </form>
      </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fw-bold fs-3 text-primary">{{ $summary['total_bookings'] }}</div>
          <div class="small text-muted">Total Bookings</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fw-bold fs-3 text-success">{{ $summary['confirmed'] }}</div>
          <div class="small text-muted">Confirmed</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fw-bold fs-3 text-warning">{{ $summary['pending'] }}</div>
          <div class="small text-muted">Pending</div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
          <div class="fw-bold fs-3 text-danger">{{ $summary['cancelled'] }}</div>
          <div class="small text-muted">Cancelled</div>
        </div>
      </div>
    </div>

    {{-- Bookings Table --}}
    <div class="card border-0 shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover small mb-0">
          <thead class="table-light">
            <tr>
              <th>Booking #</th><th>Tourist</th><th>Package</th>
              <th>Tour Date</th><th>Guests</th><th>Total</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $b)
            <tr>
              <td class="font-monospace">#{{ $b->booking_number }}</td>
              <td>{{ $b->user->name }}</td>
              <td>{{ $b->tourPackage->name }}</td>
              <td>{{ $b->tour_date->format('M d, Y') }}</td>
              <td>{{ $b->num_guests }}</td>
              <td>₱{{ number_format($b->total_price, 2) }}</td>
              <td><span class="badge badge-{{ $b->status }}">{{ ucfirst($b->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No bookings in this date range.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</x-layout>
