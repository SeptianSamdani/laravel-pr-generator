<div>
    <!-- Trigger Button -->
    <button 
        wire:click="openInstructions" 
        class="btn-secondary w-full group relative overflow-hidden hover:border-primary-400 transition-all duration-300"
    >
        <div class="flex items-center justify-center gap-2 relative z-10">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span class="font-semibold">Download as PDF</span>
        </div>
        
        <!-- Animated gradient background -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary-50 to-orange-light-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </button>

    <!-- Modal/Popover Instructions -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" wire:transition>
            <!-- Backdrop with blur -->
            <div 
                class="fixed inset-0 bg-secondary-900/60 backdrop-blur-sm transition-opacity"
                wire:click="closeModal"
            ></div>

            <!-- Modal Content -->
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                <div 
                    class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full animate-scale-in overflow-hidden"
                    style="animation: slideUp 0.3s ease-out"
                >
                    <!-- Header with Orange Gradient -->
                    <div class="relative bg-gradient-to-br from-primary-500 via-primary-600 to-primary-700 px-6 sm:px-8 py-6 text-white overflow-hidden">
                        <!-- Decorative circles -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full -ml-12 -mb-12"></div>
                        
                        <div class="relative z-10 flex items-start justify-between">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold">Cara Download PDF</h3>
                                    <p class="text-primary-100 text-sm mt-1 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 bg-primary-200 rounded-full"></span>
                                        {{ $pr->pr_number }}
                                    </p>
                                </div>
                            </div>
                            <button 
                                wire:click="closeModal" 
                                class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 sm:px-8 py-8 space-y-6">
                        
                        <!-- Info Alert -->
                        <div class="bg-gradient-to-r from-orange-light-50 to-primary-50 border-l-4 border-primary-500 p-4 rounded-r-lg">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-primary-900">Konversi DOCX ke PDF</p>
                                    <p class="text-xs text-primary-700 mt-1">Gunakan layanan gratis iLovePDF untuk konversi - mudah, cepat, dan aman!</p>
                                </div>
                            </div>
                        </div>

                        <!-- Steps dengan design modern -->
                        <div class="space-y-5">
                            
                            <!-- Step 1 -->
                            <div class="group hover:bg-orange-light-50 rounded-xl p-5 transition-all duration-300 border-2 border-transparent hover:border-primary-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-orange flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                        1
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div>
                                            <h4 class="font-bold text-secondary-900 text-lg mb-1 flex items-center gap-2">
                                                Download File DOCX
                                                <svg class="w-4 h-4 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                                </svg>
                                            </h4>
                                            <p class="text-sm text-secondary-600">
                                                Download file PR dalam format Word (.docx) terlebih dahulu
                                            </p>
                                        </div>
                                        <button 
                                            wire:click="downloadDocx"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-500 hover:bg-primary-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-orange hover:shadow-orange-lg hover:scale-105"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Download DOCX
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider dengan icon -->
                            <div class="flex items-center justify-center">
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-secondary-200 to-transparent"></div>
                                <div class="mx-4 w-8 h-8 bg-orange-light-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-secondary-200 to-transparent"></div>
                            </div>

                            <!-- Step 2 -->
                            <div class="group hover:bg-orange-light-50 rounded-xl p-5 transition-all duration-300 border-2 border-transparent hover:border-primary-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-soft flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                        2
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <div>
                                            <h4 class="font-bold text-secondary-900 text-lg mb-1 flex items-center gap-2">
                                                Buka iLovePDF
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </h4>
                                            <p class="text-sm text-secondary-600">
                                                Akses layanan konversi Word ke PDF secara gratis
                                            </p>
                                        </div>
                                        <a 
                                            href="https://www.ilovepdf.com/word_to_pdf" 
                                            target="_blank"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow-soft hover:shadow-soft-lg hover:scale-105"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                            Buka iLovePDF.com
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="flex items-center justify-center">
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-secondary-200 to-transparent"></div>
                                <div class="mx-4 w-8 h-8 bg-orange-light-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-secondary-200 to-transparent"></div>
                            </div>

                            <!-- Step 3 -->
                            <div class="group hover:bg-orange-light-50 rounded-xl p-5 transition-all duration-300 border-2 border-transparent hover:border-primary-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl flex items-center justify-center font-bold text-lg shadow-soft flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                        3
                                    </div>
                                    <div class="flex-1">
                                        <div class="mb-3">
                                            <h4 class="font-bold text-secondary-900 text-lg mb-1 flex items-center gap-2">
                                                Upload & Convert
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </h4>
                                            <p class="text-sm text-secondary-600">
                                                Ikuti langkah mudah di iLovePDF
                                            </p>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex items-start gap-3 text-sm">
                                                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <p class="text-secondary-700">Klik tombol <span class="font-semibold text-primary-600">"Select WORD files"</span></p>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <p class="text-secondary-700">Pilih file <span class="font-semibold text-primary-600">DOCX</span> yang sudah didownload</p>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <p class="text-secondary-700">Klik <span class="font-semibold text-primary-600">"Convert to PDF"</span></p>
                                            </div>
                                            <div class="flex items-start gap-3 text-sm">
                                                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <p class="text-secondary-700">Download <span class="font-semibold text-primary-600">hasil PDF</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips Card dengan design Sushi Mentai -->
                        <div class="relative overflow-hidden rounded-xl border-2 border-primary-200 bg-gradient-to-br from-orange-light-50 via-white to-primary-50 p-5">
                            <!-- Decorative element -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/5 rounded-full -mr-16 -mt-16"></div>
                            
                            <div class="relative flex items-start gap-4">
                                <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-orange">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-primary-900 mb-2">Pro Tips:</p>
                                    <ul class="space-y-1.5">
                                        <li class="flex items-start gap-2 text-xs text-secondary-700">
                                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>iLovePDF <strong class="text-primary-700">gratis & tidak perlu registrasi</strong></span>
                                        </li>
                                        <li class="flex items-start gap-2 text-xs text-secondary-700">
                                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Proses konversi <strong class="text-primary-700">cepat (± 5-10 detik)</strong></span>
                                        </li>
                                        <li class="flex items-start gap-2 text-xs text-secondary-700">
                                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Kualitas PDF <strong class="text-primary-700">sama dengan dokumen asli</strong></span>
                                        </li>
                                        <li class="flex items-start gap-2 text-xs text-secondary-700">
                                            <svg class="w-4 h-4 text-primary-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span><strong class="text-primary-700">Aman & terpercaya</strong> - file otomatis dihapus setelah konversi</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer dengan CTA -->
                    <div class="bg-secondary-50 px-6 sm:px-8 py-5 border-t border-secondary-100">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <button 
                                wire:click="closeModal"
                                class="order-2 sm:order-1 text-sm text-secondary-600 hover:text-secondary-900 font-medium transition-colors"
                            >
                                ← Kembali
                            </button>
                            <div class="order-1 sm:order-2 flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                                <a 
                                    href="https://www.ilovepdf.com/word_to_pdf" 
                                    target="_blank"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-orange hover:shadow-orange-lg hover:scale-105"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Mulai Konversi Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Custom Animations -->
    <style>
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes scale-in {
            from {
                opacity: 0;
                transform: scale(0.95);
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