<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use Illuminate\Http\Request;

class PackageApiController extends Controller
{
    // GET /api/packages
    public function index(Request $request)
    {
        $packages = TourPackage::active()
            ->when($request->type,      fn($q) => $q->where('type', $request->type))
            ->when($request->max_price, fn($q) => $q->where('price', '<=', $request->max_price))
            ->orderBy('rating', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $packages,
        ]);
    }

    // GET /api/packages/{id}
    public function show($id)
    {
        $package = TourPackage::with('reviews.user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $package,
        ]);
    }
}
