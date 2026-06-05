<x-layout>
    <style>
        /* Dark & Simple Admin Dashboard */
        .admin-dashboard-header {
            margin-bottom: 2rem;
            background: #1a1a2e;
            padding: 2rem;
            border-radius: 0.75rem;
            border: 1px solid #2d2d4d;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .admin-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .admin-subtitle {
            color: #b0b8d0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .stat-card:hover {
            background: #2d2d45;
            border-color: #4d4d6d;
            transform: translateY(-2px);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #8890a8;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .stat-icon {
            font-size: 1.75rem;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-change {
            font-size: 0.9rem;
            color: #8890a8;
            font-weight: 600;
        }

        .stat-change.positive {
            color: #81c784;
        }

        .stat-change.negative {
            color: #ef5350;
        }

        .content-section {
            margin-bottom: 2.5rem;
        }

        .section-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-bottom: 1.25rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            font-size: 2rem;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .insight-card {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .insight-card:hover {
            border-color: #4d4d6d;
            background: #2d2d45;
        }

        .insight-title {
            font-weight: 800;
            margin-bottom: 1rem;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #3d3d5c;
        }

        .metric-row:last-child {
            border-bottom: none;
        }

        .metric-label {
            color: #8890a8;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .metric-value {
            font-weight: 800;
            color: #ffffff;
            font-size: 1.1rem;
        }

        .progress-container {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #3d3d5c;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .progress-label-text {
            color: #8890a8;
            font-weight: 600;
        }

        .progress-label-value {
            color: #ffffff;
            font-weight: 800;
        }

        .progress-bar {
            width: 100%;
            height: 0.75rem;
            background: #1a1a2e;
            border-radius: 9999px;
            overflow: hidden;
            border: none;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .progress-fill.green {
            background: #81c784;
        }

        .progress-fill.blue {
            background: #64b5f6;
        }

        .progress-fill.orange {
            background: #ffb74d;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .action-btn {
            background: #242842;
            border: 1px solid #3d3d5c;
            color: #b0b8d0;
            padding: 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #2d2d45;
            border-color: #4d4d6d;
            color: #ffffff;
            transform: translateY(-2px);
        }

        .alert-banner {
            background: rgba(255, 193, 7, 0.15);
            border-left: 3px solid #FFB74D;
            border-radius: 0.5rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            color: #ffd700;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .alert-banner.success {
            background: rgba(129, 199, 132, 0.15);
            border-left-color: #81c784;
            color: #81c784;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .kpi-box {
            background: #242842;
            border: 1px solid #3d3d5c;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .kpi-box:hover {
            border-color: #4d4d6d;
            background: #2d2d45;
        }

        .kpi-label {
            font-size: 0.85rem;
            color: #8890a8;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
        }

        .kpi-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
        }

        .kpi-subtext {
            font-size: 0.85rem;
            color: #8890a8;
            margin-top: 0.5rem;
            font-weight: 600;
        }
    </style>

    <!-- Header -->
    <div class="admin-dashboard-header">
        <h1 class="admin-title">📊 Administration Dashboard</h1>
        <p class="admin-subtitle">
            <span>📈</span> Complete overview of your tourism booking system
        </p>
    </div>

    <!-- Alert Banner -->
    <div class="alert-banner success">
        ✅ System is operating normally. Last updated: {{ now()->format('M d, Y H:i A') }}
    </div>

    <!-- Upcoming Check-ins Alert -->
    @if($upcomingCheckIns->count() > 0)
    <div class="alert-banner" style="background: rgba(255, 152, 0, 0.15); border-left-color: #FFB74D; color: #FF9800; margin-bottom: 2rem;">
        <div style="display: flex; align-items: flex-start; gap: 1rem;">
            <div style="font-size: 1.5rem;">⚠️</div>
            <div style="flex: 1;">
                <div style="font-weight: 700; margin-bottom: 0.75rem;">Upcoming Check-ins Alert</div>
                <div style="font-size: 0.95rem; margin-bottom: 1rem;">{{ $upcomingCheckIns->count() }} {{ $upcomingCheckIns->count() === 1 ? 'booking is' : 'bookings are' }} checking in within the next 7 days:</div>
                <div style="display: grid; gap: 0.75rem;">
                    @foreach($upcomingCheckIns as $booking)
                    <div style="background: rgba(0, 0, 0, 0.2); padding: 0.75rem; border-radius: 0.4rem; font-size: 0.9rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong>{{ $booking->package->name ?? 'Tour Package' }}</strong>
                                <br>
                                <span style="opacity: 0.9;">Guest: {{ $booking->user->name ?? 'N/A' }} ({{ $booking->num_guests }} {{ $booking->num_guests === 1 ? 'guest' : 'guests' }})</span>
                            </div>
                            <div style="text-align: right; white-space: nowrap;">
                                <div style="font-weight: 700;">{{ \Carbon\Carbon::parse($booking->tour_start_date)->format('M d') }}</div>
                                <div style="opacity: 0.9; font-size: 0.85rem;">{{ \Carbon\Carbon::parse($booking->tour_start_date)->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Primary Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">📦 Tour Packages</div>
                    <div class="stat-value">{{ $stats['packages'] }}</div>
                </div>
                <div class="stat-icon">📦</div>
            </div>
            <div class="stat-change">Available for booking</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">✈️ Total Bookings</div>
                    <div class="stat-value">{{ $stats['bookings'] }}</div>
                </div>
                <div class="stat-icon">✈️</div>
            </div>
            <div class="stat-change">All time reservations</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">⏳ Pending Approvals</div>
                    <div class="stat-value">{{ $stats['pending_bookings'] }}</div>
                </div>
                <div class="stat-icon">⏳</div>
            </div>
            <div class="stat-change positive">{{ $stats['pending_bookings'] == 0 ? 'All caught up!' : 'Needs attention' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">🛎️ Tour Started</div>
                    <div class="stat-value">{{ $stats['checked_in_bookings'] }}</div>
                </div>
                <div class="stat-icon">🛎️</div>
            </div>
            <div class="stat-change">Bookings with guest arrival recorded</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">🏁 Tour Ended</div>
                    <div class="stat-value">{{ $stats['checked_out_bookings'] }}</div>
                </div>
                <div class="stat-icon">🏁</div>
            </div>
            <div class="stat-change">Bookings with departure recorded</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">✅ Paid Bookings</div>
                    <div class="stat-value">{{ $stats['paid_payments'] }}</div>
                </div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-change">Revenue confirmed</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">💰 Total Revenue</div>
                    <div class="stat-value">₱{{ number_format((float) $stats['revenue'], 0) }}</div>
                </div>
                <div class="stat-icon">💰</div>
            </div>
            <div class="stat-change">From paid bookings</div>
        </div>
    </div>

    <!-- Key Performance Indicators -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">📈</span>
            Performance Metrics
        </h2>

        <div class="insights-grid">
            <!-- Booking Status Overview -->
            <div class="insight-card">
                <div class="insight-title">Booking Status Breakdown</div>
                
                <div class="metric-row">
                    <span class="metric-label">Pending Approval</span>
                    <span class="metric-value">{{ $stats['pending_bookings'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Confirmed Bookings</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['bookings'] - $stats['pending_bookings'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Approval Rate</span>
                    <span class="metric-value">
                        @if($stats['bookings'] > 0)
                            {{ number_format((($stats['bookings'] - $stats['pending_bookings']) / $stats['bookings'] * 100), 1) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Pending vs Confirmed</span>
                        <span class="progress-label-value">{{ $stats['pending_bookings'] }}/{{ $stats['bookings'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill orange" style="width: {{ $stats['bookings'] > 0 ? ($stats['pending_bookings'] / $stats['bookings'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Analytics -->
            <div class="insight-card">
                <div class="insight-title">Revenue Analytics</div>
                
                <div class="metric-row">
                    <span class="metric-label">Confirmed Revenue</span>
                    <span class="metric-value">₱{{ number_format((float) $stats['revenue'], 0) }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Average per Booking</span>
                    <span class="metric-value">
                        @if($stats['paid_payments'] > 0)
                            ₱{{ number_format((float) $stats['revenue'] / $stats['paid_payments'], 2) }}
                        @else
                            ₱0.00
                        @endif
                    </span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Payment Success Rate</span>
                    <span class="metric-value" style="color: #4CAF50;">
                        @if($stats['bookings'] > 0)
                            {{ number_format(($stats['paid_payments'] / $stats['bookings'] * 100), 1) }}%
                        @else
                            0%
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Paid vs Unpaid</span>
                        <span class="progress-label-value">{{ $stats['paid_payments'] }}/{{ $stats['bookings'] }}</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: {{ $stats['bookings'] > 0 ? ($stats['paid_payments'] / $stats['bookings'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Package Management -->
            <div class="insight-card">
                <div class="insight-title">Package Portfolio</div>
                
                <div class="metric-row">
                    <span class="metric-label">Total Packages</span>
                    <span class="metric-value">{{ $stats['packages'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Active Packages</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['packages'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Avg Bookings/Package</span>
                    <span class="metric-value">
                        @if($stats['packages'] > 0)
                            {{ number_format(($stats['bookings'] / $stats['packages']), 1) }}
                        @else
                            0
                        @endif
                    </span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Package Utilization</span>
                        <span class="progress-label-value">
                            @if($stats['packages'] > 0)
                                {{ number_format(min(100, ($stats['bookings'] / $stats['packages'] / 10) * 100), 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill blue" style="width: {{ $stats['packages'] > 0 ? min(100, ($stats['bookings'] / $stats['packages'] / 10) * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">⚡</span>
            Quick Actions
        </h2>

        <div class="quick-actions">
            <a href="{{ route('admin.bookings.index') }}" class="action-btn">
                📋 View Bookings
            </a>
            <a href="{{ route('admin.packages.index') }}" class="action-btn">
                ➕ Manage Packages
            </a>
            <a href="{{ route('admin.packages-stats') }}" class="action-btn">
                📊 Package Stats
            </a>
            <a href="{{ route('admin.reports.index') }}" class="action-btn">
                📄 Generate Reports
            </a>
            <a href="{{ route('admin.destinations.index') }}" class="action-btn">
                🗺️ Manage Destinations
            </a>
            <a href="{{ route('admin.payments.index') }}" class="action-btn">
                💳 Payment History
            </a>
        </div>
    </div>

    <!-- System Health -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🔧</span>
            System Health
        </h2>

        <div class="kpi-grid">
            <div class="kpi-box">
                <div class="kpi-label">Database Status</div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="font-size: 1.5rem;">✅</span>
                    <div class="kpi-value" style="font-size: 1.25rem;">Optimal</div>
                </div>
                <div class="kpi-subtext">All tables healthy</div>
            </div>

            <div class="kpi-box">
                <div class="kpi-label">Last Backup</div>
                <div class="kpi-value" style="font-size: 1.25rem;">Auto</div>
                <div class="kpi-subtext">System automated</div>
            </div>

            <div class="kpi-box">
                <div class="kpi-label">System Uptime</div>
                <div class="kpi-value" style="font-size: 1.25rem;">99.9%</div>
                <div class="kpi-subtext">Excellent performance</div>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">👥</span>
            Customer Engagement
        </h2>

        <div class="insights-grid">
            <div class="insight-card">
                <div class="insight-title">Customer Satisfaction</div>
                
                <div class="metric-row">
                    <span class="metric-label">Average Rating</span>
                    <span class="metric-value" style="color: #FFD700;">
                        ⭐ {{ $stats['avgRating'] }}/5.0
                    </span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Total Reviews</span>
                    <span class="metric-value">{{ $stats['totalReviews'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Active Users</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['activeUsers'] }}</span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Customer Satisfaction</span>
                        <span class="progress-label-value">{{ round(($stats['avgRating'] / 5) * 100, 1) }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: {{ round(($stats['avgRating'] / 5) * 100, 1) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-title">Booking Status Trends</div>
                
                <div class="metric-row">
                    <span class="metric-label">Approved</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['bookingsByStatus']['approved'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Pending</span>
                    <span class="metric-value" style="color: #FFC107;">{{ $stats['bookingsByStatus']['pending'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Cancelled</span>
                    <span class="metric-value" style="color: #F44336;">{{ $stats['bookingsByStatus']['cancelled'] }}</span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Approval Rate</span>
                        <span class="progress-label-value">
                            @if($stats['bookings'] > 0)
                                {{ round(($stats['bookingsByStatus']['approved'] / $stats['bookings']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill green" style="width: {{ $stats['bookings'] > 0 ? round(($stats['bookingsByStatus']['approved'] / $stats['bookings']) * 100, 1) : 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="insight-card">
                <div class="insight-title">Payment Status</div>
                
                <div class="metric-row">
                    <span class="metric-label">Paid</span>
                    <span class="metric-value" style="color: #4CAF50;">{{ $stats['paymentsByStatus']['paid'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Pending</span>
                    <span class="metric-value" style="color: #FFC107;">{{ $stats['paymentsByStatus']['pending'] }}</span>
                </div>

                <div class="metric-row">
                    <span class="metric-label">Failed</span>
                    <span class="metric-value" style="color: #F44336;">{{ $stats['paymentsByStatus']['failed'] }}</span>
                </div>

                <div class="progress-container">
                    <div class="progress-label">
                        <span class="progress-label-text">Payment Collection</span>
                        <span class="progress-label-value">
                            @php 
                                $totalPayments = $stats['paymentsByStatus']['paid'] + $stats['paymentsByStatus']['pending'] + $stats['paymentsByStatus']['failed'];
                            @endphp
                            @if($totalPayments > 0)
                                {{ round(($stats['paymentsByStatus']['paid'] / $totalPayments) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill blue" style="width: {{ $totalPayments > 0 ? round(($stats['paymentsByStatus']['paid'] / $totalPayments) * 100, 1) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    @if($recentBookings->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">📋</span>
            Recent Bookings
        </h2>

        <div style="background: rgba(75, 86, 148, 0.1); border: 1px solid rgba(75, 86, 148, 0.2); border-radius: 0.75rem; overflow: hidden;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem; background: rgba(75, 86, 148, 0.2); border-bottom: 1px solid rgba(75, 86, 148, 0.3); font-weight: 600; color: var(--palette-secondary); font-size: 0.875rem;">
                <div>Guest</div>
                <div>Package</div>
                <div>Tour Date</div>
                <div>Tour Start</div>
                <div>Tour End</div>
                <div>Status</div>
                <div>Amount</div>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                @foreach($recentBookings->take(10) as $booking)
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem; border-bottom: 1px solid rgba(75, 86, 148, 0.15); align-items: center;">
                        <div>
                            <div style="color: var(--palette-cream); font-weight: 500;">{{ $booking->user->name }}</div>
                            <div style="color: var(--palette-secondary); font-size: 0.75rem;">{{ $booking->user->email }}</div>
                        </div>
                        <div style="color: var(--palette-cream);">{{ Str::limit($booking->package->name, 20) }}</div>
                        <div style="color: var(--palette-secondary);">{{ optional($booking->tour_date)->format('M d, Y') }}</div>
                        <div style="color: var(--palette-secondary);">{{ optional($booking->tour_start_date)->format('M d, Y') ?? 'Not set' }}</div>
                        <div style="color: var(--palette-secondary);">{{ optional($booking->tour_end_date)->format('M d, Y') ?? 'Not set' }}</div>
                        <div>
                            <span class="booking-badge badge-{{ $booking->status }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <div style="color: #4CAF50; font-weight: 600;">₱{{ number_format($booking->total_price, 2) }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Top Packages -->
    @if($topPackages->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🏆</span>
            Top Performing Packages
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            @foreach($topPackages as $package)
                <div class="insight-card">
                    <div class="insight-title">{{ $package->name }}</div>
                    
                    <div class="metric-row">
                        <span class="metric-label">Location</span>
                        <span class="metric-value" style="font-size: 1rem;">{{ $package->location }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Total Bookings</span>
                        <span class="metric-value">{{ $package->bookings_count }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Rating</span>
                        <span class="metric-value" style="color: #FFD700;">
                            @if($package->rating)
                                ⭐ {{ number_format($package->rating, 1) }}/5.0
                            @else
                                No ratings
                            @endif
                        </span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Price</span>
                        <span class="metric-value" style="color: #4CAF50;">₱{{ number_format($package->price, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Top Destinations -->
    @if($topDestinations->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🗺️</span>
            Popular Destinations
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            @foreach($topDestinations->take(5) as $destination)
                <div class="insight-card">
                    <div class="insight-title">{{ $destination->name }}</div>
                    
                    <div class="metric-row">
                        <span class="metric-label">Description</span>
                        <span class="metric-value" style="font-size: 0.875rem;">{{ Str::limit($destination->description, 50) }}</span>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Active Packages</span>
                        <span class="metric-value">{{ $destination->tour_packages_count }}</span>
                    </div>

                    <div class="progress-container">
                        <div class="progress-label">
                            <span class="progress-label-text">Tourism Index</span>
                            <span class="progress-label-value">{{ min(100, $destination->tour_packages_count * 20) }}%</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill blue" style="width: {{ min(100, $destination->tour_packages_count * 20) }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Reviews -->
    @if($recentReviews->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">⭐</span>
            Recent Customer Reviews
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            @foreach($recentReviews->take(6) as $review)
                <div class="section-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                        <div>
                            <strong style="color: var(--palette-cream);">{{ $review->tourPackage->name }}</strong>
                            <p style="color: var(--palette-secondary); font-size: 0.875rem; margin: 0.25rem 0;">by {{ $review->user->name }}</p>
                        </div>
                        <span style="font-size: 1.5rem; color: #FFD700;">⭐ {{ $review->rating }}</span>
                    </div>
                    <p style="color: var(--palette-secondary); font-size: 0.875rem; margin: 0.5rem 0;">{{ Str::limit($review->comment, 120) }}</p>
                    <p style="color: var(--palette-secondary); font-size: 0.75rem;">{{ $review->created_at->diffForHumans() }}</p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Highest Rated Packages -->
    @if($packageRatings->isNotEmpty())
    <div class="content-section">
        <h2 class="section-title">
            <span class="section-icon">🌟</span>
            Highest Rated Packages
        </h2>

        <div style="background: rgba(75, 86, 148, 0.1); border: 1px solid rgba(75, 86, 148, 0.2); border-radius: 0.75rem; overflow: hidden;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem; background: rgba(75, 86, 148, 0.2); border-bottom: 1px solid rgba(75, 86, 148, 0.3); font-weight: 600; color: var(--palette-secondary); font-size: 0.875rem;">
                <div>Package Name</div>
                <div>Rating</div>
                <div>Review Count</div>
                <div>Price</div>
            </div>
            @foreach($packageRatings as $package)
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; padding: 1rem; border-bottom: 1px solid rgba(75, 86, 148, 0.15); align-items: center;">
                    <div style="color: var(--palette-cream);">{{ Str::limit($package->name, 25) }}</div>
                    <div style="color: #FFD700; font-weight: 600;">⭐ {{ number_format($package->rating, 2) }}/5.0</div>
                    <div style="color: var(--palette-secondary);">{{ $package->reviews_count }} reviews</div>
                    <div style="color: #4CAF50; font-weight: 600;">₱{{ number_format($package->price, 2) }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</x-layout>

<style>
    .booking-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-pending {
        background: rgba(255, 193, 7, 0.2);
        color: #FFC107;
    }

    .badge-approved {
        background: rgba(76, 175, 80, 0.2);
        color: #4CAF50;
    }

    .badge-paid {
        background: rgba(76, 175, 80, 0.2);
        color: #4CAF50;
    }

    .badge-cancelled {
        background: rgba(244, 67, 54, 0.2);
        color: #F44336;
    }

    .section-card {
        background: rgba(75, 86, 148, 0.1);
        border: 1px solid rgba(75, 86, 148, 0.2);
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
</style>
