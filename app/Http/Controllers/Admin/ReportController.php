<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // ── Report index page ──────────────────────────────────────
    public function index(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        $bookings = Booking::with(['user', 'tourPackage', 'payment'])
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->latest()
            ->get();

        $summary = [
            'total_bookings'  => $bookings->count(),
            'confirmed'       => $bookings->where('status', 'confirmed')->count(),
            'pending'         => $bookings->where('status', 'pending')->count(),
            'cancelled'       => $bookings->where('status', 'cancelled')->count(),
            'total_revenue'   => $bookings->whereNotNull('payment')
                                          ->where('payment.status', 'paid')
                                          ->sum('total_price'),
        ];

        return view('admin.reports.index', compact('bookings', 'summary', 'from', 'to'));
    }

    // ── PDF Export ─────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $bookings = $this->getBookings($request);

        $pdf = Pdf::loadView('admin.reports.pdf', compact('bookings'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('bolinao-bookings-report-' . now()->format('Ymd') . '.pdf');
    }

    // ── CSV Export ─────────────────────────────────────────────
    public function exportCsv(Request $request)
    {
        $bookings = $this->getBookings($request);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings-report-' . now()->format('Ymd') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            // Header row
            fputcsv($handle, ['Booking #', 'Tourist', 'Email', 'Package', 'Tour Date', 'Guests', 'Total', 'Status']);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->booking_number,
                    $b->user->name,
                    $b->user->email,
                    $b->tourPackage->name,
                    $b->tour_date->format('Y-m-d'),
                    $b->num_guests,
                    $b->total_price,
                    $b->status,
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    // ── XLSX Export ────────────────────────────────────────────
    public function exportXlsx(Request $request)
    {
        // Using maatwebsite/excel  →  composer require maatwebsite/excel
        // Simplified: return CSV with xlsx extension for demo.
        // For production: use Laravel Excel Export class.
        return $this->exportCsv($request);
    }

    // ── JSON Export ────────────────────────────────────────────
    public function exportJson(Request $request)
    {
        $bookings = $this->getBookings($request)->map(fn($b) => [
            'booking_number' => $b->booking_number,
            'tourist'        => $b->user->name,
            'email'          => $b->user->email,
            'package'        => $b->tourPackage->name,
            'tour_date'      => $b->tour_date->format('Y-m-d'),
            'num_guests'     => $b->num_guests,
            'total_price'    => $b->total_price,
            'status'         => $b->status,
        ]);

        return Response::json($bookings, 200, [
            'Content-Disposition' => 'attachment; filename="bookings-report-' . now()->format('Ymd') . '.json"',
        ]);
    }

    // ── Shared query ───────────────────────────────────────────
    private function getBookings(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();

        return Booking::with(['user', 'tourPackage', 'payment'])
            ->whereBetween('created_at', [$from, $to . ' 23:59:59'])
            ->latest()
            ->get();
    }
}
