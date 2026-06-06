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

                        <!-- Social Media Link -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                Link Postingan / Sumber Promosi Sosmed
                            </label>
                            <input 
                                type="url" 
                                wire:model="social_media_link"
                                class="input @error('social_media_link') input-error @enderror"
                                placeholder="https://instagram.com/p/..."
                            >
                            @error('social_media_link')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-secondary-500">Opsional: Link postingan Instagram, TikTok, dll</p>
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

                        {{-- Staff Signature Section --}}
                        <div class="bg-white rounded-xl border border-secondary-200 shadow-sm p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-sm font-semibold text-secondary-900">Tanda Tangan Staff</h3>
                                    <p class="text-xs text-secondary-500 mt-1">Upload tanda tangan digital Anda</p>
                                </div>
                                @if($status === 'draft')
                                    <span class="text-xs px-2 py-1 bg-secondary-100 text-secondary-600 rounded-full">Optional untuk Draft</span>
                                @else
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-600 rounded-full">Required untuk Submit</span>
                                @endif
                            </div>

                            {{-- Signature Preview --}}
                            <div class="mb-4">
                                @if($staffSignature)
                                    {{-- New uploaded signature --}}
                                    <div class="relative inline-block">
                                        <div class="w-64 h-32 rounded-lg border-2 border-primary-200 bg-white p-3 flex items-center justify-center">
                                            <img src="{{ $staffSignature->temporaryUrl() }}" class="max-w-full max-h-full object-contain">
                                        </div>
                                        <button 
                                            type="button" 
                                            wire:click="$set('staffSignature', null)"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                        <p class="text-xs text-green-600 mt-2 font-medium">✓ Signature baru akan di-upload</p>
                                    </div>
                                @elseif($existingStaffSignature)
                                    {{-- Existing signature --}}
                                    <div class="space-y-3">
                                        <div class="w-64 h-32 rounded-lg border-2 border-secondary-200 bg-white p-3 flex items-center justify-center">
                                            <img src="{{ Storage::url($existingStaffSignature) }}" class="max-w-full max-h-full object-contain">
                                        </div>
                                        
                                        {{-- Check if signature is from user profile --}}
                                        @if($existingStaffSignature === Auth::user()->signature_path)
                                            <div class="flex items-start gap-2 text-xs">
                                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <div>
                                                    <p class="text-blue-700 font-medium">Menggunakan signature dari profile Anda</p>
                                                    <p class="text-blue-600 mt-0.5">Upload signature baru di bawah jika ingin menggunakan yang berbeda</p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-xs text-green-600 font-medium">✓ Signature tersimpan</p>
                                        @endif
                                    </div>
                                @else
                                    {{-- No signature --}}
                                    <div class="w-64 h-32 rounded-lg border-2 border-dashed border-secondary-300 bg-secondary-50 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-10 h-10 mx-auto text-secondary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                            </svg>
                                            <p class="text-xs text-secondary-500 font-medium">Belum ada signature</p>
                                            <p class="text-xs text-secondary-400 mt-1">Upload di bawah atau set di <a href="{{ route('profile') }}" class="text-primary-600 hover:underline">Profile</a></p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Upload Input --}}
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <label class="btn-secondary cursor-pointer text-sm">
                                        <input type="file" wire:model="staffSignature" accept="image/*" class="hidden">
                                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        {{ $existingStaffSignature ? 'Ganti Signature' : 'Upload Signature' }}
                                    </label>

                                    @if(!Auth::user()->hasSignature())
                                        <a href="{{ route('profile') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                            atau set di Profile →
                                        </a>
                                    @endif
                                </div>

                                <p class="text-xs text-secondary-500">
                                    Format: JPG, PNG. Max 2MB. Scan atau foto tanda tangan Anda di kertas putih.
                                </p>

                                @error('staffSignature') 
                                    <p class="text-xs text-red-600 font-medium">{{ $message }}</p> 
                                @enderror

                                {{-- Upload Progress --}}
                                <div wire:loading wire:target="staffSignature" class="flex items-center gap-2 text-xs text-primary-600">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Uploading signature...
                                </div>

                                {{-- Info Alert - No Profile Signature --}}
                                @if(!Auth::user()->hasSignature() && !$existingStaffSignature)
                                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            <div class="flex-1">
                                                <p class="text-xs font-semibold text-amber-800">Tips: Set signature di Profile</p>
                                                <p class="text-xs text-amber-700 mt-1">Upload signature sekali di <a href="{{ route('profile') }}" class="underline font-medium">Profile</a>, otomatis terisi untuk semua PR baru!</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
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
                <div class="card animate-fade-in" style="animation-delay: 0.1s;" x-data="itemsCalculator()">
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
                                                x-data
                                                x-on:input="$dispatch('recalculate')"
                                                x-on:focus="$event.target.select()"
                                                class="input @error('items.' . $index . '.jumlah') input-error @enderror"
                                                min="1"
                                                data-index="{{ $index }}"
                                                data-field="jumlah"
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
                                                x-data
                                                x-on:input="$dispatch('recalculate')"
                                                x-on:focus="$event.target.select()"
                                                class="input @error('items.' . $index . '.harga') input-error @enderror"
                                                min="0"
                                                step="1"
                                                data-index="{{ $index }}"
                                                data-field="harga"
                                            >
                                            @error('items.' . $index . '.harga')
                                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                            @enderror
                                        </td>

                                        <!-- Subtotal (client-side display) -->
                                        <td class="font-semibold text-primary-600">
                                            <span 
                                                id="subtotal-{{ $index }}"
                                                x-text="formatRupiah(subtotals[{{ $index }}] ?? {{ $item['subtotal'] }})"
                                            >
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </span>
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
                                        <span x-text="formatRupiah(grandTotal)">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                <div class="card-body space-y-3">

                    <!-- Submit Button (hanya untuk draft) -->
                    @if($status === 'draft')
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
                    @endif

                    <!-- Save Changes Button (untuk submitted & approved) -->
                    @if(in_array($status, ['submitted', 'approved']))
                        <button 
                            type="button"
                            wire:click="saveDraft"
                            class="btn-primary w-full"
                            wire:loading.attr="disabled"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Processing...</span>
                        </button>
                    @endif

                    <!-- Save Draft Button (hanya untuk draft) -->
                    @if($status === 'draft')
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
                    @endif

                    <!-- Cancel Button -->
                    <a href="{{ route('pr.index') }}" class="btn-ghost w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Batal
                    </a>
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

@push('scripts')
<script>
function itemsCalculator() {
    return {
        subtotals: @json(array_column($items, 'subtotal')),
        grandTotal: {{ $total }},

        init() {
            // Listen recalculate event dari input
            this.$el.addEventListener('recalculate', () => this.recalculate());

            // Setelah Livewire morphed (tambah/hapus item), 
            // recalculate dari DOM — JANGAN reset dari PHP values
            document.addEventListener('livewire:morphed', () => {
                this.$nextTick(() => this.recalculate());
            });
        },

        recalculate() {
            const rows = this.$el.querySelectorAll('tbody tr[wire\\:key]');
            let grand = 0;
            const newSubtotals = [];

            rows.forEach((row, index) => {
                const jumlahInput = row.querySelector('[data-field="jumlah"]');
                const hargaInput  = row.querySelector('[data-field="harga"]');

                if (!jumlahInput || !hargaInput) return;

                const jumlah   = parseFloat(jumlahInput.value) || 0;
                const harga    = parseFloat(hargaInput.value)  || 0;
                const subtotal = jumlah * harga;

                newSubtotals[index] = subtotal;
                grand += subtotal;
            });

            this.subtotals  = newSubtotals;
            this.grandTotal = grand;
        },

        formatRupiah(value) {
            return 'Rp ' + Math.round(value || 0).toLocaleString('id-ID');
        }
    }
}
</script>
@endpush