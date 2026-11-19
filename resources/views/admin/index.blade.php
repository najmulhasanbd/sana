@extends('admin.layouts.master')
@section('title', 'ড্যাশবোর্ড - সানা ফিশারিজ')

@section('content')
<div class="container-fluid p-3 p-md-4">
    <!-- Top Welcome & Quick Actions Bar -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
        <div class="card-body p-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <span style="color: #0284c7;"><i class="fa-solid fa-fish-fins"></i></span>
                    <span>স্বাগতম, {{ Auth::user()->name ?? 'অ্যাডমিন' }}!</span>
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.88rem;">
                    সানা ফিশারিজ - মাছের পোনা ও নার্সিং পয়েন্টের সার্বিক রিয়েল-টাইম হিসাব ড্যাশবোর্ড
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.sales.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-plus"></i>
                    <span>নতুন বিক্রয় চালান</span>
                </a>
                <a href="{{ route('admin.customer-due.index') }}" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>কাস্টমার বকেয়া শিট</span>
                </a>
                <a href="{{ route('admin.purchase.create') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>পোনা ক্রয়</span>
                </a>
                <a href="{{ route('admin.expense.index') }}" class="btn btn-outline-warning rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5 text-dark" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-receipt"></i>
                    <span>দৈনিক খরচ</span>
                </a>
                <a href="{{ route('admin.cash-book.index') }}" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                    <i class="fa-solid fa-wallet"></i>
                    <span>ক্যাশ ও আয়-ব্যয় খাতা</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 1. Hero 3 Top Stat Cards (মানুষের কাছে মোট বাকি, সর্বমোট বিক্রয়, মোট পোনা ক্রয়) -->
    <div class="row g-3 mb-4">
        <!-- 1. মানুষের কাছে সর্বমোট বাকি (Total Customer Due) - Prominently Highlighted -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%); border-left: 5px solid #ef4444 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-danger fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-hand-holding-dollar me-1"></i>মানুষের কাছে মোট বাকি
                    </span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger bg-danger bg-opacity-10"
                         style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-triangle-exclamation fs-6"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-1 text-danger">
                    ৳ {{ number_format($totalCustomerDue, 2) }}
                </h2>

                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                    <small class="text-muted" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-users me-1"></i>{{ $customersWithDueCount }} জন কাস্টমারের বকেয়া
                    </small>
                    <a href="{{ route('admin.customer-due.index') }}" class="text-danger fw-semibold text-decoration-none small" style="font-size: 0.8rem;">
                        বকেয়া শিট দেখুন <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. সর্বমোট বিক্রয় (Total Sales) -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-left: 5px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-success fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-cart-shopping me-1"></i>সর্বমোট বিক্রয়
                    </span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10"
                         style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-arrow-trend-up fs-6"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-1 text-success">
                    ৳ {{ number_format($totalSales, 2) }}
                </h2>

                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                    <small class="text-muted" style="font-size: 0.78rem;">
                        নগদ আদায়: ৳ {{ number_format($totalCollected, 2) }}
                    </small>
                    <a href="{{ route('admin.sales.index') }}" class="text-success fw-semibold text-decoration-none small" style="font-size: 0.8rem;">
                        {{ $totalSalesCount }} টি চালান <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 3. মোট পোনা ক্রয় (Total Purchases) -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); border-left: 5px solid #0284c7 !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-primary fw-bold text-uppercase" style="font-size: 0.78rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-truck-ramp-box me-1"></i>মোট পোনা ক্রয়
                    </span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10"
                         style="width: 38px; height: 38px;">
                        <i class="fa-solid fa-boxes-stacked fs-6"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-1 text-primary">
                    ৳ {{ number_format($totalPurchases, 2) }}
                </h2>

                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top">
                    <small class="text-muted" style="font-size: 0.78rem;">
                        সাপ্লায়ার বাকি: ৳ {{ number_format($totalSupplierDue, 2) }}
                    </small>
                    <a href="{{ route('admin.purchase.index') }}" class="text-primary fw-semibold text-decoration-none small" style="font-size: 0.8rem;">
                        {{ $totalPurchasesCount }} টি চালান <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Today's Quick Pulse (3 Cards: আজকের বিক্রয়, আজকের নগদ আদায়, আজকের খরচ) -->
    <div class="row g-3 mb-4">
        <!-- Today's Sales -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 44px; height: 44px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের বিক্রয়</small>
                        <h5 class="fw-bold mb-0 text-dark">৳ {{ number_format($todaySales, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Cash Collection -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের নগদ আদায়</small>
                        <h5 class="fw-bold mb-0 text-dark">৳ {{ number_format($todayCashCollected, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Expense -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 44px; height: 44px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-receipt fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের খরচ</small>
                        <h5 class="fw-bold mb-0 text-dark">৳ {{ number_format($todayExpenses, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Middle Section: Chart & Quick Stats Overview -->
    <div class="row g-3 mb-4">
        <!-- Monthly Sales vs Costs Trend (Chart.js) -->
        <div class="col-xl-8 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-chart-column text-primary"></i>
                            <span>গত ৬ মাসের বিক্রয় ও ব্যয়ের তুলনামূলক চিত্র</span>
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">মাসিক মোট বিক্রয় বনাম সামগ্রিক ব্যয় (ক্রয় + বেতন + অন্যান্য খরচ)</small>
                    </div>
                    <div class="d-flex align-items-center gap-3 small">
                        <span><i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> বিক্রয়</span>
                        <span><i class="fa-solid fa-circle text-danger me-1" style="font-size: 8px;"></i> ব্যয়</span>
                    </div>
                </div>
                <div class="position-relative" style="height: 300px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Summary & Counts -->
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-cubes text-primary"></i>
                    <span>ব্যবসার সার্বিক একনজর</span>
                </h6>

                <div class="d-flex flex-column gap-3">
                    <!-- Total Customers -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <span class="fw-semibold text-dark small">নিবন্ধিত কাস্টমার</span>
                        </div>
                        <a href="{{ route('admin.customer.index') }}" class="fw-bold text-primary text-decoration-none small">
                            {{ number_format($totalCustomersCount) }} জন <i class="fa-solid fa-chevron-right ms-1"></i>
                        </a>
                    </div>

                    <!-- Total Suppliers -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-info bg-info bg-opacity-10" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </div>
                            <span class="fw-semibold text-dark small">হ্যাচারি সরবরাহকারী</span>
                        </div>
                        <a href="{{ route('admin.supplier.index') }}" class="fw-bold text-info text-decoration-none small">
                            {{ number_format($totalSuppliersCount) }} জন <i class="fa-solid fa-chevron-right ms-1"></i>
                        </a>
                    </div>

                    <!-- Active Employees -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                            <span class="fw-semibold text-dark small">সক্রিয় কর্মচারী ও স্টাফ</span>
                        </div>
                        <a href="{{ route('admin.employee.index') }}" class="fw-bold text-success text-decoration-none small">
                            {{ number_format($totalEmployeesCount) }} জন <i class="fa-solid fa-chevron-right ms-1"></i>
                        </a>
                    </div>

                    <!-- Total General Expenses -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-warning bg-warning bg-opacity-10" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <span class="fw-semibold text-dark small">দৈনিক অন্যান্য খরচ</span>
                        </div>
                        <span class="fw-bold text-dark small">৳ {{ number_format($totalDailyExpenses, 2) }}</span>
                    </div>

                    <!-- Total Salaries -->
                    <div class="d-flex align-items-center justify-content-between p-2.5 rounded-3 bg-light">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-secondary bg-secondary bg-opacity-10" style="width: 34px; height: 34px;">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <span class="fw-semibold text-dark small">মোট বেতন পরিশোধ</span>
                        </div>
                        <span class="fw-bold text-dark small">৳ {{ number_format($totalSalariesPaid, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Bottom Section: Recent Sales & Top Products -->
    <div class="row g-3">
        <!-- Recent Sales -->
        <div class="col-xl-8 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                            <i class="fa-solid fa-cart-shopping text-primary"></i>
                            <span>সাম্প্রতিক বিক্রয় চালান (Recent Sales)</span>
                        </h6>
                    </div>
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary" style="font-size: 0.8rem;">
                        সব চালান দেখুন
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-3 py-2.5">ইনভয়েস #</th>
                                <th class="py-2.5">কাস্টমার</th>
                                <th class="py-2.5">তারিখ</th>
                                <th class="py-2.5 text-end">মোট মূল্য</th>
                                <th class="py-2.5 text-end">বকেয়া</th>
                                <th class="py-2.5 text-center">স্ট্যাটাস</th>
                                <th class="pe-3 py-2.5 text-end">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="text-decoration-none">
                                            {{ $sale->invoice_no }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $sale->customer?->name ?? 'ক্যাশ কাস্টমার' }}</div>
                                        <small class="text-muted" style="font-size: 0.72rem;">{{ $sale->customer?->mobile ?? '' }}</small>
                                    </td>
                                    <td>
                                        {{ $sale->sale_date ? $sale->sale_date->format('d M, Y') : '—' }}
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        ৳ {{ number_format((float) $sale->total_amount, 2) }}
                                    </td>
                                    <td class="text-end fw-bold {{ (float) $sale->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                        ৳ {{ number_format((float) $sale->due_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($sale->payment_status === 'Paid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">পরিশোধ</span>
                                        @elseif($sale->payment_status === 'Partial')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">আংশিক</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">বকেয়া</span>
                                        @endif
                                    </td>
                                    <td class="pe-3 text-end">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn btn-sm btn-light border rounded-2" title="চালান দেখুন">
                                            <i class="fa-solid fa-eye text-primary"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        এখনও কোনো বিক্রয় চালান তৈরি করা হয়নি।
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Selling Fish Fry Products -->
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                        <i class="fa-solid fa-fish text-primary"></i>
                        <span>জনপ্রিয় মাছের পোনা</span>
                    </h6>
                    <a href="{{ route('admin.sale-product.index') }}" class="btn btn-light btn-sm rounded-pill px-3 fw-semibold text-primary" style="font-size: 0.8rem;">
                        সব পণ্য
                    </a>
                </div>

                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-3">
                        @forelse($topProducts as $tp)
                            <div class="d-flex align-items-center justify-content-between p-2.5 border rounded-3 bg-white">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-primary bg-light"
                                         style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-fish-fins fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.88rem;">
                                            {{ $tp->product?->name ?? 'অনির্দিষ্ট মাছ' }}
                                        </h6>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            বিক্রয় পরিমাণ: {{ number_format((float) $tp->total_qty) }}
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <h6 class="fw-bold mb-0 text-success" style="font-size: 0.88rem;">
                                        ৳ {{ number_format((float) $tp->total_revenue, 2) }}
                                    </h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">মোট আয়</small>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="fa-solid fa-fish fs-2 text-muted opacity-50 mb-2"></i>
                                <p class="mb-0 small">কোনো বিক্রয় তথ্য পাওয়া যায়নি।</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('monthlyTrendChart');
        if (ctx) {
            const chartLabels = {!! json_encode($chartLabels) !!};
            const salesData = {!! json_encode($chartSales) !!};
            const costsData = {!! json_encode($chartCosts) !!};

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'মোট বিক্রয় (৳)',
                            data: salesData,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 6,
                        },
                        {
                            label: 'মোট ব্যয় (৳)',
                            data: costsData,
                            backgroundColor: 'rgba(239, 68, 68, 0.85)',
                            borderColor: '#ef4444',
                            borderWidth: 1,
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ৳ ' + Number(context.parsed.y).toLocaleString('en-IN', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '৳ ' + value.toLocaleString();
                                },
                                font: {
                                    size: 11
                                }
                            },
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
