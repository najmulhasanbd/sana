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
                        <!-- Tab 1: Business & Memo Challan Info (Unified, Zero Duplicates) -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                                    id="business-memo-tab" data-bs-toggle="pill" data-bs-target="#business-memo-pane" type="button" role="tab">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                                <span>ব্যবসা ও মেমো চালান সেটিংস</span>
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

                        <!-- Tab 3: SMS Gateway & Dynamic Templates -->
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                                    id="sms-tab" data-bs-toggle="pill" data-bs-target="#sms-pane" type="button" role="tab">
                                <i class="fa-solid fa-comment-sms"></i>
                                <span>এসএমএস ও টেমপ্লেট</span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content Body -->
                <div class="card-body p-3 p-md-4">
                    <div class="tab-content" id="settingsTabContent">

                        <!-- TAB 1: Business & Memo Challan Info (Merged & Zero Duplicates) -->
                        <div class="tab-pane fade show active" id="business-memo-pane" role="tabpanel">
                            
                            <!-- Section 1: Business Identity & Memo Header -->
                            <div class="card border border-light-subtle rounded-3 p-3 p-md-4 mb-4 bg-light bg-opacity-40">
                                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                    <div class="rounded-2 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-building" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">১. প্রতিষ্ঠানের মূল পরিচিতি ও মেমোর শিরোনাম</h6>
                                        <small class="text-muted" style="font-size: 0.78rem;">সাইডবার, হেডার এবং বিক্রয় মেমো প্যাডের প্রধান পরিচিতি</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Business / Memo Title -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="business_name">
                                            প্রতিষ্ঠানের নাম (Business / Memo Name) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-shop"></i></span>
                                            <input type="text" class="form-control" id="business_name" name="business_name" 
                                                   value="{{ old('business_name', $settings['business_name'] ?? $settings['memo_business_name'] ?? 'মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট') }}" 
                                                   placeholder="যেমন: মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট" required>
                                        </div>
                                    </div>

                                    <!-- Memo Subtitle / Tagline -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="memo_subtitle">
                                            ট্যাগলাইন / মেমো সাবটাইটেল (Tagline / Subtitle)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-quote-left"></i></span>
                                            <input type="text" class="form-control" id="memo_subtitle" name="memo_subtitle" 
                                                   value="{{ old('memo_subtitle', $settings['memo_subtitle'] ?? $settings['site_tagline'] ?? 'এখানে সকল প্রকার বাগদার পোনা নার্সিং করা হয় ও বিক্রয়ের নির্ভরযোগ্য প্রতিষ্ঠান।') }}" 
                                                   placeholder="স্লোগান বা মূল বার্তা">
                                        </div>
                                    </div>

                                    <!-- Business Category -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-secondary" for="business_type">
                                            ব্যবসার ধরণ (Business Category)
                                        </label>
                                        <input type="text" class="form-control" id="business_type" name="business_type" 
                                               value="{{ old('business_type', $settings['business_type'] ?? 'বাগদার পোনা নার্সিং ও মৎস্য হ্যাচারি') }}" 
                                               placeholder="যেমন: বাগদার পোনা নার্সিং ও মৎস্য হ্যাচারি">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Proprietor & Contacts -->
                            <div class="card border border-light-subtle rounded-3 p-3 p-md-4 mb-4 bg-light bg-opacity-40">
                                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                    <div class="rounded-2 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-user-tie" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">২. প্রোপাইটার ও যোগাযোগ নম্বরসমূহ</h6>
                                        <small class="text-muted" style="font-size: 0.78rem;">মেমো প্যাডের ১ম ও ২য় লাইন এবং প্রাতিষ্ঠানিক যোগাযোগ তথ্য</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Proprietor Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="proprietor_name">
                                            প্রোপাইটারের নাম (Proprietor Name)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-user"></i></span>
                                            <input type="text" class="form-control" id="proprietor_name" name="proprietor_name" 
                                                   value="{{ old('proprietor_name', $settings['proprietor_name'] ?? 'আলহাজ্ব নজরুল ইসলাম সানা') }}" 
                                                   placeholder="যেমন: আলহাজ্ব নজরুল ইসলাম সানা">
                                        </div>
                                    </div>

                                    <!-- Proprietor Mobile -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="proprietor_mobile">
                                            প্রোপাইটারের মোবাইল নম্বর (Primary Mobile) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-phone"></i></span>
                                            <input type="text" class="form-control" id="proprietor_mobile" name="proprietor_mobile" 
                                                   value="{{ old('proprietor_mobile', $settings['proprietor_mobile'] ?? $settings['mobile'] ?? '01737 908309') }}" 
                                                   placeholder="যেমন: 01737 908309" required>
                                        </div>
                                    </div>

                                    <!-- Other Contact Numbers -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="contact_numbers">
                                            অন্যান্য যোগাযোগ নম্বর (Contacts - Line 2)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-address-book"></i></span>
                                            <input type="text" class="form-control" id="contact_numbers" name="contact_numbers" 
                                                   value="{{ old('contact_numbers', $settings['contact_numbers'] ?? '01352 383531, 01352 383532') }}" 
                                                   placeholder="যেমন: 01352 383531, 01352 383532">
                                        </div>
                                    </div>

                                    <!-- WhatsApp -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-secondary" for="whatsapp">
                                            হোয়াটসঅ্যাপ নম্বর
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-success"><i class="fa-brands fa-whatsapp"></i></span>
                                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" 
                                                   value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}" 
                                                   placeholder="+88017xxxxxxxx">
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold text-secondary" for="email">
                                            অফিসিয়াল ইমেইল
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="{{ old('email', $settings['email'] ?? '') }}" 
                                                   placeholder="info@example.com">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Operator & Manager Details -->
                            <div class="card border border-light-subtle rounded-3 p-3 p-md-4 mb-4 bg-light bg-opacity-40">
                                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                    <div class="rounded-2 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-users-gear" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">৩. পরিচালনা ও ব্যবস্থাপক (Operator / Manager)</h6>
                                        <small class="text-muted" style="font-size: 0.78rem;">চালান মেমোর চালান নম্বরের পাশে প্রদর্শিত পরিচালকের তথ্য</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Operator Name -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="operator_name">
                                            পরিচালনায় (Operator / Manager Name)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-id-badge"></i></span>
                                            <input type="text" class="form-control" id="operator_name" name="operator_name" 
                                                   value="{{ old('operator_name', $settings['operator_name'] ?? 'মোমিনুর রহমান') }}" 
                                                   placeholder="যেমন: মোমিনুর রহমান">
                                        </div>
                                    </div>

                                    <!-- Operator Contact -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="operator_contact">
                                            পরিচালকের ফোন / নগদ / বিকাশ (Operator Contact)
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-mobile-screen-button"></i></span>
                                            <input type="text" class="form-control" id="operator_contact" name="operator_contact" 
                                                   value="{{ old('operator_contact', $settings['operator_contact'] ?? '01751-722350 NG/BK') }}" 
                                                   placeholder="যেমন: 01751-722350 NG/BK">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Addresses -->
                            <div class="card border border-light-subtle rounded-3 p-3 p-md-4 mb-4 bg-light bg-opacity-40">
                                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                    <div class="rounded-2 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-location-dot" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">৪. নার্সিং পয়েন্ট ও প্রধান অফিসের ঠিকানা</h6>
                                        <small class="text-muted" style="font-size: 0.78rem;">চালান মেমোর বেগুনী ব্যানারে এবং সফটওয়্যারের সিস্টেম ঠিকানায় প্রদর্শিত হবে</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Nursing Point Address -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="nursing_point_address">
                                            নার্সিং পয়েন্টের ঠিকানা (Nursing Point Address)
                                        </label>
                                        <input type="text" class="form-control" id="nursing_point_address" name="nursing_point_address" 
                                               value="{{ old('nursing_point_address', $settings['nursing_point_address'] ?? 'দক্ষিণ পুঁইজালা গেটের ধার, শ্রীউলা, আশাশুনি, সাতক্ষীরা।') }}" 
                                               placeholder="নার্সিং পয়েন্টের পূর্ণাঙ্গ ঠিকানা">
                                    </div>

                                    <!-- Office Address -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="office_address">
                                            অফিস ও হ্যাচারির প্রধান ঠিকানা (Office Address)
                                        </label>
                                        <input type="text" class="form-control" id="office_address" name="office_address" 
                                               value="{{ old('office_address', $settings['office_address'] ?? $settings['address'] ?? 'পুঁইজালা বাল্লা বাজার, আশাশুনি, সাতক্ষীরা।') }}" 
                                               placeholder="অফিস বা প্রধান কার্যালয়ের ঠিকানা">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Challan Footer & Signature -->
                            <div class="card border border-light-subtle rounded-3 p-3 p-md-4 mb-2 bg-light bg-opacity-40">
                                <div class="d-flex align-items-center gap-2 mb-3 border-bottom pb-2">
                                    <div class="rounded-2 p-1.5 bg-primary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                        <i class="fa-solid fa-signature" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark m-0">৫. মেমো ও চালান প্যাডের ফুটার ও স্বাক্ষর</h6>
                                        <small class="text-muted" style="font-size: 0.78rem;">মেমোর নিচের ধন্যবাদ বার্তা এবং কর্তৃপক্ষের সিল/স্বাক্ষর টেক্সট</small>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <!-- Memo Slogan -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="memo_slogan">
                                            মেমোর নিচের স্লোগান (Footer Slogan)
                                        </label>
                                        <input type="text" class="form-control" id="memo_slogan" name="memo_slogan" 
                                               value="{{ old('memo_slogan', $settings['memo_slogan'] ?? 'ধন্যবাদ আবার আসবেন | সততাই ব্যবসার মূল লক্ষ্য') }}" 
                                               placeholder="যেমন: ধন্যবাদ আবার আসবেন | সততাই ব্যবসার মূল লক্ষ্য">
                                    </div>

                                    <!-- Authority Signature -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-secondary" for="memo_authority_signature">
                                            অনুমোদনকারীর স্বাক্ষর টেক্সট (Authority Signature)
                                        </label>
                                        <input type="text" class="form-control" id="memo_authority_signature" name="memo_authority_signature" 
                                               value="{{ old('memo_authority_signature', $settings['memo_authority_signature'] ?? 'পক্ষে - মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট') }}" 
                                               placeholder="যেমন: পক্ষে - মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট">
                                    </div>
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

                        <!-- TAB 5: SMS Gateway & Dynamic Templates -->
                        <div class="tab-pane fade" id="sms-pane" role="tabpanel">
                            <div class="row g-4">
                                <!-- Section 1: Gateway Configuration -->
                                <div class="col-12 border-bottom pb-2 mb-1 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <div>
                                        <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-server text-primary"></i>
                                            <span>এসএমএস গেটওয়ে সংযোগ ও সক্রিয়করণ</span>
                                        </h6>
                                        <small class="text-muted">বাংলাদেশি বাল্ক এসএমএস গেটওয়ে সেটিংস ও লাইভ/টেস্ট মোড কনফিগার করুন</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#testSmsModal" style="font-size: 0.8rem;">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        <span>টেস্ট মেসেজ পাঠান</span>
                                    </button>
                                </div>

                                <!-- Switches: Enable & Test Mode -->
                                <div class="col-md-6">
                                    <div class="card border rounded-3 p-3 bg-light h-100">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="sms_enabled" name="sms_enabled" value="1"
                                                   {{ !empty($settings['sms_enabled']) && $settings['sms_enabled'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="sms_enabled">
                                                এসএমএস সার্ভিস চালু রাখুন (SMS Service Enabled)
                                            </label>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            চালু থাকলে মেমো তৈরি বা বকেয়া তাগাদার সময় কাস্টমারদের মোবাইলে মেসেজ পাঠানোর সুবিধা কার্যকর থাকবে।
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border rounded-3 p-3 bg-light h-100">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" role="switch" id="sms_test_mode" name="sms_test_mode" value="1"
                                                   {{ !isset($settings['sms_test_mode']) || $settings['sms_test_mode'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-primary" for="sms_test_mode">
                                                সিমুলেশন / টেস্ট মোড (Sandbox / Test Mode)
                                            </label>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            টেস্ট মোড চালু থাকলে কোনো আসল এসএমএস ব্যালেন্স কাটবে না; সিস্টেমের মেসেজ লগ ও মেমোর হিসাব যাচাই করা যাবে।
                                        </small>
                                    </div>
                                </div>

                                <!-- Auto Send SMS on Sale Creation -->
                                <div class="col-12">
                                    <div class="card border rounded-3 p-3 bg-light">
                                        <div class="form-check form-switch mb-1">
                                            <input class="form-check-input" type="checkbox" role="switch" id="auto_sms_on_sale" name="auto_sms_on_sale" value="1"
                                                   {{ !isset($settings['auto_sms_on_sale']) || $settings['auto_sms_on_sale'] == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-success" for="auto_sms_on_sale">
                                                <i class="fa-solid fa-paper-plane me-1"></i> চালান তৈরির সাথে সাথে স্বয়ংক্রিয় মেমো এসএমএস প্রেরণ (Auto SMS on Invoice Creation)
                                            </label>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.8rem;">
                                            এটি চালু থাকলে বিক্রয় চালান তৈরি হওয়ার সাথে সাথেই কাস্টমারের মোবাইলে স্বয়ংক্রিয়ভাবে মেমো এসএমএস চলে যাবে। আলাদা করে পরে পাঠাতে হবে না।
                                        </small>
                                    </div>
                                </div>

                                <!-- Gateway Selection -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary" for="sms_gateway">
                                        এসএমএস গেটওয়ে প্রোভাইডার
                                    </label>
                                    @php
                                        $selectedGateway = $settings['sms_gateway'] ?? 'sandbox';
                                    @endphp
                                    <select class="form-select" id="sms_gateway" name="sms_gateway">
                                        <option value="sandbox" {{ $selectedGateway === 'sandbox' ? 'selected' : '' }}>টেস্ট মোড / সিমুলেশন (API ছাড়া পরীক্ষা)</option>
                                        <option value="greenweb" {{ $selectedGateway === 'greenweb' ? 'selected' : '' }}>Greenweb BD (গ্রিনওয়েব)</option>
                                        <option value="mimsms" {{ $selectedGateway === 'mimsms' ? 'selected' : '' }}>MimSMS (মিমএসএমএস)</option>
                                        <option value="elitbuzz" {{ $selectedGateway === 'elitbuzz' ? 'selected' : '' }}>ElitBuzz (এলিটবাজ)</option>
                                        <option value="generic" {{ $selectedGateway === 'generic' ? 'selected' : '' }}>অন্যান্য কাস্টম গেটওয়ে (Custom HTTP API)</option>
                                    </select>
                                </div>

                                <!-- Sender ID / Masking -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary" for="sms_sender_id">
                                        সেন্ডার আইডি / মাস্কিং নাম (Sender ID)
                                    </label>
                                    <input type="text" class="form-control" id="sms_sender_id" name="sms_sender_id" 
                                           value="{{ $settings['sms_sender_id'] ?? '' }}" 
                                           placeholder="যেমন: SanaFish বা অনুমোদিত নন-মাস্কিং নম্বর">
                                </div>

                                <!-- API Key / Token -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-secondary" for="sms_api_key">
                                        API কী বা টোকেন (API Key / Token)
                                    </label>
                                    <input type="password" class="form-control" id="sms_api_key" name="sms_api_key" 
                                           value="{{ $settings['sms_api_key'] ?? '' }}" 
                                           placeholder="প্রোভাইডার থেকে প্রাপ্ত API Key">
                                </div>

                                <!-- Custom API URL -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-secondary" for="sms_api_url">
                                        কাস্টম API URL (ঐচ্ছিক / ডিফল্ট স্বয়ংক্রিয়ভাবে ব্যবহৃত হয়)
                                    </label>
                                    <input type="url" class="form-control" id="sms_api_url" name="sms_api_url" 
                                           value="{{ $settings['sms_api_url'] ?? '' }}" 
                                           placeholder="http://api.greenweb.com.bd/api.php বা কাস্টম গেটওয়ে লিঙ্ক">
                                </div>

                                <!-- Section 2: Dynamic Templates -->
                                <div class="col-12 border-bottom pb-2 mt-4 mb-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-file-pen text-primary"></i>
                                                <span>এসএমএস টেমপ্লেট খাতা (ডাইনামিক মেমো ও হিসাব)</span>
                                            </h6>
                                            <small class="text-muted">মেসেজের ফরম্যাট একবারই লেখা থাকবে; মেমোর হিসাব স্বয়ংক্রিয়ভাবে ট্যাগ অনুযায়ী বসে যাবে</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Template 1: Sale Memo -->
                                <div class="col-12 col-lg-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-none">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1" for="sms_template_sale">
                                            <span>১. বিক্রয় মেমো এসএমএস টেমপ্লেট</span>
                                            <span class="badge bg-primary-subtle text-primary rounded-pill">চালান কনফার্মেশন</span>
                                        </label>
                                        <textarea class="form-control template-textarea mb-2" id="sms_template_sale" name="sms_template_sale" rows="4" style="font-size: 0.88rem;">{{ $settings['sms_template_sale'] ?? 'প্রিয় {customer_name}, সানা ফিশারিজ থেকে আপনার মেমো #{invoice_no} তৈরি হয়েছে। মোট বিল: ৳{total_amount}, জমা: ৳{paid_amount}, বকেয়া: ৳{due_amount}। ধন্যবাদ!' }}</textarea>
                                        
                                        <div class="tag-helper small">
                                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem;">ক্লিক করে ট্যাগ যুক্ত করুন:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{customer_name}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{invoice_no}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{total_amount}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{paid_amount}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{due_amount}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{sale_date}</span>
                                                <span class="badge bg-light text-primary border cursor-pointer tag-badge" data-target="sms_template_sale">{company_phone}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Template 2: Due Reminder -->
                                <div class="col-12 col-lg-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-none">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1" for="sms_template_due">
                                            <span>২. বকেয়া তাগাদা এসএমএস টেমপ্লেট</span>
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">বকেয়া রিমাইন্ডার</span>
                                        </label>
                                        <textarea class="form-control template-textarea mb-2" id="sms_template_due" name="sms_template_due" rows="4" style="font-size: 0.88rem;">{{ $settings['sms_template_due'] ?? 'জনাব {customer_name}, সানা ফিশারিজে আপনার পূর্বের মোট বকেয়া রয়েছে ৳{due_amount}। দ্রুত পরিশোধের জন্য অনুরোধ করা হলো। ফোন: {company_phone}' }}</textarea>
                                        
                                        <div class="tag-helper small">
                                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem;">ক্লিক করে ট্যাগ যুক্ত করুন:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-light text-danger border cursor-pointer tag-badge" data-target="sms_template_due">{customer_name}</span>
                                                <span class="badge bg-light text-danger border cursor-pointer tag-badge" data-target="sms_template_due">{due_amount}</span>
                                                <span class="badge bg-light text-danger border cursor-pointer tag-badge" data-target="sms_template_due">{company_name}</span>
                                                <span class="badge bg-light text-danger border cursor-pointer tag-badge" data-target="sms_template_due">{company_phone}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Template 3: Payment Receipt -->
                                <div class="col-12 col-lg-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-none">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1" for="sms_template_payment">
                                            <span>৩. টাকা আদায় / পেমেন্ট রসিদ টেমপ্লেট</span>
                                            <span class="badge bg-success-subtle text-success rounded-pill">টাকা জমা</span>
                                        </label>
                                        <textarea class="form-control template-textarea mb-2" id="sms_template_payment" name="sms_template_payment" rows="4" style="font-size: 0.88rem;">{{ $settings['sms_template_payment'] ?? 'প্রিয় {customer_name}, আপনার ৳{paid_amount} সফলভাবে জমা হয়েছে। বর্তমান অবশিষ্ট বকেয়া: ৳{due_amount}। সানা ফিশারিজ।' }}</textarea>
                                        
                                        <div class="tag-helper small">
                                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem;">ক্লিক করে ট্যাগ যুক্ত করুন:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-light text-success border cursor-pointer tag-badge" data-target="sms_template_payment">{customer_name}</span>
                                                <span class="badge bg-light text-success border cursor-pointer tag-badge" data-target="sms_template_payment">{paid_amount}</span>
                                                <span class="badge bg-light text-success border cursor-pointer tag-badge" data-target="sms_template_payment">{due_amount}</span>
                                                <span class="badge bg-light text-success border cursor-pointer tag-badge" data-target="sms_template_payment">{company_phone}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Template 4: Broadcast Notice -->
                                <div class="col-12 col-lg-6">
                                    <div class="card border rounded-3 p-3 bg-white h-100 shadow-none">
                                        <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-1" for="sms_template_broadcast">
                                            <span>৪. সাধারণ নোটিশ / ঘোষণা / শুভেচ্ছা টেমপ্লেট</span>
                                            <span class="badge bg-info-subtle text-info rounded-pill">প্রচার ও অফার</span>
                                        </label>
                                        <textarea class="form-control template-textarea mb-2" id="sms_template_broadcast" name="sms_template_broadcast" rows="4" style="font-size: 0.88rem;">{{ $settings['sms_template_broadcast'] ?? 'সম্মানিত কাস্টমার, সানা ফিশারিজে উন্নত জাতের মাছের পোনার নতুন চালান এসেছে। যোগাযোগ: {company_phone}' }}</textarea>
                                        
                                        <div class="tag-helper small">
                                            <span class="text-muted d-block mb-1" style="font-size: 0.75rem;">ক্লিক করে ট্যাগ যুক্ত করুন:</span>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge bg-light text-info border cursor-pointer tag-badge" data-target="sms_template_broadcast">{customer_name}</span>
                                                <span class="badge bg-light text-info border cursor-pointer tag-badge" data-target="sms_template_broadcast">{company_name}</span>
                                                <span class="badge bg-light text-info border cursor-pointer tag-badge" data-target="sms_template_broadcast">{company_phone}</span>
                                            </div>
                                        </div>
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

<!-- ================= TEST SMS MODAL ================= -->
<div class="modal fade" id="testSmsModal" tabindex="-1" aria-labelledby="testSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">গেটওয়ে টেস্ট এসএমএস পাঠান</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">কনফিগারেশন সঠিক আছে কি না তা যাচাই করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.sms.test-gateway') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="testMobileInput" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            টেস্ট মোবাইল নম্বর <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">+88</span>
                            <input type="text" 
                                   id="testMobileInput" 
                                   name="test_mobile" 
                                   class="form-control" 
                                   placeholder="017xxxxxxxx" 
                                   value="{{ $settings['mobile'] ?? '' }}"
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="testMessageInput" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            টেস্ট মেসেজ <span class="text-danger">*</span>
                        </label>
                        <textarea id="testMessageInput" 
                                  name="test_message" 
                                  rows="3" 
                                  class="form-control" 
                                  required 
                                  style="font-size: 0.88rem;">সানা ফিশারিজ: এটি একটি টেস্ট এসএমএস। গেটওয়ে সংযোগ সফলভাবে সম্পন্ন হয়েছে।</textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="মেসেজ পাঠানো হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-paper-plane me-1"></i>টেস্ট মেসেজ পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>
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

        // Click tag to insert into template textarea
        document.querySelectorAll('.tag-badge').forEach(badge => {
            badge.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const tag = this.innerText.trim();
                const textarea = document.getElementById(targetId);
                if (textarea) {
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const text = textarea.value;
                    textarea.value = text.substring(0, start) + tag + text.substring(end);
                    textarea.focus();
                    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
                }
            });
        });
    });
</script>
@endsection
