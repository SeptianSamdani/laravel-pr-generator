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

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-secondary-900">Daftar Purchase Requisition</h3>
                <p class="text-sm text-secondary-600">Total: {{ $purchaseRequisitions->total() }} PR</p>
            </div>
            @can('pr.create')
                <a href="{{ route('pr.create') }}" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New PR
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">Status</label>
                        <select wire:model.live="statusFilter" class="input">
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
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
                                <th>PR Number</th>
                                <th>Tanggal</th>
                                <th>Perihal</th>
                                <th>Outlet</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseRequisitions as $pr)
                                <tr wire:key="pr-{{ $pr->id }}">
                                    <td class="font-semibold text-primary-600">
                                        <a href="{{ route('pr.show', $pr->id) }}" class="hover:underline">
                                            {{ $pr->pr_number }}
                                        </a>
                                    </td>
                                    <td>{{ $pr->tanggal->format('d M Y') }}</td>
                                    <td class="max-w-xs truncate">{{ $pr->perihal }}</td>
                                    <td>{{ $pr->outlet->name }}</td>
                                    <td class="font-semibold">Rp {{ number_format($pr->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($pr->status === 'draft')
                                            <span class="badge badge-light">Draft</span>
                                        @elseif($pr->status === 'submitted')
                                            <span class="badge badge-warning">Submitted</span>
                                        @elseif($pr->status === 'approved')
                                            <span class="badge badge-success">Approved</span>
                                        @elseif($pr->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">{{ $pr->creator->name }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <!-- Edit (only for draft) -->
                                            @if($pr->status === 'draft')
                                                @can('pr.edit')
                                                    <a 
                                                        href="{{ route('pr.edit', $pr->id) }}"
                                                        class="text-secondary-600 hover:text-primary-600 p-1"
                                                        title="Edit"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </a>
                                                @endcan

                                                <!-- Delete (only for draft) -->
                                                @can('pr.delete')
                                                    <button 
                                                        wire:click="deletePr({{ $pr->id }})"
                                                        wire:confirm="Apakah Anda yakin ingin menghapus PR ini?"
                                                        class="text-red-600 hover:text-red-800 p-1"
                                                        title="Delete"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-12">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="font-semibold text-secondary-700">Tidak ada data</p>
                                        <p class="text-sm text-secondary-500 mt-1">Belum ada purchase requisition yang dibuat</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($purchaseRequisitions->hasPages())
                    <div class="px-6 py-4 border-t border-secondary-200">
                        {{ $purchaseRequisitions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>