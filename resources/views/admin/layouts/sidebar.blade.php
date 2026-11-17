<aside class="app-sidebar shadow-lg" id="app-sidebar">
    <!-- Sidebar Brand / Logo Header -->
    <div class="sidebar-header border-bottom border-secondary border-opacity-25 px-3 py-3">
        <a href="{{ route('admin.dashboard') }}" class="logo text-decoration-none d-flex align-items-center gap-2.5">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white shadow-sm flex-shrink-0" 
                 style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                <i class="fa-solid fa-fish fs-5"></i>
            </div>
            <div class="logo-text text-start overflow-hidden">
                <h6 class="m-0 fw-bold text-white tracking-wide text-truncate" style="font-size: 1.05rem; letter-spacing: -0.2px;">সানা ফিশারিজ</h6>
                <small class="text-cyan-400 d-block text-truncate" style="font-size: 0.72rem; color: #38bdf8 !important;">মাছের ছানা ও পোনা বাজার</small>
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
                    <span>ড্যাশবোর্ড (Dashboard)</span>
                </a>
            </li>

            <!-- Section: Hatchery & Stock -->
            <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                মৎস্য ও হ্যাচারি
            </li>

            <!-- Fish Fry Stock -->
            <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-fish-fins"></i>
                    <span>মাছের ছানা ও স্টক</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>সকল পোনার স্টক</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>নতুন পোনা ব্যাচ যোগ</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>জাত ও সাইজ ক্যাটাগরি</a></li>
                </ul>
            </li>

            <!-- Orders & Sales -->
            <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>অর্ডার ও বিক্রয়</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>সকল অর্ডার তালিকা</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>অপেক্ষমাণ চালান (Pending)</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>সম্পন্ন চালান (Delivered)</a></li>
                </ul>
            </li>

            <!-- Delivery & Transport -->
            <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>ডেলিভারি ও চালান</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>অক্সিজেন প্যাক ট্র্যাকিং</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>পরিবহন হিসাব</a></li>
                </ul>
            </li>

            <!-- Section: Parties & Accounts -->
            <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                পার্টনার ও হিসাব
            </li>

            <!-- Farmers & Hatcheries -->
            <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-users"></i>
                    <span>খামারি ও হ্যাচারি</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>খামারি ক্রেতা তালিকা</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>হ্যাচারি সরবরাহকারী</a></li>
                </ul>
            </li>

            <!-- Accounts & Billing -->
            <li class="nav-item has-dropdown">
                <div class="dropdown-header">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>হিসাব ও লেনদেন</span>
                    <i class="fa-solid fa-chevron-right arrow"></i>
                </div>
                <ul class="submenu">
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>দৈনিক ক্যাশ হিসাব</a></li>
                    <li><a href="#"><i class="fa-solid fa-circle-dot me-2" style="font-size: 7px;"></i>বকেয়া ও কালেকশন</a></li>
                </ul>
            </li>

            <!-- Section: System -->
            <li class="nav-section-title px-4 pt-3 pb-1 text-uppercase text-muted" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; opacity: 0.75;">
                সিস্টেম
            </li>

            <!-- Settings Link -->
            <li class="nav-item {{ request()->routeIs('setting.*') ? 'active' : '' }}">
                <a href="{{ route('setting.index') }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>সিস্টেম সেটিংস (Settings)</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Bottom Status Footer -->
    <div class="sidebar-footer p-3 mt-auto border-top border-secondary border-opacity-25">
        <div class="p-2.5 rounded-3 d-flex align-items-center gap-2" style="background: rgba(255, 255, 255, 0.05);">
            <div class="position-relative flex-shrink-0">
                <div class="rounded-circle bg-success" style="width: 10px; height: 10px;"></div>
                <div class="position-absolute top-0 start-0 rounded-circle bg-success animate-ping" style="width: 10px; height: 10px; opacity: 0.75;"></div>
            </div>
            <div class="overflow-hidden">
                <small class="text-white d-block fw-semibold text-truncate" style="font-size: 0.78rem;">হ্যাচারি ডাটাবেজ সচল</small>
                <small class="text-muted d-block text-truncate" style="font-size: 0.68rem;">v২.০ • নিরাপদ সিস্টেম</small>
            </div>
        </div>
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
