<header class="app-header sticky d-flex align-items-center justify-content-between px-3 px-md-4">
    <!-- Header Left: Sidebar Toggle -->
    <div class="header-left d-flex align-items-center">
        <button class="btn-toggle-sidebar" id="sidebar-toggle" type="button" title="Sidebar Toggle">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <!-- Header Right: Profile Only (Aligned to the far right with ms-auto) -->
    <div class="header-right ms-auto d-flex align-items-center">
        <div class="user-profile dropdown">
            <div class="profile-toggle d-flex align-items-center" id="profileDropdown" onclick="toggleProfileMenu(event)" style="cursor: pointer;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=0284c7&color=fff&bold=true" class="rounded-circle user-img" alt="User" style="width: 38px; height: 38px; object-fit: cover;" />
                <div class="text-start ms-2 d-none d-md-block">
                    <p class="m-0 fw-bold user-name">
                        {{ Auth::user()->name ?? 'Mahabub' }}
                        <i class="fa-solid fa-chevron-down ms-1 dropdown-arrow"></i>
                    </p>
                    <small class="text-muted">Admin</small>
                </div>
            </div>

            <ul class="dropdown-menu-custom" id="profileMenu">
                <li>
                    <a href="{{ route('setting.index') }}">
                        <i class="fa-solid fa-sliders text-primary"></i> 
                        <span>Manage Account</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('password.change') }}">
                        <i class="fa-solid fa-key text-primary"></i> 
                        <span>Password Change</span>
                    </a>
                </li>
                <li class="border-top">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center w-100 text-danger border-0 bg-transparent py-2 px-3" style="font-size: 0.88rem; cursor: pointer;" data-loading-text="Logging out...">
                            <i class="fa-solid fa-right-from-bracket text-danger me-2"></i>
                            <span>Log out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
