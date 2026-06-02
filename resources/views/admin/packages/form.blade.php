<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method === 'PUT')
                @method('PUT')
            @endif

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Package name</label>
                    <input type="text" name="name" value="{{ old('name', $package->name) }}" class="form-control">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" value="{{ old('location', $package->location) }}" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" rows="4" class="form-control">{{ old('description', $package->description) }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-3">
                    <label class="form-label">Price</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $package->price) }}" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Duration (days)</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $package->duration_days) }}" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Max guests</label>
                    <input type="number" name="max_guests" value="{{ old('max_guests', $package->max_guests) }}" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Rating</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" value="{{ old('rating', $package->rating) }}" class="form-control">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Image path</label>
                    <input type="text" name="image" value="{{ old('image', $package->image) }}" class="form-control" placeholder="images/your-image.png">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status', $package->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $package->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">{{ $button }}</button>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
