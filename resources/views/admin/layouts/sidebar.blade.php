<aside class="app-sidebar shadow-lg" id="app-sidebar">
    <!-- Sidebar Brand / Logo Header -->
    <div class="sidebar-header border-bottom border-secondary border-opacity-25 px-3 py-3">
        <a href="{{ route('admin.dashboard') }}" class="logo text-decoration-none d-flex align-items-center gap-2.5">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" 
                 style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                <i class="fa-solid fa-fish fs-5"></i>
            </div>
            <div class="logo-text text-start overflow-hidden">
                <h6 class="m-0 fw-bold text-white tracking-wide text-truncate" style="font-size: 1.05rem; letter-spacing: -0.2px;">Sana Fisheries</h6>
                <small class="text-cyan-400 d-block text-truncate" style="font-size: 0.72rem; color: #38bdf8 !important;">Fish Fry & Hatchery Hub</small>
            </div>
        </a>
    </div>

    <!-- Sidebar Navigation Links -->
    <div class="sidebar-content py-2">
        <ul class="nav-list">
            <!-- Dashboard Link -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-house-chimney"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Section: Hatchery & Stock -->
            <!-- <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                Fish & Hatchery
            </li> -->

            <!-- Fish Fry Stock -->
            <!-- <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-fish-fins"></i>
                    <span>Fish Fry & Stock</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"></i>All Fry Stock</a></li>
                    <li><a href="#"></i>Add New Batch</a></li>
                    <li><a href="#"></i>Species & Sizes</a></li>
                </ul>
            </li> -->

            <!-- Orders & Sales -->
            <!-- <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Orders & Sales</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"></i>All Orders</a></li>
                    <li><a href="#"></i>Pending Orders</a></li>
                    <li><a href="#"></i>Delivered Orders</a></li>
                </ul>
            </li> -->

            <!-- Delivery & Transport -->
            <!-- <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>Delivery & Transport</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"></i>Oxygen Pack Tracking</a></li>
                    <li><a href="#"></i>Transport Cost</a></li>
                </ul>
            </li> -->

            <!-- Section: Parties & Accounts -->
            <!-- <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                Partners & Accounts
            </li> -->

            <!-- Suppliers (Direct Top-Level Menu for Quick Access) -->
            <li class="nav-item {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }}">
                <a href="{{ route('admin.supplier.index') }}">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>Suppliers</span>
                </a>
            </li>

            <!-- Purchase Management -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.purchase.*') || request()->routeIs('admin.purchase-product.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <i class="fa-solid fa-cart-flatbed"></i>
                    <span>Purchase Management</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.purchase.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.purchase.index') }}" class="{{ request()->routeIs('admin.purchase.index') ? 'text-primary fw-bold' : '' }}">
                            All Purchases
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.purchase-product.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.purchase-product.index') }}" class="{{ request()->routeIs('admin.purchase-product.*') ? 'text-primary fw-bold' : '' }}">
                            Purchase Products
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Sales Management -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.sales.*') || request()->routeIs('admin.sale-product.*') || request()->routeIs('admin.customer.*') || request()->routeIs('admin.customer-due.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <i class="fa-solid fa-boxes-packing"></i>
                    <span>Sales Management</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.sales.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.index') ? 'text-primary fw-bold' : '' }}">
                            All Sales
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.customer-due.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customer-due.index') }}" class="{{ request()->routeIs('admin.customer-due.*') ? 'text-primary fw-bold' : '' }}">
                            Customer Due Sheet
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customer.index') }}" class="{{ request()->routeIs('admin.customer.*') ? 'text-primary fw-bold' : '' }}">
                            Customers
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.sale-product.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sale-product.index') }}" class="{{ request()->routeIs('admin.sale-product.*') ? 'text-primary fw-bold' : '' }}">
                            Sale Products
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Staff & Payroll Management -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.employee.*') || request()->routeIs('admin.employee-salary.*') || request()->routeIs('admin.designation.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>Staff Management</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.employee.index') }}" class="{{ request()->routeIs('admin.employee.index') ? 'text-primary fw-bold' : '' }}">
                            All Employees
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.employee-salary.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.employee-salary.index') }}" class="{{ request()->routeIs('admin.employee-salary.*') ? 'text-primary fw-bold' : '' }}">
                            Salary Payments
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.designation.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.designation.index') }}" class="{{ request()->routeIs('admin.designation.*') ? 'text-primary fw-bold' : '' }}">
                            Designations
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Accounts & Billing -->
            <!-- <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>Accounts & Finance</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"></i>Daily Cash Book</a></li>
                    <li><a href="#"></i>Due & Collections</a></li>
                </ul>
            </li> -->

            <li class="nav-item has-dropdown {{ request()->routeIs('admin.division.*') || request()->routeIs('admin.district.*') || request()->routeIs('admin.zone.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Location Management</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.division.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.division.index') }}" class="{{ request()->routeIs('admin.division.*') ? 'text-primary fw-bold' : '' }}">
                            </i>Divisions
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.district.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.district.index') }}" class="{{ request()->routeIs('admin.district.*') ? 'text-primary fw-bold' : '' }}">
                            </i>Districts
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.zone.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.zone.index') }}" class="{{ request()->routeIs('admin.zone.*') ? 'text-primary fw-bold' : '' }}">
                            </i>Zones / Upazilas
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Section: System -->
            <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                System
            </li>
        </ul>
    </div>
</aside>

<style>
    /* Mini Sidebar Responsive behavior */
    body.sidebar-mini .app-sidebar:not(:hover) .logo-text,
    body.sidebar-mini .app-sidebar:not(:hover) .nav-section-title,
    body.sidebar-mini .app-sidebar:not(:hover) .sidebar-footer {
        display: none !important;
    }
</style>
