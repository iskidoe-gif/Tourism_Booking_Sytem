<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->touristData($request);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('dashboard', $data);
    }

    public function admin(Request $request): JsonResponse|View
    {
        $data = $this->buildDashboardData($request);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.dashboard', $data);
    }

    public function packages(Request $request): JsonResponse|View
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->touristData($request);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('tourist.packages', $data);
    }

    public function reservations(Request $request): JsonResponse|View
    {
        if ($request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->touristData($request);

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('tourist.reservations', $data);
    }

    public function storeBooking(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'booking_date' => ['required', 'date'],
            'guests' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $package = TourPackage::whereKey($validated['tour_package_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $booking = Booking::create([
            'booking_code' => $this->bookingCode(),
            'user_id' => $request->user()->id,
            'tour_package_id' => $package->id,
            'booking_date' => $validated['booking_date'],
            'guests' => $validated['guests'],
            'status' => 'pending',
            'total_amount' => $package->price * $validated['guests'],
            'notes' => $validated['notes'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tour booked successfully.',
                'data' => $booking->load(['package', 'payment']),
            ], 201);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Tour booked successfully.');
    }

    private function touristData(Request $request): array
    {
        $user = $request->user();
        $bookingQuery = Booking::with(['user', 'package', 'payment'])
            ->where('user_id', $user->id)
            ->latest();
        $bookings = $bookingQuery->get();
        $paymentQuery = Payment::whereIn('booking_id', Booking::where('user_id', $user->id)->select('id'));

        return [
            'stats' => [
                'packages' => TourPackage::where('is_active', true)->count(),
                'bookings' => $bookingQuery->count(),
                'pending_bookings' => (clone $bookingQuery)->where('status', 'pending')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
            ],
            'availablePackages' => TourPackage::query()
                ->where('is_active', true)
                ->latest()
                ->get(),
            'recentBookings' => $bookings,
            'user' => $user,
        ];
    }

    private function buildDashboardData(Request $request): array
    {
        $user = $request->user();
        $bookingQuery = Booking::with(['user', 'package', 'payment'])->latest();
        $bookings = $bookingQuery->take(5)->get();
        $bookingCountQuery = Booking::query();
        $paymentQuery = Payment::query();

        return [
            'stats' => [
                'packages' => TourPackage::count(),
                'bookings' => $bookingCountQuery->count(),
                'pending_bookings' => (clone $bookingCountQuery)->where('status', 'pending')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
            ],
            'availablePackages' => TourPackage::query()
                ->where('is_active', true)
                ->latest()
                ->get(),
            'recentBookings' => $bookings,
            'user' => $user,
        ];
    }

    private function bookingCode(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }
}
