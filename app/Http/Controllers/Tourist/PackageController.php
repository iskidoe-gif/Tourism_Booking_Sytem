<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = TourPackage::active()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('location', 'like', "%{$request->search}%")
            )
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->orderBy('rating', 'desc')
            ->paginate(9)
            ->withQueryString();

        return view('tourist.packages.index', compact('packages'));
    }

    public function show(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        $tourPackage->load(['reviews.user', 'destination']);

        return view('tourist.packages.show', compact('tourPackage'));
    }
}
