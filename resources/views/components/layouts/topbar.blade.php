@props(['title' => 'Dashboard'])

<header class="sticky top-0 z-20 h-16 bg-white/95 backdrop-blur-md border-b border-secondary-200">
    <div class="h-full px-6 flex items-center justify-between">
        
        {{-- LEFT SECTION --}}
        <div class="flex items-center gap-4">
            {{-- Mobile Menu Toggle --}}
            <button id="mobileMenuBtn" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-secondary-50 text-secondary-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            
            {{-- Breadcrumb / Page Title --}}
            <div class="hidden sm:block">
                <nav class="flex items-center gap-2 text-sm">
                    <a href="{{ route('dashboard') }}" class="text-secondary-500 hover:text-secondary-700 transition-colors">
                        Home
                    </a>
                    <svg class="w-4 h-4 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-secondary-900 font-medium">{{ $title }}</span>
                </nav>
            </div>

            {{-- Mobile Title --}}
            <h1 class="sm:hidden text-base font-semibold text-secondary-900">
                {{ $title }}
            </h1>
        </div>

        {{-- RIGHT SECTION --}}
        <div class="flex items-center gap-2">
            
            {{-- Search Button (Optional) --}}
            <button class="hidden md:flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-secondary-50 text-secondary-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <span class="text-sm">Search</span>
                <kbd class="hidden lg:inline-flex px-1.5 py-0.5 text-xs font-mono bg-secondary-100 border border-secondary-200 rounded">⌘K</kbd>
            </button>

            {{-- Notification Bell --}}
            <livewire:notification-bell />

            {{-- Divider --}}
            <div class="w-px h-6 bg-secondary-200"></div>

            {{-- User Dropdown --}}
            <div class="relative">
                <button id="userMenuBtn" class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-secondary-50 transition-colors">
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-semibold text-secondary-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-secondary-500">
                            {{ ucfirst(Auth::user()->roles->first()?->name ?? 'User') }}
                        </p>
                    </div>
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=171717&color=fff&bold=true" 
                             class="w-9 h-9 rounded-lg border border-secondary-200">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                    </div>
                </button>

                {{-- Dropdown Menu --}}
                <div id="userDropdown" class="dropdown-menu hidden absolute right-0 mt-2 w-56 bg-white border border-secondary-200 rounded-lg shadow-xl overflow-hidden">
                    <div class="px-4 py-3 bg-secondary-50 border-b border-secondary-200">
                        <p class="text-sm font-semibold text-secondary-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-secondary-500 mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <div class="py-1">
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-secondary-50 transition-colors group">
                            <svg class="w-4 h-4 text-secondary-400 group-hover:text-secondary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm text-secondary-700 group-hover:text-secondary-900">Profile</span>
                        </a>

                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 hover:bg-secondary-50 transition-colors group">
                            <svg class="w-4 h-4 text-secondary-400 group-hover:text-secondary-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-secondary-700 group-hover:text-secondary-900">Settings</span>
                        </a>
                    </div>

                    <div class="border-t border-secondary-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 transition-colors group">
                                <svg class="w-4 h-4 text-secondary-400 group-hover:text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="text-sm text-secondary-700 group-hover:text-red-600">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>