<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }
        
        /* Sidebar transitions */
        .sidebar {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .main-content {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Dropdown animation */
        .dropdown-menu {
            transform-origin: top right;
            animation: dropdownOpen 0.15s ease-out;
        }
        
        @keyframes dropdownOpen {
            from {
                opacity: 0;
                transform: scale(0.97) translateY(-8px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        
        .hide-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .hide-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 2px;
        }
        
        .hide-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
        
        /* Logo styling */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0.5rem 0;
            flex-shrink: 0;
        }
        
        .logo-img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }
        
        .logo-collapsed {
            width: 32px;
            height: 32px;
        }
        
        .logo-expanded {
            width: 110px;
            height: auto;
        }

        /* Icon styling for collapsed state */
        .icon-container {
            transition: all 0.3s ease-in-out;
        }

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

        /* Badge positioning in collapsed state */
        .sidebar-collapsed .nav-badge {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            transform: scale(0.8);
        }
    </style>
</head>
<body class="bg-secondary-50 font-sans antialiased">

    {{-- MOBILE OVERLAY --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    {{-- SIDEBAR --}}
    <x-layouts.sidebar />

    {{-- MAIN CONTENT WRAPPER --}}
    <div id="mainContent" class="main-content min-h-screen lg:ml-64">
        
        {{-- TOPBAR --}}
        <x-layouts.topbar :title="$title ?? 'Dashboard'" />

        {{-- PAGE CONTENT --}}
        <main class="p-6 lg:p-8">
            <div class="max-w-[1400px] mx-auto">
                {{ $slot }}
            </div>
        </main>

        {{-- FOOTER --}}
        <x-layouts.footer />
    </div>

    @livewireScripts
    
    {{-- SCRIPTS --}}
    <x-layouts.scripts />
</body>
</html>