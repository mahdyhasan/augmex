<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="modern-profile p-3 pb-0">
        <div class="sidebar-nav mb-3">
            <ul class="nav nav-tabs nav-tabs-solid nav-tabs-rounded nav-justified bg-transparent"
                role="tablist">
                <!-- <li class="nav-item"><a class="nav-link active border-0" href="#">Menu</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="chat.html">Chats</a></li>
                <li class="nav-item"><a class="nav-link border-0" href="email.html">Inbox</a></li> -->
            </ul>
        </div>
    </div>

            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
            <li>
                <h6 class="submenu-hdr">Main Menu</h6>
                    <ul>
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
                            <i class="ti ti-wallet"></i><span>Accounts</span>
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
                            <i class="ti ti-users"></i><span>HRM</span>
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
                        <button class="btn btn-primary btn-sm w-100">
                            <a href="{{ route('attendance.clockIn') }}" class="text-white text-decoration-none">Clock In</a>
                        </button>
                    </li>
                </ul>
            </li>
        </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
