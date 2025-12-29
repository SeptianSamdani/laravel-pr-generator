<aside id="sidebar" class="sidebar fixed top-0 left-0 h-screen bg-white border-r border-secondary-100 z-40 flex flex-col w-64 -translate-x-full lg:translate-x-0 shadow-soft">
    
    {{-- LOGO & BRAND SECTION --}}
    <div class="h-20 flex items-center justify-between px-6 border-b border-secondary-100 flex-shrink-0">
        <div id="brandContent" class="flex items-center gap-3 ml-10 mr-3">
            <div class="logo-container">
                <img 
                    src="/sushi-mentai-logo.png" 
                    alt="Logo" 
                    id="logoImg"
                    class="logo-img logo-expanded"
                >
            </div>
        </div>
        
        {{-- Collapse Button --}}
        <button id="toggleCollapse" class="hidden lg:flex p-2 rounded-lg hover:bg-orange-light-50 text-secondary-600 hover:text-primary-500 transition-all duration-200 group">
            <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    {{-- NAVIGATION - CLEAN & MINIMAL --}}
    <nav class="flex-1 overflow-y-auto hide-scrollbar py-6 px-4">
        @php
            $menus = [
                ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'permission' => null],
                ['name' => 'Purchase Requisition', 'route' => 'pr.index', 'icon' => 'file-text', 'permission' => 'pr.view'],
                ['name' => 'Approval', 'route' => 'approval.index', 'icon' => 'check-circle', 'permission' => 'pr.approve'],
                ['name' => 'User Management', 'route' => 'users.index', 'icon' => 'users', 'permission' => 'user.view'],
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
                   class="nav-link relative flex items-center gap-3 px-3 py-3 rounded-lg mb-1 text-sm font-medium group transition-all duration-200
                          {{ $isActive 
                             ? 'bg-primary-50 text-primary-600 shadow-orange-soft' 
                             : 'text-secondary-600 hover:bg-orange-light-50 hover:text-primary-600' }}">
                    
                    {{-- Icon Container --}}
                    <div class="icon-container flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center
                                {{ $isActive 
                                   ? 'bg-white/20' 
                                   : 'bg-orange-light-50 group-hover:bg-primary-50' }}
                                transition-all duration-200">
                        @if($menu['icon'] === 'home')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @elseif($menu['icon'] === 'file-text')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        @elseif($menu['icon'] === 'check-circle')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($menu['icon'] === 'users')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        @endif
                    </div>
                    
                    <span class="nav-text flex-1 truncate">{{ $menu['name'] }}</span>

                    {{-- Badge - Clean Style --}}
                    @if($pendingCount > 0)
                        <span class="nav-badge flex-shrink-0 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold bg-red-500 text-white rounded-full">
                            {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                        </span>
                    @endif
                    
                    {{-- Active Indicator --}}
                    @if($isActive)
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-full"></div>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- HELP SECTION - CLEAN MINIMAL --}}
    <div id="helpSection" class="p-4 border-t border-secondary-100 flex-shrink-0">
        <div class="rounded-lg p-4 bg-orange-light-50 border border-primary-100">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-secondary-800">Digital Marketing</p>
                    <p class="text-xs text-secondary-600 mt-0.5">Sushi Mentai</p>
                </div>
            </div>
        </div>
    </div>
</aside>

<style>
    /* Custom Scrollbar */
    .hide-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    
    .hide-scrollbar::-webkit-scrollbar-track {
        background: #fafafa;
    }
    
    .hide-scrollbar::-webkit-scrollbar-thumb {
        background: #fed7aa;
        border-radius: 2px;
    }
    
    .hide-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #fdba74;
    }

    /* Logo Transitions */
    .logo-img {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .logo-collapsed {
        width: 32px;
        height: 32px;
    }
    
    .logo-expanded {
        width: 110px;
        height: auto;
    }

    /* Icon Container */
    .icon-container {
        transition: all 0.2s ease;
    }

    /* Collapsed State */
    .sidebar-collapsed .icon-container {
        margin-left: 0;
        justify-content: center;
    }

    .sidebar-collapsed .nav-link {
        justify-content: center;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
    }

    .sidebar-collapsed .nav-text {
        display: none;
    }

    .sidebar-collapsed .nav-badge {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
        transform: scale(0.8);
    }

    /* Sidebar Transitions */
    .sidebar {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>