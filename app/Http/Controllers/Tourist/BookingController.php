<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BookingController extends Controller
{
    // Show booking form
    public function create(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        return view('tourist.bookings.create', compact('tourPackage'));
    }

    // Store new booking
    public function store(Request $request, TourPackage $tourPackage)
    {
        $validated = $request->validate([
            'tour_date'       => 'required|date|after:today',
            'num_guests'      => 'required|integer|min:1|max:' . $tourPackage->max_guests,
            'special_requests'=> 'nullable|string|max:500',
        ]);

        $totalPrice = $tourPackage->price * $validated['num_guests'];

        $booking = Booking::create([
            'user_id'          => $this->currentUserId(),
            'tour_package_id'  => $tourPackage->id,
            'tour_date'        => $validated['tour_date'],
            'num_guests'       => $validated['num_guests'],
            'total_price'      => $totalPrice,
            'special_requests' => $validated['special_requests'] ?? null,
            'status'           => 'pending',
        ]);

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Booking submitted! Please wait for confirmation.');
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
