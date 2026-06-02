<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingApiController extends Controller
{
    // GET /api/bookings  — list current user's bookings
    public function index()
    {
        $bookings = Booking::with(['tourPackage', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    // POST /api/bookings
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_package_id'  => 'required|exists:tour_packages,id',
            'tour_date'        => 'required|date|after:today',
            'num_guests'       => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $package = TourPackage::findOrFail($validated['tour_package_id']);

        if ($validated['num_guests'] > $package->max_guests) {
            return response()->json(['success' => false, 'message' => 'Exceeds max guests for this package.'], 422);
        }

        $booking = Booking::create([
            'user_id'          => Auth::id(),
            'tour_package_id'  => $package->id,
            'tour_date'        => $validated['tour_date'],
            'num_guests'       => $validated['num_guests'],
            'total_price'      => $package->price * $validated['num_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status'           => 'pending',
        ]);

        return response()->json(['success' => true, 'data' => $booking], 201);
    }

    // GET /api/bookings/{id}
    public function show($id)
    {
        $booking = Booking::with(['tourPackage', 'payment'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $booking]);
    }

    // PUT /api/bookings/{id}  — update special_requests only (pending)
    public function update(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending bookings can be updated.'], 403);
        }

        $validated = $request->validate([
            'special_requests' => 'nullable|string|max:500',
            'tour_date'        => 'nullable|date|after:today',
        ]);

        $booking->update($validated);

        return response()->json(['success' => true, 'data' => $booking]);
    }

    // DELETE /api/bookings/{id}  — cancel booking
    public function destroy($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending bookings can be cancelled.'], 403);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json(['success' => true, 'message' => 'Booking cancelled.']);
    }
}
