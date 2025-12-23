<div>
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="alert-success mb-6">
            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold">Berhasil!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Form -->
    <form wire:submit.prevent="submitForApproval">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Header Information -->
                <div class="card animate-fade-in">
                    <div class="card-header">
                        <h3 class="text-primary-50 text-lg font-bold">Informasi Dasar</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                wire:model="tanggal"
                                class="input @error('tanggal') input-error @enderror"
                            >
                            @error('tanggal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Perihal -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Perihal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="perihal"
                                class="input @error('perihal') input-error @enderror"
                                placeholder="Jakarta Food Bangers & IG Ads"
                            >
                            @error('perihal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Outlet -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Outlet <span class="text-red-500">*</span>
                            </label>
                            <select 
                                wire:model="outlet_id"
                                class="input @error('outlet_id') input-error @enderror"
                            >
                                <option value="">Pilih Outlet</option>
                                @foreach($outlets as $outlet)
                                    <option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
                                @endforeach
                            </select>
                            @error('outlet_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alasan -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Alasan
                            </label>
                            <textarea 
                                wire:model="alasan"
                                rows="3"
                                class="input @error('alasan') input-error @enderror"
                                placeholder="Opsional: Jelaskan alasan pembelian"
                            ></textarea>
                            @error('alasan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Invoice & Staff Signature Upload Section -->
                <div class="card animate-fade-in" style="animation-delay: 0.05s;">
                    <div class="card-header">
                        <h3 class="text-primary-50 text-lg font-bold">Upload Invoice & Tanda Tangan</h3>
                        <p class="text-xs text-primary-100 mt-1">Upload invoice dari talent dan tanda tangan staff</p>
                    </div>
                    <div class="card-body space-y-6">
                        
                        <!-- INVOICES SECTION -->
                        <div class="border-b border-secondary-200 pb-6">
                            <h4 class="text-sm font-bold text-secondary-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                </svg>
                                Invoice dari Talent
                            </h4>

                            <!-- Existing Invoices (Edit Mode) -->
                            @if($isEdit && !empty($existingInvoices))
                                <div class="mb-4">
                                    <p class="text-xs font-medium text-secondary-600 mb-2">Invoice yang sudah diupload:</p>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($existingInvoices as $invoice)
                                            <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    @if(str_contains($invoice['file_type'], 'image'))
                                                        <div class="w-8 h-8 bg-primary-100 rounded flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="w-8 h-8 bg-red-100 rounded flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-medium text-secondary-900">{{ $invoice['file_name'] }}</p>
                                                        <p class="text-xs text-secondary-500">{{ number_format($invoice['file_size'] / 1024, 0) }} KB</p>
                                                    </div>
                                                </div>
                                                <button 
                                                    type="button"
                                                    wire:click="removeExistingInvoice({{ $invoice['id'] }})"
                                                    wire:confirm="Hapus invoice ini?"
                                                    class="text-red-600 hover:text-red-800 p-2"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Upload New Invoices -->
                            <div>
                                <label class="block text-xs font-medium text-secondary-700 mb-2">
                                    Upload Invoice Baru 
                                    @if($status === 'submitted')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="invoices"
                                    multiple
                                    accept="image/jpeg,image/jpg,image/png,application/pdf"
                                    class="input text-sm @error('invoices') input-error @enderror @error('invoices.*') input-error @enderror"
                                >
                                @error('invoices')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                @error('invoices.*')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <!-- Upload Progress -->
                                <div wire:loading wire:target="invoices" class="mt-2">
                                    <div class="flex items-center gap-2 text-primary-600">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs">Uploading files...</span>
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-secondary-500">
                                    Format: JPG, PNG, PDF • Max 5 files • 5MB per file
                                </p>
                            </div>

                            <!-- Preview New Invoices -->
                            @if(!empty($invoices))
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-secondary-600 mb-2">File yang akan diupload:</p>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach($invoices as $invoice)
                                            <div class="flex items-center justify-between p-2 bg-blue-50 border border-blue-200 rounded text-xs">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="text-secondary-900">{{ $invoice->getClientOriginalName() }}</span>
                                                </div>
                                                <span class="text-secondary-500">{{ number_format($invoice->getSize() / 1024, 0) }} KB</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- STAFF SIGNATURE SECTION -->
                        <div>
                            <h4 class="text-sm font-bold text-secondary-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                Tanda Tangan Staff
                            </h4>

                            <!-- Existing Staff Signature (Edit Mode) -->
                            @if($isEdit && !empty($existingStaffSignature))
                                <div class="mb-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-xs font-medium text-green-800">Tanda tangan sudah diupload</span>
                                    </div>
                                    <p class="text-xs text-green-600">Upload baru untuk mengganti tanda tangan yang ada</p>
                                </div>
                            @endif

                            <!-- Upload Staff Signature -->
                            <div>
                                <label class="block text-xs font-medium text-secondary-700 mb-2">
                                    Upload Tanda Tangan
                                    @if($status === 'submitted')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input 
                                    type="file" 
                                    wire:model="staffSignature"
                                    accept="image/jpeg,image/jpg,image/png"
                                    class="input text-sm @error('staffSignature') input-error @enderror"
                                >
                                @error('staffSignature')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                
                                <!-- Upload Progress -->
                                <div wire:loading wire:target="staffSignature" class="mt-2">
                                    <div class="flex items-center gap-2 text-primary-600">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-xs">Uploading signature...</span>
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-secondary-500">
                                    Format: JPG, PNG • Max 2MB • Scan/foto tanda tangan Anda
                                </p>
                            </div>

                            <!-- Preview New Staff Signature -->
                            @if($staffSignature)
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-xs font-medium text-blue-800 mb-2">Preview Tanda Tangan:</p>
                                    <img src="{{ $staffSignature->temporaryUrl() }}" class="h-20 object-contain" alt="Staff Signature Preview">
                                </div>
                            @endif
                        </div>

                        <!-- RECIPIENT INFO SECTION -->
                        <div class="border-t border-secondary-200 pt-6">
                            <h4 class="text-sm font-bold text-secondary-900 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Informasi Penerima Transfer
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Recipient Name -->
                                <div>
                                    <label class="block text-xs font-medium text-secondary-700 mb-2">
                                        Nama Penerima
                                        @if($status === 'submitted')
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="recipient_name"
                                        class="input text-sm @error('recipient_name') input-error @enderror"
                                        placeholder="John Doe (Talent)"
                                    >
                                    @error('recipient_name')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Recipient Bank -->
                                <div>
                                    <label class="block text-xs font-medium text-secondary-700 mb-2">
                                        Bank Penerima
                                        @if($status === 'submitted')
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="recipient_bank"
                                        class="input text-sm @error('recipient_bank') input-error @enderror"
                                        placeholder="BCA, Mandiri, BNI, dll"
                                    >
                                    @error('recipient_bank')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Recipient Account Number -->
                                <div>
                                    <label class="block text-xs font-medium text-secondary-700 mb-2">
                                        Nomor Rekening
                                        @if($status === 'submitted')
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="recipient_account_number"
                                        class="input text-sm @error('recipient_account_number') input-error @enderror"
                                        placeholder="1234567890"
                                    >
                                    @error('recipient_account_number')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Recipient Phone -->
                                <div>
                                    <label class="block text-xs font-medium text-secondary-700 mb-2">
                                        No. Telepon (Opsional)
                                    </label>
                                    <input 
                                        type="text" 
                                        wire:model="recipient_phone"
                                        class="input text-sm @error('recipient_phone') input-error @enderror"
                                        placeholder="081234567890"
                                    >
                                    @error('recipient_phone')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Warning untuk Submit -->
                        @if($status !== 'submitted')
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                <div class="flex gap-2">
                                    <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="text-xs text-amber-800">
                                        <p class="font-semibold mb-1">Untuk submit PR ke approval, diperlukan:</p>
                                        <ul class="list-disc list-inside space-y-0.5">
                                            <li>Minimal 1 invoice dari talent</li>
                                            <li>Tanda tangan staff</li>
                                            <li>Informasi penerima transfer lengkap</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Items -->
                <div class="card animate-fade-in" style="animation-delay: 0.1s;">
                    <div class="card-header flex items-center justify-between mb-2">
                        <h3 class="text-primary-50 text-lg font-bold">Detail Item</h3>
                        <button 
                            type="button" 
                            wire:click="addItem"
                            class="btn-outline text-sm"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Item
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="overflow-x-auto">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="w-12">No</th>
                                        <th>Nama Item</th>
                                        <th class="w-24">Jumlah</th>
                                        <th class="w-32">Satuan</th>
                                        <th class="w-40">Harga (Rp)</th>
                                        <th class="w-40">Subtotal</th>
                                        <th class="w-20">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr wire:key="item-{{ $index }}">
                                            <td class="text-center font-semibold">{{ $index + 1 }}</td>
                                            
                                            <!-- Nama Item -->
                                            <td>
                                                <input 
                                                    type="text" 
                                                    wire:model.blur="items.{{ $index }}.nama_item"
                                                    class="input @error('items.' . $index . '.nama_item') input-error @enderror"
                                                    placeholder="Nama item"
                                                >
                                                @error('items.' . $index . '.nama_item')
                                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>

                                            <!-- Jumlah -->
                                            <td>
                                                <input 
                                                    type="number" 
                                                    wire:model.blur="items.{{ $index }}.jumlah"
                                                    class="input @error('items.' . $index . '.jumlah') input-error @enderror"
                                                    min="1"
                                                >
                                                @error('items.' . $index . '.jumlah')
                                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>

                                            <!-- Satuan -->
                                            <td>
                                                <input 
                                                    type="text" 
                                                    wire:model.blur="items.{{ $index }}.satuan"
                                                    class="input @error('items.' . $index . '.satuan') input-error @enderror"
                                                    placeholder="pcs"
                                                >
                                                @error('items.' . $index . '.satuan')
                                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>

                                            <!-- Harga -->
                                            <td>
                                                <input 
                                                    type="number" 
                                                    wire:model.blur="items.{{ $index }}.harga"
                                                    class="input @error('items.' . $index . '.harga') input-error @enderror"
                                                    min="0"
                                                    step="0.01"
                                                >
                                                @error('items.' . $index . '.harga')
                                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>

                                            <!-- Subtotal -->
                                            <td class="font-semibold text-primary-600">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </td>

                                            <!-- Actions -->
                                            <td class="text-center">
                                                @if(count($items) > 1)
                                                    <button 
                                                        type="button"
                                                        wire:click="removeItem({{ $index }})"
                                                        class="text-red-600 hover:text-red-800 p-1"
                                                        title="Hapus item"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    <!-- Total Row -->
                                    <tr class="bg-orange-light-100 font-bold">
                                        <td colspan="5" class="text-right uppercase text-secondary-900">Total</td>
                                        <td class="text-primary-600 text-lg">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if(empty($items))
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-semibold">Belum ada item</p>
                                <p class="text-sm">Klik tombol "Tambah Item" untuk menambahkan item</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar - Summary & Actions -->
            <div class="space-y-6">
                <!-- Summary -->
                <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="card-header">
                        <h3 class="text-primary-50 text-lg font-bold">Ringkasan</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-secondary-200">
                            <span class="text-sm text-secondary-600">Total Item</span>
                            <span class="font-bold text-secondary-900">{{ count($items) }} item</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-secondary-200">
                            <span class="text-sm text-secondary-600">Total Invoice</span>
                            <span class="font-bold text-secondary-900">
                                {{ count($existingInvoices) + count($invoices) }} file
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-secondary-200">
                            <span class="text-sm text-secondary-600">Total Harga</span>
                            <span class="font-bold text-primary-600 text-xl">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-secondary-600">Status</span>
                            <span class="badge badge-light">{{ ucfirst($status) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="card-header">
                        <h3 class="text-primary-50 text-lg font-bold">Aksi</h3>
                    </div>
                    <div class="card-body space-y-3">
                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="btn-primary w-full"
                            wire:loading.attr="disabled"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span wire:loading.remove>Submit untuk Approval</span>
                            <span wire:loading>Processing...</span>
                        </button>

                        <!-- Save Draft Button -->
                        <button 
                            type="button"
                            wire:click="saveDraft"
                            class="btn-secondary w-full"
                            wire:loading.attr="disabled"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            Simpan sebagai Draft
                        </button>

                        <!-- Cancel Button -->
                        <a 
                            href="{{ route('pr.index') }}"
                            class="btn-ghost w-full"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batal
                        </a>
                    </div>
                </div>

                <!-- Help -->
                <div class="card animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="card-body">
                        <div class="flex gap-3">
                            <svg class="w-5 h-5 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-secondary-600">
                                <p class="font-semibold mb-1">Tips:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Pastikan semua item terisi dengan benar</li>
                                    <li>Draft dapat diedit kapan saja</li>
                                    <li>PR yang sudah disubmit tidak dapat diedit</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>