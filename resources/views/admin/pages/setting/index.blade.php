@extends('admin.layouts.master')

@section('title', 'সিস্টেম ও ব্যবসা সেটিংস')

@section('content')
<!-- Page Header & Breadcrumb -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">
                    <i class="fa-solid fa-sliders text-primary me-2"></i>ব্যবসা ও সিস্টেম সেটিংস
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    সানা ফিশারিজের লোগো, ফেভিকন, যোগাযোগের তথ্য এবং হ্যাচারি সেটিংস পরিচালনা করুন
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">সেটিংস</li>
                </ol>
            </nav>
        </div>

        <!-- Validation Errors Alert -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    <strong class="text-danger">কিছু তথ্য সঠিকভাবে পূরণ করা হয়নি:</strong>
                </div>
                <ul class="mb-0 text-sm ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Settings Card -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            
            <form action="{{ route('setting.store') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
                @csrf

                <!-- Tab Navigation Header -->
                <div class="card-header bg-white border-bottom p-3 p-md-4 pb-0">
                    <ul class="nav nav-pills custom-settings-tabs gap-2" id="settingsTab" role="tablist">
                        <!-- Tab 1: General Info -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                                    id="general-tab" data-bs-toggle="pill" data-bs-target="#general-pane" type="button" role="tab">
                                <i class="fa-solid fa-building"></i>
                                <span>সাধারণ তথ্য</span>
                            </button>
                        </li>

                        <!-- Tab 2: Logo & Favicon -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                                    id="branding-tab" data-bs-toggle="pill" data-bs-target="#branding-pane" type="button" role="tab">
                                <i class="fa-solid fa-palette"></i>
                                <span>লোগো ও ব্র্যান্ডিং</span>
                            </button>
                        </li>

                        <!-- Tab 3: Contact & Address -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                                    id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-pane" type="button" role="tab">
                                <i class="fa-solid fa-address-book"></i>
                                <span>যোগাযোগ ও ঠিকানা</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content Body -->
                <div class="card-body p-3 p-md-4">
                    <div class="tab-content" id="settingsTabContent">

                        <!-- TAB 1: General Info -->
                        <div class="tab-pane fade show active" id="general-pane" role="tabpanel">
                            <div class="row g-3 g-md-4">
                                <div class="col-12 border-bottom pb-2 mb-2">
                                    <h6 class="fw-bold text-dark m-0">ব্যবসায়িক প্রাথমিক পরিচিতি</h6>
                                    <small class="text-muted">প্রতিষ্ঠানের নাম, ধরণ ও মুদ্রা সংক্রান্ত সেটিংস</small>
                                </div>

                                <!-- Business Name -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="business_name">
                                        ব্যবসার নাম (Business Name) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-shop"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="business_name" name="business_name" 
                                               value="{{ $settings['business_name'] ?? 'সানা ফিশারিজ' }}" placeholder="প্রতিষ্ঠানের নাম লিখুন" required>
                                    </div>
                                </div>

                                <!-- Tagline / Slogan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="site_tagline">
                                        ট্যাগলাইন / স্লোগান (Tagline)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-quote-left"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="site_tagline" name="site_tagline" 
                                               value="{{ $settings['site_tagline'] ?? '' }}" placeholder="স্লোগান বা মূল বার্তা">
                                    </div>
                                </div>

                                <!-- Business Type -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary" for="business_type">
                                        ব্যবসার ধরণ (Business Category)
                                    </label>
                                    <input type="text" class="form-control" id="business_type" name="business_type" 
                                           value="{{ $settings['business_type'] ?? '' }}" placeholder="যেমন: মৎস্য হ্যাচারি ও নার্সারি">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Logo & Branding -->
                        <div class="tab-pane fade" id="branding-pane" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12 border-bottom pb-2 mb-2">
                                    <h6 class="fw-bold text-dark m-0">লোগো ও ওয়েবসাইট আইকন</h6>
                                    <small class="text-muted">সফটওয়্যার, ইনভয়েস ও হেডারে প্রদর্শিত লোগো এবং ফেভিকন আপলোড করুন</small>
                                </div>

                                <!-- Site Logo Box -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2">
                                            <span><i class="fa-regular fa-image text-primary me-2"></i>প্রধান লোগো (Main Logo)</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i>WebP কনভার্ট
                                            </span>
                                        </label>

                                        <!-- Preview Box -->
                                        <div class="bg-white p-3 rounded-3 border text-center mb-3 d-flex align-items-center justify-content-center position-relative" style="min-height: 120px;">
                                            @if (!empty($settings['site_logo']) && file_exists(public_path($settings['site_logo'])))
                                                <img id="logoPreview" src="{{ asset($settings['site_logo']) }}" alt="Logo" class="img-fluid" style="max-height: 90px; object-fit: contain;">
                                            @else
                                                <div id="logoDefaultPlaceholder" class="d-flex align-items-center gap-2">
                                                    <div class="rounded-3 p-2 bg-primary text-white"><i class="fa-solid fa-fish fs-4"></i></div>
                                                    <span class="fw-bold text-dark fs-5">সানা ফিশারিজ</span>
                                                </div>
                                                <img id="logoPreview" src="" alt="Logo" class="img-fluid d-none" style="max-height: 90px; object-fit: contain;">
                                            @endif
                                        </div>

                                        <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*" onchange="previewImage(this, 'logoPreview', 'logoDefaultPlaceholder')">
                                        <x-image-dimension-help width="240" height="60" format="PNG, JPG, WebP" max="2MB" note="স্বচ্ছ বা হালকা ব্যাকগ্রাউন্ড" />
                                    </div>
                                </div>

                                <!-- Site Favicon Box -->
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2">
                                            <span><i class="fa-solid fa-globe text-primary me-2"></i>ব্রাউজার ফেভিকন (Favicon)</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-wand-magic-sparkles me-1"></i>WebP কনভার্ট
                                            </span>
                                        </label>

                                        <!-- Preview Box -->
                                        <div class="bg-white p-3 rounded-3 border text-center mb-3 d-flex align-items-center justify-content-center position-relative" style="min-height: 120px;">
                                            @if (!empty($settings['site_favicon']) && file_exists(public_path($settings['site_favicon'])))
                                                <img id="faviconPreview" src="{{ asset($settings['site_favicon']) }}" alt="Favicon" class="img-fluid" style="width: 48px; height: 48px; object-fit: contain;">
                                            @else
                                                <div id="faviconDefaultPlaceholder" class="rounded-circle p-2 bg-info bg-opacity-25 text-info">
                                                    <i class="fa-solid fa-fish fs-3"></i>
                                                </div>
                                                <img id="faviconPreview" src="" alt="Favicon" class="img-fluid d-none" style="width: 48px; height: 48px; object-fit: contain;">
                                            @endif
                                        </div>

                                        <input type="file" class="form-control" id="site_favicon" name="site_favicon" accept="image/*,.ico" onchange="previewImage(this, 'faviconPreview', 'faviconDefaultPlaceholder')">
                                        <x-image-dimension-help width="32" height="32" format="PNG, ICO, WebP" max="1MB" note="স্কয়ার সাইজ (৩২x৩২ বা ৬৪x৬৪)" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: Contact & Address -->
                        <div class="tab-pane fade" id="contact-pane" role="tabpanel">
                            <div class="row g-3 g-md-4">
                                <div class="col-12 border-bottom pb-2 mb-2">
                                    <h6 class="fw-bold text-dark m-0">যোগাযোগ ও অফিস ঠিকানা</h6>
                                    <small class="text-muted">গ্রাহক ও খামারিদের সাথে যোগাযোগের ফোন, হোয়াটসঅ্যাপ ও ঠিকানার বিবরণ</small>
                                </div>

                                <!-- Mobile Number -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="mobile">
                                        মোবাইল নম্বর (Primary Phone) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="mobile" name="mobile" 
                                               value="{{ $settings['mobile'] ?? '' }}" placeholder="০১৭১১-XXXXXX" required>
                                    </div>
                                </div>

                                <!-- WhatsApp Number -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="whatsapp">
                                        হোয়াটসঅ্যাপ নম্বর (WhatsApp Number)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-success"><i class="fa-brands fa-whatsapp fs-5"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="whatsapp" name="whatsapp" 
                                               value="{{ $settings['whatsapp'] ?? '' }}" placeholder="+88017XXXXXXXX">
                                    </div>
                                    <small class="text-muted">সরাসরি অর্ডার চ্যাটের জন্য কান্ট্রি কোডসহ দিন (+880)</small>
                                </div>

                                <!-- Email Address -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="email">
                                        অফিসিয়াল ইমেইল (Official Email)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                                               value="{{ $settings['email'] ?? '' }}" placeholder="info@example.com">
                                    </div>
                                </div>

                                <!-- Alternative Mobile -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold text-secondary" for="alt_mobile">
                                        জরুরি সাপোর্ট বা দ্বিতীয় ফোন নম্বর
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-headset"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-0" id="alt_mobile" name="alt_mobile" 
                                               value="{{ $settings['alt_mobile'] ?? '' }}" placeholder="বিকল্প ফোন নম্বর">
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary" for="address">
                                        হ্যাচারি ও প্রধান অফিসের ঠিকানা (Address)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-location-dot"></i></span>
                                        <textarea class="form-control border-start-0 ps-0" id="address" name="address" rows="2" 
                                                  placeholder="অফিস বা হ্যাচারির পূর্ণাঙ্গ ঠিকানা লিখুন">{{ $settings['address'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Bar with Submit Button Spinner Trait -->
                <div class="card-footer bg-light p-3 p-md-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="text-muted" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-circle-info text-info me-1"></i>
                        পরিবর্তনসমূহ সাথে সাথেই সিস্টেমে কার্যকর হবে।
                    </div>

                    <button type="submit" 
                            class="btn-submit-with-spinner btn btn-primary px-4 py-2.5 rounded-pill shadow-sm fw-bold d-flex align-items-center gap-2"
                            data-loading-text="সেটিংস সংরক্ষণ হচ্ছে...">
                        <span class="btn-spinner d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="btn-content d-flex align-items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>সেটিংস সংরক্ষণ করুন (Save Settings)</span>
                        </span>
                    </button>
                </div>

            </form>
        </div>

<style>
    .custom-settings-tabs .nav-link {
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.88rem;
        transition: all 0.2s ease;
    }
    .custom-settings-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .custom-settings-tabs .nav-link.active {
        background: #4f46e5;
        color: #ffffff !important;
        border-color: #4f46e5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
    .custom-settings-tabs .nav-link i {
        font-size: 0.95rem;
    }
</style>

<script>
    // Live Image Preview Helper
    function previewImage(input, previewId, placeholderId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Tab persistence in URL hash
    document.addEventListener("DOMContentLoaded", function () {
        const hash = window.location.hash;
        if (hash) {
            const tabBtn = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }

        const tabButtons = document.querySelectorAll('#settingsTab button[data-bs-toggle="pill"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('data-bs-target');
                if (target) {
                    history.replaceState(null, null, target);
                }
            });
        });
    });
</script>
@endsection
