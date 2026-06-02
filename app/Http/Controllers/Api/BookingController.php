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

        $validated['booking_number'] = $this->bookingNumber();
        $validated['tour_date'] = $validated['tour_date'] ?? now()->toDateString();
        $validated['num_guests'] = $validated['num_guests'] ?? 1;
        $validated['status'] = $validated['status'] ?? 'pending';
        $validated['total_price'] = $validated['total_price'] ?? ($package->price * $validated['num_guests']);
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

        if (! array_key_exists('total_price', $validated)) {
            $validated['total_price'] = ($validated['num_guests'] ?? $booking->num_guests) * $package->price;
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
            'tour_date' => ['sometimes', 'required', 'date'],
            'num_guests' => ['sometimes', 'required', 'integer', 'min:1'],
            'status' => ['nullable', 'in:pending,approved,cancelled,completed'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'special_requests' => ['nullable', 'string'],
            'approved_by' => ['nullable', 'exists:admins,id'],
            'approved_at' => ['nullable', 'date'],
        ]);
    }

    private function bookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }
}
