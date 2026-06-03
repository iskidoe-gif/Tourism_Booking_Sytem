<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(private readonly ReportExportService $exporter)
    {
    }

    public function index(Request $request): Response
    {
        return response()->view('admin.reports');
    }

    public function bookings(Request $request, string $format = 'json'): JsonResponse|Response
    {
        $bookings = Booking::with(['user', 'package', 'payment', 'approver'])
            ->latest()
            ->get();

        $rows = $bookings->map(function (Booking $booking): array {
            return [
                $booking->booking_number,
                $booking->user?->name ?? '',
                $booking->package?->name ?? '',
                optional($booking->tour_date)->format('Y-m-d') ?? '',
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

        $format = strtolower($format);

        if (! Storage::disk('local')->exists('reports')) {
            Storage::disk('local')->makeDirectory('reports');
        }

        if ($format === 'csv') {
            $csv = $this->exporter->csv($headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.csv';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $csv);

            return response(
                $csv,
                200,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        }

        if ($format === 'xlsx') {
            $file = $this->exporter->xlsx('Bookings', $headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.xlsx';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, file_get_contents($file));
            @unlink($file);

            return response(
                Storage::disk('local')->get($path),
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        }

        if ($format === 'pdf') {
            $pdf = $this->exporter->pdf('Bookings Report', $headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.pdf';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $pdf);

            return response(
                Storage::disk('local')->get($path),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
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
