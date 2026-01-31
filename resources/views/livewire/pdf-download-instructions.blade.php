<div>
    <!-- Trigger Button - Replace existing PDF download button -->
    <button 
        wire:click="openInstructions" 
        class="btn-secondary w-full group relative overflow-hidden"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Download PDF (via iLovePDF)</span>
        
        <!-- Animated gradient background on hover -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
    </button>

    <!-- Modal/Popover Instructions -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:transition>
            <!-- Backdrop -->
            <div 
                class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"
                wire:click="closeModal"
            ></div>

            <!-- Modal Content -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div 
                    class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full animate-scale-in"
                    x-data="{ step: 1 }"
                >
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-t-2xl p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 rounded-full p-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Cara Download PDF</h3>
                                    <p class="text-sm text-blue-100">{{ $pr->pr_number }}</p>
                                </div>
                            </div>
                            <button 
                                wire:click="closeModal" 
                                class="text-white/80 hover:text-white transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-6">
                        <!-- Info Alert -->
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-blue-800 font-medium">Konversi DOCX ke PDF</p>
                                    <p class="text-xs text-blue-600 mt-1">Menggunakan layanan gratis iLovePDF untuk konversi file</p>
                                </div>
                            </div>
                        </div>

                        <!-- Steps -->
                        <div class="space-y-4">
                            <!-- Step 1 -->
                            <div class="flex items-start space-x-4 group cursor-pointer" @click="step = 1">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold shadow-lg">
                                    1
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">Download File DOCX</h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Download file PR dalam format Word (.docx) terlebih dahulu
                                    </p>
                                    <button 
                                        wire:click="downloadDocx"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download DOCX
                                    </button>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="flex items-center">
                                <div class="flex-1 border-t-2 border-dashed border-gray-300"></div>
                                <svg class="w-5 h-5 text-gray-400 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                                <div class="flex-1 border-t-2 border-dashed border-gray-300"></div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex items-start space-x-4 group cursor-pointer" @click="step = 2">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center font-bold shadow-lg">
                                    2
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">Buka iLovePDF</h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        Akses layanan konversi Word ke PDF secara gratis
                                    </p>
                                    <a 
                                        href="https://www.ilovepdf.com/word_to_pdf" 
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Buka iLovePDF
                                    </a>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="flex items-center">
                                <div class="flex-1 border-t-2 border-dashed border-gray-300"></div>
                                <svg class="w-5 h-5 text-gray-400 mx-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                </svg>
                                <div class="flex-1 border-t-2 border-dashed border-gray-300"></div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex items-start space-x-4 group cursor-pointer" @click="step = 3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-green-600 text-white flex items-center justify-center font-bold shadow-lg">
                                    3
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900 mb-1">Upload & Convert</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <p>• Klik tombol "Select WORD files"</p>
                                        <p>• Pilih file DOCX yang sudah didownload</p>
                                        <p>• Klik "Convert to PDF"</p>
                                        <p>• Download hasil PDF</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Tips -->
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-amber-900">Tips:</p>
                                    <ul class="text-xs text-amber-700 mt-1 space-y-0.5">
                                        <li>✓ iLovePDF gratis & tidak perlu registrasi</li>
                                        <li>✓ Proses konversi cepat (± 5 detik)</li>
                                        <li>✓ Kualitas PDF sama dengan dokumen asli</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 rounded-b-2xl px-6 py-4 flex items-center justify-between">
                        <button 
                            wire:click="closeModal"
                            class="text-sm text-gray-600 hover:text-gray-900 font-medium transition-colors"
                        >
                            Tutup
                        </button>
                        <div class="flex items-center space-x-2">
                            <a 
                                href="https://www.ilovepdf.com/word_to_pdf" 
                                target="_blank"
                                class="btn-primary"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Mulai Konversi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js for smooth interactions -->
    <style>
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .animate-scale-in {
            animation: scale-in 0.2s ease-out;
        }
    </style>
</div>