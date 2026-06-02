<x-layout>
    <div class="section">
        <h1 class="title">Package Management</h1>
        <p class="lead">View all packages and their status.</p>

        <div class="actions">
            <a href="{{ route('admin.packages.create') }}" class="navbtn">Create Package</a>
        </div>
    </div>

    <div class="stats">
        <div class="card">
            <p>Total Packages</p>
            <strong class="value">{{ $stats['total'] }}</strong>
        </div>
        <div class="card">
            <p>Active</p>
            <strong class="value">{{ $stats['active'] }}</strong>
        </div>
        <div class="card">
            <p>Inactive</p>
            <strong class="value">{{ $stats['inactive'] }}</strong>
        </div>
    </div>

    <div class="card">
        <h2 class="subtitle">All Packages</h2>
        <div class="stack">
            @forelse($packages as $package)
                <div class="mini package-row">
                    <div class="package-info">
                        <strong>{{ $package->name }}</strong>
                        <p class="lead">{{ $package->location }}</p>
                        <p class="lead">PHP {{ number_format((float) $package->price, 2) }} • {{ $package->duration }} days</p>
                        <p class="lead">Max Guests: {{ $package->max_guests }}</p>
                    </div>
                    <div class="package-meta">
                        <span class="status {{ $package->status === 'active' ? 'status-approved' : 'status-declined' }}">
                            {{ ucfirst($package->status) }}
                        </span>
                        <div class="booking-actions">
                            <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-primary">Edit</a>
                            <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('Delete this package?')">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="lead">No packages found.</p>
            @endforelse

            @if($packages->hasPages())
                <div class="pagination-wrapper">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
