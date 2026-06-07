<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);
        return view('tourist.bookings.create', compact('tourPackage'));
    }

    public function store(Request $request, TourPackage $tourPackage)
    {
        if (auth()->user()?->isGuest()) {
            return redirect()
                ->route('packages.show', $tourPackage)
                ->with('error', 'Guest accounts can only browse tours. Please create a tourist account or sign in to book.');
        }

        $validated = $request->validate([
            'tour_date'        => 'required|date|after:today',
            'num_guests'       => "required|integer|min:1|max:{$tourPackage->max_guests}",
            'special_requests' => 'nullable|string|max:500',
        ]);

        $booking = Booking::create([
            'user_id'          => Auth::id(),
            'tour_package_id'  => $tourPackage->id,
            'tour_date'        => $validated['tour_date'],
            'num_guests'       => $validated['num_guests'],
            'total_price'      => $tourPackage->price * $validated['num_guests'],
            'special_requests' => $validated['special_requests'],
            'status'           => 'pending',
        ]);

        // Automatically create a payment record for the booking
        $booking->payment()->create([
            'amount' => $booking->total_price,
            'status' => 'unpaid',
            'method' => 'cash',
        ]);

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', "Booking #{$booking->booking_number} submitted! Waiting for admin approval.");
    }
}
