<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Booking::with(['user', 'package', 'payment'])->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateBooking($request);
        $package = TourPackage::findOrFail($validated['tour_package_id']);

        $validated['booking_code'] = $this->bookingCode();
        $validated['booking_date'] = $validated['booking_date'] ?? now()->toDateString();
        $validated['guests'] = $validated['guests'] ?? 1;
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['total_amount'] = $validated['total_amount'] ?? ($package->price * $validated['guests']);
        $validated['user_id'] = $validated['user_id'] ?? $request->user()?->id;

        if (! $validated['user_id']) {
            return response()->json(['message' => 'The user field is required.'], 422);
        }

        $booking = Booking::create($validated);

        return response()->json(['data' => $booking->load(['user', 'package'])], 201);
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json(['data' => $booking->load(['user', 'package', 'payment', 'approver'])]);
    }

    public function update(Request $request, Booking $booking): JsonResponse
    {
        $validated = $this->validateBooking($request);
        $package = TourPackage::findOrFail($validated['tour_package_id'] ?? $booking->tour_package_id);

        if (! array_key_exists('total_amount', $validated)) {
            $validated['total_amount'] = ($validated['guests'] ?? $booking->guests) * $package->price;
        }

        $booking->update($validated);

        return response()->json(['data' => $booking->refresh()->load(['user', 'package', 'payment'])]);
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }

    private function validateBooking(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'booking_date' => ['sometimes', 'required', 'date'],
            'guests' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['nullable', 'in:pending,approved,cancelled,completed'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'approved_by' => ['nullable', 'exists:admins,id'],
            'approved_at' => ['nullable', 'date'],
        ]);
    }

    private function bookingCode(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
