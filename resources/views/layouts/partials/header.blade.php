<!-- Start topbar header -->
<header class="topbar-nav">
    <nav class="navbar navbar-expand fixed-top bg-dark shadow-sm">
        <!-- Left Side: Toggle Menu and Company Name -->
        <ul class="navbar-nav mr-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link toggle-menu text-white" href="javascript:void(0);">
                    <i class="icon-menu menu-icon"></i>
                </a>
            </li>
            <li class="nav-item ml-3">
                <h5 class="mb-0 text-white font-weight-bold">{{ @$settings->company_name ?? 'Augmex' }}</h5>
            </li>
        </ul>

        <!-- Right Side: User Profile Dropdown -->
        <ul class="navbar-nav align-items-center right-nav-link">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret text-white" data-toggle="dropdown" href="#">
                    <span class="user-profile">
                        <img src="{{ asset('public/all-assets/expert/assets/images/avatars/avataruser.png') }}" class="img-circle" alt="user avatar" width="30">
                    </span>
                    <span class="ml-2">{{ Auth::user()->name }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-right animated fadeIn p-3">
                    <!-- User Details -->
                    <li class="dropdown-item user-details">
                        <div class="media align-items-center">
                            <div class="avatar mr-3">
                                <img src="{{ asset('public/all-assets/expert/assets/images/avatars/avataruser.png') }}" alt="user avatar" class="img-circle" width="50">
                            </div>
                            <div class="media-body">
                                <h6 class="mt-0 user-title">{{ Auth::user()->name }}</h6>
                                <p class="mb-0 user-subtitle text-muted">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </li>
                    <li class="dropdown-divider my-2"></li>

                    <!-- Logout -->
                    <li class="dropdown-item">
                        @auth
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-danger">
                                <i class="icon-power mr-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
<!-- End topbar header -->