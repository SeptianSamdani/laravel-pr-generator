<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert-success mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert-danger mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Pending PRs -->
        <div class="card animate-fade-in">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Pending Approval</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ $stats['pending'] }}</h3>
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

        <!-- Approved Today -->
        <div class="card animate-fade-in" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Approved Today</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ $stats['approved_today'] }}</h3>
                        <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
                            <div class="status-active"></div>
                            Hari ini
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

        <!-- Total Amount Pending -->
        <div class="card animate-fade-in" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600">Total Amount Pending</p>
                        <h3 class="text-2xl font-bold text-secondary-900 mt-2">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                        <p class="text-xs text-secondary-500 mt-2">Pending approval</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-orange">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-secondary-900">Purchase Requisition Approvals</h3>
                <p class="text-sm text-secondary-600">Review dan setujui PR yang sedang pending</p>
            </div>
            @if(count($selectedPrs) > 0)
                <button 
                    wire:click="bulkApprove"
                    wire:confirm="Apakah Anda yakin ingin approve {{ count($selectedPrs) }} PR sekaligus?"
                    class="btn-primary"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approve {{ count($selectedPrs) }} PR
                </button>
            @endif
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">Search</label>
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            class="input"
                            placeholder="PR number, perihal..."
                        >
                    </div>

                    <!-- Outlet Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">Outlet</label>
                        <select wire:model.live="outletFilter" class="input">
                            <option value="">Semua Outlet</option>
                            @foreach($outlets as $outlet)
                                <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">Dari Tanggal</label>
                        <input 
                            type="date" 
                            wire:model.live="dateFrom"
                            class="input"
                        >
                    </div>

                    <!-- Date To -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">Sampai Tanggal</label>
                        <input 
                            type="date" 
                            wire:model.live="dateTo"
                            class="input"
                        >
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button wire:click="resetFilters" class="btn-ghost text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="w-12">
                                    <input 
                                        type="checkbox" 
                                        wire:model.live="selectAll"
                                        class="w-4 h-4 text-primary-600 bg-white border-secondary-300 rounded focus:ring-primary-500"
                                    >
                                </th>
                                <th>PR Number</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>Outlet</th>
                                <th>Total</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingPrs as $pr)
                                <tr wire:key="pr-{{ $pr->id }}">
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            wire:model.live="selectedPrs" 
                                            value="{{ $pr->id }}"
                                            class="w-4 h-4 text-primary-600 bg-white border-secondary-300 rounded focus:ring-primary-500"
                                        >
                                    </td>
                                    <td class="font-semibold text-primary-600">
                                        <a href="{{ route('pr.show', $pr->id) }}" class="hover:underline">
                                            {{ $pr->pr_number }}
                                        </a>
                                    </td>
                                    <td>{{ $pr->tanggal->format('d M Y') }}</td>
                                    <td class="max-w-xs truncate">{{ $pr->perihal }}</td>
                                    <td>{{ $pr->outlet->name }}</td>
                                    <td class="font-semibold">Rp {{ number_format($pr->total, 0, ',', '.') }}</td>
                                    <td class="text-sm">{{ $pr->creator->name }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <!-- View Detail -->
                                            <a 
                                                href="{{ route('pr.show', $pr->id) }}"
                                                class="text-secondary-600 hover:text-primary-600 p-1"
                                                title="View Detail"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>

                                            <!-- Approve -->
                                            <button 
                                                wire:click="approvePr({{ $pr->id }})"
                                                wire:confirm="Apakah Anda yakin ingin approve PR {{ $pr->pr_number }}?"
                                                class="text-green-600 hover:text-green-800 p-1"
                                                title="Approve"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>

                                            <!-- Reject -->
                                            <button 
                                                wire:click="openRejectModal({{ $pr->id }})"
                                                class="text-red-600 hover:text-red-800 p-1"
                                                title="Reject"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="font-semibold text-secondary-700">Tidak ada PR pending</p>
                                        <p class="text-sm text-secondary-500 mt-1">Semua PR sudah diproses</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($pendingPrs->hasPages())
                    <div class="px-6 py-4 border-t border-secondary-200">
                        {{ $pendingPrs->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    @if($showRejectModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full animate-slide-in">
                <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-t-xl">
                    <h3 class="text-lg font-bold">Reject Purchase Requisition</h3>
                    <p class="text-sm text-red-100 mt-1">Berikan alasan penolakan PR ini</p>
                </div>
                
                <form wire:submit.prevent="rejectPr" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            wire:model="rejectionNote"
                            rows="4"
                            class="input @error('rejectionNote') input-error @enderror"
                            placeholder="Jelaskan alasan mengapa PR ini ditolak (minimal 10 karakter)"
                        ></textarea>
                        @error('rejectionNote')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-secondary-500">
                            {{ strlen($rejectionNote) }}/500 karakter
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button 
                            type="button"
                            wire:click="closeRejectModal"
                            class="btn-secondary flex-1"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="btn-danger flex-1"
                        >
                            Reject PR
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>