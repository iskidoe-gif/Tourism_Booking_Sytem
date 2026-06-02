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
        TourPackage::create($this->validatePackage($request));

        return redirect()
            ->route('admin.packages.index')
            ->with('success', 'Tour package added successfully.');
    }

    public function edit(TourPackage $package): View
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, TourPackage $package): RedirectResponse
    {
        $package->update($this->validatePackage($request, $package));

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
            'status' => ['required', 'in:active,inactive'],
            'rating' => ['nullable', 'numeric', 'between:0,5'],
        ]);
    }
}
