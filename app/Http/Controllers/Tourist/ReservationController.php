<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['package', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('tourist.reservations.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        $booking->load(['package', 'payment']);
        return view('tourist.reservations.show', compact('booking'));
    }

    public function cancel(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->status !== 'pending', 403, 'Only pending bookings can be cancelled.');

        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->route('reservations.index')
            ->with('success', "Booking #{$booking->booking_number} cancelled.");
    }

    public function checkIn(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!$booking->canCheckIn(), 403, 'This reservation cannot be checked in yet.');

        $booking->markAsCheckedIn();

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'You have successfully checked in for your reservation.');
    }

    public function checkOut(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if(!$booking->canCheckOut(), 403, 'This reservation cannot be checked out yet.');

        $booking->markAsCheckedOut();

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'You have successfully checked out from your reservation.');
    }
}
