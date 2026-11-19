@extends('admin.layouts.master')

@section('title', 'বিক্রয় মেমো / চালান - ' . $sale->invoice_no)

@section('content')
<div class="w-100 pb-4">
    <!-- Top Action Bar (Screen Only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-receipt text-primary"></i>
                <span>বিক্রয় মেমো / চালান</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}" class="text-decoration-none text-muted">বিক্রয় তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $sale->invoice_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
            <a href="{{ route('admin.sales.create') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন বিক্রয়</span>
            </a>
            @if($sale->due_amount > 0)
                <button type="button" 
                        class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addPaymentModal"
                        style="font-size: 0.85rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>বকেয়া আদায় / জমা</span>
                </button>
            @endif
            @if(!empty($sale->customer?->mobile))
                <button type="button" 
                        class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#sendSaleSmsModal"
                        style="font-size: 0.85rem;">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>মেমো SMS পাঠান</span>
                </button>
            @endif
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট মেমো (A4)</span>
            </button>
        </div>
    </div>

    <!-- Payment History Strip (Screen Only) -->
    @if($sale->payments->isNotEmpty())
        <div class="card border-0 shadow-sm rounded-4 bg-white p-3 mb-4 d-print-none">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="fw-bold text-dark small d-flex align-items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>পরিশোধ ও জমার ইতিহাস ({{ $sale->payments->count() }}টি)
                </span>
                <span class="badge {{ $sale->due_amount > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3 py-1 fw-bold" style="font-size: 0.78rem;">
                    {{ $sale->due_amount > 0 ? 'বকেয়া: ৳' . number_format($sale->due_amount, 2) : 'সম্পূর্ণ পরিশোধিত' }}
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="ps-2">তারিখ</th>
                            <th>মাধ্যম</th>
                            <th>ট্রানজেকশন আইডি</th>
                            <th>নোট</th>
                            <th class="text-end pe-2">জমার পরিমাণ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->payments as $payment)
                            <tr>
                                <td class="ps-2">{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : '—' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                                <td class="text-muted">{{ $payment->transaction_id ?? '—' }}</td>
                                <td class="text-muted">{{ $payment->note ?? '—' }}</td>
                                <td class="text-end pe-2 fw-bold text-success">৳{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- OFFICIAL FULL A4 MEMO (Screen & Print Perfect)                             -->
    <!-- ========================================================================= -->
    @php
        $memoBusinessName = \App\Models\Setting::get('memo_business_name', \App\Models\Setting::get('business_name', 'মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট'));
        $memoSubtitle = \App\Models\Setting::get('memo_subtitle', 'এখানে সকল প্রকার বাগদার পোনা নার্সিং করা হয় ও বিক্রয়ের নির্ভরযোগ্য প্রতিষ্ঠান।');
        $proprietorName = \App\Models\Setting::get('proprietor_name', 'আলহাজ্ব নজরুল ইসলাম সানা');
        $proprietorMobile = \App\Models\Setting::get('proprietor_mobile', '01737 908309');
        $contactNumbers = \App\Models\Setting::get('contact_numbers', '01352 383531, 01352 383532');
        $operatorName = \App\Models\Setting::get('operator_name', 'মোমিনুর রহমান');
        $operatorContact = \App\Models\Setting::get('operator_contact', '01751-722350 NG/BK');
        $nursingPointAddress = \App\Models\Setting::get('nursing_point_address', 'দক্ষিণ পুঁইজালা গেটের ধার, শ্রীউলা, আশাশুনি, সাতক্ষীরা।');
        $officeAddress = \App\Models\Setting::get('office_address', 'পুঁইজালা বাল্লা বাজার, আশাশুনি, সাতক্ষীরা।');
        $memoSlogan = \App\Models\Setting::get('memo_slogan', 'ধন্যবাদ আবার আসবেন | সততাই ব্যবসার মূল লক্ষ্য');
        $memoAuthoritySignature = \App\Models\Setting::get('memo_authority_signature', 'পক্ষে - মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট');
        $siteLogo = \App\Models\Setting::get('site_logo');
    @endphp
    <div class="d-flex justify-content-center">
        <div class="official-memo-wrapper bg-white shadow-sm">
            <div class="official-memo-border d-flex flex-column justify-content-between">
                
                <!-- UPPER SECTION: HEADER, CUSTOMER & ITEMS -->
                <div>
                    <!-- TOP HEADER -->
                    <div class="memo-header text-center position-relative">
                        <!-- Bismillah -->
                        <div class="bismillah text-dark fw-bold mb-1" style="font-size: 11pt; letter-spacing: 0.5px;">
                            বিসমিল্লাহির রাহমানির রাহিম
                        </div>

                        <!-- Challan Badge -->
                        <div class="d-inline-block challan-badge mb-2">
                            চা &nbsp;|&nbsp; লা &nbsp;|&nbsp; ন
                        </div>

                        <!-- Header Content with Logo -->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <!-- Left Circular Logo -->
                            <div class="memo-logo flex-shrink-0">
                                @if(!empty($siteLogo) && file_exists(public_path($siteLogo)))
                                    <img src="{{ asset($siteLogo) }}" alt="{{ $memoBusinessName }}" style="max-width: 86px; max-height: 86px; object-fit: contain;">
                                @else
                                    <div class="logo-circle">
                                        <svg viewBox="0 0 100 100" class="logo-svg">
                                            <circle cx="50" cy="50" r="46" fill="#f0fdf4" stroke="#0284c7" stroke-width="2.5" />
                                            <circle cx="50" cy="50" r="42" fill="none" stroke="#0ea5e9" stroke-width="1" stroke-dasharray="2,2" />
                                            <g fill="#0284c7">
                                                <path d="M30 42 C 35 32, 55 30, 68 36 C 72 38, 76 43, 70 45 C 64 47, 52 44, 42 46 C 36 47, 32 45, 30 42 Z" />
                                                <circle cx="72" cy="40" r="2" fill="#0f172a" />
                                                <path d="M72 39 Q 82 34 88 30" stroke="#0284c7" stroke-width="1.2" fill="none" />
                                                <path d="M72 41 Q 84 38 90 38" stroke="#0284c7" stroke-width="1.2" fill="none" />
                                                <path d="M32 43 Q 22 46 18 52" stroke="#0284c7" stroke-width="1.5" fill="none" />
                                                
                                                <path d="M70 58 C 65 68, 45 70, 32 64 C 28 62, 24 57, 30 55 C 36 53, 48 56, 58 54 C 64 53, 68 55, 70 58 Z" fill="#0d9488" />
                                                <circle cx="28" cy="60" r="2" fill="#0f172a" />
                                                <path d="M28 61 Q 18 66 12 70" stroke="#0d9488" stroke-width="1.2" fill="none" />
                                                <path d="M28 59 Q 16 62 10 62" stroke="#0d9488" stroke-width="1.2" fill="none" />
                                                <path d="M68 57 Q 78 54 82 48" stroke="#0d9488" stroke-width="1.5" fill="none" />
                                            </g>
                                        </svg>
                                        <div class="logo-inner-text">
                                            <span>মেসার্স</span><br>
                                            <strong>সানা ফিস</strong>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Center Title & Tagline -->
                            <div class="memo-title-block flex-grow-1 px-2">
                                <h1 class="memo-company-title mb-0">{{ $memoBusinessName }}</h1>
                                @if(!empty($memoSubtitle))
                                    <div class="memo-subtitle fw-bold text-dark mt-1">
                                        {{ $memoSubtitle }}
                                    </div>
                                @endif
                                @if(!empty($proprietorName) || !empty($proprietorMobile) || !empty($contactNumbers))
                                    <div class="memo-proprietor mt-1">
                                        (
                                        @if(!empty($proprietorName))
                                            প্রোপাইটার: {{ $proprietorName }}
                                        @endif
                                        @if(!empty($proprietorMobile))
                                            ({{ $proprietorMobile }})
                                        @endif
                                        @if(!empty($contactNumbers))
                                            &nbsp;&nbsp;(যোগাযোগ: {{ $contactNumbers }})
                                        @endif
                                        )
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice No & Operator Row -->
                        <div class="d-flex justify-content-between align-items-center px-1 mb-2">
                            <div class="memo-invoice-number fw-bold text-dark" style="font-size: 11pt;">
                                চালান নং - <span class="text-primary fw-bolder">{{ $sale->invoice_no }}</span>
                            </div>
                            @if(!empty($operatorName) || !empty($operatorContact))
                                <div class="d-flex align-items-center gap-2">
                                    @if(!empty($operatorName))
                                        <span class="operator-badge">পরিচালনায়: {{ $operatorName }}</span>
                                    @endif
                                    @if(!empty($operatorContact))
                                        <span class="contact-pill">
                                            <i class="fa-brands fa-whatsapp text-success me-1"></i>
                                            <i class="fa-solid fa-phone text-primary me-1"></i>
                                            <strong>{{ $operatorContact }}</strong>
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Purple Address Banner Box (Shows only if at least one address is set) -->
                        @if(!empty($nursingPointAddress) || !empty($officeAddress))
                            <div class="purple-address-banner mb-3">
                                @if(!empty($nursingPointAddress))
                                    <div>নার্সিং পয়েন্ট: {{ $nursingPointAddress }}</div>
                                @endif
                                @if(!empty($officeAddress))
                                    <div>অফিস: {{ $officeAddress }}</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- CUSTOMER INFO GRID BOX -->
                    <div class="customer-info-grid mb-3">
                        <table class="w-100 customer-table">
                            <tr>
                                <td class="customer-label" style="width: 85px;">নাম:</td>
                                <td class="customer-value" colspan="3">
                                    <span class="fw-bold text-dark" style="font-size: 11pt;">{{ $sale->customer?->name ?? '—' }}</span>
                                    @if(!empty($sale->customer?->mobile))
                                        <span class="text-muted ms-2 fw-normal">({{ $sale->customer->mobile }})</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="customer-label" style="width: 85px;">ঠিকানা:</td>
                                <td class="customer-value">
                                    {{ $sale->customer?->location_text ?: ($sale->customer?->address ?? '—') }}
                                </td>
                                <td class="customer-label" style="width: 80px; text-align: center;">তারিখ:</td>
                                <td class="customer-value" style="width: 140px; text-align: center; font-weight: bold;">
                                    {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : date('d/m/Y') }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- DYNAMIC ITEMS TABLE (Columns based on item data, rows exactly match item count) -->
                    @php
                        $hasBox = $sale->items->contains(fn($it) => !empty($it->box) && $it->box != '0' && $it->box !== '—');
                        $hasPoly = $sale->items->contains(fn($it) => !empty($it->poly) && $it->poly != '0' && $it->poly !== '—');
                        $hasMeer = $sale->items->contains(fn($it) => !empty($it->meer) && $it->meer != '0' && $it->meer !== '—');
                        $hasCustomUnit = $sale->items->contains(fn($it) => !empty($it->unit) && !in_array(trim($it->unit), ['পিস', 'piece', 'pcs', 'টি', '']));
                        $colCount = 3 + ($hasBox ? 1 : 0) + ($hasPoly ? 1 : 0) + ($hasMeer ? 1 : 0) + ($hasCustomUnit ? 1 : 0);
                    @endphp
                    <div class="items-table-container position-relative mb-3">
                        <!-- Faint Center Watermark -->
                        <div class="watermark-overlay" aria-hidden="true">
                            @if(!empty($siteLogo) && file_exists(public_path($siteLogo)))
                                <img src="{{ asset($siteLogo) }}" alt="" style="max-width: 220px; max-height: 220px; object-fit: contain; opacity: 0.10;">
                            @else
                                <svg viewBox="0 0 200 200" width="240" height="240" class="watermark-svg">
                                    <circle cx="100" cy="100" r="92" fill="none" stroke="#0284c7" stroke-width="4" stroke-opacity="0.12" />
                                    <circle cx="100" cy="100" r="84" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-opacity="0.1" stroke-dasharray="4,4" />
                                    <g fill="#0284c7" fill-opacity="0.12">
                                        <path d="M60 84 C 70 64, 110 60, 136 72 C 144 76, 152 86, 140 90 C 128 94, 104 88, 84 92 C 72 94, 64 90, 60 84 Z" />
                                        <circle cx="144" cy="80" r="4" />
                                        <path d="M140 116 C 130 136, 90 140, 64 128 C 56 124, 48 114, 60 110 C 72 106, 96 112, 116 108 C 128 106, 136 110, 140 116 Z" />
                                        <circle cx="56" cy="120" r="4" />
                                    </g>
                                </svg>
                            @endif
                            <div class="watermark-text">{{ $memoBusinessName }}</div>
                        </div>

                        <!-- Items Table -->
                        <table class="memo-items-table w-100">
                            <thead>
                                <tr>
                                    <th>ব্র্যান্ড / পোনার নাম</th>
                                    @if($hasBox)
                                        <th style="width: 80px; text-align: center;">বক্স</th>
                                    @endif
                                    @if($hasPoly)
                                        <th style="width: 80px; text-align: center;">পলি</th>
                                    @endif
                                    @if($hasMeer)
                                        <th style="width: 85px; text-align: center;">মীর</th>
                                    @endif
                                    <th style="width: 110px; text-align: center;">মোট পোনা</th>
                                    @if($hasCustomUnit)
                                        <th style="width: 80px; text-align: center;">একক</th>
                                    @endif
                                    <th style="width: 105px; text-align: right;">দর (৳)</th>
                                    <th style="width: 125px; text-align: right;">মোট দাম (৳)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sale->items as $item)
                                    <tr class="item-row">
                                        <td class="text-start ps-3 fw-bold text-dark">
                                            {{ $item->product?->name ?? $item->product_name }}
                                        </td>
                                        @if($hasBox)
                                            <td class="text-center">{{ $item->box ?: '—' }}</td>
                                        @endif
                                        @if($hasPoly)
                                            <td class="text-center">{{ $item->poly ?: '—' }}</td>
                                        @endif
                                        @if($hasMeer)
                                            <td class="text-center">{{ $item->meer ?: '—' }}</td>
                                        @endif
                                        <td class="text-center fw-bold text-dark">
                                            {{ number_format($item->quantity) }}
                                        </td>
                                        @if($hasCustomUnit)
                                            <td class="text-center text-muted">
                                                {{ $item->unit ?: 'পিস' }}
                                            </td>
                                        @endif
                                        <td class="text-end pe-2">
                                            {{ number_format($item->unit_price, 2) }}
                                        </td>
                                        <td class="text-end pe-2 fw-bold text-dark">
                                            {{ number_format($item->total_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $colCount }}" class="text-center py-3 text-muted">
                                            কোনো পণ্য আইটেম পাওয়া যায়নি।
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- LOWER SECTION: TOTALS, IN WORDS, SLOGAN & SIGNATURES -->
                <div class="memo-bottom-block">
                    <!-- TOTALS & PREVIOUS DUE CALCULATION SECTION -->
                    <div class="memo-bottom-section d-flex justify-content-between align-items-start gap-3 mb-3">
                        <!-- Left: In Words (টাকা কথায়) & Note -->
                        <div class="in-words-container flex-grow-1">
                            <div class="in-words-box d-flex align-items-center mb-2">
                                <div class="in-words-label">টাকা (কথায়):</div>
                                <div class="in-words-value flex-grow-1 ps-2 fw-bold text-dark">
                                    {{ \App\Helpers\BanglaNumberHelper::bnWords(($previousDue ?? 0) > 0 ? ($grandTotal ?? $sale->total_amount) : $sale->total_amount) }}
                                </div>
                            </div>

                            @if(!empty($sale->note))
                                <div class="memo-note-box p-2 border rounded-2 bg-light">
                                    <small class="fw-bold text-muted d-block">মন্তব্য / নোট:</small>
                                    <span class="text-dark small">{{ $sale->note }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Summary Table (মোট, পূর্ব জের, সর্বমোট, জমা, পাওনা - শর্তসাপেক্ষ ডায়নামিক) -->
                        <div class="summary-table-container" style="width: 250px;">
                            <table class="w-100 summary-table">
                                <tr>
                                    <td class="summary-label">মোট:</td>
                                    <td class="summary-val text-end pe-2 fw-bold">
                                        {{ number_format($sale->total_amount, 2) }}
                                    </td>
                                </tr>
                                @if(($previousDue ?? 0) > 0)
                                    <tr>
                                        <td class="summary-label">পূর্ব জের:</td>
                                        <td class="summary-val text-end pe-2 fw-bold text-secondary">
                                            {{ number_format($previousDue, 2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="summary-label">সর্বমোট:</td>
                                        <td class="summary-val text-end pe-2 fw-bold text-dark" style="background:#f8fafc;">
                                            {{ number_format($grandTotal ?? $sale->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                @if($sale->paid_amount > 0)
                                    <tr>
                                        <td class="summary-label">জমা:</td>
                                        <td class="summary-val text-end pe-2 fw-bold text-success">
                                            {{ number_format($sale->paid_amount, 2) }}
                                        </td>
                                    </tr>
                                @endif
                                @php
                                    $dueBalance = (float)($totalOutstanding ?? $sale->due_amount);
                                @endphp
                                @if($dueBalance > 0)
                                    <tr>
                                        <td class="summary-label bg-danger-cell">পাওনা:</td>
                                        <td class="summary-val text-end pe-2 fw-bolder text-danger" style="background:#fef2f2;">
                                            {{ number_format($dueBalance, 2) }}
                                        </td>
                                    </tr>
                                @elseif($sale->paid_amount > 0 && $dueBalance <= 0)
                                    <tr>
                                        <td class="summary-label bg-success text-white">অবস্থা:</td>
                                        <td class="summary-val text-end pe-2 fw-bold text-success" style="background:#f0fdf4;">
                                            <i class="fa-solid fa-circle-check me-1"></i>পরিশোধিত
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- FOOTER SLOGAN BANNER (DYNAMIC FROM SETTINGS) -->
                    @if($memoSlogan)
                        <div class="text-center mb-4">
                            <div class="slogan-pill-banner d-inline-block">
                                {{ $memoSlogan }}
                            </div>
                        </div>
                    @endif

                    <!-- SIGNATURE BLOCK (DYNAMIC FROM SETTINGS) -->
                    <div class="signature-row d-flex justify-content-between align-items-end pt-4 pb-2">
                        <div class="text-center" style="width: 200px;">
                            <div class="sig-line">................................................</div>
                            <div class="fw-bold text-dark small mt-1">ক্রেতার স্বাক্ষর</div>
                        </div>
                        <div class="text-center" style="width: 260px;">
                            <div class="sig-line">................................................</div>
                            <div class="fw-bold text-dark small mt-1">{{ $memoAuthoritySignature }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ================= DUE PAYMENT MODAL ================= -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">বকেয়া টাকা আদায় / জমা</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">চালান নম্বর: {{ $sale->invoice_no }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.sales.payment.store', $sale) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">বর্তমান অবশিষ্ট বকেয়া</small>
                            <span class="fw-bold text-danger fs-5">৳{{ number_format($sale->due_amount, 2) }}</span>
                        </div>
                        <span class="badge bg-danger rounded-pill px-2.5 py-1">বকেয়া</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="payment_date" 
                                   value="{{ date('Y-m-d') }}" 
                                   class="form-control" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   min="1" 
                                   max="{{ $sale->due_amount }}" 
                                   value="{{ $sale->due_amount }}" 
                                   name="amount" 
                                   class="form-control fw-bold text-success" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার মাধ্যম <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" class="select2 form-select" required>
                                <option value="Cash" selected>ক্যাশ (Cash)</option>
                                <option value="bKash">বিকাশ (bKash)</option>
                                <option value="Nagad">নগদ (Nagad)</option>
                                <option value="Rocket">রকেট (Rocket)</option>
                                <option value="Bank">ব্যাংক ট্রান্সফার (Bank)</option>
                                <option value="Other">অন্যান্য (Other)</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                ট্রানজেকশন আইডি <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   class="form-control" 
                                   placeholder="e.g. TRX123456" 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                নোট / বিবরণ <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="note" 
                                   class="form-control" 
                                   placeholder="আদায়ের মন্তব্য বা তথ্য..." 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="প্রসেসিং..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>জমা সম্পন্ন করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($sale->customer?->mobile))
@php
    $smsService = app(\App\Services\SmsService::class);
    $saleTemplate = \App\Models\Setting::get('sms_template_sale', 'প্রিয় {customer_name}, সানা ফিশারিজ থেকে আপনার মেমো #{invoice_no} তৈরি হয়েছে। মোট বিল: ৳{total_amount}, জমা: ৳{paid_amount}, বকেয়া: ৳{due_amount}। ধন্যবাদ!');
    $renderedSaleSms = $smsService->renderSaleTemplate($saleTemplate, $sale);
@endphp
<!-- ================= SEND SALE MEMO SMS MODAL ================= -->
<div class="modal fade" id="sendSaleSmsModal" tabindex="-1" aria-labelledby="sendSaleSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-paper-plane"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">মেমোর হিসাব এসএমএস পাঠান</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">কাস্টমারের মোবাইলে মেমোর হিসাব পাঠানো</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.sales.send-sms', $sale->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="card border rounded-3 p-3 bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">কাস্টমার:</span>
                            <strong class="text-dark">{{ $sale->customer->name }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">মোবাইল নম্বর:</span>
                            <strong class="text-primary">{{ $sale->customer->mobile }}</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">মোট বিল:</span>
                            <span class="fw-bold text-dark">৳ {{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">অবশিষ্ট বকেয়া:</span>
                            <span class="fw-bold text-danger">৳ {{ number_format($sale->due_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="saleSmsMessage" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            এসএমএস মেসেজ (স্বয়ংক্রিয় হিসাবকৃত)
                        </label>
                        <textarea id="saleSmsMessage" 
                                  name="message" 
                                  rows="4" 
                                  class="form-control" 
                                  required 
                                  style="font-size: 0.88rem;">{{ $renderedSaleSms }}</textarea>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            প্রয়োজনে পাঠানোর আগে মেসেজের লেখা সম্পাদন করতে পারেন।
                        </small>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="এসএমএস পাঠানো হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-paper-plane me-1"></i>এসএমএস পাঠান
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ================= STYLES FOR FULL A4 MEMO & PRINT ================= -->
<style>
    /* Memo Container Dimensions */
    .official-memo-wrapper {
        width: 100%;
        max-width: 860px;
        background: #ffffff;
        box-sizing: border-box;
    }
    .official-memo-border {
        border: 2.5px solid #0284c7;
        padding: 24px 26px;
        position: relative;
        background: #ffffff;
        min-height: 1050px;
    }

    /* Challan Badge */
    .challan-badge {
        background: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 11pt;
        letter-spacing: 2px;
        padding: 2px 20px;
        border-radius: 4px;
        border: 1px solid #1e293b;
    }

    /* Logo Styling */
    .memo-logo {
        width: 90px;
        height: 90px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .logo-circle {
        position: relative;
        width: 86px;
        height: 86px;
    }
    .logo-svg {
        width: 100%;
        height: 100%;
    }
    .logo-inner-text {
        position: absolute;
        bottom: 6px;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        line-height: 1.1;
        color: #0369a1;
        font-weight: bold;
    }

    /* Memo Company Title */
    .memo-company-title {
        font-size: 28pt;
        font-weight: 900;
        color: #1e3a8a;
        letter-spacing: -0.5px;
        line-height: 1.15;
        text-shadow: 1px 1px 0px #bae6fd;
    }
    .memo-subtitle {
        font-size: 10.5pt;
        color: #1e293b;
    }
    .memo-proprietor {
        font-size: 10pt;
        font-weight: bold;
        color: #881337;
    }

    /* Operator & Contact Badges */
    .operator-badge {
        background: #1e3a8a;
        color: #ffffff;
        padding: 3px 14px;
        border-radius: 4px;
        font-size: 10pt;
        font-weight: bold;
    }
    .contact-pill {
        font-size: 10pt;
        color: #0f172a;
    }

    /* Purple Address Banner */
    .purple-address-banner {
        background: #701a75;
        color: #ffffff;
        font-size: 9.5pt;
        font-weight: 600;
        padding: 7px 16px;
        border-radius: 12px;
        line-height: 1.45;
        text-align: center;
    }

    /* Customer Info Table */
    .customer-table {
        border-collapse: collapse;
        border: 2px solid #0284c7;
    }
    .customer-table td {
        border: 1.5px solid #0284c7;
        padding: 6px 10px;
        font-size: 10.5pt;
    }
    .customer-label {
        background-color: #0284c7;
        color: #ffffff;
        font-weight: bold;
    }
    .customer-value {
        background-color: #ffffff;
    }

    /* Items Table */
    .items-table-container {
        border: 2px solid #0284c7;
        position: relative;
    }
    .memo-items-table {
        border-collapse: collapse;
        position: relative;
        z-index: 2;
    }
    .memo-items-table th {
        background-color: #1e3a8a;
        color: #ffffff;
        font-weight: bold;
        font-size: 10pt;
        padding: 8px 8px;
        border: 1.5px solid #0284c7;
    }
    .memo-items-table td {
        border: 1.5px solid #0284c7;
        padding: 8px 8px;
        font-size: 10pt;
    }
    .item-row:nth-child(even) {
        background: rgba(248, 250, 252, 0.5);
    }

    /* Watermark */
    .watermark-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 1;
        pointer-events: none;
        opacity: 0.12;
    }
    .watermark-text {
        font-size: 16pt;
        font-weight: 900;
        color: #0284c7;
        margin-top: 6px;
        letter-spacing: 0.5px;
    }

    /* In-Words Box */
    .in-words-box {
        border: 2px solid #0284c7;
        padding: 5px;
        border-radius: 4px;
    }
    .in-words-label {
        background-color: #0284c7;
        color: #ffffff;
        font-weight: bold;
        padding: 5px 12px;
        font-size: 10pt;
        border-radius: 2px;
        white-space: nowrap;
    }
    .in-words-value {
        font-size: 10.5pt;
    }

    /* Summary Table (Bottom Right) */
    .summary-table {
        border-collapse: collapse;
        border: 2px solid #0284c7;
    }
    .summary-table td {
        border: 1.5px solid #0284c7;
        padding: 5px 10px;
        font-size: 10.5pt;
    }
    .summary-label {
        background-color: #1e3a8a;
        color: #ffffff;
        font-weight: bold;
        width: 85px;
        text-align: center;
    }
    .summary-label.bg-danger-cell {
        background-color: #b91c1c;
    }
    .summary-val {
        background-color: #ffffff;
    }

    /* Slogan Banner */
    .slogan-pill-banner {
        background-color: #701a75;
        color: #ffffff;
        font-size: 10pt;
        font-weight: bold;
        padding: 5px 26px;
        border-radius: 12px;
        letter-spacing: 0.3px;
    }

    /* Signature Line */
    .sig-line {
        color: #64748b;
        letter-spacing: -1px;
        font-weight: bold;
    }

    /* ======================================================= */
    /* FULL A4 PRINT SPECIFIC STYLES                          */
    /* ======================================================= */
    @media print {
        @page {
            size: A4 portrait;
            margin: 10mm 10mm 10mm 10mm;
        }
        html, body {
            background: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
        }
        .app-sidebar, .app-header, header, nav, .d-print-none, #addPaymentModal, #sendSaleSmsModal, footer {
            display: none !important;
        }
        .main-content, .page, .w-100 {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .official-memo-wrapper {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            min-height: 265mm !important;
        }
        .official-memo-border {
            border: 2px solid #0284c7 !important;
            padding: 16px 18px !important;
            min-height: 265mm !important;
            box-sizing: border-box !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
        }
        /* Ensure exact print colors */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection
