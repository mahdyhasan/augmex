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
                                <li class="submenu">
                                    <a href="javascript:void(0);">
                                        <i class="ti ti-layout-2"></i><span>Dashboard</span><span class="menu-arrow"></span>
                                    </a>
                                    <ul>
                                        <li><a href="{{ route('dashboard') }}">Acounts Dashboard</a></li>
                                        <li><a href="leads-dashboard.html">HRM Dashboard</a></li>
                                        <li><a href="project-dashboard.html">Employee Dashboard</a></li>
                                        <li class="submenu">
                                            <a href="#"> <i class="ti ti-user-star"></i><span>Super Admin</span>
                                                <span class="menu-arrow"></span> </a>
                                            <ul>
                                                <li><a href="dashboard.html">Dashboard</a></li>
                                                <li><a href="company.html">Companies</a></li>
                                                <li><a href="subscription.html">Subscriptions</a></li>
                                                <li><a href="packages.html">Packages</a></li>
                                                <li><a href="domain.html">Domain</a></li>
                                                <li><a href="purchase-transaction.html">Purchase Transaction</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"><i class="ti ti-brand-airtable"></i><span>Accounts</span>
                                        <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="{{ route('expenses.index') }}">All Expenses</a></li>
                                        <li><a href="calendar.html">Calendar</a></li>
                                        <li><a href="email.html">Email</a></li>
                                        <li><a href="todo.html">To Do</a></li>
                                        <li><a href="notes.html">Notes</a></li>
                                        <li><a href="file-manager.html">File Manager</a></li>
                                        <li><a href="social-feed.html">Social Feed</a></li>
                                        <li><a href="kanban-view.html">Kanban</a></li>
                                        <li><a href="invoice.html">Invoices</a></li>
                                    </ul>
                                </li>
                                <li class="submenu">
                                    <a href="javascript:void(0);"><i class="ti ti-brand-airtable"></i><span>HRM</span>
                                        <span class="menu-arrow"></span></a>
                                    <ul>
                                        <li><a href="chat.html">Chat</a></li>
                                        <li><a href="calendar.html">Calendar</a></li>
                                        <li><a href="email.html">Email</a></li>
                                        <li><a href="todo.html">To Do</a></li>
                                        <li><a href="notes.html">Notes</a></li>
                                        <li><a href="file-manager.html">File Manager</a></li>
                                        <li><a href="social-feed.html">Social Feed</a></li>
                                        <li><a href="kanban-view.html">Kanban</a></li>
                                        <li><a href="invoice.html">Invoices</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <button><a href="{{ route('attendance.clockIn') }}">Clock In</a></button>
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
                            <img src="{{ asset('public/assets/img/profiles/avatar-20.jpg') }}" alt="Profile">
                        </span>
                        <span class="badge badge-success rounded-pill"></span>
                    </span>
                </a>
                <div class="dropdown-menu menu-drop-user">
                    <div class="profilename">
                        <a class="dropdown-item" href="{{ route('dashboard') }}"> <i class="ti ti-layout-2"></i> Dashboard </a>
                        <a class="dropdown-item" href="{{ route('attendance.index') }}"> <i class="ti ti-arrows-horizontal"></i> Attendance Sheet </a>
                        <a class="dropdown-item" href="{{ route('employee.profile') }}"> <i class="ti ti-user-pin"></i> My Profile </a>
                        <a class="dropdown-item" href=""> <i class="ti ti-money"></i> Commission </a>
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
            <a class="dropdown-item" href="{{ route('user.profile') }}"> <i class="ti ti-user-pin"></i> My Profile </a>
            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> <i class="ti ti-lock"></i> Logout </a>
        </div>
    </div>
    <!-- /Mobile Menu -->

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>

</div>
<!-- /Header -->