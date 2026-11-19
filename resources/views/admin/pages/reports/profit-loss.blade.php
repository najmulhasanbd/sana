@extends('admin.layouts.master')

@section('title', 'আয়-ব্যয় ও লাভ-ক্ষতির হিসাব রিপোর্ট')

@push('styles')
<style>
    @media print {
        body {
            background-color: #fff !important;
            color: #000 !important;
            font-size: 11pt !important;
        }
        .app-sidebar,
        .app-header,
        .no-print,
        .btn,
        .breadcrumb,
        .filter-card {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .card {
            border: 1px solid #cbd5e1 !important;
            box-shadow: none !important;
            break-inside: avoid;
        }
        .print-header {
            display: block !important;
            margin-bottom: 25px !important;
        }
        .bg-light {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table {
            border-color: #cbd5e1 !important;
        }
    }
    .print-header {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="w-100 pb-5">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 no-print">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-chart-pie text-primary"></i>
                <span>আয়, ব্যয় এবং লাভ-ক্ষতির হিসাব (Profit & Loss Report)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">রিপোর্ট ও হিসাব</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">লাভ-ক্ষতি রিপোর্ট</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customer-due.index') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                <span>কাস্টমার বকেয়া শিট</span>
            </a>
            <a href="{{ route('admin.expense.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-receipt"></i>
                <span>দৈনিক খরচ খাতা</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3.5 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট রিপোর্ট</span>
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 filter-card no-print">
        <div class="card-body p-3 p-md-4">
            <!-- Quick Date Preset Buttons -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                <span class="text-secondary fw-semibold small me-1"><i class="fa-regular fa-calendar-check me-1"></i>সময়কাল নির্বাচন:</span>
                @php
                    $presets = [
                        'today' => 'আজকে',
                        'this_week' => 'চলতি সপ্তাহ',
                        'this_month' => 'চলতি মাস',
                        'last_month' => 'গত মাস',
                        'this_year' => 'চলতি বছর',
                        'all' => 'সব সময় (সর্বমোট)',
                    ];
                @endphp
                @foreach($presets as $key => $label)
                    <a href="{{ route('admin.report.profit-loss', ['date_preset' => $key]) }}" 
                       class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $datePreset === $key ? 'btn-primary' : 'btn-light border text-secondary' }}"
                       style="font-size: 0.8rem;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Custom Date Range Form -->
            <form action="{{ route('admin.report.profit-loss') }}" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="date_preset" value="custom">

                <div class="col-12 col-sm-5 col-md-4">
                    <label class="form-label text-secondary small fw-semibold mb-1">হতে (শুরুর তারিখ)</label>
                    <input type="date" 
                           name="from_date" 
                           value="{{ $fromDate ?? '' }}" 
                           class="form-control bg-light" 
                           style="font-size: 0.85rem;">
                </div>

                <div class="col-12 col-sm-5 col-md-4">
                    <label class="form-label text-secondary small fw-semibold mb-1">পর্যন্ত (শেষ তারিখ)</label>
                    <input type="date" 
                           name="to_date" 
                           value="{{ $toDate ?? '' }}" 
                           class="form-control bg-light" 
                           style="font-size: 0.85rem;">
                </div>

                <div class="col-12 col-sm-2 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if($datePreset !== 'this_month')
                        <a href="{{ route('admin.report.profit-loss') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Official Printable Document Header -->
    <div class="print-header text-center border-bottom pb-3 mb-4">
        <h3 class="fw-bold mb-1 text-dark">{{ $settings->company_name ?? 'সানা ফিশারিজ' }}</h3>
        <p class="text-muted mb-1" style="font-size: 0.9rem;">
            {{ $settings->address ?? 'হ্যাচারি ও নার্সিং পয়েন্ট' }} | ফোন: {{ $settings->phone ?? '' }}
        </p>
        <h5 class="fw-bold text-primary mt-2 mb-1">আয়, ব্যয় ও লাভ-ক্ষতির পূর্ণাঙ্গ হিসাব বিবরণী</h5>
        <div class="badge bg-light text-dark border px-3 py-1.5" style="font-size: 0.85rem;">
            সময়কাল: 
            <strong>{{ $fromDate ? date('d M, Y', strtotime($fromDate)) : 'শুরু থেকে' }}</strong> 
            হতে 
            <strong>{{ $toDate ? date('d M, Y', strtotime($toDate)) : 'আজ পর্যন্ত' }}</strong>
        </div>
        <div class="text-muted mt-1" style="font-size: 0.75rem;">
            রিপোর্ট তৈরির সময়: {{ now()->translatedFormat('d F, Y - h:i A') }}
        </div>
    </div>

    <!-- Highlight Net Profit / Loss Banner Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" 
         style="background: {{ $isProfit ? 'linear-gradient(135deg, #065f46 0%, #047857 100%)' : 'linear-gradient(135deg, #991b1b 0%, #b91c1c 100%)' }}; color: #fff;">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill px-3 py-1.5 fw-bold" 
                              style="background-color: {{ $isProfit ? '#10b981' : '#f87171' }}; color: #fff; font-size: 0.85rem;">
                            <i class="fa-solid {{ $isProfit ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} me-1"></i>
                            {{ $isProfit ? 'নিট ব্যবসায়িক লাভ (Net Profit)' : 'নিট ব্যবসায়িক ক্ষতি (Net Loss)' }}
                        </span>
                        <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 0.8rem;">
                            {{ $datePreset === 'all' ? 'সর্বমোট সময়' : 'নির্বাচিত সময়কাল' }}
                        </span>
                    </div>

                    <h1 class="display-5 fw-bold mb-2">
                        ৳ {{ number_format(abs($netProfit), 2) }}
                    </h1>

                    <p class="mb-0 text-white text-opacity-85" style="font-size: 0.95rem;">
                        @if($isProfit)
                            <i class="fa-solid fa-circle-check me-1 text-success"></i> 
                            নির্বাচিত সময়ে আপনার ব্যবসা <strong>৳ {{ number_format($netProfit, 2) }}</strong> লাভজনক অবস্থায় রয়েছে (প্রফিট মার্জিন: {{ $profitMargin }}%)।
                        @else
                            <i class="fa-solid fa-triangle-exclamation me-1 text-warning"></i> 
                            নির্বাচিত সময়ে আয়ের চেয়ে ব্যয় <strong>৳ {{ number_format(abs($netProfit), 2) }}</strong> বেশি হয়েছে।
                        @endif
                    </p>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="rounded-4 p-3.5 bg-black bg-opacity-20 border border-white border-opacity-15">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-15">
                            <span class="text-white text-opacity-80 small">সর্বমোট বিক্রয় (আয়):</span>
                            <span class="fw-bold text-white fs-6">৳ {{ number_format($totalSales, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-white border-opacity-15">
                            <span class="text-white text-opacity-80 small">সর্বমোট সামগ্রিক ব্যয়:</span>
                            <span class="fw-bold text-white fs-6">৳ {{ number_format($totalExpensesAndCosts, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-white text-opacity-80 small">নগদ ক্যাশ ফ্লো উদ্বৃত্ত:</span>
                            <span class="fw-bold {{ $netCashFlow >= 0 ? 'text-cyan-300' : 'text-warning' }} fs-6" style="color: {{ $netCashFlow >= 0 ? '#6ee7b7' : '#fca5a5' }};">
                                ৳ {{ number_format($netCashFlow, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Side-by-Side Detailed Breakdown -->
    <div class="row g-4 mb-4">
        <!-- 1. REVENUE / INCOME COLUMN -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                <div class="card-header bg-success-subtle border-bottom border-success-subtle p-3.5 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                            <i class="fa-solid fa-arrow-down-long"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-success mb-0">মোট ব্যবসায়িক আয় (Revenue & Sales)</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">মাছ ও পোনা বিক্রয় এবং নগদ আদায়</small>
                        </div>
                    </div>
                    <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
                        ৳ {{ number_format($totalSales, 2) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <!-- Total Invoiced Sales -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark d-block">মাছ ও পোনা বিক্রয় চালান (Invoiced Sales)</span>
                                <small class="text-muted">মোট চালান সংখ্যা: {{ number_format($totalSalesInvoices) }} টি</small>
                            </div>
                            <span class="fw-bold text-dark fs-6">৳ {{ number_format($totalSales, 2) }}</span>
                        </div>

                        <!-- Cash Collected -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-semibold text-success d-block">
                                    <i class="fa-solid fa-hand-holding-dollar me-1"></i>নগদ আদায়কৃত টাকা (Cash Received)
                                </span>
                                <small class="text-muted">কাস্টমারদের নিকট হতে আদায়কৃত ক্যাশ</small>
                            </div>
                            <span class="fw-bold text-success fs-6">৳ {{ number_format($totalSalesCashReceived, 2) }}</span>
                        </div>

                        <!-- Sales Due Amount -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-semibold text-danger d-block">
                                    <i class="fa-solid fa-clock me-1"></i>বিক্রয় বকেয়া (Sales Due Remaining)
                                </span>
                                <small class="text-muted">নির্বাচিত সময়ের বিক্রয়ের বাকি টাকা</small>
                            </div>
                            <span class="fw-bold text-danger fs-6">৳ {{ number_format($totalSalesDue, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 no-print">
                        <a href="{{ route('admin.customer-due.index') }}" class="btn btn-light border w-100 py-2.5 rounded-3 fw-semibold text-primary d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-users"></i>
                            <span>কাস্টমারভিত্তিক বকেয়া শিট দেখুন</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. EXPENDITURES & COSTS COLUMN -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                <div class="card-header bg-danger-subtle border-bottom border-danger-subtle p-3.5 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                             style="width: 36px; height: 36px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                            <i class="fa-solid fa-arrow-up-long"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-danger mb-0">মোট প্রাতিষ্ঠানিক ব্যয় (Total Expenditures)</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">পোনা ক্রয়, বেতন এবং অন্যান্য প্রাতিষ্ঠানিক খরচ</small>
                        </div>
                    </div>
                    <span class="badge bg-danger rounded-pill px-3 py-1.5 fw-bold" style="font-size: 0.85rem;">
                        ৳ {{ number_format($totalExpensesAndCosts, 2) }}
                    </span>
                </div>

                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        <!-- Purchases -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark d-block">১. পোনা ও মাছ ক্রয় (Purchases)</span>
                                <small class="text-muted">হ্যাচারি থেকে ক্রয় চালান: {{ number_format($totalPurchaseInvoices) }} টি (পরিশোধ: ৳ {{ number_format($totalPurchasesPaid, 2) }})</small>
                            </div>
                            <span class="fw-bold text-dark fs-6">৳ {{ number_format($totalPurchases, 2) }}</span>
                        </div>

                        <!-- Salaries -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark d-block">২. কর্মচারীদের বেতন (Staff Salaries)</span>
                                <small class="text-muted">পরিশোধিত বেতন ভাউচার সংখ্যা: {{ number_format($totalSalaryCount) }} টি</small>
                            </div>
                            <span class="fw-bold text-dark fs-6">৳ {{ number_format($totalSalariesPaid, 2) }}</span>
                        </div>

                        <!-- Daily Expenses -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center border-bottom">
                            <div>
                                <span class="fw-bold text-dark d-block">৩. দৈনিক প্রাতিষ্ঠানিক খরচ (Daily Expenses)</span>
                                <small class="text-muted">বিদ্যুৎ বিল, খাবার, পরিবহন, নাস্তা ইত্যাদি (ভাউচার: {{ number_format($totalExpenseCount) }} টি)</small>
                            </div>
                            <span class="fw-bold text-dark fs-6">৳ {{ number_format($totalDailyExpenses, 2) }}</span>
                        </div>

                        <!-- Total Overall Costs Highlight -->
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-light rounded-3 px-3 mt-2">
                            <span class="fw-bold text-danger">সর্বমোট ব্যয় (১ + ২ + ৩):</span>
                            <span class="fw-bold text-danger fs-5">৳ {{ number_format($totalExpensesAndCosts, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 no-print">
                        <a href="{{ route('admin.expense.index') }}" class="btn btn-light border w-100 py-2.5 rounded-3 fw-semibold text-primary d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>দৈনিক খরচ এন্ট্রি বা বিস্তারিত খাতা দেখুন</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category-Wise Breakdown of General Expenses -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <span>দৈনিক খরচের খাতভিত্তিক বিভাজন (Category-wise Expense Breakdown)</span>
            </h6>
            <span class="badge bg-light text-secondary border rounded-pill px-3 py-1">
                মোট {{ $expenseCategoryBreakdown->count() }} টি খাতে খরচ
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">#</th>
                        <th class="py-3">খরচের খাত</th>
                        <th class="py-3 text-center">ভাউচার সংখ্যা</th>
                        <th class="py-3 text-end">টাকার পরিমাণ (৳)</th>
                        <th class="py-3" style="width: 250px;">খরচের অনুপাত</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenseCategoryBreakdown as $catBreakdown)
                        @php
                            $percentage = $totalDailyExpenses > 0 ? round(($catBreakdown->total_amount / $totalDailyExpenses) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $catBreakdown->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1">
                                    {{ $catBreakdown->count }} টি
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                ৳ {{ number_format((float) $catBreakdown->total_amount, 2) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="text-muted small fw-semibold" style="width: 45px;">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                নির্বাচিত সময়ের মধ্যে কোনো দৈনিক প্রাতিষ্ঠানিক খরচ পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Monthly Trend Table (Current Year) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-calendar-days text-primary"></i>
                <span>চলতি বছরের মাসিক লাভ-ক্ষতির তুলনামূলক চিত্র ({{ now()->year }})</span>
            </h6>
            <small class="text-muted">মাসভিত্তিক আয়, ক্রয়, বেতন ও অন্যান্য ব্যয়ের চিত্র</small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3">মাস</th>
                        <th class="py-3 text-end">মোট বিক্রয় (৳)</th>
                        <th class="py-3 text-end">পোনা ক্রয় (৳)</th>
                        <th class="py-3 text-end">কর্মচারী বেতন (৳)</th>
                        <th class="py-3 text-end">অন্যান্য খরচ (৳)</th>
                        <th class="py-3 text-end">মোট ব্যয় (৳)</th>
                        <th class="pe-4 py-3 text-end">নিট লাভ / ক্ষতি (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyTrend as $mTrend)
                        @php
                            $mIsProfit = $mTrend['net_profit'] >= 0;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $mTrend['month_name'] }}</td>
                            <td class="text-end fw-semibold text-success">
                                ৳ {{ number_format($mTrend['sales'], 2) }}
                            </td>
                            <td class="text-end text-muted">
                                ৳ {{ number_format($mTrend['purchases'], 2) }}
                            </td>
                            <td class="text-end text-muted">
                                ৳ {{ number_format($mTrend['salaries'], 2) }}
                            </td>
                            <td class="text-end text-muted">
                                ৳ {{ number_format($mTrend['expenses'], 2) }}
                            </td>
                            <td class="text-end fw-semibold text-danger">
                                ৳ {{ number_format($mTrend['total_cost'], 2) }}
                            </td>
                            <td class="pe-4 text-end">
                                <span class="badge {{ $mIsProfit ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-2.5 py-1 fw-bold fs-7">
                                    {{ $mIsProfit ? '+' : '' }} ৳ {{ number_format($mTrend['net_profit'], 2) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
