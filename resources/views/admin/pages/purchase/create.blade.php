@extends('admin.layouts.master')

@section('title', 'নতুন ক্রয় এন্ট্রি (New Purchase)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-cart-plus text-primary"></i>
                <span>নতুন ক্রয় এন্ট্রি (New Purchase)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.purchase.index') }}" class="text-decoration-none text-muted">ক্রয় তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">নতুন ক্রয়</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.purchase.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>ক্রয় তালিকায় ফিরে যান</span>
            </a>
        </div>
    </div>

    <!-- Form Container (Full Width) -->
    <form action="{{ route('admin.purchase.store') }}" method="POST" id="purchaseForm">
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
            <!-- Left Side: Invoice Header & Product Items -->
            <div class="col-12 col-xl-8">
                <!-- Card 1: Invoice & Supplier Info -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-file-invoice text-primary"></i>
                            <span>চালান ও সরবরাহকারী তথ্য</span>
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1.5 rounded-pill fw-semibold">
                            চালান নং: {{ $nextInvoiceNo }}
                        </span>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <div class="row g-3">
                            <!-- Supplier Selection with Select2 Search (Search by name or mobile) -->
                            <div class="col-12 col-md-7">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    সরবরাহকারী নির্বাচন করুন (নাম বা মোবাইল দিয়ে খুঁজুন) <span class="text-danger">*</span>
                                </label>
                                <select name="supplier_id" id="supplierSelect" class="select2 form-select @error('supplier_id') is-invalid @enderror" data-placeholder="-- সরবরাহকারীর নাম বা মোবাইল নম্বর লিখুন --" required>
                                    <option value="">-- সরবরাহকারীর নাম বা মোবাইল নম্বর লিখুন --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" 
                                                data-mobile="{{ $supplier->mobile }}" 
                                                data-location="{{ collect([$supplier->zone?->name, $supplier->district?->name])->filter()->implode(', ') }}"
                                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }} — {{ $supplier->mobile }} {{ $supplier->district ? "({$supplier->district->name})" : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Purchase Date -->
                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    ক্রয়ের তারিখ <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-regular fa-calendar"></i>
                                    </span>
                                    <input type="date" 
                                           name="purchase_date" 
                                           value="{{ old('purchase_date', date('Y-m-d')) }}" 
                                           class="form-control @error('purchase_date') is-invalid @enderror" 
                                           required 
                                           style="font-size: 0.88rem;">
                                </div>
                                @error('purchase_date')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mir Suggestions Preset Datalist -->
                @php
                    $rawMirOptions = \App\Models\Setting::get('mir_preset_options', '1800, 1900, 2000, 2100, 2200, 2300, 2400, 2500');
                    $mirPresetList = array_values(array_filter(array_map('trim', explode(',', $rawMirOptions))));
                @endphp

                <datalist id="mirSuggestionsList">
                    @foreach(($mirPresets ?? $mirPresetList) as $mPreset)
                        <option value="{{ $mPreset }}">{{ $mPreset }}</option>
                    @endforeach
                </datalist>

                <!-- Card 2: Purchase Items (Matching Sales Packaging 2-Row Layout) -->
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-fish text-primary"></i>
                                <span>ক্রয়কৃত মাছ ও পোনার তালিকা (চালান বিবরণ)</span>
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
                                    ['purchase_product_id' => '', 'box' => '', 'poly' => '', 'meer' => '', 'quantity' => '', 'unit' => 'পোনা', 'unit_price' => '']
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
                                            <select name="items[{{ $idx }}][purchase_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                                                <option value="">-- পণ্য নির্বাচন করুন --</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}" {{ (string)($item['purchase_product_id'] ?? '') === (string)$prod->id ? 'selected' : '' }}>
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
                                            <input type="hidden" name="items[{{ $idx }}][unit]" value="পোনা">
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

            <!-- Right Side: Calculation & Payment Summary -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calculator text-primary"></i>
                            <span>হিসাব ও পরিশোধ বিবরণী</span>
                        </h6>
                    </div>

                    <div class="card-body p-3 p-md-4">
                        <!-- Subtotal / Total Amount -->
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted fw-semibold" style="font-size: 0.85rem;">সর্বমোট ক্রয় মূল্য (Total):</span>
                                <span class="fw-bold text-dark fs-5" id="displayTotalAmount">৳0.00</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">(মোট পোনা ÷ ১০০০) × দর হিসেবে হিসাবকৃত</small>
                        </div>

                        <!-- Paid Amount Input -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পরিশোধিত টাকা (Paid Amount)
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

                        <!-- Payment Method (Active if Paid Amount > 0) -->
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
                                ট্রানজেকশন আইডি / রেফারেন্স <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}" 
                                   class="form-control" 
                                   placeholder="যেমন: TRX123456" 
                                   style="font-size: 0.85rem;">
                        </div>

                        <!-- Note -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য বা বিশেষ নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <textarea name="note" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="ক্রয় সম্পর্কিত কোনো তথ্য বা নোট থাকলে লিখুন..." 
                                      style="font-size: 0.85rem;">{{ old('note') }}</textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" 
                                    class="btn btn-primary py-2.5 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-submit-with-spinner" 
                                    data-loading-text="ক্রয় সংরক্ষণ হচ্ছে...">
                                <i class="fa-solid fa-check"></i>
                                <span>ক্রয় নিশ্চিত ও সংরক্ষণ করুন</span>
                            </button>
                            <a href="{{ route('admin.purchase.index') }}" class="btn btn-light border py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
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

    // 1. Supplier Select Initialization with dual Search (Name & Mobile)
    function initSupplierSelect() {
        const $supplierSelect = $('#supplierSelect');
        if ($supplierSelect.hasClass('select2-hidden-accessible')) {
            $supplierSelect.select2('destroy');
        }

        $supplierSelect.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- সরবরাহকারীর নাম বা মোবাইল নম্বর লিখুন --',
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
    }

    // 2. Product Select Initialization with formatting for disabled options
    function initProductSelect($element) {
        $element.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: '-- পণ্য নির্বাচন করুন --',
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

    // 3. Synchronize product options across all rows (Disable selected products in other rows)
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

    // 4. Hatchery Real-World Formula Calculation for Row
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
            // If box/mir not set, keep existing qty if entered
            totalFry = parseFloat($row.find('.item-qty').val()) || 0;
        }

        // 4. Dor manually input, mot pona 1000 diye vag diye dor gun hobe mot dam
        const dorVal = parseFloat($row.find('.item-price').val());
        let lineTotal = 0;
        if (totalFry > 0 && !isNaN(dorVal) && dorVal >= 0) {
            lineTotal = (totalFry / 1000) * dorVal;
        }
        $row.find('.item-total').text('৳' + lineTotal.toFixed(2));
    }

    // 5. Function to calculate all line totals and grand total
    function calculateTotals() {
        let currentTotal = 0;

        $('#itemsContainer .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            
            // Hatchery Bagda fry is priced per 1,000 fry: (qty * price) / 1000
            const lineTotal = (qty * price) / 1000;

            $(this).find('.item-total').text('৳' + lineTotal.toFixed(2));
            currentTotal += lineTotal;
        });

        $('#displayTotalAmount').text('৳' + currentTotal.toFixed(2));

        const paid = parseFloat($('#paidAmountInput').val()) || 0;
        const due = Math.max(0, currentTotal - paid);

        $('#displayDueAmount').text('৳' + due.toFixed(2));

        const $dueContainer = $('#dueContainer');
        const $dueNotice = $('#dueStatusNotice');

        if (due <= 0 && currentTotal > 0) {
            $dueContainer.css({ 'background': '#f0fdf4', 'border-color': '#bbf7d0' });
            $('#displayDueAmount').removeClass('text-danger').addClass('text-success');
            $dueNotice.text('সম্পূর্ণ পরিশোধিত (Paid in Full)').removeClass('text-muted').addClass('text-success fw-semibold');
        } else if (paid > 0 && due > 0) {
            $dueContainer.css({ 'background': '#fffbeb', 'border-color': '#fde68a' });
            $('#displayDueAmount').removeClass('text-success').addClass('text-danger');
            $dueNotice.text('আংশিক পরিশোধ (Partial Due)').removeClass('text-success').addClass('text-warning fw-semibold');
        } else {
            $dueContainer.css({ 'background': '#fff5f5', 'border-color': '#fed7d7' });
            $('#displayDueAmount').removeClass('text-success').addClass('text-danger');
            $dueNotice.text('সম্পূর্ণ বকেয়া প্রযোজ্য (Full Due)').removeClass('text-success text-warning').addClass('text-muted');
        }
    }

    // 6. Add new product row (2-Row Packaging Card matching Sales)
    function addNewRow() {
        const currentRowsCount = $('.item-row').length;
        if (totalAvailableProducts > 0 && currentRowsCount >= totalAvailableProducts) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'সকল পণ্য নির্বাচিত',
                    text: 'ডাটাবেজে থাকা সকল ক্রয় পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!',
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: 'ঠিক আছে'
                });
            } else {
                alert('সকল ক্রয় পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!');
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
                            <span>ক্রয়কৃত পোনা / পণ্য নির্বাচন করুন</span>
                            <span class="text-danger">*</span>
                        </label>
                        <select name="items[${rowIndex}][purchase_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
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
                        <input type="hidden" name="items[${rowIndex}][unit]" value="পোনা">
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

        // Initialize Select2 on the newly created select
        initProductSelect($newRow.find('select.product-select'));

        // Immediately synchronize options to disable products selected elsewhere
        syncProductOptions();
        updateRemoveButtons();
        updateRowBadges();
    }

    // Add row button click
    $('#addRowBtn, #addRowBtnBottom').on('click', function() {
        addNewRow();
    });

    // Remove row button click
    $(document).on('click', '.btn-remove-row', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
            updateRemoveButtons();
            updateRowBadges();
            syncProductOptions(); // Re-enable the released product in other rows!
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

    // Product selection change event
    $(document).on('change', '.product-select', function() {
        syncProductOptions();
        calculateTotals();
    });

    // Live calculation listeners for Box, Mir, Qty, and Dor
    $(document).on('input change', '.item-box, .item-meer, .item-price', function() {
        const $row = $(this).closest('.item-row');
        recalculateRow($row);
        calculateTotals();
    });

    // Input events for real-time payment calculation
    $(document).on('input', '#paidAmountInput', function() {
        calculateTotals();
    });

    // Ensure readonly inputs are submitted with form
    $('form').on('submit', function() {
        $('.item-poly, .item-qty').prop('readonly', false);
    });

    // Initialize on document ready
    initSupplierSelect();

    $('.product-select').each(function() {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
        initProductSelect($(this));
    });

    $('.item-row').each(function() {
        recalculateRow($(this));
    });

    syncProductOptions();
    calculateTotals();
    updateRemoveButtons();
});
</script>
@endpush
