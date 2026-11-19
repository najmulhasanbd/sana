<header class="app-header sticky d-flex align-items-center justify-content-between px-3 px-md-4">
    <!-- Header Left: Sidebar Toggle Button -->
    <div class="header-left d-flex align-items-center flex-shrink-0">
        <button class="btn-toggle-sidebar flex-shrink-0" id="sidebar-toggle" type="button" title="সাইডবার মেনু টগল করুন">
            <i class="fa-solid fa-bars" id="toggle-icon"></i>
        </button>
    </div>

    <!-- Header Right: Quick Actions, Cache, Fullscreen & Profile -->
    <div class="header-right ms-auto d-flex align-items-center gap-1.5 gap-sm-2 flex-shrink-0">
        <!-- 1. Quick New Sale Button (+ নতুন বিক্রয়) -->
        <a href="{{ route('admin.sales.create') }}" class="btn btn-sm btn-header-action btn-header-primary text-decoration-none d-flex align-items-center gap-1.5 flex-shrink-0" title="নতুন বিক্রয় বা চালান তৈরি করুন">
            <i class="fa-solid fa-circle-plus"></i>
            <span class="d-none d-sm-inline">নতুন বিক্রয়</span>
            <span class="d-inline d-sm-none" style="font-size: 0.75rem;">বিক্রয়</span>
        </a>

        <!-- 2. Quick Expense Button (+ খরচ) -->
        <a href="{{ route('admin.expense.index') }}" class="btn btn-sm btn-header-action btn-header-soft-danger text-decoration-none d-none d-md-flex align-items-center gap-1.5 flex-shrink-0" title="দৈনিক প্রাতিষ্ঠানিক খরচ এন্ট্রি">
            <i class="fa-solid fa-receipt"></i>
            <span>খরচ</span>
        </a>

        <!-- 3. Quick SMS Button (এসএমএস) -->
        <a href="{{ route('admin.sms.index') }}" class="btn btn-sm btn-header-action btn-header-soft-sky text-decoration-none d-none d-md-flex align-items-center gap-1.5 flex-shrink-0" title="মেমো বা কাস্টমার বকেয়া এসএমএস পাঠান">
            <i class="fa-solid fa-comment-sms"></i>
            <span>এসএমএস</span>
        </a>

        <!-- Separator -->
        <div class="vr bg-slate-300 opacity-50 mx-1 d-none d-sm-block" style="height: 22px;"></div>

        <!-- 4. Artisan Cache Clear Action Button -->
        <a href="{{ route('admin.cache.clear') }}" 
           class="header-icon-btn flex-shrink-0" 
           title="সিস্টেম ক্যাশ ক্লিয়ার করুন (optimize:clear)">
            <i class="fa-solid fa-broom text-amber-500"></i>
        </a>

        <!-- 5. Fullscreen Toggle Button -->
        <button type="button" 
                class="header-icon-btn d-none d-sm-flex flex-shrink-0" 
                id="headerFullscreenBtn" 
                onclick="toggleHeaderFullscreen()" 
                title="ফুলস্ক্রিন মোড">
            <i class="fa-solid fa-expand text-slate-600" id="fullscreenIcon"></i>
        </button>

        <!-- 6. User Profile Dropdown Pill -->
        <div class="user-profile dropdown position-relative ms-0.5 flex-shrink-0">
            <div class="profile-pill d-flex align-items-center gap-1.5 gap-sm-2" id="profileDropdown" onclick="toggleProfileMenu(event)">
                <div class="profile-avatar-box flex-shrink-0">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=0284c7&color=fff&bold=true" class="rounded-circle profile-img" alt="User" />
                    <span class="avatar-online-dot"></span>
                </div>
                <div class="text-start d-none d-md-block profile-meta pe-1">
                    <div class="d-flex align-items-center gap-1">
                        <span class="user-name fw-bold text-slate-800 text-truncate" style="max-width: 105px; font-size: 0.83rem;">{{ Auth::user()->name ?? 'অ্যাডমিন' }}</span>
                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-1 py-0.2" style="font-size: 0.58rem; font-weight: 700;">PRO</span>
                    </div>
                    <small class="user-role text-muted d-block" style="font-size: 0.67rem; line-height: 1;">অ্যাডমিনিস্ট্রেটর</small>
                </div>
                <i class="fa-solid fa-chevron-down dropdown-arrow text-slate-400 me-0.5 d-none d-sm-inline-block" style="font-size: 0.68rem; transition: transform 0.25s ease;"></i>
            </div>

            <ul class="dropdown-menu-custom shadow-xl border border-slate-100" id="profileMenu" style="width: 250px; border-radius: 14px; overflow: hidden; padding: 0;">
                <!-- Dropdown Header Info -->
                <li class="p-3 bg-light border-bottom border-slate-100 d-flex align-items-center gap-2.5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=0284c7&color=fff&bold=true" class="rounded-circle shadow-xs" style="width: 40px; height: 40px; object-fit: cover;" alt="User">
                    <div class="overflow-hidden">
                        <h6 class="m-0 fw-bold text-dark text-truncate" style="font-size: 0.88rem;">{{ Auth::user()->name ?? 'অ্যাডমিন' }}</h6>
                        <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">{{ Auth::user()->email ?? 'admin@sanafish.com' }}</small>
                    </div>
                </li>
                
                <div class="py-1.5">
                    <!-- Quick Links for Small Screen Users -->
                    <li class="d-md-none">
                        <a href="{{ route('admin.expense.index') }}" class="dropdown-item-custom">
                            <div class="dropdown-icon-box text-danger bg-danger bg-opacity-10">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="dropdown-text">
                                <span class="fw-semibold">দৈনিক খরচ এন্ট্রি</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">প্রতিদিনের খরচ হিসাব</small>
                            </div>
                        </a>
                    </li>
                    <li class="d-md-none">
                        <a href="{{ route('admin.sms.index') }}" class="dropdown-item-custom">
                            <div class="dropdown-icon-box text-info bg-info bg-opacity-10">
                                <i class="fa-solid fa-comment-sms"></i>
                            </div>
                            <div class="dropdown-text">
                                <span class="fw-semibold">এসএমএস হাব</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">মেমো ও বকেয়া বার্তা</small>
                            </div>
                        </a>
                    </li>
                    <li class="d-md-none border-bottom border-slate-100 my-1"></li>

                    <li>
                        <a href="{{ route('setting.index') }}" class="dropdown-item-custom">
                            <div class="dropdown-icon-box text-primary bg-primary bg-opacity-10">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="dropdown-text">
                                <span class="fw-semibold">ব্যবসা ও সেটিংস</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">লোগো, এসএমএস ও ভাউচার</small>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('password.change') }}" class="dropdown-item-custom">
                            <div class="dropdown-icon-box text-warning bg-warning bg-opacity-10">
                                <i class="fa-solid fa-key"></i>
                            </div>
                            <div class="dropdown-text">
                                <span class="fw-semibold">পাসওয়ার্ড পরিবর্তন</span>
                                <small class="text-muted d-block" style="font-size: 0.68rem;">লগইন নিরাপত্তা</small>
                            </div>
                        </a>
                    </li>
                </div>

                <li class="border-top border-slate-100 p-2 bg-slate-50 bg-opacity-50">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-light text-danger w-100 d-flex align-items-center justify-content-center gap-2 py-1.5 rounded-3 fw-semibold border-0" style="font-size: 0.85rem;" data-loading-text="লগআউট হচ্ছে...">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>লগআউট করুন</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    function toggleHeaderFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                const icon = document.getElementById('fullscreenIcon');
                if (icon) icon.className = 'fa-solid fa-compress text-slate-600';
            }).catch(err => console.log(err));
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen().then(() => {
                    const icon = document.getElementById('fullscreenIcon');
                    if (icon) icon.className = 'fa-solid fa-expand text-slate-600';
                });
            }
        }
    }
</script>
