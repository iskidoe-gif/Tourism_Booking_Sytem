<x-layout>
    <div class="section">
        <h1 class="title">Reports</h1>
        <p class="lead">Export booking reports in your preferred format.</p>
    </div>

    <div class="card">
        <h2 class="subtitle">Booking Reports</h2>
        <div class="stack" style="margin-top: 2rem;">
            <div class="report-option">
                <div>
                    <strong>CSV Report</strong>
                    <p class="lead">Download as CSV format for spreadsheet applications.</p>
                </div>
                <a href="{{ route('admin.reports.bookings', ['format' => 'csv']) }}" class="btn btn-primary">
                    Download CSV
                </a>
            </div>

            <div class="report-option">
                <div>
                    <strong>XLSX Report</strong>
                    <p class="lead">Download as XLSX format (Excel spreadsheet).</p>
                </div>
                <a href="{{ route('admin.reports.bookings', ['format' => 'xlsx']) }}" class="btn btn-primary">
                    Download XLSX
                </a>
            </div>

            <div class="report-option">
                <div>
                    <strong>PDF Report</strong>
                    <p class="lead">Download as PDF format for printing and sharing.</p>
                </div>
                <a href="{{ route('admin.reports.bookings', ['format' => 'pdf']) }}" class="btn btn-primary">
                    Download PDF
                </a>
            </div>
        </div>
    </div>

</x-layout>
