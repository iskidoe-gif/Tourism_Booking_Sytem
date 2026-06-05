<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Destination;
use App\Models\Payment;
use App\Models\Review;
use App\Models\TourPackage;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Carbon\Carbon;

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
        $selectedDuration = $request->input('duration', 'all');

        if (! in_array($selectedDuration, ['all', '1', '2_4'], true)) {
            $selectedDuration = 'all';
        }

        if ($request->boolean('dur_1') && ! $request->boolean('dur_all')) {
            $selectedDuration = '1';
        } elseif ($request->boolean('dur_2') && ! $request->boolean('dur_all')) {
            $selectedDuration = '2_4';
        }

        $capacity = $request->filled('capacity') && $request->integer('capacity') > 0
            ? $request->integer('capacity')
            : null;

        $packages = TourPackage::active()
            ->bolinao()
            ->when($request->search, fn($q) => $q->where(function($sub) use ($request) {
                $sub->where('name', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            })
            )
            ->when($request->destination, fn($q) => $q->where('destination_id', $request->destination))
            ->when($request->category && array_key_exists($request->category, $categoryMap), function($q) use ($request, $categoryMap) {
                // prefer explicit DB category if set, otherwise fallback to keyword match
                $q->where(function($sub) use ($request, $categoryMap) {
                    $sub->where('category', $request->category);
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
                });
            })
            ->when($selectedDuration !== 'all', function($q) use ($selectedDuration) {
                if ($selectedDuration === '1') {
                    $q->where('duration_days', 1);
                } elseif ($selectedDuration === '2_4') {
                    $q->whereBetween('duration_days', [2, 4]);
                }
            })
            ->when($capacity, fn($q) => $q->where('max_guests', '>=', $capacity))
            ->orderBy('updated_at', 'desc')
            ->paginate(6)
            ->withQueryString();
        $destinations = Destination::orderBy('name')->get();
        $data = compact('packages', 'destinations', 'categoryMap', 'selectedDuration', 'capacity');

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
        if ($request->user()?->isGuest()) {
            $message = 'Guest accounts can only browse tours. Please register or sign in to book.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $message);
        }

        $validated = $request->validate([
            'tour_package_id' => ['required', 'exists:tour_packages,id'],
            'tour_start_date' => ['required', 'date', 'after_or_equal:today'],
            'tour_end_date' => ['required', 'date', 'after:tour_start_date'],
            'num_adults' => ['required', 'integer', 'min:0'],
            'num_children' => ['required', 'integer', 'min:0'],
            'num_seniors' => ['required', 'integer', 'min:0'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['nullable', 'string', 'max:50'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'services' => ['nullable', 'array'],
            'services.*' => ['in:airport_transfer,travel_insurance,meal_plan'],
        ]);

        $package = TourPackage::whereKey($validated['tour_package_id'])
            ->where('status', 'active')
            ->firstOrFail();

        $checkIn = Carbon::parse($validated['tour_start_date']);
        $checkOut = Carbon::parse($validated['tour_end_date']);
        $expectedCheckOut = $checkIn->copy()->addDays($package->duration_days);

        if ($checkOut->diffInDays($checkIn, false) !== $package->duration_days) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['tour_end_date' => "Tour end date must be exactly {$package->duration_days} day(s) after tour start for this package. Please select {$expectedCheckOut->format('Y-m-d')}."]);
        }

        $totalGuests = $validated['num_adults'] + $validated['num_children'] + $validated['num_seniors'];

        if ($totalGuests < 1 || $totalGuests > $package->max_guests) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', "Please select between 1 and {$package->max_guests} guests.");
        }

        $availableServices = [
            'airport_transfer' => ['name' => 'Airport transfer', 'price' => 1200],
            'travel_insurance' => ['name' => 'Travel insurance', 'price' => 450],
            'meal_plan' => ['name' => 'Meal plan', 'price' => 650],
        ];

        $serviceItems = [];
        $serviceTotal = 0;

        foreach ($validated['services'] ?? [] as $serviceKey) {
            if (isset($availableServices[$serviceKey])) {
                $serviceItems[] = $availableServices[$serviceKey] + ['key' => $serviceKey];
                $serviceTotal += $availableServices[$serviceKey]['price'];
            }
        }

        $bookingService = new BookingService();
        $booking = $bookingService->createBooking([
            'user_id' => $request->user()->id,
            'tour_package_id' => $package->id,
            'tour_date' => $validated['tour_start_date'],
            'tour_start_date' => $validated['tour_start_date'],
            'tour_end_date' => $validated['tour_end_date'],
            'num_guests' => $totalGuests,
            'num_adults' => $validated['num_adults'],
            'num_children' => $validated['num_children'],
            'num_seniors' => $validated['num_seniors'],
            'base_price' => $package->price * $totalGuests,
            'additional_fees' => $serviceTotal,
            'services' => collect($serviceItems),
            'guest_details' => collect([
                'contact_name' => $validated['guest_name'],
                'contact_email' => $validated['guest_email'],
                'contact_phone' => $validated['guest_phone'],
            ]),
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tour booking request submitted successfully.',
                'data' => $booking->load(['package', 'payment']),
            ], 201);
        }

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Your booking request has been submitted. We will confirm availability shortly.');
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

        // Get top-rated packages
        $topRatedPackages = TourPackage::active()
            ->bolinao()
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        // Get recent reviews from the user
        $userReviews = Review::where('user_id', $user->id)
            ->with(['tourPackage'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent reviews from all users (for recommendations)
        $communityReviews = Review::with(['user', 'tourPackage'])
            ->where('rating', '>=', 4)
            ->latest()
            ->limit(6)
            ->get();

        // Get booking breakdown by status
        $bookingsByStatus = [
            'approved' => (clone $bookingQuery)->where('status', 'approved')->count(),
            'pending' => (clone $bookingQuery)->where('status', 'pending')->count(),
            'cancelled' => (clone $bookingQuery)->where('status', 'cancelled')->count(),
        ];

        // Get most booked destinations
        $topDestinations = TourPackage::active()
            ->bolinao()
            ->withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        return [
            'stats' => [
                'packages' => TourPackage::active()->bolinao()->count(),
                'bookings' => $bookingQuery->count(),
                'pending_bookings' => (clone $bookingQuery)->where('status', 'pending')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
                'bookingsByStatus' => $bookingsByStatus,
            ],
            'availablePackages' => TourPackage::query()
                ->active()
                ->bolinao()
                ->latest()
                ->get(),
            'topRatedPackages' => $topRatedPackages,
            'recentBookings' => $bookings,
            'userReviews' => $userReviews,
            'communityReviews' => $communityReviews,
            'topDestinations' => $topDestinations,
            'user' => $user,
        ];
    }

    private function buildDashboardData(Request $request): array
    {
        $user = Auth::guard('admin')->user() ?? $request->user();
        $bookingCountQuery = Booking::query();
        $paymentQuery = Payment::query();

        // Get recent bookings with details
        $recentBookings = Booking::with(['user', 'package', 'payment'])
            ->latest()
            ->limit(10)
            ->get();

        // Get top packages by booking count
        $topPackages = TourPackage::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        // Get top destinations by bookings
        $topDestinations = Destination::withCount(['tourPackages' => function($q) {
            $q->withCount('bookings');
        }])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // Get monthly booking data (last 6 months)
        $monthlyBookings = Booking::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(total_price) as revenue')
            ->whereRaw('YEAR(created_at) = YEAR(NOW())')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Get booking status breakdown
        $bookingsByStatus = [
            'pending' => (clone $bookingCountQuery)->where('status', 'pending')->count(),
            'approved' => (clone $bookingCountQuery)->where('status', 'approved')->count(),
            'cancelled' => (clone $bookingCountQuery)->where('status', 'cancelled')->count(),
        ];

        // Get payment breakdown
        $paymentsByStatus = [
            'paid' => (clone $paymentQuery)->where('status', 'paid')->count(),
            'pending' => (clone $paymentQuery)->where('status', 'pending')->count(),
            'failed' => (clone $paymentQuery)->where('status', 'failed')->count(),
        ];

        // Get recent reviews
        $recentReviews = Review::with(['user', 'tourPackage'])
            ->latest()
            ->limit(8)
            ->get();

        // Get average ratings by package
        $packageRatings = TourPackage::withCount('reviews')
            ->with('reviews')
            ->orderBy('rating', 'desc')
            ->limit(5)
            ->get();

        // Calculate customer satisfaction
        $avgRating = Review::avg('rating') ?? 0;
        $totalReviews = Review::count();

        // Get active users count
        $activeUsers = Booking::distinct('user_id')->count('user_id');

        // Get upcoming check-ins (tours starting within next 7 days with approved status)
        $upcomingCheckIns = Booking::with(['user', 'package'])
            ->where('status', 'approved')
            ->whereNull('tour_started_at')  // Not yet checked in
            ->whereBetween('tour_start_date', [Carbon::today(), Carbon::today()->addDays(7)])
            ->orderBy('tour_start_date', 'asc')
            ->get();

        return [
            'stats' => [
                'packages' => TourPackage::count(),
                'bookings' => $bookingCountQuery->count(),
                'pending_bookings' => (clone $bookingCountQuery)->where('status', 'pending')->count(),
                'checked_in_bookings' => (clone $bookingCountQuery)->whereNotNull('tour_started_at')->count(),
                'checked_out_bookings' => (clone $bookingCountQuery)->whereNotNull('tour_ended_at')->count(),
                'paid_payments' => (clone $paymentQuery)->where('status', 'paid')->count(),
                'revenue' => (clone $paymentQuery)->where('status', 'paid')->sum('amount'),
                'bookingsByStatus' => $bookingsByStatus,
                'paymentsByStatus' => $paymentsByStatus,
                'avgRating' => round($avgRating, 2),
                'totalReviews' => $totalReviews,
                'activeUsers' => $activeUsers,
            ],
            'recentBookings' => $recentBookings,
            'topPackages' => $topPackages,
            'topDestinations' => $topDestinations,
            'monthlyBookings' => $monthlyBookings,
            'recentReviews' => $recentReviews,
            'packageRatings' => $packageRatings,
            'upcomingCheckIns' => $upcomingCheckIns,
        ];
    }

    private function bookingNumber(): string
    {
        do {
            $code = 'BK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Booking::where('booking_number', $code)->exists());

        return $code;
    }

    // Enhanced Booking Management Methods

    public function showBooking(Booking $booking): View
    {
        $this->authorizeBookingAccess($booking);

        return view('bookings.show', [
            'booking' => $booking->load(['user', 'package', 'payment', 'approver']),
            'remainingDays' => $booking->remaining_days,
        ]);
    }

    public function cancelBooking(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
            'confirm' => ['required', 'boolean'],
        ]);

        if (!$validated['confirm']) {
            return back()->with('error', 'Cancellation not confirmed.');
        }

        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }

        $bookingService = new \App\Services\BookingService();
        $bookingService->cancelBooking(
            $booking,
            $validated['reason'] ?? 'User requested cancellation'
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking cancelled successfully.']);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Booking cancelled successfully. Refund will be processed.');
    }

    public function addNote(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:internal,admin'],
        ]);

        $bookingService = new \App\Services\BookingService();

        if ($validated['type'] === 'internal') {
            $bookingService->addInternalNote($booking, $validated['note']);
        } else {
            $userName = $request->user()?->name ?? 'System';
            $bookingService->addAdminNote($booking, $validated['note'], $userName);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Note added successfully.']);
        }

        return back()->with('success', 'Note added successfully.');
    }

    public function updateGuests(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        $this->authorizeBookingAccess($booking);

        $validated = $request->validate([
            'guests.*.name' => ['required', 'string', 'max:100'],
            'guests.*.email' => ['nullable', 'email'],
            'guests.*.phone' => ['nullable', 'string'],
            'guests.*.age' => ['nullable', 'integer', 'min:1', 'max:150'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->updateGuestDetails($booking, $validated['guests'] ?? []);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Guest details updated.']);
        }

        return back()->with('success', 'Guest details updated successfully.');
    }

    public function applyDiscount(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'discount_code' => ['required', 'string'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->applyDiscount(
            $booking,
            $validated['discount_amount'],
            $validated['discount_code']
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Discount applied.', 'booking' => $booking]);
        }

        return back()->with('success', 'Discount applied successfully.');
    }

    public function confirmBooking(Request $request, Booking $booking): RedirectResponse|JsonResponse
    {
        if (!Auth::guard('admin')->check() && $request->user()?->role !== 'admin') {
            return back()->with('error', 'Unauthorized');
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $bookingService = new \App\Services\BookingService();
        $bookingService->confirmBooking($booking, $validated['admin_notes'] ?? '');

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Booking confirmed.']);
        }

        return back()->with('success', 'Booking confirmed successfully.');
    }

    public function exportBooking(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        // Generate PDF or download as file
        $html = view('bookings.pdf', ['booking' => $booking->load(['user', 'package', 'payment'])])->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"Booking_{$booking->booking_number}.pdf\"");
    }

    private function authorizeBookingAccess(Booking $booking): void
    {
        $user = auth()->user();
        $isAdmin = Auth::guard('admin')->check() || $user?->role === 'admin';
        $isOwner = $user?->id === $booking->user_id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Unauthorized access to booking.');
        }
    }
}
