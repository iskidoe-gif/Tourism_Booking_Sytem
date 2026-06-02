<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\TourPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse|View|RedirectResponse
    {
        if (Auth::guard('admin')->check() || $request->user()?->role === 'admin') {
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

    public function packages(Request $request): JsonResponse|View|RedirectResponse
    {
        if (Auth::guard('admin')->check() || $request->user()?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $packages = TourPackage::where('status', 'active')
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('location', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
            )
            ->orderBy('updated_at', 'desc')
            ->paginate(6)
            ->withQueryString();

        $data = compact('packages');

        if ($request->user()) {
            $data = array_merge($this->touristData($request), $data);
        }

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('tourist.packages', $data);
    }

    public function reservations(Request $request): JsonResponse|View|RedirectResponse
    {
        if (Auth::guard('admin')->check() || $request->user()?->role === 'admin') {
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
            'tour_date' => ['required', 'date', 'after:today'],
            'num_guests' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ]);

        $package = TourPackage::whereKey($validated['tour_package_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $request->validate([
            'num_guests' => ['max:' . $package->max_guests],
        ]);

        $booking = Booking::create([
            'booking_number' => $this->bookingNumber(),
            'user_id' => $request->user()->id,
            'tour_package_id' => $package->id,
            'tour_date' => $validated['tour_date'],
            'num_guests' => $validated['num_guests'],
            'status' => 'pending',
            'total_price' => $package->price * $validated['num_guests'],
            'special_requests' => $validated['special_requests'] ?? null,
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

    public function adminBookings(Request $request): JsonResponse|View
    {
        $bookings = Booking::with(['user', 'package', 'payment', 'approver'])
            ->latest()
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json(['data' => $bookings]);
        }

        return view('admin.bookings', ['bookings' => $bookings]);
    }

    public function adminPackages(Request $request): JsonResponse|View
    {
        $activePackages = TourPackage::where('status', 'active')->count();
        $inactivePackages = TourPackage::where('status', '!=', 'active')->count();
        $packages = TourPackage::latest()->paginate(20);

        $data = [
            'stats' => [
                'active' => $activePackages,
                'inactive' => $inactivePackages,
                'total' => $activePackages + $inactivePackages,
            ],
            'packages' => $packages,
        ];

        if ($request->expectsJson()) {
            return response()->json($data);
        }

        return view('admin.packages-stats', $data);
    }

    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,cancelled'],
        ]);

        $booking->update([
            'status' => $validated['status'],
            'approved_by' => Auth::guard('admin')->id() ?? $request->user()?->id,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Booking status updated successfully.');
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
                'packages' => TourPackage::where('status', 'active')->count(),
                'bookings' => $bookingQuery->count(),
                'pending_bookings' => (clone $bookingQuery)->where('status', 'pending')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
            ],
            'availablePackages' => TourPackage::query()
                ->where('status', 'active')
                ->latest()
                ->get(),
            'recentBookings' => $bookings,
            'user' => $user,
        ];
    }

    private function buildDashboardData(Request $request): array
    {
        $user = Auth::guard('admin')->user() ?? $request->user();
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
        ];
    }

    private function bookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }
}
