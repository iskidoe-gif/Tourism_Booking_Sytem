<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ReportHistory;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
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
        if (! Schema::hasTable('report_histories')) {
            $history = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

            return response()->view('admin.reports', compact('history'));
        }

        $history = ReportHistory::latest()->paginate(10);

        return response()->view('admin.reports', compact('history'));
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
        Storage::disk('local')->ensureDirectoryExists('reports');

        if ($format === 'csv') {
            $csv = $this->exporter->csv($headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.csv';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $csv);
            $this->recordExport($format, count($rows), $filename, $path, $bookings->sum('total_price'));

            return response()->streamDownload(
                fn () => print($csv),
                $filename,
                ['Content-Type' => 'text/csv; charset=UTF-8']
            );
        }

        if ($format === 'xlsx') {
            $file = $this->exporter->xlsx('Bookings', $headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.xlsx';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, file_get_contents($file));
            @unlink($file);
            $this->recordExport($format, count($rows), $filename, $path, $bookings->sum('total_price'));

            return response()->download(storage_path('app/' . $path), $filename);
        }

        if ($format === 'pdf') {
            $pdf = $this->exporter->pdf('Bookings Report', $headers, $rows);
            $filename = 'bookings-report-' . now()->format('YmdHis') . '.pdf';
            $path = 'reports/' . $filename;
            Storage::disk('local')->put($path, $pdf);
            $this->recordExport($format, count($rows), $filename, $path, $bookings->sum('total_price'));

            return response()->streamDownload(
                fn () => print($pdf),
                $filename,
                ['Content-Type' => 'application/pdf']
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

    public function downloadHistory(ReportHistory $report): Response
    {
        return response()->download(storage_path('app/' . $report->path), $report->filename);
    }

    private function recordExport(string $format, int $rowCount, string $filename, string $path, float $totalRevenue): void
    {
        if (! Schema::hasTable('report_histories')) {
            return;
        }

        ReportHistory::create([
            'format' => $format,
            'filename' => $filename,
            'path' => $path,
            'row_count' => $rowCount,
            'total_revenue' => $totalRevenue,
            'generated_by' => Auth::guard('admin')->user()?->name ?? Auth::user()?->name ?? 'System',
        ]);
    }
}
