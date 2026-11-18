@extends('admin.layouts.master')

@section('title', 'বেতন রসিদ / ভাউচার - ' . ($salary->employee?->name ?? 'কর্মচারী'))

@section('content')
<div class="w-100 pb-4">
    <!-- Top Action Bar (Screen Only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-receipt text-success"></i>
                <span>কর্মচারী বেতন পরিশোধ রসিদ</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee-salary.index') }}" class="text-decoration-none text-muted">বেতন তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বেতন রসিদ</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employee-salary.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
            <a href="{{ route('admin.employee.show', $salary->employee_id) }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-user"></i>
                <span>কর্মচারীর প্রোফাইল</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-success rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>রসিদ প্রিন্ট করুন</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- OFFICIAL SALARY VOUCHER / PAYSLIP (Screen & Print Perfect)                  -->
    <!-- ========================================================================= -->
    @php
        $businessName = \App\Models\Setting::get('memo_business_name', \App\Models\Setting::get('business_name', 'মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট'));
        $subtitle = \App\Models\Setting::get('memo_subtitle', 'এখানে সকল প্রকার বাগদার পোনা নার্সিং করা হয় ও বিক্রয়ের নির্ভরযোগ্য প্রতিষ্ঠান।');
        $contactNumbers = \App\Models\Setting::get('contact_numbers', \App\Models\Setting::get('mobile', '01737 908309, 01352 383531'));
        $officeAddress = \App\Models\Setting::get('office_address', \App\Models\Setting::get('address', 'পুঁইজালা বাল্লা বাজার, আশাশুনি, সাতক্ষীরা।'));
        $siteLogo = \App\Models\Setting::get('site_logo');
        $authoritySig = \App\Models\Setting::get('memo_authority_signature', 'পক্ষে - মেসার্স সানা ফিস এন্ড নার্সিং পয়েন্ট');
    @endphp

    <div class="d-flex justify-content-center">
        <div class="salary-voucher-wrapper bg-white shadow-sm">
            <div class="salary-voucher-border">
                
                <!-- TOP HEADER -->
                <div class="text-center mb-3">
                    <div class="bismillah text-dark fw-bold mb-1" style="font-size: 10.5pt;">
                        বিসমিল্লাহির রাহমানির রাহিম
                    </div>
                    
                    <div class="d-inline-block voucher-badge mb-2">
                        বে &nbsp;|&nbsp; ত &nbsp;|&nbsp; ন &nbsp;&nbsp;&nbsp;&nbsp; র &nbsp;|&nbsp; সি &nbsp;|&nbsp; দ
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="voucher-logo flex-shrink-0" style="width: 70px;">
                            @if(!empty($siteLogo) && file_exists(public_path($siteLogo)))
                                <img src="{{ asset($siteLogo) }}" alt="{{ $businessName }}" style="max-width: 65px; max-height: 65px; object-fit: contain;">
                            @endif
                        </div>
                        <div class="flex-grow-1 px-2 text-center">
                            <h2 class="voucher-company-title mb-0">{{ $businessName }}</h2>
                            @if(!empty($subtitle))
                                <div class="voucher-subtitle fw-bold text-dark mt-0.5" style="font-size: 9.5pt;">
                                    {{ $subtitle }}
                                </div>
                            @endif
                            <div class="voucher-address text-muted mt-0.5" style="font-size: 9pt;">
                                {{ $officeAddress }} {{ $contactNumbers ? ' | যোগাযোগ: ' . $contactNumbers : '' }}
                            </div>
                        </div>
                        <div style="width: 70px;"></div>
                    </div>
                </div>

                <!-- EMPLOYEE & VOUCHER DETAILS GRID -->
                <div class="employee-info-box mb-3">
                    <table class="w-100 employee-table">
                        <tr>
                            <td class="emp-label" style="width: 120px;">কর্মচারীর নাম:</td>
                            <td class="emp-val fw-bold text-dark" style="font-size: 11pt;">
                                {{ $salary->employee?->name ?? '—' }}
                            </td>
                            <td class="emp-label" style="width: 110px;">ভাউচার আইডি:</td>
                            <td class="emp-val fw-bold text-primary" style="width: 140px;">
                                #PAY-{{ str_pad($salary->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="emp-label">পদবী / কাজ:</td>
                            <td class="emp-val">
                                {{ $salary->employee?->designation ?? '—' }}
                                @if($salary->employee?->mobile)
                                    <span class="text-muted ms-2">({{ $salary->employee->mobile }})</span>
                                @endif
                            </td>
                            <td class="emp-label">পরিশোধের তারিখ:</td>
                            <td class="emp-val fw-bold">
                                {{ $salary->payment_date ? $salary->payment_date->format('d/m/Y') : '—' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="emp-label">বেতনের মাস:</td>
                            <td class="emp-val fw-bold text-dark">
                                {{ $salary->salary_month_name }}
                            </td>
                            <td class="emp-label">পরিশোধ মাধ্যম:</td>
                            <td class="emp-val">
                                {{ $salary->payment_method }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- SALARY BREAKDOWN TABLE -->
                <div class="salary-breakdown-box mb-3">
                    <table class="w-100 salary-table">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center bg-primary text-white py-2" style="width: 50%;">উপার্জন (Earnings)</th>
                                <th colspan="2" class="text-center bg-danger text-white py-2" style="width: 50%;">কর্তন (Deductions)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="item-name">মূল বেতন:</td>
                                <td class="item-amount text-end">৳{{ number_format($salary->base_salary, 2) }}</td>
                                <td class="item-name">অগ্রিম বাবদ কর্তন (-):</td>
                                <td class="item-amount text-end text-danger fw-semibold">
                                    {{ $salary->advance_deduction > 0 ? '৳' . number_format($salary->advance_deduction, 2) : '৳০.০০' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="item-name">বোনাস / অতিরিক্ত ভাতা:</td>
                                <td class="item-amount text-end">
                                    {{ $salary->bonus_amount > 0 ? '৳' . number_format($salary->bonus_amount, 2) : '৳০.০০' }}
                                </td>
                                <td class="item-name">অন্যান্য কর্তন:</td>
                                <td class="item-amount text-end text-muted">৳০.০০</td>
                            </tr>
                            <tr class="fw-bold bg-light">
                                <td class="item-name text-dark">মোট উপার্জন (Gross):</td>
                                <td class="item-amount text-end text-dark">
                                    ৳{{ number_format($salary->base_salary + $salary->bonus_amount, 2) }}
                                </td>
                                <td class="item-name text-danger">মোট কর্তন:</td>
                                <td class="item-amount text-end text-danger">
                                    ৳{{ number_format($salary->advance_deduction, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- NET SALARY PAID BANNER -->
                <div class="net-salary-banner d-flex justify-content-between align-items-center p-3 mb-3">
                    <div>
                        <span class="text-uppercase fw-bold text-muted small d-block">চূড়ান্ত প্রদেয় নিট বেতন</span>
                        <h2 class="fw-bolder text-success mb-0" style="font-size: 20pt;">৳{{ number_format($salary->net_salary, 2) }}</h2>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-circle-check me-1"></i>সম্পূর্ণ পরিশোধিত
                        </span>
                        @if(($salary->employee?->advance_balance ?? 0) > 0)
                            <small class="d-block text-warning-emphasis fw-bold mt-1" style="font-size: 0.8rem;">
                                অবশিষ্ট বকেয়া অগ্রিম: ৳{{ number_format($salary->employee->advance_balance, 2) }}
                            </small>
                        @endif
                    </div>
                </div>

                <!-- IN WORDS & NOTE -->
                <div class="in-words-box d-flex align-items-center p-2 mb-3 border rounded-2">
                    <strong class="text-secondary ps-2 pe-3 border-end" style="font-size: 0.9rem; white-space: nowrap;">টাকা (কথায়):</strong>
                    <span class="fw-bold text-dark ps-3" style="font-size: 0.95rem;">
                        {{ \App\Helpers\BanglaNumberHelper::bnWords($salary->net_salary) }}
                    </span>
                </div>

                @if(!empty($salary->note))
                    <div class="p-2 border rounded-2 bg-light mb-3" style="font-size: 0.85rem;">
                        <strong class="text-muted">মন্তব্য / নোট:</strong>
                        <span class="text-dark">{{ $salary->note }}</span>
                    </div>
                @endif

                <!-- SIGNATURES -->
                <div class="signature-row d-flex justify-content-between align-items-end pt-5 pb-2">
                    <div class="text-center" style="width: 180px;">
                        <div class="sig-line">................................................</div>
                        <div class="fw-bold text-dark small mt-1">কর্মচারীর স্বাক্ষর</div>
                    </div>
                    <div class="text-center" style="width: 180px;">
                        <div class="sig-line">................................................</div>
                        <div class="fw-bold text-dark small mt-1">হিসাবরক্ষক / প্রস্তুতকারী</div>
                    </div>
                    <div class="text-center" style="width: 220px;">
                        <div class="sig-line">................................................</div>
                        <div class="fw-bold text-dark small mt-1">{{ $authoritySig }}</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .salary-voucher-wrapper {
        width: 100%;
        max-width: 820px;
        background: #ffffff;
    }
    .salary-voucher-border {
        border: 2px solid #0284c7;
        padding: 24px 28px;
        background: #ffffff;
        min-height: 800px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .voucher-badge {
        background: #0f172a;
        color: #ffffff;
        font-weight: 800;
        font-size: 10.5pt;
        letter-spacing: 2px;
        padding: 2px 20px;
        border-radius: 4px;
    }
    .voucher-company-title {
        font-size: 22pt;
        font-weight: 900;
        color: #1e3a8a;
        letter-spacing: -0.5px;
    }
    .employee-table {
        border-collapse: collapse;
        border: 1.5px solid #0284c7;
    }
    .employee-table td {
        border: 1px solid #0284c7;
        padding: 6px 10px;
        font-size: 9.5pt;
    }
    .emp-label {
        background-color: #0284c7;
        color: #ffffff;
        font-weight: bold;
    }
    .emp-val {
        background-color: #ffffff;
    }
    .salary-table {
        border-collapse: collapse;
        border: 1.5px solid #0284c7;
    }
    .salary-table th {
        font-size: 10pt;
        font-weight: bold;
        border: 1px solid #0284c7;
    }
    .salary-table td {
        border: 1px solid #0284c7;
        padding: 7px 10px;
        font-size: 9.5pt;
    }
    .net-salary-banner {
        border: 2px solid #10b981;
        border-radius: 8px;
        background: #f0fdf4;
    }
    .sig-line {
        color: #64748b;
        letter-spacing: -1px;
        font-weight: bold;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        html, body {
            background: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .app-sidebar, .app-header, header, nav, .d-print-none, footer {
            display: none !important;
        }
        .main-content, .page, .w-100 {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .salary-voucher-wrapper {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        .salary-voucher-border {
            border: 2px solid #0284c7 !important;
            padding: 16px 20px !important;
        }
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
    }
</style>
@endsection
