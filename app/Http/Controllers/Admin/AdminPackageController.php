<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPackageController extends Controller
{
    public function index()
    {
        $packages = TourPackage::withCount('bookings')->latest()->paginate(10);
        return view('admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.packages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'description'   => 'required|string',
            'location'      => 'required|string|max:150',
            'price'         => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1',
            'max_guests'    => 'required|integer|min:1',
            'type'          => 'required|in:beach,island,nature,heritage,adventure',
            'status'        => 'required|in:active,inactive',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        TourPackage::create($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Tour package created successfully.');
    }

    public function edit(TourPackage $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'description'   => 'required|string',
            'location'      => 'required|string|max:150',
            'price'         => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1',
            'max_guests'    => 'required|integer|min:1',
            'type'          => 'required|in:beach,island,nature,heritage,adventure',
            'status'        => 'required|in:active,inactive',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image) Storage::disk('public')->delete($package->image);
            $validated['image'] = $request->file('image')->store('packages', 'public');
        }

        $package->update($validated);

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy(TourPackage $package)
    {
        if ($package->image) Storage::disk('public')->delete($package->image);
        $package->delete();

        return redirect()->route('admin.packages.index')
            ->with('success', 'Package deleted.');
    }

    public function show(TourPackage $package)
    {
        $package->load('bookings.user', 'reviews.user');
        return view('admin.packages.show', compact('package'));
    }
}
