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

                <!-- Card 2: Purchase Items Dynamic Table -->
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-primary"></i>
                            <span>ক্রয়কৃত পণ্য সামগ্রী</span>
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
                                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 45%;">পণ্য নির্বাচন (Product) <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 20%;">পরিমাণ (Qty) <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 20%;">দর / একক মূল্য (৳) <span class="text-danger">*</span></th>
                                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 20%;">মোট (৳)</th>
                                        <th class="pe-4 py-3 text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTableBody">
                                    @php
                                        $oldItems = old('items', [
                                            ['purchase_product_id' => '', 'quantity' => '', 'unit_price' => '']
                                        ]);
                                    @endphp
                                    @foreach($oldItems as $idx => $item)
                                        <tr class="item-row">
                                            <td class="ps-4">
                                                <select name="items[{{ $idx }}][purchase_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                                                    <option value="">-- পণ্য নির্বাচন করুন --</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}" {{ (string)($item['purchase_product_id'] ?? '') === (string)$prod->id ? 'selected' : '' }}>
                                                            {{ $prod->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="items[{{ $idx }}][quantity]" 
                                                       value="{{ $item['quantity'] ?? '' }}"
                                                       class="form-control text-center item-qty" 
                                                       step="0.01" 
                                                       min="0.01" 
                                                       placeholder="পরিমাণ" 
                                                       required 
                                                       style="font-size: 0.88rem;">
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
                        <small class="text-muted">একাধিক পণ্য থাকলে "পণ্য যোগ করুন" বাটনে ক্লিক করে সারি বৃদ্ধি করুন।</small>
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
                            <span>হিসাব ও পরিশোধ বিবরণী</span>
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
            // Gather all selected product IDs (excluding empty "")
            const selectedProductIds = [];
            $('.product-select').each(function() {
                const val = $(this).val();
                if (val && val !== '') {
                    selectedProductIds.push(val.toString());
                }
            });

            // Update disabled state for every option in each product select
            $('.product-select').each(function() {
                const $select = $(this);
                const currentVal = ($select.val() || '').toString();
                let optionChanged = false;

                $select.find('option').each(function() {
                    const optVal = $(this).val();
                    if (!optVal) return; // skip placeholder

                    // Should this option be disabled?
                    const shouldDisable = selectedProductIds.includes(optVal.toString()) && optVal.toString() !== currentVal;
                    const isCurrentlyDisabled = $(this).prop('disabled');

                    if (shouldDisable !== isCurrentlyDisabled) {
                        $(this).prop('disabled', shouldDisable);
                        optionChanged = true;
                    }
                });

                // If any option's disabled state changed, re-render this Select2
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

    // 4. Function to calculate all line totals and grand total
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
            $dueNotice.text('আংশিক পরিশোধ (Partial Due)').removeClass('text-success').addClass('text-warning fw-semibold');
        } else {
            $dueContainer.css({ 'background': '#fff5f5', 'border-color': '#fed7d7' });
            $('#displayDueAmount').removeClass('text-success').addClass('text-danger');
            $dueNotice.text('সম্পূর্ণ বকেয়া প্রযোজ্য (Full Due)').removeClass('text-success text-warning').addClass('text-muted');
        }
    }

    // 5. Add new product row
    function addNewRow() {
        const currentRowsCount = $('#itemsTableBody .item-row').length;
        if (totalAvailableProducts > 0 && currentRowsCount >= totalAvailableProducts) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'সকল পণ্য নির্বাচিত',
                    text: 'ডাটাবেজে থাকা সকল পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!',
                    confirmButtonColor: '#0284c7',
                    confirmButtonText: 'ঠিক আছে'
                });
            } else {
                alert('সকল পণ্য ইতিমধ্যে তালিকায় যোগ করা হয়েছে!');
            }
            return;
        }

        const newRowHtml = `
            <tr class="item-row">
                <td class="ps-4">
                    <select name="items[${rowIndex}][purchase_product_id]" class="form-select product-select" data-placeholder="-- পণ্য নির্বাচন করুন --" required>
                        <option value="">-- পণ্য নির্বাচন করুন --</option>
                        ${productOptionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" 
                           name="items[${rowIndex}][quantity]" 
                           class="form-control text-center item-qty" 
                           step="0.01" 
                           min="0.01" 
                           placeholder="পরিমাণ" 
                           required 
                           style="font-size: 0.88rem;">
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

        // Initialize Select2 on the newly created select
        initProductSelect($newRow.find('select.product-select'));

        // Immediately synchronize options to disable products selected elsewhere
        syncProductOptions();

        // Update remove buttons visibility
        updateRemoveButtons();
    }

    // Add row button click
    $('#addRowBtn, #addRowBtnBottom').on('click', function() {
        addNewRow();
    });

    // Remove row button click
    $(document).on('click', '.btn-remove-row', function() {
        if ($('#itemsTableBody .item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotals();
            updateRemoveButtons();
            syncProductOptions(); // Re-enable the released product in other rows!
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

    // Product selection change event
    $(document).on('change', '.product-select', function() {
        syncProductOptions();
        calculateTotals();
    });

    // Input events for real-time calculation
    $(document).on('input', '.item-qty, .item-price, #paidAmountInput', function() {
        calculateTotals();
    });

    // Initialize on document ready
    initSupplierSelect();

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
