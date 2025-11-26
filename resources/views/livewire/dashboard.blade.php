<div>
    <!-- Filters Section -->
    <div class="card mb-6 animate-fade-in">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date Range Filter -->
                <div>
                    <label class="block text-sm font-semibold text-secondary-900 mb-2">Period</label>
                    <select wire:model.live="dateRange" class="input">
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                @if($dateRange === 'custom')
                <!-- Custom Date Range -->
                <div>
                    <label class="block text-sm font-semibold text-secondary-900 mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate" class="input">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-secondary-900 mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate" class="input">
                </div>
                @endif

                <!-- Outlet Filter -->
                <div>
                    <label class="block text-sm font-semibold text-secondary-900 mb-2">Outlet</label>
                    <select wire:model.live="selectedOutlet" class="input">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total PRs -->
        <div class="card animate-fade-in">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Total PRs</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ number_format($stats['total_prs']) }}</h3>
                        <p class="text-xs mt-2 flex items-center gap-1
                            {{ $stats['percentage_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                @if($stats['percentage_change'] >= 0)
                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                @else
                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                @endif
                            </svg>
                            {{ abs($stats['percentage_change']) }}% vs previous period
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-orange">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="card animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Pending Approval</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ number_format($stats['pending']) }}</h3>
                        <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                            <div class="status-pending"></div>
                            Awaiting approval
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="card animate-fade-in" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Approved</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ number_format($stats['approved']) }}</h3>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <div class="status-active"></div>
                            {{ $stats['approved_today'] }} today
                        </p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Amount -->
        <div class="card animate-fade-in" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Total Amount</p>
                        <h3 class="text-2xl font-bold text-secondary-900 mt-2">
                            Rp {{ number_format($stats['total_amount'] / 1000000, 1) }}M
                        </h3>
                        <p class="text-xs text-secondary-500 mt-2">Selected period</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-secondary-700 to-secondary-900 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Monthly Chart -->
        <div class="lg:col-span-2 card animate-slide-in">
            <div class="card-header">
                <h3 class="text-lg font-bold">PR Trends (Last 6 Months)</h3>
            </div>
            <div class="card-body">
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="card animate-slide-in" style="animation-delay: 0.1s;">
            <div class="card-header">
                <h3 class="text-lg font-bold">Status Distribution</h3>
            </div>
            <div class="card-body">
                <div class="h-64 flex items-center justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent PRs -->
        <div class="lg:col-span-2 card animate-slide-in">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-bold">Recent Purchase Requisitions</h3>
                <a href="{{ route('pr.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-semibold">
                    View all →
                </a>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Outlet</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPRs as $pr)
                                <tr>
                                    <td class="font-semibold text-primary-600">
                                        <a href="{{ route('pr.show', $pr->id) }}" class="hover:underline">
                                            {{ $pr->pr_number }}
                                        </a>
                                    </td>
                                    <td>{{ $pr->outlet->name }}</td>
                                    <td>{{ $pr->tanggal->format('d M Y') }}</td>
                                    <td class="font-semibold">Rp {{ number_format($pr->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pr->status === 'draft')
                                            <span class="badge badge-light">Draft</span>
                                        @elseif($pr->status === 'submitted')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($pr->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($pr->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-secondary-500">
                                        No recent purchase requisitions
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card animate-slide-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Quick Actions</h3>
                </div>
                <div class="card-body space-y-3">
                    @can('pr.create')
                    <a href="{{ route('pr.create') }}" class="btn-primary w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New PR
                    </a>
                    @endcan

                    @can('pr.approve')
                    <a href="{{ route('approval.index') }}" class="btn-secondary w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Review Approvals
                        @if($stats['pending'] > 0)
                            <span class="badge badge-danger ml-auto">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                    @endcan

                    <a href="{{ route('pr.index') }}" class="btn-outline w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        View All PRs
                    </a>
                </div>
            </div>

            <!-- Top Outlets -->
            <div class="card animate-slide-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Top Outlets</h3>
                </div>
                <div class="card-body space-y-4">
                    @forelse($topOutlets as $index => $item)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-600 font-bold flex items-center justify-center text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm text-secondary-900 truncate">
                                    {{ $item->outlet->name }}
                                </p>
                                <p class="text-xs text-secondary-500">
                                    {{ $item->total_prs }} PRs • Rp {{ number_format($item->total_amount / 1000000, 1) }}M
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-secondary-500 text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Pending Approvals (for managers) -->
            @can('pr.approve')
            @if($pendingApprovals->count() > 0)
            <div class="card animate-slide-in" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Pending Approvals</h3>
                </div>
                <div class="card-body space-y-3">
                    @foreach($pendingApprovals as $pr)
                        <div class="flex items-start justify-between p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('pr.show', $pr->id) }}" class="font-semibold text-sm text-primary-600 hover:underline truncate block">
                                    {{ $pr->pr_number }}
                                </a>
                                <p class="text-xs text-secondary-600 mt-1">
                                    {{ $pr->outlet->name }} • {{ $pr->creator->name }}
                                </p>
                                <p class="text-xs font-semibold text-secondary-900 mt-1">
                                    Rp {{ number_format($pr->total, 0, ',', '.') }}
                                </p>
                            </div>
                            <a href="{{ route('pr.show', $pr->id) }}" class="btn-ghost text-xs py-1 px-2 ml-2">
                                Review
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endcan
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            initCharts();
        });

        document.addEventListener('DOMContentLoaded', function() {
            initCharts();
        });

        function initCharts() {
            // Monthly Chart
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                const monthlyData = @json($monthlyChartData);
                
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Total PRs',
                                data: monthlyData.map(d => d.total),
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Approved',
                                data: monthlyData.map(d => d.approved),
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Rejected',
                                data: monthlyData.map(d => d.rejected),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // Status Distribution Chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                const statusData = @json($statusDistribution);
                
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Draft', 'Pending', 'Approved', 'Rejected'],
                        datasets: [{
                            data: [
                                statusData.draft,
                                statusData.submitted,
                                statusData.approved,
                                statusData.rejected
                            ],
                            backgroundColor: [
                                '#9ca3af',
                                '#f59e0b',
                                '#22c55e',
                                '#ef4444'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        }
    </script>
</div>