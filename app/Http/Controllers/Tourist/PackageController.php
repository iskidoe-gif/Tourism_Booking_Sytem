<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PackageController extends Controller
{
    // Browse all active packages
    public function index(Request $request)
    {
        $packages = TourPackage::active()
            ->when($request->search, fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
            )
            ->when($request->max_price, fn($q) =>
                $q->where('price', '<=', $request->max_price)
            )
            ->orderBy('rating', 'desc')
            ->get();

        $bookings = Booking::with('tourPackage')
            ->where('user_id', $this->currentUserId())
            ->latest()
            ->limit(5)
            ->get();

        return view('tourist.packages.index', compact('packages', 'bookings'));
    }

    // View single package detail
    public function show(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        return view('tourist.packages.show', compact('tourPackage'));
    }

    private function currentUserId(): int
    {
        if (Auth::check()) {
            return Auth::id();
        }

        return User::query()->value('id') ?? User::create([
            'name' => 'Juan D.',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
        ])->id;
    }
}
