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

        @php
            function parsePhpSize(string $size): int
            {
                $size = trim($size);
                $unit = strtolower(substr($size, -1));
                $value = (int) $size;

                return match ($unit) {
                    'g' => $value * 1024 * 1024 * 1024,
                    'm' => $value * 1024 * 1024,
                    'k' => $value * 1024,
                    default => $value,
                };
            }

            $uploadLimit = parsePhpSize(ini_get('upload_max_filesize') ?: '2M');
            $postLimit = parsePhpSize(ini_get('post_max_size') ?: '8M');
            // cap at 1GB
            $phpMaxUpload = min($uploadLimit, $postLimit, 1024 * 1024 * 1024);
            $maxUploadLabel = number_format($phpMaxUpload / 1024 / 1024, 2) . ' MB';
        @endphp

        <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
            @csrf
            @if(isset($method) && in_array(strtoupper($method), ['PUT', 'PATCH'], true))
                @method(strtoupper($method))
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

            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">None / Uncategorised</option>
                        <option value="natural" {{ old('category', $package->category) === 'natural' ? 'selected' : '' }}>Natural Attractions</option>
                        <option value="cultural" {{ old('category', $package->category) === 'cultural' ? 'selected' : '' }}>Cultural & Historical Sites</option>
                        <option value="recreational" {{ old('category', $package->category) === 'recreational' ? 'selected' : '' }}>Recreational & Adventure Spots</option>
                        <option value="accommodation" {{ old('category', $package->category) === 'accommodation' ? 'selected' : '' }}>Accommodation & Hospitality</option>
                        <option value="events" {{ old('category', $package->category) === 'events' ? 'selected' : '' }}>Events & Festivals</option>
                        <option value="ecotourism" {{ old('category', $package->category) === 'ecotourism' ? 'selected' : '' }}>Ecotourism & Conservation Areas</option>
                    </select>
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
            </div>

            <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                    <label class="form-label">Upload image</label>
                    <input type="file" name="image_file" accept="image/*" class="form-control" id="image_file_input" @if($package->exists) data-upload-url="{{ route('admin.packages.upload-image', $package) }}" @endif>
                    <div class="mt-2">
                        @php
                            $previewPath = null;
                            if ($package->image) {
                                // check storage public disk first
                                try {
                                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($package->image)) {
                                        $previewPath = asset('storage/' . ltrim($package->image, '/')) . '?v=' . \Illuminate\Support\Facades\Storage::disk('public')->lastModified($package->image);
                                    } elseif (file_exists(public_path($package->image))) {
                                        $previewPath = asset($package->image) . '?v=' . filemtime(public_path($package->image));
                                    }
                                } catch (\Throwable $_) {
                                    $previewPath = null;
                                }
                            }
                        @endphp
                        <img id="image_preview" src="{{ $previewPath ?? asset('images/package-default.svg') }}" alt="Preview" style="max-width:160px; max-height:120px; object-fit:cover; border-radius:6px;">
                        @if($package->image)
                            <div class="mt-1" style="font-size:12px;">
                                <strong>Saved image:</strong>
                                <a id="image_debug_link" href="{{ $previewPath ?? asset($package->image) }}" target="_blank">Open image</a>
                                <span class="text-muted"> ({{ $package->image }})</span>
                            </div>
                        @endif
                    </div>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('image_file_input');
    const preview = document.getElementById('image_preview');
    if (!input) return;
    
    // Track upload state
    let uploadInProgress = false;
    let uploadFailed = false;
    let uploadSucceeded = false;

    input.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;

        // Check if upload is possible (package must exist)
        const uploadUrl = input.dataset.uploadUrl;
        if (!uploadUrl) {
            // During creation, just preview - no upload yet
            const reader = new FileReader();
            preview.onerror = function () {
                preview.src = '{{ asset('images/package-default.svg') }}';
            };
            reader.onload = function (ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
                preview.style.maxWidth = '100%';
                preview.style.maxHeight = '160px';
                preview.style.objectFit = 'cover';
            };
            reader.onerror = function (err) {
                console.error('FileReader error', err);
            };
            reader.readAsDataURL(file);
            return;
        }

        // Reset states
        uploadInProgress = true;
        uploadFailed = false;
        uploadSucceeded = false;

        // immediate client-side preview (robust)
        try {
            const reader = new FileReader();
            preview.onerror = function () {
                const toast = document.getElementById('upload_toast');
                if (toast) {
                    toast.textContent = 'Preview image failed to load, using placeholder.';
                    toast.style.display = 'block';
                    setTimeout(() => { toast.style.display = 'none'; }, 2500);
                }
                preview.src = '{{ asset('images/package-default.svg') }}';
            };

            reader.onload = function (ev) {
                try {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    preview.style.maxWidth = '100%';
                    preview.style.maxHeight = '160px';
                    preview.style.objectFit = 'cover';
                } catch (inner) {
                    console.error('Preview render failed', inner);
                }
            };
            reader.onerror = function (err) {
                console.error('FileReader error', err);
                const toast = document.getElementById('upload_toast');
                if (toast) { toast.textContent = 'Preview failed: unable to read file'; toast.style.display = 'block'; setTimeout(()=>{ toast.style.display = 'none'; },2000); }
            };
            reader.readAsDataURL(file);
        } catch (err) {
            console.error('Preview setup failed', err);
        }

        // if package exists, upload immediately to server
        const MAX_CLIENT_UPLOAD = 10 * 1024 * 1024; // 10MB direct upload threshold (avoid chunked for most images)

        const tokenInput = document.querySelector('input[name="_token"]');
        const csrf = tokenInput ? tokenInput.value : '';

        async function doSimpleUpload(file, uploadUrl) {
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('image_file', file);
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 60000); // 60 second timeout
            
            try {
                const res = await fetch(uploadUrl, { 
                    method: 'POST', 
                    body: fd, 
                    credentials: 'same-origin',
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
                const body = await res.text();
                try { return { status: res.status, body: JSON.parse(body) }; } catch (e) { throw new Error('Invalid server response: ' + body); }
            } catch (e) {
                clearTimeout(timeoutId);
                if (e.name === 'AbortError') {
                    throw new Error('Upload timed out. Please try again with a smaller file.');
                }
                throw e;
            }
        }

        async function doChunkedUpload(file, uploadUrl) {
            const CHUNK_SIZE = 1 * 1024 * 1024; // 1MB, keep each chunk below PHP upload_max_filesize defaults
            const total = Math.ceil(file.size / CHUNK_SIZE);
            const uploadId = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2,9);
            const chunkUrl = uploadUrl.replace('/upload-image', '/upload-chunk');
            const completeUrl = uploadUrl.replace('/upload-image', '/complete-upload');

            let uploadedBytes = 0;
            for (let i = 0; i < total; i++) {
                const start = i * CHUNK_SIZE;
                const end = Math.min(start + CHUNK_SIZE, file.size);
                const blob = file.slice(start, end);
                const fd = new FormData();
                fd.append('_token', csrf);
                fd.append('upload_id', uploadId);
                fd.append('chunk_index', i);
                fd.append('total_chunks', total);
                fd.append('chunk', blob, file.name + '.part.' + i);

                const res = await fetch(chunkUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
                if (!res.ok) {
                    const txt = await res.text();
                    throw new Error('Chunk upload failed: ' + txt);
                }
                uploadedBytes += (end - start);
                const toast = document.getElementById('upload_toast');
                if (toast) {
                    const percent = Math.round((uploadedBytes / file.size) * 100);
                    toast.textContent = 'Uploading: ' + percent + '%';
                    toast.style.display = 'block';
                }
            }

            // finalize
            const fd2 = new FormData();
            fd2.append('_token', csrf);
            fd2.append('upload_id', uploadId);
            fd2.append('original_name', file.name);
            fd2.append('total_chunks', total);
            const finalRes = await fetch(completeUrl, { method: 'POST', body: fd2, credentials: 'same-origin' });
            const finalText = await finalRes.text();
            try { return { status: finalRes.status, body: JSON.parse(finalText) }; } catch (e) { throw new Error('Invalid server response: ' + finalText); }
        }

        (async function () {
            try {
                let result;
                if (file.size > MAX_CLIENT_UPLOAD) {
                    console.log('File exceeds server limit; using chunked upload', file.size);
                    result = await doChunkedUpload(file, uploadUrl);
                } else {
                    result = await doSimpleUpload(file, uploadUrl);
                }

                uploadInProgress = false; // mark as done ASAP
                const data = result.body;
                if (result.status >= 200 && result.status < 300 && data.url) {
                    const ts = data.timestamp || Date.now();
                    preview.src = data.url + '?v=' + ts;
                    const debugLink = document.getElementById('image_debug_link');
                    if (debugLink) {
                        debugLink.href = data.url + '?v=' + ts;
                        debugLink.textContent = data.path;
                    }
                    const toast = document.getElementById('upload_toast');
                    if (toast) {
                        toast.textContent = 'Image uploaded: ' + data.path;
                        toast.style.display = 'block';
                        toast.style.opacity = '1';
                        setTimeout(() => {
                            toast.style.transition = 'opacity 300ms ease';
                            toast.style.opacity = '0';
                            setTimeout(() => { toast.style.display = 'none'; toast.style.transition = ''; }, 350);
                        }, 2500);
                    }
                    uploadSucceeded = true;
                    uploadFailed = false;
                } else {
                    let errorMessage = (data && data.error) ? data.error : 'Upload failed';
                    let extra = '';
                    if (data && data.message) {
                        extra = ' — ' + data.message;
                    } else if (data && data.errors && data.errors.image_file) {
                        extra = ' — ' + JSON.stringify(data.errors.image_file);
                    }
                    const toast = document.getElementById('upload_toast');
                    if (toast) {
                        toast.textContent = 'Upload failed: ' + errorMessage + extra;
                        toast.style.display = 'block';
                        toast.style.opacity = '1';
                        setTimeout(() => {
                            toast.style.transition = 'opacity 300ms ease';
                            toast.style.opacity = '0';
                            setTimeout(() => { toast.style.display = 'none'; toast.style.transition = ''; }, 350);
                        }, 2500);
                    }
                    console.error('Upload failed', result.status, data);
                    uploadFailed = true;
                    uploadSucceeded = false;
                    uploadInProgress = false;
                }
            } catch (err) {
                console.error('Upload error', err);
                const toast = document.getElementById('upload_toast');
                if (toast) {
                    toast.textContent = 'Image upload failed: ' + err.message;
                    toast.style.display = 'block';
                    toast.style.opacity = '1';
                    setTimeout(() => {
                        toast.style.transition = 'opacity 300ms ease';
                        toast.style.opacity = '0';
                        setTimeout(() => { toast.style.display = 'none'; toast.style.transition = ''; }, 350);
                    }, 2500);
                }
                uploadFailed = true;
                uploadSucceeded = false;
                uploadInProgress = false;
            }
        })();
    });

    // Prevent form submission if upload is still in progress
    const form = input.closest('form');
    if (form) {
        form.addEventListener('submit', function (e) {
            // Only block if user selected a file AND it's still uploading
            if (input.files && input.files.length > 0) {
                if (uploadInProgress) {
                    e.preventDefault();
                    const toast = document.getElementById('upload_toast');
                    if (toast) {
                        toast.textContent = 'Image upload still in progress...';
                        toast.style.display = 'block';
                        setTimeout(() => { toast.style.display = 'none'; }, 2500);
                    }
                    return false;
                }
                // If upload failed, clear the file input to allow form to submit without image
                if (uploadFailed) {
                    input.value = '';
                    input.files = new DataTransfer().items;
                }
            }
        });
    }
});
</script>
<div id="upload_toast" role="status" aria-live="polite" style="position:fixed;right:20px;top:20px;display:none;z-index:1100;background:rgba(0,0,0,0.85);color:#fff;padding:10px 14px;border-radius:6px;box-shadow:0 6px 18px rgba(0,0,0,0.2);font-size:13px;"></div>
