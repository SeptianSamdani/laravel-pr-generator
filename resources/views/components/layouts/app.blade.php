<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }
        
        /* Sidebar transitions */
        .sidebar {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }
        
        .main-content {
            transition: margin-left 0.3s ease-in-out;
        }
        
        /* Dropdown animation */
        .dropdown-menu {
            transform-origin: top right;
            animation: dropdownOpen 0.2s ease-out;
        }
        
        @keyframes dropdownOpen {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-orange-light-50 font-sans antialiased">

    {{-- MOBILE OVERLAY --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar fixed top-0 left-0 h-screen bg-white shadow-lg z-40 flex flex-col w-64 -translate-x-full lg:translate-x-0">
        
        {{-- LOGO & BRAND --}}
        <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 flex-shrink-0">
            <div id="brandContent" class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-orange">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-lg font-heading font-bold text-gray-800">PR Generator</span>
            </div>
            <button id="toggleCollapse" class="hidden lg:block p-1.5 rounded-lg hover:bg-orange-50 text-gray-500 hover:text-primary-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
        </div>

        {{-- NAVIGATION --}}
        <nav class="flex-1 overflow-y-auto hide-scrollbar py-4 px-3">
            @php
                $menus = [
                    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'permission' => null],
                    ['name' => 'Purchase Requisition', 'route' => 'pr.index', 'icon' => 'file-text', 'permission' => 'pr.view'],
                    ['name' => 'Approval', 'route' => 'approval.index', 'icon' => 'check-circle', 'permission' => 'pr.approve'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @if(!$menu['permission'] || auth()->user()->can($menu['permission']))
                    @php 
                        $isActive = request()->routeIs($menu['route']); 
                        
                        // Count pending PRs for approval badge
                        $pendingCount = 0;
                        if($menu['route'] === 'approval.index') {
                            $pendingCount = \App\Models\PurchaseRequisition::where('status', 'submitted')->count();
                        }
                    @endphp
                    <a href="{{ route($menu['route']) }}" 
                    class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 font-semibold text-sm group
                            {{ $isActive ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-orange' : 'text-gray-600 hover:bg-orange-50 hover:text-primary-600' }}">
                        
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @if($menu['icon'] === 'home')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            @elseif($menu['icon'] === 'file-text')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            @elseif($menu['icon'] === 'check-circle')
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                        
                        <span class="nav-text truncate">{{ $menu['name'] }}</span>
                        
                        @if($isActive)
                            <span class="ml-auto w-2 h-2 bg-white rounded-full animate-pulse"></span>
                        @elseif($pendingCount > 0)
                            <span class="ml-auto badge badge-danger text-xs">{{ $pendingCount }}</span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- HELP SECTION --}}
        <div id="helpSection" class="p-3 border-t border-gray-200 flex-shrink-0">
            <div class="bg-gradient-to-br from-orange-light-100 to-orange-light-200 rounded-lg p-3">
                <p class="text-xs font-semibold text-gray-700 mb-1">Need Help?</p>
                <p class="text-xs text-gray-600">Check our documentation</p>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div id="mainContent" class="main-content min-h-screen lg:ml-64">
        
        {{-- TOPBAR --}}
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
                            {{ $title ?? 'Dashboard' }}
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

        {{-- PAGE CONTENT --}}
        <main class="p-4 lg:p-6">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>

        {{-- FOOTER --}}
        <footer class="px-4 lg:px-6 py-4 bg-white/50 border-t border-gray-200">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-sm text-gray-500">
                <p>© {{ date('Y') }} PR Generator. All rights reserved.</p>
                <p>Made with <span class="text-primary-500">♥</span> by Your Team</p>
            </div>
        </footer>
    </div>

    @livewireScripts
    
    <script>
        // ========================================
        // VANILLA JS - NO ALPINE NEEDED
        // ========================================
        
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const mobileOverlay = document.getElementById('mobileOverlay');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const toggleCollapse = document.getElementById('toggleCollapse');
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        const brandContent = document.getElementById('brandContent');
        const helpSection = document.getElementById('helpSection');
        const navTexts = document.querySelectorAll('.nav-text');
        
        let isCollapsed = false;
        let isMobileOpen = false;

        // ========================================
        // MOBILE MENU TOGGLE
        // ========================================
        mobileMenuBtn?.addEventListener('click', () => {
            isMobileOpen = !isMobileOpen;
            
            if (isMobileOpen) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                mobileOverlay.classList.add('hidden');
            }
        });

        // Close mobile menu when clicking overlay
        mobileOverlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            isMobileOpen = false;
        });

        // ========================================
        // DESKTOP SIDEBAR COLLAPSE
        // ========================================
        toggleCollapse?.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            
            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                mainContent.classList.remove('lg:ml-64');
                mainContent.classList.add('lg:ml-20');
                
                // Hide text elements
                brandContent.classList.add('hidden');
                helpSection.classList.add('hidden');
                navTexts.forEach(text => text.classList.add('hidden'));
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                mainContent.classList.remove('lg:ml-20');
                mainContent.classList.add('lg:ml-64');
                
                // Show text elements
                brandContent.classList.remove('hidden');
                helpSection.classList.remove('hidden');
                navTexts.forEach(text => text.classList.remove('hidden'));
            }
        });

        // ========================================
        // USER DROPDOWN
        // ========================================
        userMenuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!userMenuBtn?.contains(e.target) && !userDropdown?.contains(e.target)) {
                userDropdown?.classList.add('hidden');
            }
        });

        // ========================================
        // CLOSE MOBILE MENU ON RESIZE
        // ========================================
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.add('hidden');
                isMobileOpen = false;
            } else {
                if (!isMobileOpen) {
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
    </script>
</body>
</html>