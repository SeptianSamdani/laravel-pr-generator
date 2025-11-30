{{-- resources/views/components/layouts/sidebar.blade.php --}}
<aside id="sidebar" class="sidebar fixed top-0 left-0 h-screen bg-white shadow-lg z-40 flex flex-col w-64 -translate-x-full lg:translate-x-0">
    
    {{-- LOGO & BRAND --}}
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 flex-shrink-0">
        <div id="brandContent" class="flex items-center gap-3">
            <div class="logo-container">
                <img 
                    src="/sushi-mentai-logo.png" 
                    alt="Logo Sushi Mentai" 
                    id="logoImg"
                    class="logo-img logo-expanded"
                >
            </div>
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
                class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 font-semibold text-sm group
                        {{ $isActive ? 'bg-orange-100 text-primary-600' : 'text-gray-600 hover:bg-orange-50 hover:text-primary-600' }}">
                    
                    {{-- Icon Container --}}
                    <div class="icon-container flex-shrink-0 flex justify-center w-5 h-5">
                        @if($menu['icon'] === 'home')
                            <svg class="w-5 h-5 icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        @elseif($menu['icon'] === 'file-text')
                            <svg class="w-5 h-5 icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        @elseif($menu['icon'] === 'check-circle')
                            <svg class="w-5 h-5 icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif($menu['icon'] === 'users')
                            <svg class="w-5 h-5 icon-svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        @endif
                    </div>
                    
                    <span class="nav-text truncate">{{ $menu['name'] }}</span>
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