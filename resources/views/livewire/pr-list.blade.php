<div>
    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 p-3 border border-green-200 bg-green-50 rounded-lg shadow-soft mb-6">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium text-green-700">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-3 border border-red-200 bg-red-50 rounded-lg shadow-soft mb-6">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-sm font-medium text-red-700">
                {{ session('error') }}
            </span>
        </div>
    @endif


    <div class="space-y-6">
        {{-- Headers --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-secondary-900">Purchase Requisition</h3>
                <p class="text-sm text-secondary-600">
                    Total: {{ $purchaseRequisitions->total() }} records
                </p>
            </div>

            @can('pr.create')
                <a href="{{ route('pr.create') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-500 text-white text-sm hover:bg-primary-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New PR
                </a>
            @endcan
        </div>

        <!-- Filters -->
        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Search --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Search</label>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        class="input bg-white border-secondary-200 focus:border-primary-400"
                        placeholder="PR number, perihal..."
                    >
                </div>

                {{-- Status --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Status</label>
                    <select wire:model.live="statusFilter" 
                        class="input bg-white border-secondary-200 focus:border-primary-400">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="paid">Paid</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                {{-- Outlet --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">Outlet</label>
                    <select wire:model.live="outletFilter" 
                        class="input bg-white border-secondary-200 focus:border-primary-400">
                        <option value="">All Outlets</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date From --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">From</label>
                    <input type="date" 
                        wire:model.live="dateFrom"
                        class="input bg-white border-secondary-200 focus:border-primary-400">
                </div>

                {{-- Date To --}}
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-medium text-secondary-700">To</label>
                    <input type="date" 
                        wire:model.live="dateTo"
                        class="input bg-white border-secondary-200 focus:border-primary-400">
                </div>

            </div>

            {{-- Reset --}}
            <div class="mt-4 flex justify-end">
                <button wire:click="resetFilters"
                    class="flex items-center gap-1.5 text-xs px-3 py-2 rounded-lg border border-secondary-200 
                        text-secondary-700 hover:bg-secondary-100/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="card">
            <div class="card-body p-0">
                <!-- Loading Indicator -->
                <div wire:loading class="absolute inset-0 bg-white/70 flex items-center justify-center z-10">
                    <div class="flex items-center gap-2 text-primary-600">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="font-semibold">Loading...</span>
                    </div>
                </div>
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
                                        @elseif($pr->status === 'paid')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">Paid</span>
                                        @elseif($pr->status === 'rejected')
                                            <span class="badge badge-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">{{ $pr->creator->name }}</td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <!-- View Detail (untuk semua status) -->
                                            <a 
                                                href="{{ route('pr.show', $pr->id) }}"
                                                class="text-blue-600 hover:text-blue-800 p-1"
                                                title="View Detail"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
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