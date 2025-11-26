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

    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('pr.index') }}" class="inline-flex items-center text-secondary-600 hover:text-primary-600 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Card -->
            <div class="card animate-fade-in">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold">{{ $pr->pr_number }}</h3>
                        <p class="text-sm text-secondary-600 mt-1">{{ $pr->perihal }}</p>
                    </div>
                    <div>
                        @if($pr->status === 'draft')
                            <span class="badge badge-light text-base">Draft</span>
                        @elseif($pr->status === 'submitted')
                            <span class="badge badge-warning text-base">Submitted</span>
                        @elseif($pr->status === 'approved')
                            <span class="badge badge-success text-base">Approved</span>
                        @elseif($pr->status === 'rejected')
                            <span class="badge badge-danger text-base">Rejected</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-secondary-600 mb-1">Tanggal</p>
                            <p class="font-semibold text-secondary-900">{{ $pr->tanggal->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary-600 mb-1">Outlet</p>
                            <p class="font-semibold text-secondary-900">{{ $pr->outlet->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary-600 mb-1">Created By</p>
                            <p class="font-semibold text-secondary-900">{{ $pr->creator->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-secondary-600 mb-1">Created At</p>
                            <p class="font-semibold text-secondary-900">{{ $pr->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($pr->alasan)
                            <div class="col-span-2">
                                <p class="text-sm text-secondary-600 mb-1">Alasan</p>
                                <p class="text-secondary-900">{{ $pr->alasan }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Approval Info -->
                    @if($pr->status === 'approved' || $pr->status === 'rejected')
                        <div class="mt-4 pt-4 border-t border-secondary-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">
                                        {{ $pr->status === 'approved' ? 'Approved By' : 'Rejected By' }}
                                    </p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->approver->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">
                                        {{ $pr->status === 'approved' ? 'Approved At' : 'Rejected At' }}
                                    </p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->approved_at->format('d M Y H:i') }}</p>
                                </div>
                                @if($pr->status === 'rejected' && $pr->rejection_note)
                                    <div class="col-span-2">
                                        <p class="text-sm text-secondary-600 mb-1">Alasan Penolakan</p>
                                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                            <p class="text-red-800">{{ $pr->rejection_note }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Detail Item</h3>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="w-12">No</th>
                                    <th>Nama Item</th>
                                    <th class="w-24 text-center">Jumlah</th>
                                    <th class="w-24 text-center">Satuan</th>
                                    <th class="w-40 text-right">Harga</th>
                                    <th class="w-40 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pr->items as $item)
                                    <tr>
                                        <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                        <td class="font-medium">{{ $item->nama_item }}</td>
                                        <td class="text-center">{{ $item->jumlah }}</td>
                                        <td class="text-center">{{ $item->satuan }}</td>
                                        <td class="text-right">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="text-right font-semibold text-primary-600">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                                <!-- Total Row -->
                                <tr class="bg-orange-light-100">
                                    <td colspan="5" class="text-right font-bold uppercase text-secondary-900">
                                        Grand Total
                                    </td>
                                    <td class="text-right font-bold text-primary-600 text-lg">
                                        Rp {{ number_format($pr->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Timeline</h3>
                </div>
                <div class="card-body">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-secondary-200 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-secondary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-secondary-900">PR Created</p>
                                <p class="text-sm text-secondary-600">{{ $pr->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-secondary-500">by {{ $pr->creator->name }}</p>
                            </div>
                        </div>

                        <!-- Submitted -->
                        @if($pr->status !== 'draft')
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-amber-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-secondary-900">PR Submitted</p>
                                    <p class="text-sm text-secondary-600">Waiting for approval</p>
                                </div>
                            </div>
                        @endif

                        <!-- Approved/Rejected -->
                        @if($pr->status === 'approved' || $pr->status === 'rejected')
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full {{ $pr->status === 'approved' ? 'bg-green-200' : 'bg-red-200' }} flex items-center justify-center">
                                        @if($pr->status === 'approved')
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-secondary-900">
                                        PR {{ $pr->status === 'approved' ? 'Approved' : 'Rejected' }}
                                    </p>
                                    <p class="text-sm text-secondary-600">{{ $pr->approved_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm text-secondary-500">by {{ $pr->approver->name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Actions</h3>
                </div>
                <div class="card-body space-y-3">
                    <!-- Download PDF -->
                    @can('pr.download')
                        <button wire:click="downloadPdf" class="btn-primary w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download PDF
                        </button>
                    @endcan

                    <!-- Submit for Approval (if draft) -->
                    @if($pr->status === 'draft' && ($pr->created_by === auth()->id() || auth()->user()->can('pr.edit')))
                        <button 
                            wire:click="submitForApproval"
                            wire:confirm="Apakah Anda yakin ingin submit PR ini untuk approval?"
                            class="btn-primary w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Submit untuk Approval
                        </button>
                    @endif

                    <!-- Edit (if draft) -->
                    @if($canEdit)
                        <a href="{{ route('pr.edit', $pr->id) }}" class="btn-secondary w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit PR
                        </a>
                    @endif

                    <!-- Approve (if submitted and can approve) -->
                    @if($canApprove)
                        <button 
                            wire:click="approvePr"
                            wire:confirm="Apakah Anda yakin ingin menyetujui PR ini?"
                            class="btn-primary w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Approve PR
                        </button>

                        <button 
                            wire:click="rejectPr('Rejected by manager')"
                            wire:confirm="Apakah Anda yakin ingin menolak PR ini?"
                            class="btn-danger w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reject PR
                        </button>
                    @endif

                    <!-- Delete (if draft) -->
                    @if($canDelete)
                        <button 
                            wire:click="deletePr"
                            wire:confirm="Apakah Anda yakin ingin menghapus PR ini? Tindakan ini tidak dapat dibatalkan."
                            class="btn-danger w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete PR
                        </button>
                    @endif
                </div>
            </div>

            <!-- Summary -->
            <div class="card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold">Summary</h3>
                </div>
                <div class="card-body space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-secondary-200">
                        <span class="text-sm text-secondary-600">Total Items</span>
                        <span class="font-bold text-secondary-900">{{ $pr->items->count() }} items</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-secondary-200">
                        <span class="text-sm text-secondary-600">Grand Total</span>
                        <span class="font-bold text-primary-600 text-xl">
                            Rp {{ number_format($pr->total, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-secondary-600">Status</span>
                        @if($pr->status === 'draft')
                            <span class="badge badge-light">Draft</span>
                        @elseif($pr->status === 'submitted')
                            <span class="badge badge-warning">Submitted</span>
                        @elseif($pr->status === 'approved')
                            <span class="badge badge-success">Approved</span>
                        @elseif($pr->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>