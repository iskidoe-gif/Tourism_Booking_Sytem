<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $packages = TourPackage::latest()->paginate(15);

        return view('admin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('admin.packages.create', ['package' => new TourPackage()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePackage($request);

        // handle uploaded image file if present
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $name = time() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', $file->getClientOriginalName());
            $file->move(public_path('images'), $name);
            $data['image'] = 'images/' . $name;
        }

        TourPackage::create($data);

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Tour package added successfully.');
    }

    public function show(TourPackage $package): View
    {
        return view('admin.packages.show', compact('package'));
    }

    public function edit(TourPackage $package): View
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package): RedirectResponse
    {
        $data = $this->validatePackage($request, $package);

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $name = time() . '-' . preg_replace('/[^a-z0-9\-\.]/i', '-', $file->getClientOriginalName());
            $file->move(public_path('images'), $name);
            $data['image'] = 'images/' . $name;
        }

        $package->update($data);

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Tour package updated successfully.');
    }

    public function destroy(TourPackage $package): RedirectResponse
    {
        $package->delete();

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Tour package removed successfully.');
    }

    private function validatePackage(Request $request, ?TourPackage $package = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'max_guests' => ['required', 'integer', 'min:1'],
            'image' => ['nullable', 'string', 'max:255'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
        ]);
    }
}
