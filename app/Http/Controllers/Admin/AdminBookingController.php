<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'tourPackage', 'payment'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, fn($q) =>
                $q->where('booking_number', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user', 'tourPackage', 'payment', 'review']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        abort_if($booking->status !== 'pending', 403, 'Only pending bookings can be confirmed.');

        $booking->update(['status' => 'confirmed']);

        // Auto-create a payment record
        Payment::firstOrCreate(
            ['booking_id' => $booking->id],
            ['amount' => $booking->total_price, 'status' => 'unpaid', 'method' => 'gcash']
        );

        return back()->with('success', "Booking #{$booking->booking_number} confirmed.");
    }

    public function cancel(Booking $booking)
    {
        abort_if($booking->status === 'cancelled', 403, 'Already cancelled.');

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', "Booking #{$booking->booking_number} cancelled.");
    }
}
