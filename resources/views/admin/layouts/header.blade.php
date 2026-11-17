 <header class="app-header sticky d-flex align-items-center justify-content-between px-3">
      <div class="header-left d-flex align-items-center">
        <button class="btn-toggle-sidebar me-4" id="sidebar-toggle">
          <i class="fa fa-bars" id="toggle-icon"></i>
        </button>

        <div class="header-search d-none d-md-flex align-items-center">
          <i class="fa-solid fa-search search-icon"></i>
          <input type="text" class="form-control border-0 bg-transparent" placeholder="Search here...">
          <span class="search-shortcut">⌘K</span>
        </div>
      </div>

      <div class="header-right d-flex align-items-center gap-3">
        <div class="header-action-icon cursor-pointer">
          <img src="https://flagcdn.com/w20/us.png" alt="English" style="border-radius: 2px;">
        </div>
        <div class="header-action-icon cursor-pointer">
          <i class="fa-regular fa-moon"></i>
        </div>
        <div class="notification-bell cursor-pointer position-relative me-2">
          <i class="fa-regular fa-bell"></i>
          <span
            class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
            <span class="visually-hidden">New alerts</span>
          </span>
        </div>
        <div class="user-profile dropdown">
          <div class="profile-toggle d-flex align-items-center" id="profileDropdown" onclick="toggleProfileMenu(event)">
            <img src="https://classicit.com.bd/images/team/20260218022233F0kxgc.webp" class="rounded-circle user-img"
              alt="User" />
            <div class="text-start ms-2 d-none d-md-block">
              <p class="m-0 fw-bold user-name">
                Mahabub
                <i class="fa-solid fa-chevron-down ms-1 dropdown-arrow"></i>
              </p>
              <small class="text-muted">Admin</small>
            </div>
          </div>

          <ul class="dropdown-menu-custom" id="profileMenu">
            <li>
              <a href="#"><i class="fa-solid fa-user-gear text-primary"></i> Manage
                Account</a>
            </li>
            <li>
              <a href="theme.html"><i class="fa-brands fa-atlassian"></i>Theme</a>
            </li>
            <li>
              <a href="#"><i class="fa-solid fa-key text-pink"></i> Change Password</a>
            </li>
            <li class="border-top">
              <a href="#"><i class="fa-solid fa-right-from-bracket text-danger"></i>
                Log out</a>
            </li>
          </ul>
        </div>
      </div>
    </header>