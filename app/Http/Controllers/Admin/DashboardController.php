<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_bookings'   => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed'        => Booking::where('status', 'confirmed')->count(),
            'total_tourists'   => User::where('role', 'tourist')->count(),
            'total_packages'   => TourPackage::count(),
            'total_revenue'    => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recent_bookings = Booking::with(['user', 'tourPackage'])
            ->latest()
            ->take(8)
            ->get();

        $top_packages = TourPackage::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue for chart (last 6 months)
        $monthly = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
                'month'   => $month->format('M'),
                'revenue' => Payment::where('status', 'paid')
                    ->whereMonth('paid_at', $month->month)
                    ->whereYear('paid_at',  $month->year)
                    ->sum('amount'),
            ];
        });

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'top_packages', 'monthly'));
    }
}
