{{-- resources/views/components/layouts/topbar.blade.php --}}
@props(['title' => 'Dashboard'])

<header class="sticky top-0 z-20 h-16 bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-200">
    <div class="h-full px-4 lg:px-6 flex items-center justify-between">
        
        {{-- LEFT SECTION --}}
        <div class="flex items-center gap-4">
            {{-- Mobile Menu Toggle --}}
            <button id="mobileMenuBtn" class="lg:hidden p-2 rounded-lg hover:bg-orange-50 text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            {{-- Page Title --}}
            <div>
                <h1 class="text-lg lg:text-xl font-heading font-bold text-gray-800">
                    {{ $title }}
                </h1>
                <p class="hidden sm:block text-xs text-gray-500">{{ now()->format('l, F j, Y') }}</p>
            </div>
        </div>

        {{-- RIGHT SECTION --}}
        <div class="flex items-center gap-2 lg:gap-3">
            {{-- Notification --}}
            <button class="relative p-2 rounded-lg hover:bg-orange-50 text-gray-600 hover:text-primary-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-primary-500 rounded-full ring-2 ring-white"></span>
            </button>

            {{-- User Dropdown --}}
            <div class="relative">
                <button id="userMenuBtn" class="flex items-center gap-2 lg:gap-3 px-2 lg:px-3 py-1.5 rounded-lg hover:bg-orange-50">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=f97316&color=fff&bold=true" 
                             class="w-9 h-9 rounded-lg ring-2 ring-orange-200">
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 rounded-full ring-2 ring-white"></span>
                    </div>
                </button>

                {{-- Dropdown Menu --}}
                <div id="userDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg py-2">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-orange-50 group">
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-primary-600">Profile</span>
                    </a>

                    <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-orange-50 group">
                        <svg class="w-4 h-4 text-gray-500 group-hover:text-primary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-sm font-semibold text-gray-700 group-hover:text-primary-600">Settings</span>
                    </a>

                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 group">
                                <svg class="w-4 h-4 text-gray-500 group-hover:text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-sm font-semibold text-gray-700 group-hover:text-red-600">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>