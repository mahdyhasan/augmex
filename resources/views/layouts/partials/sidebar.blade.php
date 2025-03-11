<!-- Start sidebar-wrapper -->
<div id="sidebar-wrapper" data-simplebar data-simplebar-auto-hide="true" class="border-right border-secondary-light bg-dark">
    <!-- Brand Logo -->
    <div class="brand-logo bg-dark text-center py-4 shadow-sm">
        <a href="{{ route('dashboard') }}" class="d-block">
            <img src="{{ asset('public/assets/img/logo.png') }}" class="logo-icon" alt="Logo" width="120">
        </a>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu do-nicescrol list-unstyled mt-3">
        <li class="sidebar-header text-uppercase text-muted small font-weight-bold px-3 py-2">Admin Panel</li>

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link waves-effect text-white">
                <i class="icon-home mr-3"></i> <span>Dashboard</span>
            </a>
        </li>

        <!-- Settings -->
        <li class="nav-item">
            <a href="{{ route('setting.show') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-cogs mr-3"></i> <span>Settings</span>
            </a>
        </li>

        <!-- HR Module -->
        <li class="sidebar-header text-uppercase text-muted small font-weight-bold px-3 py-2">HR Management</li>

        <li class="nav-item">
            <a href="{{ url('employees') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-users mr-3"></i> <span>Employees</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('attendance') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-calendar-check-o mr-3"></i> <span>Attendance</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('payroll') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-credit-card mr-3"></i> <span>Payroll</span>
            </a>
        </li>

        <!-- Client Management -->
        <li class="sidebar-header text-uppercase text-muted small font-weight-bold px-3 py-2">Client Management</li>

        <li class="nav-item">
            <a href="{{ route('clients.index') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-handshake-o mr-3"></i> <span>Clients</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('invoices.index') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-file-invoice-dollar mr-3"></i> <span>Invoices</span>
            </a>
        </li>

        <!-- Financial Management -->
        <li class="sidebar-header text-uppercase text-muted small font-weight-bold px-3 py-2">Finance</li>

        <li class="nav-item">
            <a href="{{ url('expenses') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-money mr-3"></i> <span>Expenses</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('transactions') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-exchange mr-3"></i> <span>Transactions</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('accounts') }}" class="nav-link waves-effect text-white">
                <i class="fa fa-university mr-3"></i> <span>Accounts</span>
            </a>
        </li>
    </ul>
</div>
<!-- End sidebar-wrapper -->
