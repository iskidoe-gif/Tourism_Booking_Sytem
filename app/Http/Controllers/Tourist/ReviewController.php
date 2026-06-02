<?php

namespace App\Http\Controllers\Tourist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->review()->exists(), 403, 'You have already submitted a review for this booking.');

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'user_id'          => Auth::id(),
            'tour_package_id'  => $booking->tour_package_id,
            'booking_id'       => $booking->id,
            'rating'           => $validated['rating'],
            'comment'          => $validated['comment'] ?? null,
        ]);

        $booking->tourPackage->updateRating();

        return redirect()
            ->route('reservations.show', $booking)
            ->with('success', 'Your review has been submitted. Thank you for your feedback!');
    }
}
