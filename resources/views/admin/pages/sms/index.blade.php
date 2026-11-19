@extends('admin.layouts.master')

@section('title', 'এসএমএস ম্যানেজমেন্ট ও নোটিফিকেশন সেন্টার')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-comment-sms text-primary"></i>
                <span>এসএমএস সেন্টার ও নোটিফিকেশন (SMS Center)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">যোগাযোগ ও মেসেজিং</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">এসএমএস সেন্টার</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customer-due.index') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>বকেয়া শিট</span>
            </a>
            <a href="{{ route('setting.index') }}#sms-pane" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-sliders"></i>
                <span>এসএমএস সেটিংস ও টেমপ্লেট</span>
            </a>
        </div>
    </div>

    <!-- Gateway Status Banner -->
    <div class="alert {{ $settings['sms_test_mode'] ? 'alert-info' : 'alert-success' }} border-0 shadow-sm rounded-4 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2.5">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white {{ $settings['sms_test_mode'] ? 'bg-info' : 'bg-success' }}" style="width: 36px; height: 36px;">
                <i class="fa-solid {{ $settings['sms_test_mode'] ? 'fa-flask' : 'fa-check' }}"></i>
            </div>
            <div>
                <strong class="d-block" style="font-size: 0.9rem;">
                    গেটওয়ে অবস্থা: 
                    @if($settings['sms_test_mode'])
                        <span class="badge bg-warning text-dark ms-1">সিমুলেশন / টেস্ট মোড সক্রিয়</span>
                    @else
                        <span class="badge bg-success ms-1">লাইভ মোড সক্রিয় ({{ strtoupper($settings['sms_gateway']) }})</span>
                    @endif
                </strong>
                <small class="text-muted" style="font-size: 0.78rem;">
                    @if($settings['sms_test_mode'])
                        টেস্ট মোডে এসএমএস পাঠালে কোনো আসল ব্যালেন্স খরচ হবে না; মেসেজের হিসাব ও লগ সিস্টেমে জমা হবে। লাইভ করতে <a href="{{ route('setting.index') }}#sms-pane" class="fw-bold text-decoration-none">অ্যাডমিন সেটিংসে যান</a>।
                    @else
                        প্রতিটি এসএমএস সরাসরি কাস্টমারদের মোবাইলে পৌঁছাবে। সেন্ডার আইডি: <strong>{{ $settings['sms_sender_id'] ?: 'নন-মাস্কিং' }}</strong>
                    @endif
                </small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-dark border px-3 py-1.5" style="font-size: 0.8rem;">
                মোট নিবন্ধিত কাস্টমার: <strong>{{ $customers->count() }} জন</strong>
            </span>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Sent -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-paper-plane fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট পাঠানো এসএমএস</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Sent -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের পাঠানো</small>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['today']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simulated -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-flask fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">টেস্ট / সিমুলেটেড</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['simulated']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Failed -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                        <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">ব্যর্থ এসএমএস</small>
                        <h4 class="fw-bold mb-0 text-danger">{{ number_format($stats['failed']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SMS Dispatch Center: 3 Sending Tabs (Single, Group Due, Broadcast) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3 p-md-4 pb-0">
            <ul class="nav nav-pills custom-sms-tabs gap-2" id="smsActionTabs" role="tablist">
                <!-- Tab 1: Single SMS -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                            id="single-sms-tab" data-bs-toggle="pill" data-bs-target="#single-sms-pane" type="button" role="tab">
                        <i class="fa-solid fa-user"></i>
                        <span>১. একক ভাবে পাঠান (Single SMS)</span>
                    </button>
                </li>

                <!-- Tab 2: Group Due Reminder -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                            id="group-due-tab" data-bs-toggle="pill" data-bs-target="#group-due-pane" type="button" role="tab">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        <span>২. বকেয়া কাস্টমারদের যৌথ এসএমএস</span>
                        <span class="badge bg-danger rounded-pill px-2">{{ $dueCustomersCount }} জন</span>
                    </button>
                </li>

                <!-- Tab 3: Broadcast SMS -->
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" 
                            id="broadcast-sms-tab" data-bs-toggle="pill" data-bs-target="#broadcast-sms-pane" type="button" role="tab">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>৩. সবাইকে একই সঙ্গে (Broadcast)</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="smsActionTabsContent">
                
                <!-- PANE 1: SINGLE SMS -->
                <div class="tab-pane fade show active" id="single-sms-pane" role="tabpanel">
                    <form action="{{ route('admin.sms.send-single') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="singleCustomerSelect" class="form-label fw-semibold text-secondary small">
                                    নিবন্ধিত কাস্টমার নির্বাচন করুন (ঐচ্ছিক)
                                </label>
                                <select id="singleCustomerSelect" class="form-select" onchange="fillCustomerMobile(this)">
                                    <option value="">-- কাস্টমার তালিকা হতে সিলেক্ট করুন --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->mobile }}" data-id="{{ $c->id }}" data-name="{{ $c->name }}">
                                            {{ $c->name }} ({{ $c->mobile }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="customer_id" id="singleCustomerId">
                            </div>

                            <div class="col-md-6">
                                <label for="singleMobileInput" class="form-label fw-semibold text-secondary small">
                                    প্রাপকের মোবাইল নম্বর <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">+88</span>
                                    <input type="text" 
                                           name="recipient" 
                                           id="singleMobileInput" 
                                           class="form-control" 
                                           placeholder="017xxxxxxxx" 
                                           required 
                                           style="font-size: 0.88rem;">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="singleMessageText" class="form-label fw-semibold text-secondary small mb-0">
                                        মেসেজের বিবরণ (বাংলা বা ইংরেজি) <span class="text-danger">*</span>
                                    </label>
                                    <span class="text-muted small" id="singleCharCount">০ অক্ষর (১ টি এসএমএস)</span>
                                </div>
                                <textarea name="message" 
                                          id="singleMessageText" 
                                          rows="3" 
                                          class="form-control" 
                                          placeholder="কাস্টমারকে পাঠানোর বার্তা লিখুন..." 
                                          required 
                                          oninput="countSmsChars(this, 'singleCharCount')"
                                          style="font-size: 0.88rem;"></textarea>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="মেসেজ পাঠানো হচ্ছে...">
                                    <i class="fa-solid fa-paper-plane me-1"></i>একক এসএমএস পাঠান
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PANE 2: GROUP DUE REMINDER -->
                <div class="tab-pane fade" id="group-due-pane" role="tabpanel">
                    <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-5 text-warning flex-shrink-0"></i>
                        <div style="font-size: 0.85rem;">
                            <strong>ডাইনামিক হিসাবের সুবিধা:</strong> এখানে মেসেজের ফরম্যাট বা টেমপ্লেট একটাই থাকবে, কিন্তু সফটওয়্যার নিজে থেকে স্বয়ংক্রিয়ভাবে প্রতিটি কাস্টমারের আসল নাম এবং <strong>যার যার নিজস্ব বকেয়া টাকার হিসাব</strong> বসিয়ে মেসেজ পাঠাবে!
                        </div>
                    </div>

                    <form action="{{ route('admin.sms.send-group-due') }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে সকল বকেয়া কাস্টমারকে তাদের নিজ নিজ বকেয়ার হিসাবসহ এসএমএস পাঠাতে চান?');">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">বিভাগভিত্তিক ফিল্টার (ঐচ্ছিক)</label>
                                <select name="division_id" class="form-select" style="font-size: 0.85rem;">
                                    <option value="">-- সকল বিভাগ --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}">{{ $div->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-secondary small">জেলা ভিত্তিক ফিল্টার (ঐচ্ছিক)</label>
                                <select name="district_id" class="form-select" style="font-size: 0.85rem;">
                                    <option value="">-- সকল জেলা --</option>
                                    @foreach($districts as $dis)
                                        <option value="{{ $dis->id }}">{{ $dis->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="dueTemplateText" class="form-label fw-semibold text-secondary small">
                                    বকেয়া তাগাদা টেমপ্লেট (সেটিংসের ডিফল্ট টেমপ্লেট ব্যবহৃত হবে, চাইলে এখানে পরিবর্তন করতে পারেন)
                                </label>
                                <textarea name="message" 
                                          id="dueTemplateText" 
                                          rows="3" 
                                          class="form-control" 
                                          style="font-size: 0.88rem;">{{ $settings['sms_template_due'] }}</textarea>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    ট্যাগসমূহ: <code class="text-primary">{customer_name}</code> = কাস্টমারের নাম, <code class="text-danger">{due_amount}</code> = ওই কাস্টমারের বকেয়া টাকা, <code class="text-dark">{company_phone}</code> = আপনার ফোন।
                                </small>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                                <span class="badge bg-light text-danger border p-2 fw-bold" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-users me-1"></i>সম্ভাব্য প্রাপক: প্রায় {{ $dueCustomersCount }} জন বকেয়া কাস্টমার
                                </span>
                                <button type="submit" class="btn btn-danger px-4 py-2 rounded-pill fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="বকেয়া এসএমএস পাঠানো হচ্ছে...">
                                    <i class="fa-solid fa-paper-plane me-1"></i>বকেয়া কাস্টমারদের এক ক্লিকে এসএমএস পাঠান
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PANE 3: BROADCAST SMS -->
                <div class="tab-pane fade" id="broadcast-sms-pane" role="tabpanel">
                    <form action="{{ route('admin.sms.send-broadcast') }}" method="POST" onsubmit="return confirm('আপনি কি নিশ্চিত যে সকল ({{ $customers->count() }} জন) কাস্টমারকে এই মেসেজটি একই সাথে পাঠাতে চান?');">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="broadcastMessageText" class="form-label fw-semibold text-secondary small mb-0">
                                        ঘোষণা বা প্রচারের মেসেজ <span class="text-danger">*</span>
                                    </label>
                                    <span class="text-muted small" id="broadcastCharCount">০ অক্ষর</span>
                                </div>
                                <textarea name="message" 
                                          id="broadcastMessageText" 
                                          rows="3" 
                                          class="form-control" 
                                          required 
                                          oninput="countSmsChars(this, 'broadcastCharCount')"
                                          style="font-size: 0.88rem;">{{ $settings['sms_template_broadcast'] }}</textarea>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    ট্যাগ: <code class="text-primary">{customer_name}</code> দিলে প্রত্যেকের ফোনে যার যার নাম স্বয়ংক্রিয়ভাবে বসে যাবে।
                                </small>
                            </div>

                            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                                <span class="badge bg-light text-primary border p-2 fw-bold" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-bullhorn me-1"></i>মোট {{ $customers->count() }} জন কাস্টমারের কাছে যাবে
                                </span>
                                <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="প্রচার এসএমএস যাচ্ছে...">
                                    <i class="fa-solid fa-tower-broadcast me-1"></i>সবাইকে একই সাথে এসএমএস পাঠান
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- SMS History & Delivery Logs Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    <span>এসএমএস প্রেরণের ইতিহাস ও ডেলিভারি লগ (SMS Delivery Logs)</span>
                </h6>
                <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                    মোট লগ: {{ number_format($logs->total()) }} টি
                </span>
            </div>

            <form action="{{ route('admin.sms.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Search -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="মোবাইল নম্বর বা টেক্সট দিয়ে খুঁজুন..." 
                               style="font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Type -->
                <div class="col-6 col-md-2">
                    <select name="type" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সব ধরন --</option>
                        <option value="single" {{ $type === 'single' ? 'selected' : '' }}>একক এসএমএস</option>
                        <option value="invoice_memo" {{ $type === 'invoice_memo' ? 'selected' : '' }}>মেমো চালান</option>
                        <option value="group_due" {{ $type === 'group_due' ? 'selected' : '' }}>বকেয়া তাগাদা</option>
                        <option value="broadcast" {{ $type === 'broadcast' ? 'selected' : '' }}>গণ সম্প্রচার</option>
                    </select>
                </div>

                <!-- Status -->
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সব স্ট্যাটাস --</option>
                        <option value="sent" {{ $status === 'sent' ? 'selected' : '' }}>সফল (Sent)</option>
                        <option value="simulated" {{ $status === 'simulated' ? 'selected' : '' }}>সিমুলেটেড (Test)</option>
                        <option value="failed" {{ $status === 'failed' ? 'selected' : '' }}>ব্যর্থ (Failed)</option>
                    </select>
                </div>

                <!-- Date -->
                <div class="col-6 col-md-2">
                    <input type="date" name="date" value="{{ $date ?? '' }}" class="form-control bg-light" style="font-size: 0.85rem;">
                </div>

                <!-- Filter Button -->
                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>খুঁজুন
                    </button>
                    @if(request()->hasAny(['search', 'type', 'status', 'date']))
                        <a href="{{ route('admin.sms.index') }}" class="btn btn-light border px-2.5 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 50px;">#</th>
                        <th class="py-3" style="width: 140px;">তারিখ ও সময়</th>
                        <th class="py-3" style="width: 160px;">প্রাপক ও কাস্টমার</th>
                        <th class="py-3" style="width: 120px;">ধরন</th>
                        <th class="py-3">মেসেজের মূল লেখা</th>
                        <th class="py-3 text-center" style="width: 110px;">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-end" style="width: 100px;">প্রেরক</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">
                                {{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $log->created_at->format('d M, Y') }}</div>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $log->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $log->recipient }}</div>
                                @if($log->customer)
                                    <small class="text-primary d-block">{{ $log->customer->name }}</small>
                                @endif
                                @if($log->sales_id && $log->sale)
                                    <a href="{{ route('admin.sales.show', $log->sales_id) }}" class="badge bg-light text-secondary border text-decoration-none" style="font-size: 0.7rem;">
                                        #{{ $log->sale->invoice_no }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($log->type === 'invoice_memo')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">মেমো চালান</span>
                                @elseif($log->type === 'group_due')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">বকেয়া তাগাদা</span>
                                @elseif($log->type === 'broadcast')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">সম্প্রচার</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">একক বার্তা</span>
                                @endif
                            </td>
                            <td>
                                <p class="mb-0 text-dark" style="font-size: 0.83rem; line-height: 1.4;">
                                    {{ $log->message }}
                                </p>
                            </td>
                            <td class="text-center">
                                @if($log->status === 'sent')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
                                        <i class="fa-solid fa-circle-check me-1"></i>সফল
                                    </span>
                                @elseif($log->status === 'simulated')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" title="টেস্ট মোডে রেকর্ড হয়েছে">
                                        <i class="fa-solid fa-flask me-1"></i>টেস্ট লগ
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" title="{{ $log->response }}">
                                        <i class="fa-solid fa-circle-xmark me-1"></i>ব্যর্থ
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end text-muted small">
                                {{ $log->sender?->name ?? 'সিস্টেম' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-comment-slash fs-2 mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0 fw-semibold">কোনো এসএমএস হিস্টোরি পাওয়া যায়নি।</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .custom-sms-tabs .nav-link {
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .custom-sms-tabs .nav-link:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    .custom-sms-tabs .nav-link.active {
        background: #0284c7;
        color: #ffffff !important;
        border-color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
    }
</style>

@push('scripts')
<script>
    function fillCustomerMobile(selectElem) {
        const selectedOpt = selectElem.options[selectElem.selectedIndex];
        const mobile = selectedOpt.value;
        const customerId = selectedOpt.getAttribute('data-id');
        
        document.getElementById('singleMobileInput').value = mobile || '';
        document.getElementById('singleCustomerId').value = customerId || '';
    }

    function countSmsChars(textarea, counterId) {
        const text = textarea.value;
        const length = text.length;
        // Simple BD Bangla / Unicode estimation: ~70 chars per SMS for Bengali
        const isBangla = /[\u0980-\u09FF]/.test(text);
        const limitPerSms = isBangla ? 70 : 160;
        const parts = length > 0 ? Math.ceil(length / limitPerSms) : 1;

        const counterElem = document.getElementById(counterId);
        if (counterElem) {
            counterElem.innerText = `${length} অক্ষর (${parts} টি এসএমএস)`;
        }
    }
</script>
@endpush
@endsection
