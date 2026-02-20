<div class="space-y-6">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-secondary-900">
                {{ $dashboardMode === 'manager' ? 'Manager Dashboard' : 'My Dashboard' }}
            </h1>
            <p class="text-sm text-secondary-500 mt-1">
                @if($dashboardMode === 'manager')
                    Overview semua purchase requisitions
                @else
                    Track purchase requisitions yang Anda buat
                @endif
            </p>
        </div>
        
        {{-- Quick Action --}}
        @can('pr.create')
            <a href="{{ route('pr.create') }}" class="btn-primary inline-flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Purchase Request
            </a>
        @endcan
    </div>

    {{-- Filter Section - HANYA untuk Manager --}}
    @if($dashboardMode === 'manager')
        <div class="bg-white rounded-xl border border-secondary-200 shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Date Range --}}
                <div>
                    <label class="text-xs font-medium text-secondary-600 mb-2 block">Period</label>
                    <select wire:model.live="dateRange" class="input text-sm">
                        <option value="today">Today</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                @if($dateRange === 'custom')
                    <div>
                        <label class="text-xs font-medium text-secondary-600 mb-2 block">Start Date</label>
                        <input type="date" wire:model.live="startDate" class="input text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-secondary-600 mb-2 block">End Date</label>
                        <input type="date" wire:model.live="endDate" class="input text-sm">
                    </div>
                @endif

                {{-- Outlet Filter --}}
                <div>
                    <label class="text-xs font-medium text-secondary-600 mb-2 block">Outlet</label>
                    <select wire:model.live="selectedOutlet" class="input text-sm">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Cards - Modern Minimalist --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Total PRs --}}
        <div class="bg-white rounded-xl border border-secondary-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-secondary-500 uppercase tracking-wide">
                        {{ $dashboardMode === 'manager' ? 'Total PRs' : 'My PRs' }}
                    </p>
                    <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ number_format($stats['total_prs']) }}</h3>
                    <div class="flex items-center gap-1 mt-2">
                        @if($stats['percentage_change'] >= 0)
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            <span class="text-xs font-semibold text-green-600">+{{ abs($stats['percentage_change']) }}%</span>
                        @else
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            <span class="text-xs font-semibold text-red-600">{{ $stats['percentage_change'] }}%</span>
                        @endif
                        <span class="text-xs text-secondary-400">vs previous</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-secondary-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Approval --}}
        <div class="bg-white rounded-xl border border-amber-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-amber-700 uppercase tracking-wide">Pending</p>
                    <h3 class="text-3xl font-bold text-amber-900 mt-2">{{ number_format($stats['pending']) }}</h3>
                    <p class="text-xs text-amber-600 mt-2">Awaiting approval</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-green-700 uppercase tracking-wide">Approved</p>
                    <h3 class="text-3xl font-bold text-green-900 mt-2">{{ number_format($stats['approved']) }}</h3>
                    @if($dashboardMode === 'manager')
                        <p class="text-xs text-green-600 mt-2">{{ $stats['approved_today'] }} approved today</p>
                    @else
                        <p class="text-xs text-green-600 mt-2">Ready for payment</p>
                    @endif
                </div>
                <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Amount --}}
        <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-primary-100 uppercase tracking-wide">Total Amount</p>
                    <h3 class="text-3xl font-bold text-white mt-2">{{ number_format($stats['total_amount'] / 1_000_000, 1) }}M</h3>
                    <p class="text-xs text-primary-100 mt-2">
                        {{ $dashboardMode === 'manager' ? 'Selected period' : 'Your contributions' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Chart Section --}}
            <div class="bg-white rounded-xl border border-secondary-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100">
                    <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-wide">
                        {{ $dashboardMode === 'manager' ? 'PR Trends' : 'My PR Trends' }}
                    </h3>
                    <p class="text-xs text-secondary-500 mt-1">Last 6 months performance</p>
                </div>
                <div class="p-6">
                    <div class="h-72">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent PRs Table --}}
            <div class="bg-white rounded-xl border border-secondary-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-wide">
                            {{ $dashboardMode === 'manager' ? 'Recent PRs' : 'My Recent PRs' }}
                        </h3>
                        <p class="text-xs text-secondary-500 mt-1">Latest purchase requisitions</p>
                    </div>
                    <a href="{{ route('pr.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-700 flex items-center gap-1">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-secondary-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">PR Number</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">Outlet</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-600 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-600 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-secondary-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary-100">
                            @forelse($recentPRs as $pr)
                                <tr class="hover:bg-secondary-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('pr.show', $pr->id) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                                            {{ $pr->pr_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-secondary-700">{{ $pr->outlet->name }}</td>
                                    <td class="px-6 py-4 text-sm text-secondary-500">{{ $pr->tanggal->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-secondary-900 text-right">
                                        Rp {{ number_format($pr->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($pr->status === 'draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-700">
                                                Draft
                                            </span>
                                        @elseif($pr->status === 'submitted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                                Pending
                                            </span>
                                        @elseif($pr->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                Approved
                                            </span>
                                        @elseif($pr->status === 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                Paid
                                            </span>
                                        @elseif($pr->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-secondary-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                        <p class="text-sm font-medium text-secondary-500">
                                            {{ $dashboardMode === 'manager' ? 'No recent purchase requisitions' : 'You haven\'t created any PRs yet' }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column (1/3) --}}
        <div class="space-y-6">
            
            {{-- Status Distribution --}}
            <div class="bg-white rounded-xl border border-secondary-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-secondary-100">
                    <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-wide">Status</h3>
                    <p class="text-xs text-secondary-500 mt-1">Distribution overview</p>
                </div>
                <div class="p-6">
                    <div class="h-56 flex items-center justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Outlets - HANYA untuk Manager --}}
            @if($dashboardMode === 'manager' && $topOutlets->count() > 0)
                <div class="bg-white rounded-xl border border-secondary-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-secondary-100">
                        <h3 class="text-sm font-semibold text-secondary-900 uppercase tracking-wide">Top Outlets</h3>
                        <p class="text-xs text-secondary-500 mt-1">Highest PR volume</p>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($topOutlets as $index => $item)
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-secondary-100 text-secondary-700 font-bold flex items-center justify-center text-sm flex-shrink-0">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-secondary-900 truncate">{{ $item->outlet->name }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-xs text-secondary-500">{{ $item->total_prs }} PRs</span>
                                        <span class="text-xs text-secondary-300">•</span>
                                        <span class="text-xs font-medium text-primary-600">Rp {{ number_format($item->total_amount / 1000000, 1) }}M</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Pending Approvals - HANYA untuk Manager --}}
            @if($dashboardMode === 'manager' && $pendingApprovals->count() > 0)
                <div class="bg-amber-50 rounded-xl border border-amber-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-amber-200 bg-amber-100">
                        <h3 class="text-sm font-semibold text-amber-900 uppercase tracking-wide">Action Required</h3>
                        <p class="text-xs text-amber-700 mt-1">{{ $pendingApprovals->count() }} pending approvals</p>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($pendingApprovals as $pr)
                            <a href="{{ route('pr.show', $pr->id) }}" class="block p-3 bg-white rounded-lg border border-amber-200 hover:border-amber-300 hover:shadow-sm transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-secondary-900 truncate">{{ $pr->pr_number }}</p>
                                        <p class="text-xs text-secondary-500 mt-0.5">{{ $pr->outlet->name }}</p>
                                        <p class="text-xs font-medium text-primary-600 mt-1">
                                            Rp {{ number_format($pr->total, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Quick Tips untuk Staff --}}
            @if($dashboardMode === 'staff')
                <div class="bg-gradient-to-br from-primary-50 to-orange-light-100 rounded-xl border-2 border-primary-200 shadow-sm overflow-hidden p-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0 shadow-orange">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-secondary-900">Quick Tips</h4>
                            <p class="text-xs text-secondary-600 mt-0.5">Cara membuat PR yang baik</p>
                        </div>
                    </div>
                    <ul class="space-y-2">
                        <li class="flex items-start gap-2 text-xs">
                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-secondary-700">Upload <strong>invoice dari talent</strong> sebelum submit</span>
                        </li>
                        <li class="flex items-start gap-2 text-xs">
                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-secondary-700">Lengkapi <strong>data penerima transfer</strong></span>
                        </li>
                        <li class="flex items-start gap-2 text-xs">
                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-secondary-700">Pastikan <strong>tanda tangan digital</strong> sudah diupload</span>
                        </li>
                        <li class="flex items-start gap-2 text-xs">
                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-secondary-700">Draft dapat <strong>diedit kapan saja</strong></span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', initCharts);
        document.addEventListener('livewire:navigated', initCharts);

        function initCharts() {
            // Monthly Trend Chart
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                const monthlyData = @json($monthlyChartData);
                
                new Chart(monthlyCtx, {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Total',
                                data: monthlyData.map(d => d.total),
                                borderColor: '#f97316',
                                backgroundColor: 'rgba(249, 115, 22, 0.05)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Approved',
                                data: monthlyData.map(d => d.approved),
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.05)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Status Doughnut Chart
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
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    usePointStyle: true
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }
    </script>
</div>