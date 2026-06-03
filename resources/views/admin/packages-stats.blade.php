<x-layout>
    <div class="section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="title">Package Management</h1>
                <p class="lead">View all packages and their status.</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Create Package</a>
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

    <div class="section">
        <div class="card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="subtitle">All Packages</h2>
            </div>
            <div class="tablewrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="th">Image</th>
                            <th class="th">Name</th>
                            <th class="th">Location</th>
                            <th class="th">Price</th>
                            <th class="th">Status</th>
                            <th class="th">Rating</th>
                            <th class="th">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td class="td">
                                    <img src="{{ $package->image_url }}" alt="{{ $package->name }}" style="width:88px;height:56px;object-fit:cover;border-radius:6px;">
                                </td>
                                <td class="td">{{ $package->name }}</td>
                                <td class="td">{{ $package->location }}</td>
                                <td class="td">PHP {{ number_format((float) $package->price, 2) }}</td>
                                <td class="td">{{ ucfirst($package->status) }}</td>
                                <td class="td">{{ number_format((float) $package->rating, 1) }}</td>
                                <td class="td d-flex gap-2">
                                    <a href="{{ route('admin.packages.show', $package) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" onsubmit="return confirm('Delete this package?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="td empty">No packages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $packages->links() }}</div>
        </div>
    </div>
</x-layout>
