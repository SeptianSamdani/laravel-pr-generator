{{-- resources/views/components/layouts/app.blade.php --}}
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
        
        /* Logo styling improvements */
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            flex-shrink: 0;
            margin-left: 30px;
        }
        
        .logo-img {
            max-width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 2px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .logo-collapsed {
            width: 40px;
            height: 40px;
        }
        
        .logo-expanded {
            width: 120px;
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

    /* Tooltip for collapsed state */
    .nav-link {
        position: relative;
    }

    .nav-link-tooltip {
        position: relat;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #1f2937;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        white-space: nowrap;
        z-index: 50;
        opacity: 0;
        visibility: hidden;
        transition: all 0.2s ease-in-out;
        margin-left: 0.5rem;
    }

    .nav-link-tooltip::before {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 0.375rem solid transparent;
        border-right-color: #1f2937;
    }

    .nav-link:hover .nav-link-tooltip {
        opacity: 1;
        visibility: visible;
    }
    </style>
</head>
<body class="bg-orange-light-50 font-sans antialiased">

    {{-- MOBILE OVERLAY --}}
    <div id="mobileOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 lg:hidden hidden"></div>

    {{-- SIDEBAR --}}
    <x-layouts.sidebar />

    {{-- MAIN CONTENT WRAPPER --}}
    <div id="mainContent" class="main-content min-h-screen lg:ml-64">
        
        {{-- TOPBAR --}}
        <x-layouts.topbar :title="$title ?? 'Dashboard'" />

        {{-- PAGE CONTENT --}}
        <main class="p-4 lg:p-6">
            <div class="max-w-7xl mx-auto">
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