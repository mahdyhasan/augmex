<!-- Start sidebar-wrapper -->
<div id="sidebar-wrapper" data-simplebar data-simplebar-auto-hide="true" class="border-right border-secondary-light bg-dark">
    <!-- Brand Logo -->
    <div class="brand-logo bg-dark text-center py-4 shadow-sm">
        <a href="{{ route('dashboard') }}" class="d-block">
            <img src="{{ asset('public/assets/img/logo.png') }}" class="logo-icon" alt="Logo" width="150">
        </a>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu do-nicescrol list-unstyled mt-3">
        <!-- Dashboard -->
        <li class="sidebar-header text-uppercase text-muted small font-weight-bold px-3 py-2">Admin Panel</li>

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link waves-effect text-white">
                <i class="icon-home mr-3"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <!-- Settings Dropdown -->
        <li class="nav-item">
            <a href="#" class="nav-link waves-effect text-white" data-toggle="collapse" data-target="#settingsMenu" aria-expanded="false">
                <i class="fa fa-indent mr-3"></i>
                <span>Settings</span>
                <i class="fa fa-angle-left float-right mt-1"></i>
            </a>
            <ul id="settingsMenu" class="sidebar-submenu collapse list-unstyled bg-dark-light pl-4">
                <li class="nav-item">
                    <a href="{{ route('setting.show') }}" class="nav-link text-white">
                        <i class="fa fa-cogs mr-2"></i>
                        General Settings
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>
<!-- End sidebar-wrapper -->