<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ReportExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportExportService $exporter)
    {
    }

    public function bookings(Request $request, string $format = 'json'): JsonResponse|Response|BinaryFileResponse
    {
        $bookings = Booking::with(['user', 'package', 'payment', 'approver'])
            ->latest()
            ->get();

        $rows = $bookings->map(function (Booking $booking): array {
            return [
                $booking->booking_number,
                $booking->user?->name ?? '',
                $booking->package?->name ?? '',
                $booking->tour_date?->format('Y-m-d') ?? '',
                $booking->num_guests,
                $booking->status,
                $booking->total_price,
                $booking->payment?->status ?? 'unpaid',
            ];
        })->all();

        $headers = [
            'Booking Number',
            'Customer',
            'Package',
            'Tour Date',
            'Guests',
            'Status',
            'Total Price',
            'Payment Status',
        ];

        if ($format === 'csv') {
            $csv = $this->exporter->csv($headers, $rows);

            return response($csv, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="bookings-report.csv"',
            ]);
        }

        if ($format === 'xlsx') {
            $file = $this->exporter->xlsx('Bookings', $headers, $rows);

            return response()->download($file, 'bookings-report.xlsx')->deleteFileAfterSend(true);
        }

        if ($format === 'pdf') {
            $pdf = $this->exporter->pdf('Bookings Report', $headers, $rows);

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="bookings-report.pdf"',
            ]);
        }

        if ($request->expectsJson() || $format === 'json') {
            return response()->json([
                'summary' => [
                    'total_bookings' => $bookings->count(),
                    'total_revenue' => $bookings->sum('total_price'),
                    'pending_bookings' => $bookings->where('status', 'pending')->count(),
                    'paid_payments' => $bookings->filter(fn (Booking $booking) => $booking->payment?->status === 'paid')->count(),
                ],
                'data' => $rows,
            ]);
        }

        return response()->json(['message' => 'Unsupported report format.'], 422);
    }
}
