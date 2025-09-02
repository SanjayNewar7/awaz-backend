<!-- resources/views/superadmin/partials/sidebar.blade.php -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-bullhorn"></i>
            <span class="logo-text">Awaz Admin</span>
        </div>
        <button class="toggle-sidebar" id="toggle-sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <nav class="sidebar-menu">
         <a href="{{ route('superadmin.dashboard') }}" class="menu-item {{ Request::is('superadmin/dashboard') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fa-solid fa-house"></i>
            </div>
            <div class="menu-text">Dashboard</div>
        </a>


        <a href="{{ route('superadmin.analytics') }}" class="menu-item {{ Request::is('superadmin/analytics*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="menu-text">Analytics</div>
        </a>

        <a href="{{ route('superadmin.users') }}" class="menu-item {{ Request::is('superadmin/users*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="menu-text">Users</div>
        </a>

        <a href="{{ route('superadmin.issues') }}" class="menu-item {{ Request::is('superadmin/issues*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="menu-text">Issues</div>
        </a>

        <a href="{{ route('superadmin.verification') }}" class="menu-item {{ Request::is('superadmin/verification*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="menu-text">Verification</div>
        </a>

        <a href="{{ route('superadmin.notifications') }}" class="menu-item {{ Request::is('superadmin/notifications*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="menu-text">Notifications</div>
        </a>

        <a href="{{ route('superadmin.settings') }}" class="menu-item {{ Request::is('superadmin/settings*') ? 'active' : '' }}">
            <div class="menu-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="menu-text">Settings</div>
        </a>
    </nav>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        const toggleSidebarBtn = document.getElementById('toggle-sidebar');

        // Toggle sidebar
        toggleSidebarBtn.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Check if sidebar should be collapsed by default on mobile
        function checkScreenSize() {
            if (window.innerWidth < 768) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
            }
        }

        // Initial check
        checkScreenSize();

        // Listen for resize events
        window.addEventListener('resize', checkScreenSize);
    });
</script>
