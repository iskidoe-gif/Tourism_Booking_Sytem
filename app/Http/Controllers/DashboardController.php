<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\Review;
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

    public function home(): View
    {
        $topRatedPackages = TourPackage::active()
            ->bolinao()
            ->where('rating', '>=', 4)
            ->orderBy('rating', 'desc')
            ->limit(3)
            ->get();

        $customerReviews = Review::with(['user', 'tourPackage'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('welcome', compact('topRatedPackages', 'customerReviews'));
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

        $categoryMap = [
            'natural' => ['label' => 'Natural Attractions', 'keywords' => ['beach','waterfall','falls','cave','river','rock','island','lagoon','spring','shore']],
            'cultural' => ['label' => 'Cultural & Historical Sites', 'keywords' => ['church','heritage','museum','lighthouse','parish','histor','monument','temple']],
            'recreational' => ['label' => 'Recreational & Adventure Spots', 'keywords' => ['island','diving','snorkel','camp','hike','hiking','island hopping','adventure','tour']],
            'accommodation' => ['label' => 'Accommodation & Hospitality', 'keywords' => ['resort','hotel','inn','homestay','transient','guesthouse','lodg','villa']],
            'events' => ['label' => 'Events & Festivals', 'keywords' => ['festival','event','parade','competition','celebration']],
            'ecotourism' => ['label' => 'Ecotourism & Conservation Areas', 'keywords' => ['mangrove','park','reserve','ecolodge','protected','sanctuary']],
        ];

        $packages = TourPackage::active()
            ->bolinao()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('location', 'like', "%{$request->search}%")
                ->orWhere('description', 'like', "%{$request->search}%")
            )
            ->when($request->destination, fn($q) => $q->where('destination_id', $request->destination))
            ->when($request->category, function($q) use ($request, $categoryMap) {
                // prefer explicit DB category if set, otherwise fallback to keyword match
                $q->where(function($sub) use ($request, $categoryMap) {
                    $sub->where('category', $request->category);
                    if ($request->category && array_key_exists($request->category, $categoryMap)) {
                        $keywords = $categoryMap[$request->category]['keywords'];
                        $sub->orWhere(function($s2) use ($keywords) {
                            foreach ($keywords as $k) {
                                $s2->orWhere('name', 'like', "%{$k}%")
                                   ->orWhere('description', 'like', "%{$k}%")
                                   ->orWhere('location', 'like', "%{$k}%");
                            }
                        })->orWhereHas('destination', function($d) use ($keywords) {
                            foreach ($keywords as $k) {
                                $d->orWhere('name', 'like', "%{$k}%");
                            }
                        });
                    }
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(6)
            ->withQueryString();
        $destinations = Destination::orderBy('name')->get();
        $data = compact('packages', 'destinations', 'categoryMap');

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

        $query = TourPackage::latest()
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('location', 'like', '%' . $request->search . '%')
                        ->orWhere('description', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->category, function ($query) use ($request) {
                $query->where('category', $request->category);
            });

        $packages = $query->paginate(5)->withQueryString();
        $categories = TourPackage::categoryLabels();

        $data = [
            'stats' => [
                'active' => $activePackages,
                'inactive' => $inactivePackages,
                'total' => $activePackages + $inactivePackages,
            ],
            'packages' => $packages,
            'categories' => $categories,
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
                'packages' => TourPackage::active()->bolinao()->count(),
                'bookings' => $bookingQuery->count(),
                'pending_bookings' => (clone $bookingQuery)->where('status', 'pending')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
            ],
            'availablePackages' => TourPackage::query()
                ->active()
                ->bolinao()
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
