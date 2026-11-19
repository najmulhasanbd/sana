@extends('admin.layouts.master')

@section('title', 'নতুন বিক্রয় এন্ট্রি (New Sale)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-cart-shopping text-primary"></i>
                <span>নতুন বিক্রয় এন্ট্রি (New Sale)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}" class="text-decoration-none text-muted">বিক্রয় তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">নতুন বিক্রয়</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>বিক্রয় তালিকায় ফিরে যান</span>
            </a>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('admin.sales.store') }}" method="POST" id="saleForm">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    <strong class="text-danger">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</strong>
                </div>
                <ul class="mb-0 ps-4 text-danger small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <!-- Left Side: Invoice Header, Customer Info & Product Items -->
            <div class="col-12 col-xl-8">
                <!-- Card 1: Invoice & Customer Info -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-primary"></i>
                            <span>চালান ও ক্রেতা / কাস্টমার তথ্য</span>
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-semibold">
                            চালান নং: {{ $nextInvoiceNo }}
                        </span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- Existing Customer Select (Quick Search) -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    পূর্ববর্তী কাস্টমার নির্বাচন করুন (নাম বা মোবাইল দিয়ে খুঁজুন) <small class="text-muted">(নতুন হলে নিচে সরাসরি পূরণ করুন)</small>
                                </label>
                                <select id="existingCustomerSelect" class="form-select" data-placeholder="-- সংরক্ষিত কাস্টমার খুঁজুন বা নিচে নতুন তথ্য দিন --">
                                    <option value="">-- সংরক্ষিত কাস্টমার খুঁজুন বা নিচে নতুন তথ্য দিন --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                data-name="{{ $customer->name }}"
                                                data-mobile="{{ $customer->mobile }}" 
                                                data-division="{{ $customer->division_id }}"
                                                data-district="{{ $customer->district_id }}"
                                                data-zone="{{ $customer->zone_id }}"
                                                data-address="{{ $customer->address }}"
                                                data-due="{{ (float)($customer->total_due ?? 0) }}"
                                                data-location="{{ collect([$customer->zone?->name, $customer->district?->name])->filter()->implode(', ') }}">
                                            {{ $customer->name }} — {{ $customer->mobile }} {{ $customer->district ? "({$customer->district->name})" : '' }} {{ ($customer->total_due ?? 0) > 0 ? '(বকেয়া: ৳' . number_format($customer->total_due, 2) . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="customer_id" id="customerIdInput" value="{{ old('customer_id') }}">
                            </div>

                            <!-- Customer Name -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    কাস্টমারের নাম <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" 
                                           id="customerNameInput" 
                                           name="customer_name" 
                                           value="{{ old('customer_name') }}" 
                                           class="form-control @error('customer_name') is-invalid @enderror" 
                                           placeholder="যেমন: মোঃ রফিকুল ইসলাম" 
                                           required 
                                           style="font-size: 0.88rem;">
                                </div>
                            </div>

                            <!-- Customer Mobile (Unique) -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    মোবাইল নম্বর <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text" 
                                           id="customerMobileInput" 
                                           name="customer_mobile" 
                                           value="{{ old('customer_mobile') }}" 
                                           class="form-control @error('customer_mobile') is-invalid @enderror" 
                                           placeholder="যেমন: 017XXXXXXXX" 
                                           required 
                                           style="font-size: 0.88rem;">
                                </div>
                            </div>

                            <!-- Sale Date -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    বিক্রয়ের তারিখ <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="date" 
                                           name="sale_date" 
                                           value="{{ old('sale_date', date('Y-m-d')) }}" 
                                           class="form-control @error('sale_date') is-invalid @enderror" 
                                           required 
                                           style="font-size: 0.88rem;">
                                </div>
                            </div>

                            <!-- Division -->
                            <div class="col-12 col-md-4">
                                <label for="divisionSelect" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    বিভাগ (Division) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <select name="division_id" id="divisionSelect" class="select2 form-select" data-placeholder="-- বিভাগ নির্বাচন করুন --">
                                    <option value="">-- বিভাগ নির্বাচন করুন --</option>
                                    @foreach($divisions as $div)
                                        <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                            {{ $div->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- District -->
                            <div class="col-12 col-md-4">
                                <label for="districtSelect" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    জেলা (District) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <select name="district_id" id="districtSelect" class="select2 form-select" data-placeholder="-- জেলা নির্বাচন করুন --">
                                    <option value="">-- জেলা নির্বাচন করুন --</option>
                                    @foreach($districts as $dist)
                                        <option value="{{ $dist->id }}" data-division-id="{{ $dist->division_id }}" {{ old('district_id') == $dist->id ? 'selected' : '' }}>
                                            {{ $dist->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Zone -->
                            <div class="col-12 col-md-4">
                                <label for="zoneSelect" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    জোন / থানা (Zone) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <select name="zone_id" id="zoneSelect" class="select2 form-select" data-placeholder="-- জোন নির্বাচন করুন --">
                                    <option value="">-- জোন নির্বাচন করুন --</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" data-district-id="{{ $zone->district_id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    ঠিকানা (Address) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="text" 
                                       id="customerAddressInput" 
                                       name="customer_address" 
                                       value="{{ old('customer_address') }}" 
                                       class="form-control" 
                                       placeholder="যেমন: গ্রাম/মহল্লা, বাজার, খামার ঠিকানা..." 
                                       style="font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Sale Items Table -->
                @php
                    $rawMirOptions = \App\Models\Setting::get('mir_preset_options', '1800, 1900, 2000, 2100, 2200, 2300, 2400, 2500');
                    $mirPresetList = array_values(array_filter(array_map('trim', explode(',', $rawMirOptions))));
                @endphp

                <datalist id="mirSuggestionsList">
                    @foreach(($mirPresets ?? $mirPresetList) as $mPreset)
                        <option value="{{ $mPreset }}">{{ $mPreset }}</option>
                    @endforeach
                </datalist>

                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-fish text-primary"></i>
                                <span>বিক্রয়কৃত মাছ ও পোনার তালিকা (চালান বিবরণ)</span>
                            </h6>
                        </div>
                        <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-plus"></i>
                            <span>নতুন পোনা যোগ করুন</span>
                        </button>
                    </div>

                    <div class="card-body p-3 p-md-4 bg-light bg-opacity-40">
                        <div id="itemsContainer" class="d-flex flex-column gap-3">
                            @php
                                $oldItems = old('items', [
                                    ['sale_product_id' => '', 'box' => '', 'poly' => '', 'meer' => '', 'quantity' => '', 'unit' => 'পিস', 'unit_price' => '']
                                ]);
                            @endphp
                            @foreach($oldItems as $idx => $item)
                                <div class="item-row card border rounded-3 p-3 bg-white shadow-sm" data-index="{{ $idx }}" style="border-color: #e2e8f0;">
                                    <!-- ROW 1: Product Selection, Line Total Badge, and Delete Button -->
                                    <div class="row g-2 align-items-center pb-2 mb-2 border-bottom">
                                        <div class="col-12 col-md-7">
                                            <label class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1.5">
                                                <span class="badge bg-primary rounded-pill px-2 py-0.5 item-index-badge">#{{ $idx + 1 }}</span>
                                                <span>ব্র্যান্ড / মাছের পোনা নির্বাচন করুন</span>
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select name="items[{{ $idx }}][sale_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                                                <option value="">-- পণ্য নির্বাচন করুন --</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}" {{ (string)($item['sale_product_id'] ?? '') === (string)$prod->id ? 'selected' : '' }}>
                                                        {{ $prod->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-8 col-md-4 text-md-end">
                                            <label class="form-label text-secondary mb-1 small fw-semibold d-block">
                                                মোট দাম <small class="text-muted">(পোনা ÷ ১০০০ × দর)</small>
                                            </label>
                                            <span class="fw-bolder text-primary fs-5 item-total">৳0.00</span>
                                        </div>
                                        <div class="col-4 col-md-1 text-end">
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-0 d-flex align-items-center justify-content-center ms-auto btn-remove-row {{ count($oldItems) > 1 ? '' : 'd-none' }}" style="width: 32px; height: 32px;" title="আইটেম মুছুন">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- ROW 2: The 5 Packaging & Calculation Fields (Box, Poly, Mir, Total Fry, Dor) -->
                                    <div class="row g-2 align-items-end pt-1">
                                        <!-- 1. Box (Manual Input) -->
                                        <div class="col-6 col-sm-4 col-md-2">
                                            <label class="form-label text-secondary fw-semibold small mb-1">
                                                বক্স <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span>
                                            </label>
                                            <input type="number" 
                                                   name="items[{{ $idx }}][box]" 
                                                   value="{{ $item['box'] ?? '' }}" 
                                                   class="form-control text-center item-box" 
                                                   min="1" 
                                                   step="1" 
                                                   placeholder="যেমন: ৫">
                                        </div>

                                        <!-- 2. Poly (Auto: Double, Locked) -->
                                        <div class="col-6 col-sm-4 col-md-2">
                                            <label class="form-label text-secondary fw-semibold small mb-1">
                                                পলি <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">অটো (×২)</span>
                                            </label>
                                            <input type="number" 
                                                   name="items[{{ $idx }}][poly]" 
                                                   value="{{ $item['poly'] ?? '' }}" 
                                                   class="form-control text-center item-poly" 
                                                   min="1" 
                                                   step="1" 
                                                   placeholder="পলি" 
                                                   readonly 
                                                   tabindex="-1"
                                                   title="বক্সের দ্বিগুণ স্বয়ংক্রিয় বসবে (বক্স × ২)"
                                                   style="background-color: #f1f5f9; cursor: not-allowed; color: #475569; font-weight: 600;">
                                        </div>

                                        <!-- 3. Mir (Manual Input) -->
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <label class="form-label text-secondary fw-semibold small mb-1">
                                                মীর (প্রতি পলি) <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span>
                                            </label>
                                            <input type="number" 
                                                   name="items[{{ $idx }}][meer]" 
                                                   value="{{ $item['meer'] ?? '' }}" 
                                                   class="form-control text-center item-meer" 
                                                   min="1" 
                                                   step="1" 
                                                   list="mirSuggestionsList"
                                                   placeholder="যেমন: ২১০০">
                                        </div>

                                        <!-- 4. Total Fry (Auto: Poly * Mir, Locked) -->
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <label class="form-label text-secondary fw-semibold small mb-1">
                                                মোট পোনা <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">অটো (পলি×মীর)</span> <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" 
                                                   name="items[{{ $idx }}][quantity]" 
                                                   value="{{ $item['quantity'] ?? '' }}"
                                                   class="form-control text-center fw-bold item-qty" 
                                                   step="1" 
                                                   min="1" 
                                                   placeholder="মোট পোনা" 
                                                   readonly 
                                                   tabindex="-1"
                                                   title="পলি ও মীর গুণ হয়ে স্বয়ংক্রিয় বসবে (পলি × মীর)"
                                                   required 
                                                   style="background-color: #f1f5f9; cursor: not-allowed; color: #0284c7;">
                                            <input type="hidden" name="items[{{ $idx }}][unit]" value="পিস">
                                        </div>

                                        <!-- 5. Rate (Manual Input) -->
                                        <div class="col-12 col-sm-4 col-md-2">
                                            <label class="form-label text-secondary fw-semibold small mb-1">
                                                দর (হাজার ৳) <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span> <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" 
                                                   name="items[{{ $idx }}][unit_price]" 
                                                   value="{{ $item['unit_price'] ?? '' }}"
                                                   class="form-control text-center fw-semibold item-price" 
                                                   step="0.01" 
                                                   min="0" 
                                                   placeholder="যেমন: ৩৭০" 
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end align-items-center">
                        <button type="button" id="addRowBtnBottom" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-plus"></i>
                            <span>নতুন পোনা যোগ করুন</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Calculation & Payment Summary (Matching Challan Exactly) -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-white border-bottom p-3 p-md-4">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calculator text-primary"></i>
                            <span>হিসাব ও আদায় বিবরণী (চালান)</span>
                        </h6>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Challan Summary Box matching printed pad exactly -->
                        <div class="rounded-3 border overflow-hidden mb-3">
                            <table class="table table-sm mb-0 summary-challan-table" style="font-size: 0.88rem;">
                                <tbody>
                                    <!-- মোট (Current Sale Total) -->
                                    <tr class="border-bottom">
                                        <td class="py-2 ps-3 text-secondary fw-semibold">মোট (এই চালান):</td>
                                        <td class="py-2 pe-3 text-end fw-bold text-dark" id="displaySubTotal">৳0.00</td>
                                    </tr>
                                    <!-- পূর্ব জের (Customer Previous Due) -->
                                    <tr class="border-bottom bg-light bg-opacity-50">
                                        <td class="py-2 ps-3 text-secondary fw-semibold">পূর্ব জের:</td>
                                        <td class="py-2 pe-3 text-end fw-bold text-secondary" id="displayPrevDue">৳0.00</td>
                                    </tr>
                                    <!-- সর্বমোট (Grand Total = মোট + পূর্ব জের) -->
                                    <tr class="border-bottom" style="background-color: #f8fafc;">
                                        <td class="py-2 ps-3 text-dark fw-bold">সর্বমোট:</td>
                                        <td class="py-2 pe-3 text-end fw-bolder text-primary fs-6" id="displayGrandTotal">৳0.00</td>
                                    </tr>
                                    <!-- জমা (Paid Amount Input) -->
                                    <tr class="border-bottom" style="background-color: #f0fdf4;">
                                        <td class="py-2 ps-3 text-success fw-bold align-middle">জমা:</td>
                                        <td class="py-1 pe-2 text-end" style="width: 140px;">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white text-success border-end-0 fw-bold">৳</span>
                                                <input type="number" 
                                                       id="paidAmountInput" 
                                                       name="paid_amount" 
                                                       value="{{ old('paid_amount', '0') }}" 
                                                       class="form-control form-control-sm text-end fw-bold text-success border-start-0" 
                                                       step="0.01" 
                                                       min="0" 
                                                       placeholder="0.00">
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- পাওনা (Net Remaining Due) -->
                                    <tr id="dueTableRow" style="background-color: #fef2f2;">
                                        <td class="py-2 ps-3 fw-bold align-middle" id="dueLabel">পাওনা:</td>
                                        <td class="py-2 pe-3 text-end fw-bolder fs-6 text-danger" id="displayDueAmount">৳0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- টাকা (কথায়) Box -->
                        <div class="p-2.5 rounded-3 border bg-light mb-3">
                            <div class="d-flex align-items-center gap-1.5 text-secondary fw-semibold mb-1" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-coins text-warning"></i>
                                <span>টাকা (কথায়):</span>
                            </div>
                            <div id="displayInWords" class="fw-bold text-dark ps-1" style="font-size: 0.82rem; line-height: 1.4;">
                                শূন্য টাকা মাত্র
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পরিশোধের মাধ্যম (Payment Method)
                            </label>
                            <select name="payment_method" class="select2 form-select">
                                <option value="Cash" selected>ক্যাশ (Cash)</option>
                                <option value="bKash">বিকাশ (bKash)</option>
                                <option value="Nagad">নগদ (Nagad)</option>
                                <option value="Rocket">রকেট (Rocket)</option>
                                <option value="Bank">ব্যাংক ট্রান্সফার (Bank)</option>
                                <option value="Other">অন্যান্য (Other)</option>
                            </select>
                        </div>

                        <!-- Transaction ID -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                ট্রানজেকশন আইডি <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}" 
                                   class="form-control" 
                                   placeholder="যেমন: TRX987654" 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- Note -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <textarea name="note" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="যেমন: মাছ ও পোনা পরিবহন সংক্রান্ত বিশেষ নোট..." 
                                      style="font-size: 0.85rem;">{{ old('note') }}</textarea>
                        </div>

                        <!-- Auto Send SMS Toggle -->
                        <div class="mb-3 p-2.5 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-paper-plane text-primary fs-6"></i>
                                <div>
                                    <label class="form-check-label fw-bold text-dark mb-0 cursor-pointer" for="send_sms" style="font-size: 0.82rem;">
                                        স্বয়ংক্রিয় মেমো SMS পাঠান
                                    </label>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">চালান তৈরির সাথে সাথেই কাস্টমারের মোবাইলে SMS চলে যাবে</small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="send_sms" id="send_sms" value="1" 
                                       {{ old('send_sms', \App\Models\Setting::get('auto_sms_on_sale', '1')) == '1' ? 'checked' : '' }} style="cursor: pointer;">
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button type="submit" 
                                    class="btn btn-primary py-2.5 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-submit-with-spinner" 
                                    data-loading-text="চালান সংরক্ষণ হচ্ছে...">
                                <i class="fa-solid fa-check"></i>
                                <span>চালান নিশ্চিত ও সংরক্ষণ করুন</span>
                            </button>
                            <a href="{{ route('admin.sales.index') }}" class="btn btn-light border py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
                                বাতিল
                            </a>
                        </div>
                    </div>
                </div>
            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let rowIndex = {{ count($oldItems) }};
    const totalAvailableProducts = {{ $products->count() }};
    let customerPreviousDue = 0;

    // Options templates for dynamically added rows
    const productOptionsHtml = `@foreach($products as $prod)<option value="{{ $prod->id }}">{{ $prod->name }}</option>@endforeach`;

    // 1. Existing Customer Select with Name & Mobile Dual Search
    const $existingCustomer = $('#existingCustomerSelect');
    $existingCustomer.select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: '-- সংরক্ষিত কাস্টমার খুঁজুন বা নিচে নতুন তথ্য দিন --',
        allowClear: true,
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }
            if (typeof data.text === 'undefined') {
                return null;
            }
            const term = params.term.toLowerCase().replace(/[-+\s]/g, '');
            const text = data.text.toLowerCase().replace(/[-+\s]/g, '');
            const mobile = ($(data.element).data('mobile') || '').toString().toLowerCase().replace(/[-+\s]/g, '');

            if (text.indexOf(term) > -1 || mobile.indexOf(term) > -1) {
                return data;
            }
            return null;
        },
        templateResult: function(data) {
            if (!data.id) return data.text;
            const $el = $(data.element);
            const mobile = $el.data('mobile') || '';
            const location = $el.data('location') || '';
            const name = (data.text || '').split('—')[0].trim();

            return $(`
                <div class="d-flex justify-content-between align-items-center py-1">
                    <div>
                        <div class="fw-semibold text-dark">${name}</div>
                        ${location ? `<div class="text-muted" style="font-size: 0.75rem;"><i class="fa-solid fa-location-dot me-1 text-secondary"></i>${location}</div>` : ''}
                    </div>
                    ${mobile ? `<span class="badge bg-light text-primary border border-primary-subtle fw-semibold px-2 py-1"><i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i>${mobile}</span>` : ''}
                </div>
            `);
        },
        templateSelection: function(data) {
            if (!data.id) return data.text;
            const $el = $(data.element);
            const mobile = $el.data('mobile') || '';
            const name = (data.text || '').split('—')[0].trim();
            if (mobile) {
                return `${name} — 📞 ${mobile}`;
            }
            return data.text;
        }
    });

    // When customer selected from dropdown, autofill customer fields & previous due
    $existingCustomer.on('change', function() {
        const $selected = $(this).find('option:selected');
        const custId = $selected.val();

        if (custId) {
            $('#customerIdInput').val(custId);
            $('#customerNameInput').val($selected.data('name'));
            $('#customerMobileInput').val($selected.data('mobile'));
            $('#customerAddressInput').val($selected.data('address') || '');

            customerPreviousDue = parseFloat($selected.data('due')) || 0;
            $('#displayPrevDue').text('৳' + customerPreviousDue.toFixed(2));

            const divId = $selected.data('division');
            const distId = $selected.data('district');
            const zoneId = $selected.data('zone');

            if (divId) {
                $('#divisionSelect').val(divId).trigger('change');
                if (distId) {
                    setTimeout(() => {
                        $('#districtSelect').val(distId).trigger('change');
                        if (zoneId) {
                            setTimeout(() => {
                                $('#zoneSelect').val(zoneId).trigger('change.select2');
                            }, 50);
                        }
                    }, 50);
                }
            }
        } else {
            $('#customerIdInput').val('');
            customerPreviousDue = 0;
            $('#displayPrevDue').text('৳0.00');
        }

        calculateTotals();
    });

    // 2. Cascading Division -> District -> Zone
    const $division = $('#divisionSelect');
    const $district = $('#districtSelect');
    const $zone = $('#zoneSelect');

    const districtOptions = $district.find('option').clone();
    const zoneOptions = $zone.find('option').clone();

    $division.on('change', function() {
        const divId = $(this).val();

        $district.empty().append('<option value="">-- জেলা নির্বাচন করুন --</option>');
        $zone.empty().append('<option value="">-- জোন নির্বাচন করুন --</option>');

        if (divId) {
            districtOptions.each(function() {
                const $opt = $(this);
                if ($opt.val() && $opt.data('division-id') == divId) {
                    $district.append($opt.clone());
                }
            });
        } else {
            districtOptions.each(function() {
                if ($(this).val()) {
                    $district.append($(this).clone());
                }
            });
        }
        $district.val('').trigger('change.select2');
        $zone.val('').trigger('change.select2');
    });

    $district.on('change', function() {
        const distId = $(this).val();

        $zone.empty().append('<option value="">-- জোন নির্বাচন করুন --</option>');

        if (distId) {
            zoneOptions.each(function() {
                const $opt = $(this);
                if ($opt.val() && $opt.data('district-id') == distId) {
                    $zone.append($opt.clone());
                }
            });
        } else {
            zoneOptions.each(function() {
                if ($(this).val()) {
                    $zone.append($(this).clone());
                }
            });
        }
        $zone.val('').trigger('change.select2');
    });

    // 3. Product Select Initialization with formatting for disabled options
    function initProductSelect($element) {
        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- বিক্রয় পণ্য নির্বাচন করুন --',
            allowClear: true,
            templateResult: function(data) {
                if (!data.id) return data.text;
                const isDisabled = $(data.element).prop('disabled');
                if (isDisabled) {
                    return $(`
                        <div class="d-flex justify-content-between align-items-center py-1 text-muted">
                            <span class="text-decoration-line-through text-secondary">${data.text}</span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-0.5" style="font-size: 0.68rem;">
                                <i class="fa-solid fa-lock me-1"></i>ইতিমধ্যে নির্বাচিত
                            </span>
                        </div>
                    `);
                }
                return data.text;
            }
        });
    }

    // 4. Synchronize product options across all rows (Disable selected products in other rows)
    let isSyncingProducts = false;
    function syncProductOptions() {
        if (isSyncingProducts) return;
        isSyncingProducts = true;

        try {
            const selectedProductIds = [];
            $('.product-select').each(function() {
                const val = $(this).val();
                if (val && val !== '') {
                    selectedProductIds.push(val.toString());
                }
            });

            $('.product-select').each(function() {
                const $select = $(this);
                const currentVal = ($select.val() || '').toString();
                let optionChanged = false;

                $select.find('option').each(function() {
                    const optVal = $(this).val();
                    if (!optVal) return;

                    const shouldDisable = selectedProductIds.includes(optVal.toString()) && optVal.toString() !== currentVal;
                    const isCurrentlyDisabled = $(this).prop('disabled');

                    if (shouldDisable !== isCurrentlyDisabled) {
                        $(this).prop('disabled', shouldDisable);
                        optionChanged = true;
                    }
                });

                if (optionChanged && $select.hasClass('select2-hidden-accessible')) {
                    const curVal = $select.val();
                    $select.select2('destroy');
                    initProductSelect($select);
                    $select.val(curVal);
                }
            });
        } finally {
            isSyncingProducts = false;
        }
    }

    // Live Bengali Currency Words Converter
    function convertNumberToBnWords(amount) {
        amount = Math.round(amount);
        if (amount <= 0 || isNaN(amount)) {
            return 'শূন্য টাকা মাত্র';
        }

        const bnWords = [
            'শূন্য', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়', 'দশ',
            'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোলো', 'সতেরো', 'আঠারো', 'উনিশ', 'বিশ',
            'একুশ', 'বাইশ', 'তেইশ', 'চব্বিশ', 'পঁচিশ', 'ছাব্বিশ', 'সাতাশ', 'আঠাশ', 'উনত্রিশ', 'ত্রিশ',
            'একত্রিশ', 'বত্রিশ', 'তেত্রিশ', 'চৌঁত্রিশ', 'পঁয়ত্রিশ', 'ছত্রিশ', 'সাঁইত্রিশ', 'আটত্রিশ', 'উনচল্লিশ', 'চল্লিশ',
            'একচল্লিশ', 'বিয়াল্লিশ', 'তেতাল্লিশ', 'চুয়াল্লিশ', 'পঁয়তাল্লিশ', 'ছেচল্লিশ', 'সাতচল্লিশ', 'আটচল্লিশ', 'উনপঞ্চাশ', 'পঞ্চাশ',
            'একান্ন', 'বায়ান্ন', 'তিপ্পান্ন', 'চুয়ান্ন', 'পঞ্চান্ন', 'ছাপ্পান্ন', 'সাতান্ন', 'আটান্ন', 'উনষাট', 'ষাট',
            'একষট্টি', 'বাষট্টি', 'তেষট্টি', 'চৌষট্টি', 'পঁয়ষট্টি', 'ছেষট্টি', 'সাতষট্টি', 'আটষট্টি', 'উনসত্তর', 'সত্তর',
            'একাত্তর', 'বাহাত্তর', 'তিয়াত্তর', 'চুয়াত্তর', 'পঁচাত্তর', 'ছিয়াত্তর', 'সাতাত্তর', 'আটাত্তর', 'উনআশি', 'আশি',
            'একাশি', 'বিরাশি', 'তিরাশি', 'চুরাশি', 'পঁচাশি', 'ছিয়াশি', 'সাতাশি', 'আটাশি', 'উননব্বই', 'নব্বই',
            'একানব্বই', 'বানব্বই', 'তিরানব্বই', 'চুরানব্বই', 'পঁচানব্বই', 'ছিয়ানব্বই', 'সাতানব্বই', 'আটানব্বই', 'নিরানব্বই'
        ];

        let crore = Math.floor(amount / 10000000);
        amount %= 10000000;

        let lakh = Math.floor(amount / 100000);
        amount %= 100000;

        let thousand = Math.floor(amount / 1000);
        amount %= 1000;

        let hundred = Math.floor(amount / 100);
        let remainder = amount % 100;

        let parts = [];

        if (crore > 0) parts.push((bnWords[crore] || crore) + ' কোটি');
        if (lakh > 0) parts.push((bnWords[lakh] || lakh) + ' লক্ষ');
        if (thousand > 0) parts.push((bnWords[thousand] || thousand) + ' হাজার');
        if (hundred > 0) parts.push((bnWords[hundred] || hundred) + ' শত');
        if (remainder > 0) parts.push(bnWords[remainder] || remainder);

        return parts.join(' ') + ' টাকা মাত্র';
    }

    // 5. Calculate all line totals and grand total (Hatchery Rate is per 1,000 fry)
    function calculateTotals() {
        let currentSaleTotal = 0;

        $('#itemsContainer .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            
            // Hatchery Bagda fry is priced per 1,000 fry: (qty * price) / 1000
            const lineTotal = (qty * price) / 1000;

            $(this).find('.item-total').text('৳' + lineTotal.toFixed(2));
            currentSaleTotal += lineTotal;
        });

        $('#displaySubTotal').text('৳' + currentSaleTotal.toFixed(2));

        const grandTotal = currentSaleTotal + customerPreviousDue;
        $('#displayGrandTotal').text('৳' + grandTotal.toFixed(2));

        const paid = parseFloat($('#paidAmountInput').val()) || 0;
        const due = Math.max(0, grandTotal - paid);

        $('#displayDueAmount').text('৳' + due.toFixed(2));

        const $dueRow = $('#dueTableRow');
        const $dueLabel = $('#dueLabel');

        if (due <= 0 && grandTotal > 0) {
            $dueRow.css('background-color', '#f0fdf4');
            $('#displayDueAmount').removeClass('text-danger').addClass('text-success');
            $dueLabel.text('পরিশোধিত:').removeClass('text-danger text-warning').addClass('text-success');
        } else if (paid > 0 && due > 0) {
            $dueRow.css('background-color', '#fffbeb');
            $('#displayDueAmount').removeClass('text-success').addClass('text-warning');
            $dueLabel.text('আংশিক বকেয়া:').removeClass('text-success text-danger').addClass('text-warning');
        } else {
            $dueRow.css('background-color', '#fef2f2');
            $('#displayDueAmount').removeClass('text-success text-warning').addClass('text-danger');
            $dueLabel.text('পাওনা:').removeClass('text-success text-warning').addClass('text-danger');
        }

        // Live Bangla Words (Matches Challan: displays Due if exists, else Total)
        const wordsAmount = due > 0 ? due : (grandTotal > 0 ? grandTotal : currentSaleTotal);
        $('#displayInWords').text(convertNumberToBnWords(wordsAmount));
    }

    // 6. Add new product row (7 Columns matching Challan)
    function addNewRow() {
        const currentRowsCount = $('.item-row').length;
        if (totalAvailableProducts > 0 && currentRowsCount >= totalAvailableProducts) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'সকল পণ্য নির্বাচিত',
                    text: 'ডাটাবেজে থাকা সকল বিক্রয় পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!',
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: 'ঠিক আছে'
                });
            } else {
                alert('সকল বিক্রয় পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!');
            }
            return;
        }

        const newRowHtml = `
            <div class="item-row card border rounded-3 p-3 bg-white shadow-sm" data-index="${rowIndex}" style="border-color: #e2e8f0;">
                <!-- ROW 1: Product Selection, Total Price Badge & Delete Button -->
                <div class="row g-2 align-items-center pb-2 mb-2 border-bottom">
                    <div class="col-12 col-md-7">
                        <label class="form-label fw-bold text-dark mb-1 small d-flex align-items-center gap-1.5">
                            <span class="badge bg-primary rounded-pill px-2 py-0.5 item-index-badge">#${currentRowsCount + 1}</span>
                            <span>ব্র্যান্ড / মাছের পোনা নির্বাচন করুন</span>
                            <span class="text-danger">*</span>
                        </label>
                        <select name="items[${rowIndex}][sale_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                            <option value="">-- পণ্য নির্বাচন করুন --</option>
                            ${productOptionsHtml}
                        </select>
                    </div>
                    <div class="col-8 col-md-4 text-md-end">
                        <label class="form-label text-secondary mb-1 small fw-semibold d-block">
                            মোট দাম <small class="text-muted">(পোনা ÷ ১০০০ × দর)</small>
                        </label>
                        <span class="fw-bolder text-primary fs-5 item-total">৳0.00</span>
                    </div>
                    <div class="col-4 col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-0 d-flex align-items-center justify-content-center ms-auto btn-remove-row" style="width: 32px; height: 32px;" title="আইটেম মুছুন">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <!-- ROW 2: The 5 Packaging & Calculation Fields (Box, Poly, Mir, Total Fry, Dor) -->
                <div class="row g-2 align-items-end pt-1">
                    <div class="col-6 col-sm-4 col-md-2">
                        <label class="form-label text-secondary fw-semibold small mb-1">
                            বক্স <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span>
                        </label>
                        <input type="number" 
                               name="items[${rowIndex}][box]" 
                               class="form-control text-center item-box" 
                               min="1" 
                               step="1" 
                               placeholder="যেমন: ৫">
                    </div>
                    <div class="col-6 col-sm-4 col-md-2">
                        <label class="form-label text-secondary fw-semibold small mb-1">
                            পলি <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">অটো (×২)</span>
                        </label>
                        <input type="number" 
                               name="items[${rowIndex}][poly]" 
                               class="form-control text-center item-poly" 
                               min="1" 
                               step="1" 
                               placeholder="পলি" 
                               readonly 
                               tabindex="-1"
                               title="বক্সের দ্বিগুণ স্বয়ংক্রিয় বসবে (বক্স × ২)"
                               style="background-color: #f1f5f9; cursor: not-allowed; color: #475569; font-weight: 600;">
                    </div>
                    <div class="col-6 col-sm-4 col-md-3">
                        <label class="form-label text-secondary fw-semibold small mb-1">
                            মীর (প্রতি পলি) <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span>
                        </label>
                        <input type="number" 
                               name="items[${rowIndex}][meer]" 
                               class="form-control text-center item-meer" 
                               min="1" 
                               step="1" 
                               list="mirSuggestionsList"
                               placeholder="যেমন: ২১০০">
                    </div>
                    <div class="col-6 col-sm-4 col-md-3">
                        <label class="form-label text-secondary fw-semibold small mb-1">
                            মোট পোনা <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">অটো (পলি×মীর)</span> <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="items[${rowIndex}][quantity]" 
                               class="form-control text-center fw-bold item-qty" 
                               step="1" 
                               min="1" 
                               placeholder="মোট পোনা" 
                               readonly 
                               tabindex="-1"
                               title="পলি ও মীর গুণ হয়ে স্বয়ংক্রিয় বসবে (পলি × মীর)"
                               required 
                               style="background-color: #f1f5f9; cursor: not-allowed; color: #0284c7;">
                        <input type="hidden" name="items[${rowIndex}][unit]" value="পিস">
                    </div>
                    <div class="col-12 col-sm-4 col-md-2">
                        <label class="form-label text-secondary fw-semibold small mb-1">
                            দর (হাজার ৳) <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-0 px-1" style="font-size: 0.65rem;">ইনপুট</span> <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="items[${rowIndex}][unit_price]" 
                               class="form-control text-center fw-semibold item-price" 
                               step="0.01" 
                               min="0" 
                               placeholder="যেমন: ৩৭০" 
                               required>
                    </div>
                </div>
            </div>
        `;

        const $newRow = $(newRowHtml);
        $('#itemsContainer').append($newRow);
        rowIndex++;

        initProductSelect($newRow.find('select.product-select'));
        syncProductOptions();
        updateRemoveButtons();
        updateRowBadges();
    }

    $('#addRowBtn, #addRowBtnBottom').on('click', function() {
        addNewRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
            updateRemoveButtons();
            updateRowBadges();
            syncProductOptions();
        }
    });

    function updateRowBadges() {
        $('.item-row').each(function(i) {
            $(this).find('.item-index-badge').text('#' + (i + 1));
        });
    }

    function updateRemoveButtons() {
        const rowsCount = $('.item-row').length;
        $('.item-row').each(function() {
            if (rowsCount <= 1) {
                $(this).find('.btn-remove-row').addClass('d-none');
            } else {
                $(this).find('.btn-remove-row').removeClass('d-none');
            }
        });
    }

    $(document).on('change', '.product-select', function() {
        syncProductOptions();
        calculateTotals();
    });

    // 7. Hatchery Real-World Formula Event Handlers:
    // - Box ja dibo poli tar double hobe (Disabled/Readonly)
    // - Mir manually input dibo
    // - Poli & Mir gun hobe mot pona hobe (Disabled/Readonly)
    // - Mot pona 1000 diye vag diye dor gun hobe mot dam (Disabled/Readonly)
    function recalculateRow($row) {
        // 1. Box -> Poly (Double)
        const boxVal = parseFloat($row.find('.item-box').val());
        let polyVal = 0;
        if (!isNaN(boxVal) && boxVal > 0) {
            polyVal = Math.round(boxVal * 2);
            $row.find('.item-poly').val(polyVal);
        } else {
            $row.find('.item-poly').val('');
        }

        // 2. Mir (Manually input)
        const mirVal = parseFloat($row.find('.item-meer').val());

        // 3. Poly & Mir gun hobe mot pona hobe
        let totalFry = 0;
        if (polyVal > 0 && !isNaN(mirVal) && mirVal > 0) {
            totalFry = Math.round(polyVal * mirVal);
            $row.find('.item-qty').val(totalFry);
        } else {
            $row.find('.item-qty').val('');
        }

        // 4. Dor manually input, mot pona 1000 diye vag diye dor gun hobe mot dam
        const dorVal = parseFloat($row.find('.item-price').val());
        let lineTotal = 0;
        if (totalFry > 0 && !isNaN(dorVal) && dorVal >= 0) {
            lineTotal = (totalFry / 1000) * dorVal;
        }
        $row.find('.item-total').text('৳' + lineTotal.toFixed(2));
    }

    // Live calculation listeners for Box, Mir and Dor
    $(document).on('input change', '.item-box, .item-meer, .item-price', function() {
        const $row = $(this).closest('.item-row');
        recalculateRow($row);
        calculateTotals();
    });

    $(document).on('input', '#paidAmountInput', function() {
        calculateTotals();
    });

    // Ensure readonly/disabled inputs are submitted with form
    $('form').on('submit', function() {
        $('.item-poly, .item-qty').prop('readonly', false);
    });

    // Initialize all product selects on page load
    $('.product-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        initProductSelect($(this));
    });

    // Initial calculations on load
    const $initialSelectedCustomer = $existingCustomer.find('option:selected');
    if ($initialSelectedCustomer.val()) {
        customerPreviousDue = parseFloat($initialSelectedCustomer.data('due')) || 0;
        $('#displayPrevDue').text('৳' + customerPreviousDue.toFixed(2));
    }

    // Initial recalculations on load
    $('.item-row').each(function() {
        recalculateRow($(this));
    });

    syncProductOptions();
    calculateTotals();
    updateRemoveButtons();
    updateRowBadges();
});
</script>
@endpush
