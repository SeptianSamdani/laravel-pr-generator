<div>
    {{-- Filter Section --}}
    <div class="card mb-6 bg-orange-light-50/60 border border-orange-light-200">
        <div class="card-body space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Date Range --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Period</label>
                    <select wire:model.live="dateRange"
                        class="input bg-white border-secondary-200 focus:ring-primary-500/30">
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                @if($dateRange === 'custom')
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Start Date</label>
                    <input type="date" wire:model.live="startDate"
                        class="input bg-white border-secondary-200 focus:ring-primary-500/30">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">End Date</label>
                    <input type="date" wire:model.live="endDate"
                        class="input bg-white border-secondary-200 focus:ring-primary-500/30">
                </div>
                @endif

                {{-- Outlet --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Outlet</label>
                    <select wire:model.live="selectedOutlet"
                        class="input bg-white border-secondary-200 focus:ring-primary-500/30">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        @php 
            $palette = [
                'Total PRs'         => 'primary',
                'Pending Approval'  => 'accent',
                'Approved'          => 'green',
                'Total Amount'      => 'secondary',
            ];
        @endphp

        @php 
            $statCards = [
                [
                    'label' => 'Total PRs',
                    'value' => number_format($stats['total_prs']),
                    'sub'   => abs($stats['percentage_change']).'% vs previous',
                    'icon'  => 'tabler:list',
                ],
                [
                    'label' => 'Pending Approval',
                    'value' => number_format($stats['pending']),
                    'sub'   => 'Awaiting approval',
                    'icon'  => 'tabler:clock',
                ],
                [
                    'label' => 'Approved',
                    'value' => number_format($stats['approved']),
                    'sub'   => $stats['approved_today'].' today',
                    'icon'  => 'tabler:check',
                ],
                [
                    'label' => 'Total Amount',
                    'value' => 'Rp '.number_format($stats['total_amount'] / 1_000_000, 1).'M',
                    'sub'   => 'Selected period',
                    'icon'  => 'tabler:credit-card',
                ],
            ];
        @endphp

        @foreach ($statCards as $card)
            @php
                $color = $palette[$card['label']] ?? 'secondary';
            @endphp

            <div class="rounded-xl border border-secondary-100 shadow-soft p-5 flex flex-col gap-3
                bg-{{ $color }}-50/40">

                {{-- Icon background --}}
                <div class="w-10 h-10 rounded-lg 
                    bg-{{ $color }}-100
                    text-{{ $color }}-700
                    flex items-center justify-center">
                    <x-icon :name="$card['icon']" class="w-5 h-5" />
                </div>

                {{-- Label --}}
                <p class="text-sm font-medium text-secondary-600">
                    {{ $card['label'] }}
                </p>

                {{-- Value --}}
                <h3 class="text-2xl font-bold text-secondary-900">
                    {{ $card['value'] }}
                </h3>

                {{-- Sub Text --}}
                <p class="text-xs text-secondary-500">
                    {{ $card['sub'] }}
                </p>

            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Monthly Chart -->
        <div class="lg:col-span-2 card animate-slide-in">
            <div class="card-header">
                <h3 class="text-primary-50 text-lg font-bold">PR Trends (Last 6 Months)</h3>
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
                <h3 class="text-primary-50 text-lg font-bold">Status Distribution</h3>
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
                <h3 class="text-primary-50 text-lg font-bold">Recent Purchase Requisitions</h3>
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
                    <h3 class="text-primary-50 text-lg font-bold">Quick Actions</h3>
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
                    <h3 class="text-primary-50 text-lg font-bold">Top Outlets</h3>
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
