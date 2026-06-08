<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\TourPackage;
use App\Models\Booking;
use App\Models\PromoPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create(TourPackage $tourPackage)
    {
        abort_if($tourPackage->status === 'inactive', 404);

        $selectedPromoId = request('promo');
        $selectedPromo = null;
        if ($selectedPromoId) {
            $promo = PromoPackage::find($selectedPromoId);
            $selectedPromo = $promo?->isActive() ? $promo : null;
        }

        return view('tourist.bookings.create', compact('tourPackage', 'selectedPromo'));
    }

    public function store(Request $request, TourPackage $tourPackage)
    {
        if (auth()->user()?->isGuest()) {
            return redirect()
                ->route('packages.show', $tourPackage)
                ->with('error', 'Guest accounts can only browse tours. Please create a tourist account or sign in to book.');
        }

        $validated = $request->validate([
            'tour_start_date'  => 'required|date|after_or_equal:today',
            'tour_end_date'    => 'required|date|after_or_equal:tour_start_date',
            'num_adults'       => "required|integer|min:0|max:{$tourPackage->max_guests}",
            'num_children'     => "required|integer|min:0|max:{$tourPackage->max_guests}",
            'num_seniors'      => "required|integer|min:0|max:{$tourPackage->max_guests}",
            'num_guests'       => "required|integer|min:1|max:{$tourPackage->max_guests}",
            'promo_package_id' => 'nullable|exists:promo_packages,id',
            'guest_name'       => 'required|string|max:255',
            'guest_email'      => 'required|email|max:255',
            'guest_phone'      => 'nullable|string|max:30',
            'special_requests' => 'nullable|string|max:500',
            'services'         => ['nullable', 'array'],
            'services.*'       => ['string', 'in:airport_transfer,travel_insurance,meal_plan'],
        ]);

        $numGuests = $validated['num_adults'] + $validated['num_children'] + $validated['num_seniors'];
        if ($numGuests !== $validated['num_guests']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['num_guests' => 'Guest total must match adults, children, and seniors.']);
        }

        $servicePrices = [
            'airport_transfer' => 1200,
            'travel_insurance' => 450,
            'meal_plan' => 650,
        ];

        $services = $validated['services'] ?? [];
        $additionalFees = array_reduce($services, function ($sum, $service) use ($servicePrices) {
            return $sum + ($servicePrices[$service] ?? 0);
        }, 0);

        $basePrice = $tourPackage->price * $numGuests;
        $discountAmount = 0;
        $discountCode = null;
        $promoPackageId = null;

        if (!empty($validated['promo_package_id'])) {
            $promoPackage = PromoPackage::find($validated['promo_package_id']);
            if ($promoPackage && $promoPackage->isActive()) {
                $discountAmount = round($basePrice * ($promoPackage->discount_percentage / 100), 2);
                $discountCode = $promoPackage->name;
                $promoPackageId = $promoPackage->id;
            }
        }

        $totalPrice = round($basePrice - $discountAmount + $additionalFees, 2);

        $booking = Booking::create([
            'user_id'          => Auth::id(),
            'tour_package_id'  => $tourPackage->id,
            'promo_package_id' => $promoPackageId,
            'tour_date'        => $validated['tour_start_date'],
            'tour_start_date'  => $validated['tour_start_date'],
            'tour_end_date'    => $validated['tour_end_date'],
            'num_guests'       => $numGuests,
            'num_adults'       => $validated['num_adults'],
            'num_children'     => $validated['num_children'],
            'num_seniors'      => $validated['num_seniors'],
            'guest_details'    => [
                'name' => $validated['guest_name'],
                'email' => $validated['guest_email'],
                'phone' => $validated['guest_phone'],
            ],
            'services'         => $services,
            'base_price'       => $basePrice,
            'additional_fees'  => $additionalFees,
            'discount_amount'  => $discountAmount,
            'discount_code'    => $discountCode,
            'total_price'      => $totalPrice,
            'special_requests' => $validated['special_requests'],
            'status'           => 'pending',
        ]);

        $booking->payment()->create([
            'amount' => $booking->total_price,
            'status' => 'unpaid',
            'method' => 'cash',
        ]);

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', "Booking #{$booking->booking_number} submitted! Waiting for admin approval.");
    }
}
