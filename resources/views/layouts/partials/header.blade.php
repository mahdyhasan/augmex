<!-- Header -->
<div class="header">

    <!-- Logo -->
    <div class="header-left active">
        <a href="{{ route('dashboard') }}" class="logo logo-normal"> <img src="{{ asset('public/assets/img/logo.png') }}" alt="Logo">
            <img src="{{ asset('public/assets/img/white-logo.svg') }}" class="white-logo" alt="Logo"> </a>
        <a href="{{ route('dashboard') }}" class="logo-small"> <img src="{{ asset('public/assets/img/logo-small.svg') }}" alt="Logo"> </a>
        <a id="toggle_btn" href="javascript:void(0);"> <i class="ti ti-arrow-bar-to-left"></i> </a>
    </div>
    <!-- /Logo -->

    <a id="mobile_btn" class="mobile_btn" href="#sidebar">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>

    <div class="header-user">
        <ul class="nav user-menu">

            <!-- Search -->
            <li class="nav-item nav-search-inputs me-auto">
                <!-- <div class="top-nav-search">
                    <a href="javascript:void(0);" class="responsive-search"> <i class="fa fa-search"></i> </a>
                    <form action="#" class="dropdown">
                        <div class="searchinputs" id="dropdownMenuClickable">
                            <input type="text" placeholder="Search">
                            <div class="search-addon">
                                <button type="submit"><i class="ti ti-command"></i></button>
                            </div>
                        </div>
                    </form>
                </div> -->
            </li>
            <!-- /Search -->

            <!-- Horizontal Single -->
            <li>
                <div class="sidebar sidebar-horizontal" id="horizontal-single">
                    <div class="sidebar-menu">
                        <div class="main-menu">
                            <ul class="nav-menu">
                                <li class="menu-title">
                                    <span>Main</span>
                                </li>
                                
                                <!-- Super Admin Access -->
                                @if(auth()->user()->isSuperAdmin())
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-brand-airtable"></i><span>Super Admin</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ route('clients.index') }}">Clients</a></li>
                                        <li><a href="{{ route('accounts.incomeStatement') }}">Profit & Loss</a></li>
                                        <hr>
                                        <li><a href="{{ route('user.index') }}">User Management</a></li>
                                    </ul>
                                </li>
                                @endif

                                 <!-- Client Menu -->
                               
                                @if(auth()->user()->isSuperAdmin())
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-flag"></i><span>Client</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li class="submenu">
                                            <a href="javascript:void(0);">
                                                <span>Divanj</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <ul>
                                                <li><a href="{{ route('divanj.dashboard') }}">Dashboard</a></li>
                                                <li><a href="{{ route('divanj.sales.summary') }}">Sales Summary</a></li>
                                                <li><a href="{{ route('divanj.commission.index') }}">Commission</a></li>
                                                <li><a href="{{ route('divanj.narrative.report') }}">Narrative Report</a></li>
                                                <li><a href="{{ route('divanj.sales.report') }}">Sales Report</a></li>

                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                @endif


                                <!-- Accounts Access -->
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isAccountant())
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-brand-airtable"></i><span>Accounts</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ route('expenses.index') }}">All Expenses</a></li>
                                        <li><a href="{{ route('bank_accounts.index') }}">Bank Account</a></li>
                                        <li><a href="{{ route('fixed_assets.index') }}">Fixed Assets</a></li>
                                        <li><a href="{{ route('tax_payments.index') }}">Tax Payment</a></li>
                                        <li><a href="{{ route('petty_cash.index') }}">Petty Cash</a></li>
                                        <hr>
                                        <li><a href="{{ route('invoices.index') }}">Invoices</a></li>
                                        <li><a href="{{ route('client_payments.index') }}">Client Payments</a></li>
                                    </ul>
                                </li>
                                @endif

                                <!-- HR Access -->
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isHR())
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-brand-airtable"></i><span>HRM</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ route('employees.index') }}">Employees</a></li>
                                        <li><a href="{{ route('payrolls.index') }}">Payroll</a></li>
                                        <li><a href="{{ route('attendance.index') }}">Attendance Sheet</a></li>
                                        <hr>
                                        <li><a href="{{ route('career-applicants.index') }}">Applicants</a></li>
                                    </ul>
                                </li>
                                @endif

                                
                                <!-- Employee Section (Only for Employees) -->
                                @if(auth()->user()->isSuperAdmin() || auth()->user()->isHR() || auth()->user()->isAccountant() || auth()->user()->isUser())
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-link"></i><span>Agent</span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ route('attendance.index') }}">Attendance</a></li>
                                        <li><a href="{{ route('divanj.sales.report') }}">Sales Report</a></li>
                                        <li><a href="{{ route('divanj.narrative.report') }}">Narrative Report</a></li>
                                        <li><a href="{{ route('divanj.agent.commission.history') }}">Commission History</a></li>
                                    </ul>
                                </li>
                                @endif


                                <!-- Clock In (Available to All Users) -->
                                <li>
                                    <button class="btn btn-primary btn-sm">
                                        <a href="{{ route('attendance.clockIn') }}" class="text-white">Clock In</a>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </li>
            <!-- /Horizontal Single -->


            <!-- Nav List -->
            <li class="nav-item nav-list">
                <ul class="nav">
                    <li>
                        <div>
                            <a href="#" class="btn btn-icon border btn-menubar btnFullscreen">
                                <i class="ti ti-maximize"></i>
                            </a>
                        </div>
                    </li>
                    <li class="dark-mode-list">
                        <a href="javascript:void(0);" id="dark-mode-toggle" class="dark-mode-toggle">
                            <i class="ti ti-sun light-mode active"></i> <i class="ti ti-moon dark-mode"></i>
                        </a>
                    </li>               
                </ul>
            </li>
            <!-- /Nav List -->


            <!-- Profile Dropdown -->
            <li class="nav-item dropdown has-arrow main-drop">
                <a href="javascript:void(0);" class="nav-link userset" data-bs-toggle="dropdown">
                    <span class="user-info">
                        <span class="user-letter">
                            <img src="{{ asset('public/assets/img/profiles/tcl.png') }}" alt="Profile">
                        </span>
                        <span class="badge badge-success rounded-pill"></span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <a class="dropdown-item" href="{{ route('dashboard') }}"> <i class="ti ti-layout-2"></i> Dashboard </a>
                        <a class="dropdown-item" href="{{ route('employees.profile') }}"> <i class="ti ti-user-pin"></i> My Profile </a>
                        <a class="dropdown-item" href="{{ route('user.passwordChange') }}"> <i class="ti ti-unlock"></i> Change Password </a>
                        <hr>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="ti ti-lock"></i> Logout </a>
                    </div>
                </div>
            </li>
            <!-- /Profile Dropdown -->

        </ul>
    </div>

    <!-- Mobile Menu -->
    <div class="dropdown mobile-user-menu">
        <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('dashboard') }}"> <i class="ti ti-layout-2"></i> Dashboard </a>
            <a class="dropdown-item" href="{{ route('employees.profile') }}"> <i class="ti ti-user-pin"></i> My Profile </a>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="ti ti-lock"></i> Logout </a>
        </div>
    </div>
    <!-- /Mobile Menu -->

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

</div>
<!-- /Header -->