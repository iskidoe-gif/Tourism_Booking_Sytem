<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentApiController extends Controller
{
    // GET /api/payments
    public function index()
    {
        $payments = Payment::with('booking')
            ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $payments]);
    }

    // POST /api/payments
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id'       => 'required|exists:bookings,id',
            'amount'           => 'required|numeric|min:0',
            'method'           => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'status'           => 'nullable|string|in:pending,completed,failed',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($validated['booking_id']);

        $payment = Payment::create([
            'booking_id'       => $booking->id,
            'amount'           => $validated['amount'],
            'method'           => $validated['method'],
            'status'           => $validated['status'] ?? 'completed',
            'reference_number' => $validated['reference_number'] ?? null,
            'paid_at'          => ($validated['status'] ?? 'completed') === 'completed' ? now() : null,
        ]);

        return response()->json(['success' => true, 'data' => $payment], 201);
    }

    // GET /api/payments/{id}
    public function show($id)
    {
        $payment = Payment::with('booking')
            ->whereHas('booking', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $payment]);
    }
}
