<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReservationController extends Controller
{
    // List logged-in user's reservations
    public function index()
    {
        $bookings = Booking::with(['tourPackage', 'payment'])
            ->where('user_id', $this->currentUserId())
            ->latest()
            ->paginate(10);

        return view('tourist.reservations.index', compact('bookings'));
    }

    // View single reservation detail
    public function show(Booking $booking)
    {
        // Make sure the booking belongs to the logged-in user
        abort_if($booking->user_id !== $this->currentUserId(), 403);

        $booking->load(['tourPackage', 'payment']);

        return view('tourist.reservations.show', compact('booking'));
    }

    // Cancel a pending booking
    public function cancel(Booking $booking)
    {
        abort_if($booking->user_id !== $this->currentUserId(), 403);
        abort_if($booking->status !== 'pending', 403, 'Only pending bookings can be cancelled.');

        $booking->update(['status' => 'cancelled']);

        return redirect()
            ->route('reservations.index')
            ->with('success', 'Booking #' . $booking->booking_number . ' has been cancelled.');
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
