<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert-success mb-6 animate-fade-in">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert-danger mb-6 animate-fade-in">
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
                        <h3 class="text-xl font-bold text-white">{{ $pr->pr_number }}</h3>
                        <p class="text-sm text-primary-100 mt-1">{{ $pr->perihal }}</p>
                    </div>
                    <div>
                        @if($pr->status === 'draft')
                            <span class="badge badge-light text-base">Draft</span>
                        @elseif($pr->status === 'submitted')
                            <span class="badge badge-warning text-base">Submitted</span>
                        @elseif($pr->status === 'approved')
                            <span class="badge badge-success text-base">Approved</span>
                        @elseif($pr->status === 'paid')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">Paid</span>
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
                    @if($pr->approved_at)
                        <div class="mt-4 pt-4 border-t border-secondary-200">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">
                                        {{ $pr->status === 'rejected' ? 'Rejected By' : 'Approved By' }}
                                    </p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->approver->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">
                                        {{ $pr->status === 'rejected' ? 'Rejected At' : 'Approved At' }}
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

                    <!-- Payment Info -->
                    @if($pr->isPaid())
                        <div class="mt-4 pt-4 border-t border-secondary-200">
                            <h4 class="font-bold text-secondary-900 mb-3">Informasi Pembayaran</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">Tanggal Transfer</p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->payment_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">Jumlah</p>
                                    <p class="font-semibold text-primary-600">Rp {{ number_format($pr->payment_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">Bank</p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->payment_bank }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary-600 mb-1">No. Rekening</p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->payment_account_number }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-sm text-secondary-600 mb-1">Nama Penerima</p>
                                    <p class="font-semibold text-secondary-900">{{ $pr->payment_account_name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Recipient Info -->
                    @if($pr->hasRecipientInfo())
                    <div class="mt-4 pt-4 border-t border-secondary-200">
                    <h4 class="font-bold text-secondary-900 mb-3">Informasi Penerima Transfer</h4>
                    <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-secondary-600 mb-1">Nama Penerima</p>
                        <p class="font-semibold text-secondary-900">{{ $pr->recipient_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600 mb-1">Bank</p>
                        <p class="font-semibold text-secondary-900">{{ $pr->recipient_bank }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-600 mb-1">No. Rekening</p>
                        <p class="font-semibold text-secondary-900">{{ $pr->recipient_account_number }}</p>
                    </div>
                    @if($pr->recipient_phone)
                        <div>
                            <p class="text-sm text-secondary-600 mb-1">No. Telepon</p>
                            <p class="font-semibold text-secondary-900">{{ $pr->recipient_phone }}</p>
                        </div>
                    @endif
                    </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Invoices Section -->
            @if($pr->invoices->count() > 0)
            <div class="card animate-fade-in" style="animation-delay: 0.05s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Invoice dari Talent ({{ $pr->invoices->count() }})</h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($pr->invoices as $invoice)
                            <div class="border border-secondary-200 rounded-lg p-4 hover:border-primary-300 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-secondary-900 truncate">{{ $invoice->file_name }}</p>
                                        <p class="text-xs text-secondary-500 mt-1">
                                            {{ $invoice->getFileSizeFormatted() }} • 
                                            {{ $invoice->created_at->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-secondary-500">by {{ $invoice->uploader->name }}</p>
                                    </div>
                                    @if($invoice->isImage())
                                        <div class="w-8 h-8 bg-primary-100 rounded flex items-center justify-center ml-2 flex-shrink-0">
                                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-red-100 rounded flex items-center justify-center ml-2 flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <button 
                                    wire:click="downloadInvoice({{ $invoice->id }})"
                                    class="btn-outline w-full text-sm py-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Download
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Staff Signature Section -->
            @if($pr->hasStaffSignature())
            <div class="card animate-fade-in" style="animation-delay: 0.075s;">
            <div class="card-header">
            <h3 class="text-lg font-bold text-white">Tanda Tangan Staff</h3>
            </div>
            <div class="card-body">
            <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-secondary-900">Tanda Tangan Staff Tersedia</p>
                    <p class="text-sm text-secondary-600">Ditandatangani oleh {{ $pr->creator->name }}</p>
                    <p class="text-xs text-secondary-500">{{ $pr->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <button 
                wire:click="downloadStaffSignature"
                class="btn-outline"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download
            </button>
            </div>
            </div>
            </div>
            @endif

            <!-- Manager Signature -->
            @if($pr->hasManagerSignature())
            <div class="card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Tanda Tangan Manager</h3>
                </div>
                <div class="card-body">
                    <div class="flex items-center justify-between p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-secondary-900">Signature Manager Tersedia</p>
                                <p class="text-sm text-secondary-600">Ditandatangani oleh {{ $pr->approver->name }}</p>
                                <p class="text-xs text-secondary-500">{{ $pr->approved_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <button 
                            wire:click="downloadManagerSignature"
                            class="btn-outline"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Payment Proof -->
            @if($pr->hasPaymentProof())
            <div class="card animate-fade-in" style="animation-delay: 0.15s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Bukti Transfer</h3>
                </div>
                <div class="card-body">
                    <div class="flex items-center justify-between p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-secondary-900">Bukti Transfer Tersedia</p>
                                <p class="text-sm text-secondary-600">Transfer: Rp {{ number_format($pr->payment_amount, 0, ',', '.') }}</p>
                                <p class="text-xs text-secondary-500">{{ $pr->payment_uploaded_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <button 
                            wire:click="downloadPaymentProof"
                            class="btn-outline"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Download
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table -->
            <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Detail Item</h3>
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

            <!-- Timeline -->
            <div class="card animate-fade-in" style="animation-delay: 0.25s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Timeline</h3>
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
                                <p class="font-semibold text-secondary-900">PR Dibuat</p>
                                <p class="text-sm text-secondary-600">{{ $pr->created_at->format('d M Y, H:i') }}</p>
                                <p class="text-sm text-secondary-500">oleh {{ $pr->creator->name }}</p>
                            </div>
                        </div>

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
                                    <p class="font-semibold text-secondary-900">PR Disubmit</p>
                                    <p class="text-sm text-secondary-600">Menunggu approval Manager</p>
                                </div>
                            </div>
                        @endif

                        @if($pr->approved_at)
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full {{ $pr->status === 'rejected' ? 'bg-red-200' : 'bg-green-200' }} flex items-center justify-center">
                                        @if($pr->status === 'rejected')
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-secondary-900">
                                        PR {{ $pr->status === 'rejected' ? 'Ditolak' : 'Disetujui' }}
                                    </p>
                                    <p class="text-sm text-secondary-600">{{ $pr->approved_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm text-secondary-500">oleh {{ $pr->approver->name }}</p>
                                </div>
                            </div>
                        @endif

                        @if($pr->isPaid())
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full bg-blue-200 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-secondary-900">Transfer Selesai</p>
                                    <p class="text-sm text-secondary-600">{{ $pr->payment_uploaded_at->format('d M Y, H:i') }}</p>
                                    <p class="text-sm text-secondary-500">Rp {{ number_format($pr->payment_amount, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Actions -->
            <div class="card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Actions</h3>
                </div>
                <div class="card-body space-y-3">
                    <!-- Download DOCX (NEW) -->
                    @can('pr.download')
                        <a href="{{ route('pr.docx', $pr->id) }}" class="btn-primary w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Download DOCX 
                        </a>

                        <!-- PDF Download dengan Instruksi iLovePDF -->
                        <livewire:pdf-download-instructions :prId="$pr->id" />
                    @endcan

                    <!-- Submit for Approval (Draft Only) -->
                    @if($pr->isDraft() && ($pr->created_by === auth()->id() || auth()->user()->can('pr.edit')))
                        <button 
                            wire:click="submitForApproval" 
                            wire:confirm="Submit PR untuk approval?" 
                            class="btn-primary w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Submit untuk Approval
                        </button>
                    @endif

                    <!-- Edit (Draft Only) -->
                    @if($canEdit)
                        <a href="{{ route('pr.edit', $pr->id) }}" class="btn-secondary w-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit PR
                        </a>
                    @endif

                    <!-- Approve Button (Manager Only) -->
                    @if($canApprove)
                        <button 
                            wire:click="openApproveModal" 
                            class="btn-primary w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Approve PR
                        </button>

                        <button 
                            wire:click="openRejectModal" 
                            class="btn-danger w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Reject PR
                        </button>
                    @endif

                    <!-- Upload Payment Proof (Manager, only if Approved) -->
                    @if($canUploadPayment)
                        <button 
                            wire:click="openPaymentModal" 
                            class="btn-primary w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Upload Bukti Transfer
                        </button>
                    @endif

                    <!-- Delete (Draft Only) -->
                    @if($canDelete)
                        <button 
                            wire:click="deletePr" 
                            wire:confirm="Hapus PR ini? Tidak bisa dibatalkan!" 
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

            <!-- Summary Card -->
            <div class="card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="card-header">
                    <h3 class="text-lg font-bold text-white">Summary</h3>
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
                        @elseif($pr->status === 'paid')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-300">Paid</span>
                        @elseif($pr->status === 'rejected')
                            <span class="badge badge-danger">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    @if($showApproveModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full animate-slide-in">
                <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-t-xl">
                    <h3 class="text-lg font-bold">Approve Purchase Requisition</h3>
                    <p class="text-sm text-green-100 mt-1">Upload tanda tangan digital Anda</p>
                </div>
                
                <form wire:submit.prevent="approvePr" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            Upload Signature <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            wire:model="managerSignature"
                            accept="image/jpeg,image/jpg,image/png"
                            class="input @error('managerSignature') input-error @enderror"
                        >
                        @error('managerSignature')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-secondary-500">
                            Format: JPG, PNG (Max 2MB)
                        </p>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                        <p class="text-sm text-amber-800">
                            <strong>Perhatian:</strong> Dengan approve PR ini, Anda menyetujui pembelian senilai 
                            <strong>Rp {{ number_format($pr->total, 0, ',', '.') }}</strong>
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button 
                            type="button"
                            wire:click="closeApproveModal"
                            class="btn-secondary flex-1"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="btn-primary flex-1"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="approvePr">Approve PR</span>
                            <span wire:loading wire:target="approvePr">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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

    <!-- Payment Modal -->
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 animate-fade-in">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full animate-slide-in">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-t-xl">
                    <h3 class="text-lg font-bold">Upload Bukti Transfer</h3>
                    <p class="text-sm text-blue-100 mt-1">Lengkapi detail pembayaran</p>
                </div>
                
                <form wire:submit.prevent="uploadPaymentProof" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Tanggal Transfer <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="paymentDate"
                                class="input @error('paymentDate') input-error @enderror"
                            >
                            @error('paymentDate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Jumlah Transfer <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                wire:model="paymentAmount"
                                class="input @error('paymentAmount') input-error @enderror"
                                step="0.01"
                            >
                            @error('paymentAmount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            Bank <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="paymentBank"
                            class="input @error('paymentBank') input-error @enderror"
                            placeholder="BCA, Mandiri, BNI, etc"
                        >
                        @error('paymentBank')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            No. Rekening Penerima <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="paymentAccountNumber"
                            class="input @error('paymentAccountNumber') input-error @enderror"
                            placeholder="1234567890"
                        >
                        @error('paymentAccountNumber')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            Nama Penerima <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="paymentAccountName"
                            class="input @error('paymentAccountName') input-error @enderror"
                            placeholder="John Doe (Talent)"
                        >
                        @error('paymentAccountName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary-900 mb-2">
                            Upload Bukti Transfer <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="file" 
                            wire:model="paymentProof"
                            accept="image/jpeg,image/jpg,image/png,application/pdf"
                            class="input @error('paymentProof') input-error @enderror"
                        >
                        @error('paymentProof')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-secondary-500">
                            Format: JPG, PNG, PDF (Max 5MB)
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button 
                            type="button"
                            wire:click="closePaymentModal"
                            class="btn-secondary flex-1"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="btn-primary flex-1"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="uploadPaymentProof">Upload</span>
                            <span wire:loading wire:target="uploadPaymentProof">Uploading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>