<div class="space-y-8">

    {{-- ========================================================= --}}
    {{-- FLASH MESSAGES --}}
    {{-- ========================================================= --}}
    @if (session('success'))
        <div class="flex items-center gap-3 p-3 rounded-lg border border-green-200 bg-green-50 text-green-700 shadow-soft">
            <x-icon name="check" class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium leading-tight">
                {{ session('success') }}
            </span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-3 rounded-lg border border-red-200 bg-red-50 text-red-700 shadow-soft">
            <x-icon name="x" class="w-5 h-5 text-red-600" />
            <span class="text-sm font-medium leading-tight">
                {{ session('error') }}
            </span>
        </div>
    @endif

    {{-- ========================================================= --}}
    {{-- TOP STAT CARDS --}}
    {{-- ========================================================= --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- PENDING --}}
        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-secondary-500">Pending Approval</p>
                <h3 class="text-3xl font-bold text-secondary-900 mt-1">{{ $stats['pending'] }}</h3>
                <p class="text-xs text-amber-600 flex items-center gap-1 mt-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu persetujuan
                </p>
            </div>

            <div class="w-12 h-12 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                <x-icon name="clock" class="w-6 h-6" />
            </div>
        </div>

        {{-- APPROVED TODAY --}}
        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-secondary-500">Approved Today</p>
                <h3 class="text-3xl font-bold text-secondary-900 mt-1">{{ $stats['approved_today'] }}</h3>
                <p class="text-xs text-green-600 flex items-center gap-1 mt-2">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span> Hari ini
                </p>
            </div>

            <div class="w-12 h-12 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                <x-icon name="check" class="w-6 h-6" />
            </div>
        </div>

        {{-- TOTAL AMOUNT --}}
        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-medium text-secondary-500">Total Amount Pending</p>
                <h3 class="text-2xl font-bold text-secondary-900 mt-1">
                    Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                </h3>
                <p class="text-xs text-secondary-500 mt-2">Pending approval</p>
            </div>

            <div class="w-12 h-12 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                <x-icon name="credit-card" class="w-6 h-6" />
            </div>
        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- FILTERS --}}
    {{-- ========================================================= --}}
    <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5 space-y-4">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- SEARCH --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Search</label>
                <input 
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="input bg-white border-secondary-200 focus:border-primary-400"
                    placeholder="PR number, perihal..."
                >
            </div>

            {{-- OUTLET --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Outlet</label>
                <select wire:model.live="outletFilter"
                    class="input bg-white border-secondary-200 focus:border-primary-400">
                    <option value="">Semua Outlet</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- DATE FROM --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Dari Tanggal</label>
                <input type="date" 
                    wire:model.live="dateFrom"
                    class="input bg-white border-secondary-200 focus:border-primary-400">
            </div>

            {{-- DATE TO --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Sampai Tanggal</label>
                <input type="date" 
                    wire:model.live="dateTo"
                    class="input bg-white border-secondary-200 focus:border-primary-400">
            </div>

        </div>

        <div class="flex justify-end">
            <button 
                wire:click="resetFilters"
                class="flex items-center gap-1.5 px-3 py-2 text-xs rounded-lg border border-secondary-200 text-secondary-700 hover:bg-secondary-100/60 transition"
            >
                <x-icon name="refresh" class="w-4 h-4" />
                Reset Filter
            </button>
        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- APPROVAL TABLE --}}
    {{-- ========================================================= --}}
    <div class="rounded-xl border border-secondary-200 bg-white shadow-soft">

        <div class="overflow-x-auto rounded-xl">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">
                            <input type="checkbox"
                                wire:model.live="selectAll"
                                class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                            >
                        </th>
                        <th>PR Number</th>
                        <th>Tanggal</th>
                        <th>Perihal</th>
                        <th>Outlet</th>
                        <th>Total</th>
                        <th>Created By</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pendingPrs as $pr)
                        <tr>
                            <td>
                                <input type="checkbox"
                                    wire:model.live="selectedPrs"
                                    value="{{ $pr->id }}"
                                    class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                                >
                            </td>

                            <td class="font-medium text-primary-600">
                                <a href="{{ route('pr.show', $pr->id) }}" class="hover:underline">
                                    {{ $pr->pr_number }}
                                </a>
                            </td>

                            <td>{{ $pr->tanggal->format('d M Y') }}</td>

                            <td class="max-w-xs truncate">{{ $pr->perihal }}</td>

                            <td>{{ $pr->outlet->name }}</td>

                            <td class="font-semibold">
                                Rp {{ number_format($pr->total, 0, ',', '.') }}
                            </td>

                            <td class="text-sm">{{ $pr->creator->name }}</td>

                            <td class="text-right">
                                <div class="flex justify-end items-center gap-2">

                                    {{-- VIEW --}}
                                    <a href="{{ route('pr.show', $pr->id) }}"
                                        class="text-secondary-600 hover:text-primary-600 p-1"
                                        title="Detail">
                                        <x-icon name="eye" class="w-5 h-5" />
                                    </a>

                                    {{-- APPROVE --}}
                                    <button 
                                        wire:click="approvePr({{ $pr->id }})"
                                        wire:confirm="Approve PR {{ $pr->pr_number }}?"
                                        class="text-green-600 hover:text-green-800 p-1"
                                        title="Approve">
                                        <x-icon name="check" class="w-5 h-5" />
                                    </button>

                                    {{-- REJECT --}}
                                    <button 
                                        wire:click="openRejectModal({{ $pr->id }})"
                                        class="text-red-600 hover:text-red-800 p-1"
                                        title="Reject">
                                        <x-icon name="x" class="w-5 h-5" />
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <x-icon name="circle-x" class="w-14 h-14 mx-auto text-secondary-300" />
                                <p class="font-semibold text-secondary-700 mt-2">Tidak ada PR pending</p>
                                <p class="text-sm text-secondary-500">Semua PR sudah diproses</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingPrs->hasPages())
            <div class="px-6 py-4 border-t border-secondary-200">
                {{ $pendingPrs->links() }}
            </div>
        @endif
    </div>

    {{-- REJECT MODAL --}}
    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">

            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-red-200 bg-red-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-red-700">Reject Purchase Requisition</h3>
                    <p class="text-xs text-red-500 mt-1">Berikan alasan penolakan PR ini</p>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="rejectPr" class="p-6 space-y-5">

                    {{-- Input --}}
                    <div>
                        <label class="text-sm font-medium text-secondary-800">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>

                        <textarea 
                            wire:model="rejectionNote"
                            rows="4"
                            class="mt-2 input bg-white border-secondary-300 focus:border-red-400 @error('rejectionNote') input-error @enderror"
                            placeholder="Jelaskan alasan penolakan (minimal 10 karakter)"
                        ></textarea>

                        @error('rejectionNote')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="mt-1 text-xs text-secondary-500">
                            {{ strlen($rejectionNote) }}/500 karakter
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeRejectModal"
                            class="w-full text-center px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 hover:bg-secondary-100 transition">
                            Batal
                        </button>

                        <button 
                            type="submit"
                            class="w-full text-center px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                            Reject PR
                        </button>
                    </div>
                </form>
            </div>

        </div>
    @endif

    {{-- APPROVE MODAL --}}
    @if($showApproveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">

            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">

                {{-- Header --}}
                <div class="px-6 py-4 border-b border-primary-200 bg-primary-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-primary-700">Approve Purchase Requisition</h3>
                    <p class="text-xs text-primary-500 mt-1">Upload tanda tangan untuk persetujuan PR</p>
                </div>

                {{-- Form --}}
                <form wire:submit.prevent="approvePr" class="p-6 space-y-5">

                    {{-- Upload Signature --}}
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-secondary-800">
                            Upload Tanda Tangan <span class="text-red-500">*</span>
                        </label>

                        <div 
                            class="border border-secondary-300 rounded-lg p-4 bg-white flex flex-col items-center justify-center text-center cursor-pointer 
                                hover:border-primary-400 transition"
                        >
                            @if ($managerSignature)
                                <img src="{{ $managerSignature->temporaryUrl() }}"
                                    class="h-28 object-contain rounded-md shadow-sm" />
                            @else
                                <x-icon name="upload" class="w-10 h-10 text-secondary-400 mb-2" />
                                <p class="text-xs text-secondary-500">Drag & drop or click to upload</p>
                            @endif

                            <input type="file" 
                                wire:model="managerSignature" 
                                class="hidden" id="signatureUpload" />
                        </div>

                        @error('managerSignature')
                            <p class="text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-secondary-500">
                            Format: JPG/PNG — Maks 2MB
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeApproveModal"
                            class="w-full text-center px-4 py-2 rounded-lg border border-secondary-300 text-secondary-700 hover:bg-secondary-100 transition">
                            Batal
                        </button>

                        <button 
                            type="submit"
                            class="w-full text-center px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                            Approve PR
                        </button>
                    </div>

                </form>

            </div>

        </div>
    @endif
</div>