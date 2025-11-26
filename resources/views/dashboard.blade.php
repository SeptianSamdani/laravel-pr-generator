@extends('components.layouts.app')

@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your purchase requisitions')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total PRs -->
        <div class="card animate-fade-in">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Total PRs</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">24</h3>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            +12% dari bulan lalu
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
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">8</h3>
                        <p class="text-xs text-amber-600 mt-2 flex items-center gap-1">
                            <div class="status-pending"></div>
                            Menunggu persetujuan
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
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">14</h3>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <div class="status-active"></div>
                            Telah disetujui
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
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">Rp 45.2M</h3>
                        <p class="text-xs text-secondary-500 mt-2">Bulan ini</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-secondary-700 to-secondary-900 flex items-center justify-center shadow-black">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent PRs -->
        <div class="lg:col-span-2 card animate-slide-in">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-lg font-bold">Recent Purchase Requisitions</h3>
                {{-- <a href="{{ route('pr.index') }}" class="text-sm text-primary-300 hover:text-white transition-colors">
                    View all →
                </a> --}}
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
                            <tr>
                                <td class="font-semibold text-primary-600">PR-20250125-0001</td>
                                <td>Sushi Mentai Kelapa Gading</td>
                                <td>25 Jan 2025</td>
                                <td class="font-semibold">Rp 888,500</td>
                                <td>
                                    <span class="badge-warning">Pending</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-semibold text-primary-600">PR-20250124-0003</td>
                                <td>Sushi Mentai Grand Indonesia</td>
                                <td>24 Jan 2025</td>
                                <td class="font-semibold">Rp 1,250,000</td>
                                <td>
                                    <span class="badge-success">Approved</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-semibold text-primary-600">PR-20250124-0002</td>
                                <td>Sushi Mentai PIK</td>
                                <td>24 Jan 2025</td>
                                <td class="font-semibold">Rp 750,000</td>
                                <td>
                                    <span class="badge-danger">Rejected</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-semibold text-primary-600">PR-20250123-0001</td>
                                <td>Sushi Mentai Pondok Indah</td>
                                <td>23 Jan 2025</td>
                                <td class="font-semibold">Rp 2,100,000</td>
                                <td>
                                    <span class="badge-success">Approved</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card animate-slide-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Quick Actions</h3>
                </div>
                <div class="card-body space-y-3">
                    {{-- @can('pr.create')
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
                    </a>
                    @endcan

                    <a href="{{ route('pr.index') }}" class="btn-outline w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        View All PRs
                    </a> --}}
                </div>
            </div>

            <!-- Status Overview -->
            <div class="card animate-slide-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Status Overview</h3>
                </div>
                <div class="card-body space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <span class="text-sm text-secondary-700">Pending</span>
                        </div>
                        <span class="text-sm font-bold text-secondary-900">8 PRs</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="text-sm text-secondary-700">Approved</span>
                        </div>
                        <span class="text-sm font-bold text-secondary-900">14 PRs</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="text-sm text-secondary-700">Rejected</span>
                        </div>
                        <span class="text-sm font-bold text-secondary-900">2 PRs</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-secondary-400"></div>
                            <span class="text-sm text-secondary-700">Draft</span>
                        </div>
                        <span class="text-sm font-bold text-secondary-900">0 PRs</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection