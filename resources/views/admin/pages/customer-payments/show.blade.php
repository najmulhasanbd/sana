@extends('admin.layouts.master')

@section('title', 'টাকা জমার রসিদ ভাউচার - ' . $customerPayment->voucher_no)

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Actions (Screen only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>টাকা জমার রসিদ ভাউচার (Money Receipt)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customer-payments.index') }}" class="text-decoration-none text-muted">ভাউচার তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $customerPayment->voucher_no }}</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customer-payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>সকল ভাউচার</span>
            </a>

            @if($customerPayment->customer)
                <a href="{{ route('admin.customer.show', $customerPayment->customer) }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                    <i class="fa-solid fa-user"></i>
                    <span>কাস্টমার লেজার</span>
                </a>
            @endif

            @if(!empty($customerPayment->customer?->mobile))
                <form action="{{ route('admin.customer-payments.send-sms', $customerPayment) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-info rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>SMS পাঠান</span>
                    </button>
                </form>
            @endif

            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট ভাউচার (A4 / A5)</span>
            </button>
        </div>
    </div>

    <!-- Screen Info Card (Screen only) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3 p-md-4 mb-4 d-print-none">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-3">
                <small class="text-muted d-block" style="font-size: 0.75rem;">ভাউচার নম্বর</small>
                <h5 class="fw-bold text-primary mb-0">{{ $customerPayment->voucher_no }}</h5>
            </div>
            <div class="col-12 col-md-3">
                <small class="text-muted d-block" style="font-size: 0.75rem;">জমার তারিখ</small>
                <h6 class="fw-bold text-dark mb-0">{{ $customerPayment->payment_date->format('d M, Y') }}</h6>
            </div>
            <div class="col-12 col-md-3">
                <small class="text-muted d-block" style="font-size: 0.75rem;">আদায়কৃত টাকা</small>
                <h5 class="fw-bold text-success mb-0">৳{{ number_format($customerPayment->amount, 2) }}</h5>
            </div>
            <div class="col-12 col-md-3">
                <small class="text-muted d-block" style="font-size: 0.75rem;">বর্তমান অবশিষ্ট বকেয়া</small>
                <h5 class="fw-bold text-danger mb-0">৳{{ number_format($customerPayment->remaining_due, 2) }}</h5>
            </div>
        </div>
    </div>

    <!-- ================= PRINTABLE MONEY RECEIPT VOUCHER (PAD STYLE) ================= -->
    <div class="voucher-pad-wrapper mx-auto">
        <div class="voucher-pad-card bg-white">
            <!-- Top Islamic Blessing -->
            <div class="text-center mb-1">
                <div class="bismillah-text fw-bold text-dark" style="font-size: 11pt; letter-spacing: 0.5px;">
                    বিসমিল্লাহির রাহমানির রাহিম
                </div>
            </div>

            <!-- Header Section with Logo & Info -->
            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom-theme">
                <!-- Left Circular Logo -->
                <div class="memo-logo flex-shrink-0">
                    @if(!empty($siteLogo) && file_exists(public_path($siteLogo)))
                        <img src="{{ asset($siteLogo) }}" alt="{{ $businessName }}" style="max-width: 80px; max-height: 80px; object-fit: contain;">
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
                <div class="text-center flex-grow-1 px-2">
                    <h2 class="voucher-company-title mb-0">{{ $businessName }}</h2>
                    <div class="voucher-subtitle fw-bold text-dark mt-0.5" style="font-size: 10pt;">
                        {{ $businessSubtitle }}
                    </div>
                    @if(!empty($proprietorName) || !empty($proprietorMobile))
                        <div class="proprietor-line text-dark mt-0.5" style="font-size: 9.5pt;">
                            (প্রোপাইটার: {{ $proprietorName }}@if(!empty($proprietorMobile)) ({{ $proprietorMobile }})@endif)
                        </div>
                    @endif
                    @if(!empty($contactNumbers))
                        <div class="contact-line text-muted mt-0.5" style="font-size: 8.5pt;">
                            (যোগাযোগ: {{ $contactNumbers }})
                        </div>
                    @endif
                </div>

                <!-- Right Money Receipt Badge -->
                <div class="text-end flex-shrink-0" style="min-width: 110px;">
                    <div class="receipt-badge">
                        টাকা জমার রসিদ
                    </div>
                    <div class="mt-1 text-muted" style="font-size: 8pt;">
                        (ক্যাশ মেমো ভাউচার)
                    </div>
                </div>
            </div>

            <!-- Sub Header: Left Voucher No | Center Operator | Right Date -->
            <div class="d-flex justify-content-between align-items-center mb-2 px-1 pb-1">
                <div class="fw-bold text-dark" style="font-size: 10pt;">
                    ভাউচার নং: <span class="text-primary">{{ $customerPayment->voucher_no }}</span>
                </div>
                <div>
                    @if(!empty($operatorName))
                        <span class="operator-badge">পরিচালনায়: {{ $operatorName }} @if(!empty($operatorMobile)) ({{ $operatorMobile }})@endif</span>
                    @endif
                </div>
                <div class="text-end text-dark fw-bold" style="font-size: 9.5pt;">
                    তারিখ: <span class="fw-normal">{{ $customerPayment->payment_date->format('d/m/Y') }}</span>
                </div>
            </div>

            <!-- Nursing Point Strip -->
            @if(!empty($businessAddress))
                <div class="nursing-point-strip text-center mb-3">
                    {{ $businessAddress }}
                </div>
            @endif

            <!-- Customer Details Block -->
            <div class="customer-info-box mb-3 p-2.5 rounded-2 border">
                <div class="row g-2">
                    <div class="col-7">
                        <div style="font-size: 9.5pt;">
                            <span class="text-muted">কাস্টমারের নাম:</span> 
                            <strong class="text-dark fs-6">{{ $customerPayment->customer->name ?? '—' }}</strong>
                        </div>
                        <div class="mt-1" style="font-size: 9pt;">
                            <span class="text-muted">ঠিকানা (গ্রাম/এলাকা):</span> 
                            <span class="text-dark fw-semibold">
                                {{ $customerPayment->customer->address ?: ($customerPayment->customer->location_text ?? '—') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-5 text-end">
                        <div style="font-size: 9.5pt;">
                            <span class="text-muted">মোবাইল:</span> 
                            <strong class="text-dark">{{ $customerPayment->customer->mobile ?? '—' }}</strong>
                        </div>
                        <div class="mt-1" style="font-size: 9pt;">
                            <span class="text-muted">পরিশোধের মাধ্যম:</span> 
                            <span class="badge bg-light text-dark border px-2 py-0.5">
                                {{ $customerPayment->payment_method }}
                                @if($customerPayment->transaction_id)
                                    (TRX: {{ $customerPayment->transaction_id }})
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Financial Statement Table -->
            <table class="table table-bordered voucher-table mb-3">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>হিসাবের বিবরণ</th>
                        <th class="text-end" style="width: 140px;">টাকার পরিমাণ (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">১</td>
                        <td>
                            <strong>পূর্বের মোট বকেয়া পাওনা (Previous Total Due)</strong>
                            <div class="text-muted small">এই জমার পূর্ব পর্যন্ত কাস্টমারের মোট বাকি হিসাব</div>
                        </td>
                        <td class="text-end fw-bold text-dark fs-6">
                            ৳{{ number_format($customerPayment->previous_due, 2) }}
                        </td>
                    </tr>
                    <tr style="background: #f0fdf4;">
                        <td class="text-center text-success fw-bold">২</td>
                        <td>
                            <strong class="text-success">আজকের আদায়কৃত / জমা টাকা (Received / Paid Amount)</strong>
                            <div class="text-muted small">পরিশোধের মাধ্যম: {{ $customerPayment->payment_method }}</div>
                        </td>
                        <td class="text-end fw-bolder text-success fs-5">
                            ৳{{ number_format($customerPayment->amount, 2) }}
                        </td>
                    </tr>
                    <tr style="background: #fef2f2;">
                        <td class="text-center text-danger fw-bold">৩</td>
                        <td>
                            <strong class="text-danger">বর্তমান অবশিষ্ট মোট বকেয়া (Current Remaining Due)</strong>
                            <div class="text-muted small">আজকের জমার পর কাস্টমারের নিকট নিট পাওনা</div>
                        </td>
                        <td class="text-end fw-bolder text-danger fs-5">
                            ৳{{ number_format($customerPayment->remaining_due, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- In Words (টাকা কথায়) -->
            <div class="in-words-box mb-3 p-2 border rounded-2 d-flex align-items-center">
                <span class="fw-bold text-dark me-2" style="font-size: 9.5pt; white-space: nowrap;">টাকা (কথায়):</span>
                <span class="fw-bold text-primary" style="font-size: 9.5pt;">
                    {{ $wordsAmount }} টাকা মাত্র।
                </span>
            </div>

            @if(!empty($customerPayment->note))
                <div class="p-2 border rounded-2 bg-light mb-3" style="font-size: 8.5pt;">
                    <strong class="text-muted">মন্তব্য / নোট:</strong>
                    <span class="text-dark">{{ $customerPayment->note }}</span>
                </div>
            @endif

            <!-- Signatures Section -->
            <div class="signatures-row d-flex justify-content-between align-items-end mt-5 pt-3">
                <div class="text-center" style="width: 170px;">
                    <div class="signature-line border-top border-dark pt-1 fw-bold text-dark" style="font-size: 9pt;">
                        কাস্টমারের স্বাক্ষর
                    </div>
                </div>
                <div class="text-center" style="width: 170px;">
                    <div class="signature-line border-top border-dark pt-1 fw-bold text-dark" style="font-size: 9pt;">
                        আদায়কারী / ক্যাশিয়ার
                    </div>
                </div>
                <div class="text-center" style="width: 170px;">
                    <div class="signature-line border-top border-dark pt-1 fw-bold text-dark" style="font-size: 9pt;">
                        স্বত্বাধিকারী / ম্যানেজার
                    </div>
                </div>
            </div>

            <!-- Footer Small Notice -->
            <div class="text-center text-muted mt-4 pt-2 border-top" style="font-size: 7.5pt;">
                সানা ফিশারিজ ও হ্যাচারি পয়েন্ট • সফ্টওয়্যার জেনারেটেড রসিদ ভাউচার
            </div>
        </div>
    </div>
</div>

<style>
/* ================= SCREEN STYLING ================= */
.voucher-pad-wrapper {
    max-width: 780px;
}

.voucher-pad-card {
    border: 2px solid #0284c7;
    border-radius: 12px;
    padding: 24px 28px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.border-bottom-theme {
    border-bottom: 2px solid #0284c7;
}

.voucher-company-title {
    font-size: 22pt;
    font-weight: 800;
    color: #0284c7;
    letter-spacing: -0.5px;
    line-height: 1.1;
}

.receipt-badge {
    background: #0284c7;
    color: #ffffff;
    padding: 5px 12px;
    font-weight: 700;
    font-size: 10pt;
    border-radius: 6px;
    letter-spacing: 0.5px;
    display: inline-block;
}

.operator-badge {
    background: #0f172a;
    color: #ffffff;
    padding: 3px 12px;
    border-radius: 4px;
    font-size: 8.5pt;
    font-weight: 600;
}

.nursing-point-strip {
    background: #4c1d95;
    color: #ffffff;
    font-size: 8.5pt;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
}

.customer-info-box {
    background: #f8fafc;
    border-color: #cbd5e1 !important;
}

.voucher-table th {
    background: #f1f5f9;
    color: #0f172a;
    font-size: 9pt;
    font-weight: 700;
    padding: 8px 12px;
}

.voucher-table td {
    padding: 10px 12px;
    vertical-align: middle;
}

.in-words-box {
    background: #f0fdf4;
    border-color: #86efac !important;
}

.logo-circle {
    width: 72px;
    height: 72px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-svg {
    width: 100%;
    height: 100%;
}

.logo-inner-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    line-height: 1;
    font-size: 7.5pt;
    color: #0284c7;
}

/* ================= PRINT STYLING (A4 & A5) ================= */
@media print {
    @page {
        size: A4 portrait;
        margin: 12mm 15mm;
    }

    body {
        background: #ffffff !important;
        color: #000000 !important;
        font-family: 'Inter', sans-serif !important;
    }

    .app-sidebar, .app-header, nav, .d-print-none, .btn, footer {
        display: none !important;
    }

    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }

    .voucher-pad-wrapper {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
    }

    .voucher-pad-card {
        border: 2px solid #000000 !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 15px 20px !important;
    }

    .border-bottom-theme {
        border-bottom: 2px solid #000000 !important;
    }

    .receipt-badge {
        background: #000000 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .operator-badge {
        background: #000000 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .nursing-point-strip {
        background: #333333 !important;
        color: #ffffff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .voucher-table th, .voucher-table td {
        border: 1px solid #000000 !important;
    }
}
</style>
@endsection
