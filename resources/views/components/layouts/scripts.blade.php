{{-- resources/views/components/layouts/scripts.blade.php --}}
<script>
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
    const navLinks = document.querySelectorAll('.nav-link');
    const logoImg = document.getElementById('logoImg');
    
    // Load state from localStorage
    let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    let isMobileOpen = false;

    // Initialize sidebar state on page load
    function initializeSidebarState() {
        if (window.innerWidth >= 1024) {
            if (isCollapsed) {
                collapseSidebar();
            } else {
                expandSidebar();
            }
        }
    }

    function collapseSidebar() {
        sidebar.classList.add('w-20', 'sidebar-collapsed');
        sidebar.classList.remove('w-64');
        mainContent.classList.add('lg:ml-20');
        mainContent.classList.remove('lg:ml-64');
        brandContent.classList.add('hidden');
        helpSection.classList.add('hidden');
        navTexts.forEach(text => text.classList.add('hidden'));
        
        if (logoImg) {
            logoImg.classList.add('logo-collapsed');
            logoImg.classList.remove('logo-expanded');
        }
        
        navLinks.forEach(link => link.classList.add('justify-center'));
        
        // Save to localStorage
        localStorage.setItem('sidebarCollapsed', 'true');
        isCollapsed = true;
    }

    function expandSidebar() {
        sidebar.classList.remove('w-20', 'sidebar-collapsed');
        sidebar.classList.add('w-64');
        mainContent.classList.remove('lg:ml-20');
        mainContent.classList.add('lg:ml-64');
        brandContent.classList.remove('hidden');
        helpSection.classList.remove('hidden');
        navTexts.forEach(text => text.classList.remove('hidden'));
        
        if (logoImg) {
            logoImg.classList.remove('logo-collapsed');
            logoImg.classList.add('logo-expanded');
        }
        
        navLinks.forEach(link => link.classList.remove('justify-center'));
        
        // Save to localStorage
        localStorage.setItem('sidebarCollapsed', 'false');
        isCollapsed = false;
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeSidebarState();
    });

    // Mobile Menu Toggle
    mobileMenuBtn?.addEventListener('click', () => {
        isMobileOpen = !isMobileOpen;
        sidebar.classList.toggle('-translate-x-full', !isMobileOpen);
        mobileOverlay.classList.toggle('hidden', !isMobileOpen);
    });

    // Close mobile menu when clicking overlay
    mobileOverlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        mobileOverlay.classList.add('hidden');
        isMobileOpen = false;
    });

    // Desktop Sidebar Collapse
    toggleCollapse?.addEventListener('click', () => {
        if (isCollapsed) {
            expandSidebar();
        } else {
            collapseSidebar();
        }
    });

    // User Dropdown
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

    // Close mobile menu on resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            mobileOverlay.classList.add('hidden');
            isMobileOpen = false;
        } else if (!isMobileOpen) {
            sidebar.classList.add('-translate-x-full');
        }
    });
</script>