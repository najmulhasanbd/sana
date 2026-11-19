@php
    $sidebarLogo = \App\Models\Setting::get('site_logo');
    $sidebarBusinessName = \App\Models\Setting::get('business_name', 'সানা ফিশারিজ');
    $sidebarTagline = \App\Models\Setting::get('site_tagline') ?: \App\Models\Setting::get('business_type', 'Fish Fry & Hatchery Hub');
    $hasSidebarLogo = !empty($sidebarLogo) && file_exists(public_path($sidebarLogo));
@endphp

<aside class="app-sidebar shadow-lg" id="app-sidebar">
    <!-- Sidebar Brand / Logo Header (Dynamic from Settings) -->
    <div class="sidebar-header px-3 py-3 border-bottom border-white border-opacity-10 d-flex align-items-center">
        <a href="{{ route('admin.dashboard') }}" class="logo text-decoration-none d-flex align-items-center gap-2.5 w-100">
            @if($hasSidebarLogo)
                <div class="brand-logo-box rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden bg-white bg-opacity-10 p-1 border border-white border-opacity-15">
                    <img src="{{ asset($sidebarLogo) }}" alt="{{ $sidebarBusinessName }}" class="brand-logo-img">
                </div>
            @else
                <div class="brand-icon-box rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0">
                    <i class="fa-solid fa-fish-fins fs-5"></i>
                </div>
            @endif
            <div class="logo-text text-start overflow-hidden flex-grow-1">
                <div class="d-flex align-items-center justify-content-between gap-1">
                    <h6 class="m-0 fw-bold text-white text-truncate tracking-tight" style="font-size: 0.95rem; letter-spacing: -0.2px;" title="{{ $sidebarBusinessName }}">
                        {{ $sidebarBusinessName }}
                    </h6>
                    <span class="badge bg-primary bg-opacity-25 border border-primary border-opacity-25 rounded-pill px-1.5 py-0.5 flex-shrink-0" style="font-size: 0.62rem; color: #38bdf8 !important;">PRO</span>
                </div>
                <small class="d-block text-truncate" style="font-size: 0.7rem; color: #38bdf8 !important;" title="{{ $sidebarTagline }}">
                    {{ $sidebarTagline }}
                </small>
            </div>
        </a>
    </div>

    <!-- Sidebar Navigation Links -->
    <div class="sidebar-content py-2">
        <ul class="nav-list">

            <!-- 1. DASHBOARD -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') || request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}" class="nav-link-item">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <span class="nav-label">ড্যাশবোর্ড (Dashboard)</span>
                </a>
            </li>

            <!-- SECTION: OPERATIONS & INVENTORY -->
            <li class="nav-section-title">
                <span>ব্যবসা ও স্টক (Operations)</span>
            </li>

            <!-- Suppliers -->
            <li class="nav-item {{ request()->routeIs('admin.supplier.*') ? 'active' : '' }}">
                <a href="{{ route('admin.supplier.index') }}" class="nav-link-item">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <span class="nav-label">হ্যাচারি সরবরাহকারী</span>
                </a>
            </li>

            <!-- Purchase Management Dropdown -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.purchase.*') || request()->routeIs('admin.purchase-product.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <span class="nav-label">পোনা ক্রয় ও স্টক</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.purchase.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.purchase.index') }}" class="{{ request()->routeIs('admin.purchase.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>সকল ক্রয় চালান (All Purchases)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.purchase-product.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.purchase-product.index') }}" class="{{ request()->routeIs('admin.purchase-product.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>ক্রয় পণ্য তালিকা (Products)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Sales Management Dropdown -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.sales.*') || request()->routeIs('admin.sale-product.*') || request()->routeIs('admin.customer.*') || request()->routeIs('admin.customer-due.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="nav-label">বিক্রয় ও কাস্টমার</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.sales.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>সকল বিক্রয় মেমো (All Sales)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.customer-due.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customer-due.index') }}" class="{{ request()->routeIs('admin.customer-due.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot text-danger"></i>
                            <span>কাস্টমার বকেয়া শিট (Due Sheet)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customer.index') }}" class="{{ request()->routeIs('admin.customer.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>কাস্টমার তালিকা (Customers)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.sale-product.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sale-product.index') }}" class="{{ request()->routeIs('admin.sale-product.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>বিক্রয় পণ্য তালিকা (Products)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- SECTION: ACCOUNTS & FINANCE -->
            <li class="nav-section-title">
                <span>হিসাব ও ব্যয় (Finance)</span>
            </li>

            <!-- Accounts & Finance Dropdown -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.expense.*') || request()->routeIs('admin.expense-category.*') || request()->routeIs('admin.report.profit-loss') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <span class="nav-label">হিসাব ও লাভ-ক্ষতি</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.expense.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.expense.index') }}" class="{{ request()->routeIs('admin.expense.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>দৈনিক খরচ খাতা (Expenses)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.expense-category.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.expense-category.index') }}" class="{{ request()->routeIs('admin.expense-category.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>খরচের খাতসমূহ (Categories)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.report.profit-loss') ? 'active' : '' }}">
                        <a href="{{ route('admin.report.profit-loss') }}" class="{{ request()->routeIs('admin.report.profit-loss') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot text-success"></i>
                            <span>লাভ-ক্ষতি রিপোর্ট (Profit/Loss)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- SECTION: TEAM & MESSAGING -->
            <li class="nav-section-title">
                <span>কর্মী ও যোগাযোগ (Team & SMS)</span>
            </li>

            <!-- Staff & Payroll Dropdown -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.employee.*') || request()->routeIs('admin.employee-salary.*') || request()->routeIs('admin.designation.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <span class="nav-label">কর্মচারী ও বেতন</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                        <a href="{{ route('admin.employee.index') }}" class="{{ request()->routeIs('admin.employee.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>সকল কর্মচারী (Employees)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.employee-salary.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.employee-salary.index') }}" class="{{ request()->routeIs('admin.employee-salary.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>বেতন পরিশোধ (Salary)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.designation.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.designation.index') }}" class="{{ request()->routeIs('admin.designation.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>পদবী ও দায়িত্ব (Designations)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- SMS Center Link -->
            <li class="nav-item {{ request()->routeIs('admin.sms.*') ? 'active' : '' }}">
                <a href="{{ route('admin.sms.index') }}" class="nav-link-item">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-comment-sms"></i>
                    </div>
                    <span class="nav-label">এসএমএস সেন্টার (SMS Center)</span>
                </a>
            </li>

            <!-- SECTION: SYSTEM & SETUP -->
            <li class="nav-section-title">
                <span>সেটিংস ও সেটআপ (System)</span>
            </li>

            <!-- Locations Dropdown -->
            <li class="nav-item has-dropdown {{ request()->routeIs('admin.division.*') || request()->routeIs('admin.district.*') || request()->routeIs('admin.zone.*') ? 'open' : '' }}">
                <div class="dropdown-header">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <span class="nav-label">এলাকা ও জোন সেটিংস</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li class="{{ request()->routeIs('admin.division.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.division.index') }}" class="{{ request()->routeIs('admin.division.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>বিভাগ (Divisions)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.district.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.district.index') }}" class="{{ request()->routeIs('admin.district.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>জেলা (Districts)</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.zone.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.zone.index') }}" class="{{ request()->routeIs('admin.zone.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-circle-dot submenu-dot"></i>
                            <span>উপজেলা / জোন (Zones)</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Business Settings Link -->
            <li class="nav-item {{ request()->routeIs('setting.*') ? 'active' : '' }}">
                <a href="{{ route('setting.index') }}" class="nav-link-item">
                    <div class="nav-icon-box">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <span class="nav-label">ব্যবসা ও সিস্টেম সেটিংস</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Bottom User / Status Footer -->
    <div class="sidebar-footer border-top border-white border-opacity-10 p-3 mt-auto">
        <div class="d-flex align-items-center gap-2.5">
            <div class="avatar-box rounded-circle d-flex align-items-center justify-content-center text-white" 
                 style="width: 34px; height: 34px; background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); font-weight: 700; font-size: 0.8rem;">
                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
            <div class="overflow-hidden flex-grow-1">
                <span class="d-block text-white fw-semibold text-truncate" style="font-size: 0.82rem;">{{ Auth::user()->name ?? 'Admin User' }}</span>
                <small class="d-block text-truncate text-muted" style="font-size: 0.7rem; color: #94a3b8 !important;">Super Admin</small>
            </div>
            <a href="{{ route('admin.cache.clear') }}" class="btn-clean-cache text-muted" title="ক্যাশ ক্লিয়ার করুন">
                <i class="fa-solid fa-broom fs-7"></i>
            </a>
        </div>
    </div>
</aside>

<style>
    /* ========================================================= */
    /* MODERN, HUMAN-FRIENDLY & SILKY SMOOTH SIDEBAR STYLING    */
    /* ========================================================= */

    :root {
        --sidebar-bg: #090e1a;
        --sidebar-bg-gradient: linear-gradient(180deg, #090e1a 0%, #0f172a 100%);
        --sidebar-border: rgba(255, 255, 255, 0.07);
        --sidebar-text: #94a3b8;
        --sidebar-text-hover: #f8fafc;
        --sidebar-accent: #0284c7;
        --sidebar-accent-light: #38bdf8;
    }

    .app-sidebar {
        background: var(--sidebar-bg-gradient) !important;
        border-right: 1px solid var(--sidebar-border) !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        letter-spacing: -0.01em;
        display: flex;
        flex-direction: column;
        height: 100vh;
    }

    /* Logo Header */
    .brand-icon-box,
    .brand-logo-box {
        width: 38px;
        height: 38px;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: transform 0.2s ease;
    }

    .brand-icon-box {
        background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);
    }

    .brand-logo-box {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(4px);
    }

    .brand-logo-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    /* Section Titles */
    .nav-section-title {
        padding: 16px 18px 6px 18px !important;
        font-size: 0.67rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.9px !important;
        color: #475569 !important;
        display: flex;
        align-items: center;
    }

    /* Nav Items (Links & Dropdown Headers) */
    .nav-item a,
    .dropdown-header {
        display: flex !important;
        align-items: center !important;
        padding: 9px 12px !important;
        margin: 3px 10px !important;
        color: var(--sidebar-text) !important;
        font-size: 0.865rem !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        cursor: pointer;
        border-radius: 10px !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
        position: relative;
    }

    /* Dedicated Icon Box */
    .nav-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.04);
        margin-right: 11px;
        flex-shrink: 0;
        font-size: 0.92rem;
        color: #94a3b8;
        transition: all 0.22s ease;
    }

    .nav-label {
        flex-grow: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.35;
    }

    /* Hover States */
    .nav-item a:hover,
    .dropdown-header:hover {
        color: var(--sidebar-text-hover) !important;
        background: rgba(255, 255, 255, 0.06) !important;
    }

    .nav-item a:hover .nav-icon-box,
    .dropdown-header:hover .nav-icon-box {
        background: rgba(56, 189, 248, 0.15);
        color: var(--sidebar-accent-light);
        transform: scale(1.05);
    }

    /* Active State (Top-Level Nav Item) */
    .nav-item.active > a {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 15px rgba(2, 132, 199, 0.35) !important;
    }

    .nav-item.active > a .nav-icon-box {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
    }

    /* Open Dropdown State */
    .nav-item.has-dropdown.open > .dropdown-header {
        background: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
    }

    .nav-item.has-dropdown.open > .dropdown-header .nav-icon-box {
        color: var(--sidebar-accent-light);
        background: rgba(56, 189, 248, 0.12);
    }

    .nav-item.has-dropdown.open .arrow {
        transform: rotate(90deg);
        color: var(--sidebar-accent-light);
    }

    /* Dropdown Arrow Indicator */
    .arrow {
        margin-left: auto;
        font-size: 0.68rem;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: #64748b;
        flex-shrink: 0;
    }

    /* Modern Tree-Lined Submenu */
    .submenu {
        margin: 4px 10px 6px 25px !important;
        padding: 4px 0 4px 10px !important;
        border-left: 1.5px solid rgba(56, 189, 248, 0.2) !important;
        background: transparent !important;
        list-style: none !important;
    }

    .submenu li a {
        padding: 7px 10px !important;
        margin: 2px 0 !important;
        font-size: 0.825rem !important;
        color: #94a3b8 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-weight: 400 !important;
        transition: all 0.2s ease !important;
    }

    .submenu-dot {
        font-size: 0.45rem;
        opacity: 0.6;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .submenu li a:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.06) !important;
        padding-left: 14px !important;
    }

    .submenu li a:hover .submenu-dot {
        opacity: 1;
        color: var(--sidebar-accent-light);
        transform: scale(1.3);
    }

    /* Submenu Active Item */
    .submenu li.active a,
    .submenu li a.active {
        color: #38bdf8 !important;
        font-weight: 600 !important;
        background: rgba(2, 132, 199, 0.14) !important;
    }

    .submenu li.active a .submenu-dot,
    .submenu li a.active .submenu-dot {
        color: #38bdf8 !important;
        opacity: 1 !important;
    }

    /* Quick Clean Cache Button in Footer */
    .btn-clean-cache {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-clean-cache:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #38bdf8 !important;
    }

    /* Mini Sidebar Responsive behavior */
    body.sidebar-mini .app-sidebar:not(:hover) .logo-text,
    body.sidebar-mini .app-sidebar:not(:hover) .nav-section-title,
    body.sidebar-mini .app-sidebar:not(:hover) .sidebar-footer,
    body.sidebar-mini .app-sidebar:not(:hover) .nav-label,
    body.sidebar-mini .app-sidebar:not(:hover) .arrow,
    body.sidebar-mini .app-sidebar:not(:hover) .submenu {
        display: none !important;
    }

    body.sidebar-mini .app-sidebar:not(:hover) .nav-item a,
    body.sidebar-mini .app-sidebar:not(:hover) .dropdown-header {
        justify-content: center !important;
        padding: 9px 0 !important;
        margin: 3px 6px !important;
    }

    body.sidebar-mini .app-sidebar:not(:hover) .nav-icon-box {
        margin-right: 0 !important;
    }
</style>
