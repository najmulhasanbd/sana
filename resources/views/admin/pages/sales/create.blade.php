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
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-list-check text-primary"></i>
                            <span>বিক্রয়কৃত মাছ ও পণ্যের তালিকা</span>
                        </h6>
                        <button type="button" id="addRowBtn" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-plus"></i>
                            <span>পণ্য যোগ করুন</span>
                        </button>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0" id="itemsTable">
                                <thead class="bg-light table-light">
                                    <tr>
                                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 25%;">ব্র্যান্ড / পণ্য নির্বাচন <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 9%;">বক্স</th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 9%;">পলি</th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 9%;">মীর</th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 15%;">মোট পোনা (Qty) <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 11%;">একক</th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 11%;">দর (৳) <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 11%;">মোট (৳)</th>
                                        <th class="pe-4 py-3 text-center" style="width: 45px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @php
                                        $oldItems = old('items', [
                                            ['sale_product_id' => '', 'box' => '', 'poly' => '', 'meer' => '', 'quantity' => '', 'unit' => 'পিস', 'unit_price' => '']
                                        ]);
                                    @endphp
                                    @foreach($oldItems as $idx => $item)
                                        <tr class="item-row">
                                            <td class="ps-4">
                                                <select name="items[{{ $idx }}][sale_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                                                    <option value="">-- পণ্য নির্বাচন করুন --</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" {{ (string)($item['sale_product_id'] ?? '') === (string)$prod->id ? 'selected' : '' }}>
                                                            {{ $prod->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="items[{{ $idx }}][box]" 
                                                       value="{{ $item['box'] ?? '' }}"
                                                       class="form-control text-center" 
                                                       placeholder="বক্স" 
                                                       style="font-size: 0.85rem;">
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="items[{{ $idx }}][poly]" 
                                                       value="{{ $item['poly'] ?? '' }}"
                                                       class="form-control text-center" 
                                                       placeholder="পলি" 
                                                       style="font-size: 0.85rem;">
                                            </td>
                                            <td>
                                                <input type="text" 
                                                       name="items[{{ $idx }}][meer]" 
                                                       value="{{ $item['meer'] ?? '' }}"
                                                       class="form-control text-center" 
                                                       placeholder="মীর" 
                                                       style="font-size: 0.85rem;">
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="items[{{ $idx }}][quantity]" 
                                                       value="{{ $item['quantity'] ?? '' }}"
                                                       class="form-control text-center item-qty" 
                                                       step="0.01" 
                                                       min="0.01" 
                                                       placeholder="0" 
                                                       required 
                                                       style="font-size: 0.88rem;">
                                            </td>
                                            <td>
                                                <select name="items[{{ $idx }}][unit]" class="form-select form-select-sm text-center" style="font-size: 0.85rem;" required>
                                                    <option value="পিস" {{ ($item['unit'] ?? '') === 'পিস' ? 'selected' : '' }}>পিস</option>
                                                    <option value="কেজি" {{ ($item['unit'] ?? '') === 'কেজি' ? 'selected' : '' }}>কেজি</option>
                                                    <option value="মণ" {{ ($item['unit'] ?? '') === 'মণ' ? 'selected' : '' }}>মণ</option>
                                                    <option value="গ্রাম" {{ ($item['unit'] ?? '') === 'গ্রাম' ? 'selected' : '' }}>গ্রাম</option>
                                                    <option value="ড্রাম" {{ ($item['unit'] ?? '') === 'ড্রাম' ? 'selected' : '' }}>ড্রাম</option>
                                                    <option value="অন্যান্য" {{ ($item['unit'] ?? '') === 'অন্যান্য' ? 'selected' : '' }}>অন্যান্য</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="items[{{ $idx }}][unit_price]" 
                                                       value="{{ $item['unit_price'] ?? '' }}"
                                                       class="form-control text-center item-price" 
                                                       step="0.01" 
                                                       min="0" 
                                                       placeholder="0.00" 
                                                       required 
                                                       style="font-size: 0.88rem;">
                                            </td>
                                            <td class="text-end fw-bold text-dark item-total" style="font-size: 0.9rem;">
                                                ৳0.00
                                            </td>
                                            <td class="pe-4 text-center">
                                                <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger btn-remove-row {{ count($oldItems) > 1 ? '' : 'd-none' }}" style="width: 32px; height: 32px;">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top p-3 d-flex justify-content-between align-items-center">
                        <small class="text-muted">একাধিক মাছ/পণ্য থাকলে "পণ্য যোগ করুন" বাটনে ক্লিক করে সারি বৃদ্ধি করুন।</small>
                        <button type="button" id="addRowBtnBottom" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="fa-solid fa-plus"></i>
                            <span>আরও পণ্য যোগ করুন</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: Calculation & Payment Summary -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white sticky-top overflow-hidden" style="top: 20px;">
                    <div class="card-header bg-white border-bottom p-3 p-md-4">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calculator text-primary"></i>
                            <span>হিসাব ও আদায় বিবরণী</span>
                        </h6>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Subtotal / Total Amount -->
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">সর্বমোট মূল্য (Total):</span>
                                <span class="fw-bold text-dark fs-5" id="displayTotalAmount">৳0.00</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">পণ্যগুলোর মোট মূল্যের সমষ্টি</small>
                        </div>

                        <!-- Paid Amount Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                আদায়কৃত টাকা (Received Amount)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">৳</span>
                                <input type="number" 
                                       id="paidAmountInput" 
                                       name="paid_amount" 
                                       value="{{ old('paid_amount', '0') }}" 
                                       class="form-control fw-bold text-success border-start-0" 
                                       step="0.01" 
                                       min="0" 
                                       placeholder="0.00" 
                                       style="font-size: 0.95rem;">
                            </div>
                        </div>

                        <!-- Due Amount Display -->
                        <div class="p-3 rounded-3 border mb-3" id="dueContainer" style="background: #fff5f5; border-color: #fed7d7 !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-secondary fw-semibold" style="font-size: 0.85rem;">বকেয়া টাকা (Due Amount):</span>
                                <span class="fw-bold text-danger fs-5" id="displayDueAmount">৳0.00</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;" id="dueStatusNotice">সম্পূর্ণ বকেয়া প্রযোজ্য</small>
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
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <textarea name="note" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="যেমন: মাছ ডেলিভারি ও পরিবহন সংক্রান্ত বিশেষ নোট..." 
                                      style="font-size: 0.85rem;">{{ old('note') }}</textarea>
                        </div>

                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <button type="submit" 
                                    class="btn btn-primary py-2.5 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-submit-with-spinner" 
                                    data-loading-text="বিক্রয় সংরক্ষণ হচ্ছে...">
                                <i class="fa-solid fa-check"></i>
                                <span>বিক্রয় নিশ্চিত ও সংরক্ষণ করুন</span>
                            </button>
                            <a href="{{ route('admin.sales.index') }}" class="btn btn-light border py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
                                বাতিল
                            </a>
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

    // Product options template for dynamically added rows
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

    // When customer selected from dropdown, autofill customer fields
    $existingCustomer.on('change', function() {
        const $selected = $(this).find('option:selected');
        const custId = $selected.val();

        if (custId) {
            $('#customerIdInput').val(custId);
            $('#customerNameInput').val($selected.data('name'));
            $('#customerMobileInput').val($selected.data('mobile'));
            $('#customerAddressInput').val($selected.data('address') || '');

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
        }
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

    // 5. Calculate all line totals and grand total
    function calculateTotals() {
        let grandTotal = 0;

        $('#itemsTableBody .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const lineTotal = qty * price;

            $(this).find('.item-total').text('৳' + lineTotal.toFixed(2));
            grandTotal += lineTotal;
        });

        $('#displayTotalAmount').text('৳' + grandTotal.toFixed(2));

        const paid = parseFloat($('#paidAmountInput').val()) || 0;
        const due = Math.max(0, grandTotal - paid);

        $('#displayDueAmount').text('৳' + due.toFixed(2));

        const $dueContainer = $('#dueContainer');
        const $dueNotice = $('#dueStatusNotice');

        if (due <= 0 && grandTotal > 0) {
            $dueContainer.css({ 'background': '#f0fdf4', 'border-color': '#bbf7d0' });
            $('#displayDueAmount').removeClass('text-danger').addClass('text-success');
            $dueNotice.text('সম্পূর্ণ পরিশোধিত (Paid in Full)').removeClass('text-muted').addClass('text-success fw-semibold');
        } else if (paid > 0 && due > 0) {
            $dueContainer.css({ 'background': '#fffbeb', 'border-color': '#fde68a' });
            $('#displayDueAmount').removeClass('text-success').addClass('text-danger');
            $dueNotice.text('আংশিক বকেয়া (Partial Due)').removeClass('text-success').addClass('text-warning fw-semibold');
        } else {
            $dueContainer.css({ 'background': '#fff5f5', 'border-color': '#fed7d7' });
            $('#displayDueAmount').removeClass('text-success').addClass('text-danger');
            $dueNotice.text('সম্পূর্ণ বকেয়া প্রযোজ্য (Full Due)').removeClass('text-success text-warning').addClass('text-muted');
        }
    }

    // 6. Add new product row
    function addNewRow() {
        const currentRowsCount = $('#itemsTableBody .item-row').length;
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
            <tr class="item-row">
                <td class="ps-4">
                    <select name="items[${rowIndex}][sale_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                        <option value="">-- পণ্য নির্বাচন করুন --</option>
                        ${productOptionsHtml}
                    </select>
                </td>
                <td>
                    <input type="text" 
                           name="items[${rowIndex}][box]" 
                           class="form-control text-center" 
                           placeholder="বক্স" 
                           style="font-size: 0.85rem;">
                </td>
                <td>
                    <input type="text" 
                           name="items[${rowIndex}][poly]" 
                           class="form-control text-center" 
                           placeholder="পলি" 
                           style="font-size: 0.85rem;">
                </td>
                <td>
                    <input type="text" 
                           name="items[${rowIndex}][meer]" 
                           class="form-control text-center" 
                           placeholder="মীর" 
                           style="font-size: 0.85rem;">
                </td>
                <td>
                    <input type="number" 
                           name="items[${rowIndex}][quantity]" 
                           class="form-control text-center item-qty" 
                           step="0.01" 
                           min="0.01" 
                           placeholder="0" 
                           required 
                           style="font-size: 0.88rem;">
                </td>
                <td>
                    <select name="items[${rowIndex}][unit]" class="form-select form-select-sm text-center" style="font-size: 0.85rem;" required>
                        <option value="পিস" selected>পিস</option>
                        <option value="কেজি">কেজি</option>
                        <option value="মণ">মণ</option>
                        <option value="গ্রাম">গ্রাম</option>
                        <option value="ড্রাম">ড্রাম</option>
                        <option value="অন্যান্য">অন্যান্য</option>
                    </select>
                </td>
                <td>
                    <input type="number" 
                           name="items[${rowIndex}][unit_price]" 
                           class="form-control text-center item-price" 
                           step="0.01" 
                           min="0" 
                           placeholder="0.00" 
                           required 
                           style="font-size: 0.88rem;">
                </td>
                <td class="text-end fw-bold text-dark item-total" style="font-size: 0.9rem;">
                    ৳0.00
                </td>
                <td class="pe-4 text-center">
                    <button type="button" class="btn btn-sm btn-light border rounded-circle text-danger btn-remove-row" style="width: 32px; height: 32px;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </td>
            </tr>
        `;

        const $newRow = $(newRowHtml);
        $('#itemsTableBody').append($newRow);
        rowIndex++;

        initProductSelect($newRow.find('select.product-select'));
        syncProductOptions();
        updateRemoveButtons();
    }

    $('#addRowBtn, #addRowBtnBottom').on('click', function() {
        addNewRow();
    });

    $(document).on('click', '.btn-remove-row', function() {
        if ($('#itemsTableBody .item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
            updateRemoveButtons();
            syncProductOptions();
        }
    });

    function updateRemoveButtons() {
        const rowsCount = $('#itemsTableBody .item-row').length;
        if (rowsCount <= 1) {
            $('#itemsTableBody .btn-remove-row').addClass('d-none');
        } else {
            $('#itemsTableBody .btn-remove-row').removeClass('d-none');
        }
    }

    $(document).on('change', '.product-select', function() {
        syncProductOptions();
        calculateTotals();
    });

    $(document).on('input', '.item-qty, .item-price, #paidAmountInput', function() {
        calculateTotals();
    });

    // Initialize all product selects on page load
    $('.product-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        initProductSelect($(this));
    });

    syncProductOptions();
    calculateTotals();
    updateRemoveButtons();
});
</script>
@endpush
