@extends('admin.layouts.master')

@section('title', 'কাস্টমার তালিকা ও হিসাব')

@section('content')
<div class="w-100 pb-4">
    <!-- Page Header & Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">
                <i class="fa-solid fa-users text-primary me-2"></i>কাস্টমার তালিকা ও লেজার
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                সকল পোনা ও মাছ ক্রেতাদের হিসাব, এলাকাভিত্তিক তথ্য ও বকেয়া স্ট্যাটাস
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.customer-due.index') }}" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>এলাকাভিত্তিক বকেয়া শিট</span>
            </a>
            <a href="{{ route('admin.sales.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন বিক্রয় চালান</span>
            </a>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট কাস্টমার</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_customers']) }} জন</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fa-solid fa-user-clock fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">বকেয়া কাস্টমার</small>
                        <h4 class="fw-bold mb-0 text-danger">{{ number_format($stats['total_due_customers']) }} জন</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-money-bill-wave fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট বিক্রয় মূল্য</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_sales'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট বকেয়া পরিমাণ</small>
                        <h4 class="fw-bold mb-0 text-danger">৳{{ number_format($stats['total_due'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter and Search Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.customer.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ $search ?? '' }}"
                               class="form-control bg-light border-start-0"
                               placeholder="নাম, মোবাইল বা এলাকা দিয়ে খুঁজুন..."
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <select name="division_id" id="division_select" class="form-select bg-light" style="font-size: 0.88rem;">
                        <option value="">সকল বিভাগ</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ (string)$divisionId === (string)$div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="district_id" id="district_select" class="form-select bg-light" style="font-size: 0.88rem;">
                        <option value="">সকল জেলা</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" data-division-id="{{ $dist->division_id }}" {{ (string)$districtId === (string)$dist->id ? 'selected' : '' }}>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <select name="zone_id" id="zone_select" class="form-select bg-light" style="font-size: 0.88rem;">
                        <option value="">সকল এলাকা/উপজেলা</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}" data-district-id="{{ $z->district_id }}" {{ (string)$zoneId === (string)$z->id ? 'selected' : '' }}>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($divisionId) || !empty($districtId) || !empty($zoneId))
                        <a href="{{ route('admin.customer.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Body -->
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light table-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কাস্টমার তথ্য</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">এলাকা / ঠিকানা</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মোট বিক্রয়</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মোট পরিশোধ</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">বর্তমান বকেয়া</th>
                        <th class="text-center text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 120px;">স্ট্যাটাস</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        @php
                            $totalSales = (float) ($customer->total_sales ?? 0);
                            $totalPaid = (float) ($customer->total_paid ?? 0);
                            $totalDue = (float) ($customer->total_due ?? 0);
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold text-muted" style="font-size: 0.85rem;">
                                {{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('admin.customer.show', $customer) }}" class="text-decoration-none fw-bold text-dark hover-primary d-inline-block" style="font-size: 0.92rem;">
                                        {{ $customer->name }}
                                    </a>
                                    <div class="small text-muted mt-0.5 d-flex align-items-center gap-2">
                                        <span><i class="fa-solid fa-phone text-secondary me-1" style="font-size: 0.72rem;"></i>{{ $customer->mobile }}</span>
                                        @if($customer->email)
                                            <span class="text-muted">• {{ $customer->email }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem;">
                                        {{ $customer->location_text }}
                                    </div>
                                    @if($customer->address && $customer->address !== $customer->location_text)
                                        <small class="text-muted d-block" style="font-size: 0.74rem;">{{ $customer->address }}</small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-dark" style="font-size: 0.88rem;">
                                ৳{{ number_format($totalSales, 2) }}
                            </td>
                            <td class="text-end fw-semibold text-success" style="font-size: 0.88rem;">
                                ৳{{ number_format($totalPaid, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $totalDue > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 0.92rem;">
                                ৳{{ number_format($totalDue, 2) }}
                            </td>
                            <td class="text-center">
                                @if($totalDue > 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-clock me-1"></i>বকেয়া আছে
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.72rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>পরিশোধিত
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.customer.show', $customer) }}"
                                       class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary shadow-xs"
                                       title="লেজার ও বিস্তারিত হিসাব">
                                        <i class="fa-solid fa-book-open me-1"></i>লেজার
                                    </a>
                                    <form action="{{ route('admin.customer.destroy', $customer) }}"
                                          method="POST"
                                          onsubmit="return confirm('আপনি কি নিশ্চিত যে কাস্টমার {{ $customer->name }} মুছে ফেলতে চান?');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-light border rounded-circle text-danger p-0 d-inline-flex align-items-center justify-content-center"
                                                style="width: 28px; height: 28px;"
                                                title="মুছে ফেলুন">
                                            <i class="fa-regular fa-trash-can" style="font-size: 0.78rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa-solid fa-users-slash text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted fw-bold">কোন কাস্টমার পাওয়া যায়নি</h6>
                                    <p class="text-muted small mb-3">আপনার সার্চ ফিল্টার পরিবর্তন করুন অথবা নতুন বিক্রয় চালান তৈরি করুন</p>
                                    <a href="{{ route('admin.sales.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন বিক্রয় চালান তৈরি করুন
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    মোট {{ $customers->total() }} টির মধ্যে {{ $customers->firstItem() }} থেকে {{ $customers->lastItem() }} দেখানো হচ্ছে
                </div>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const divisionSelect = document.getElementById('division_select');
        const districtSelect = document.getElementById('district_select');
        const zoneSelect = document.getElementById('zone_select');

        function filterDistricts() {
            if (!divisionSelect || !districtSelect) return;
            const divId = divisionSelect.value;
            const currentDistrictVal = districtSelect.value;
            let currentStillVisible = false;

            Array.from(districtSelect.options).forEach((opt, idx) => {
                if (idx === 0) return;
                const optDivId = opt.getAttribute('data-division-id');
                if (!divId || optDivId === divId) {
                    opt.style.display = '';
                    if (opt.value === currentDistrictVal) currentStillVisible = true;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (!currentStillVisible && currentDistrictVal) {
                districtSelect.value = '';
            }
            filterZones();
        }

        function filterZones() {
            if (!districtSelect || !zoneSelect) return;
            const distId = districtSelect.value;
            const currentZoneVal = zoneSelect.value;
            let currentStillVisible = false;

            Array.from(zoneSelect.options).forEach((opt, idx) => {
                if (idx === 0) return;
                const optDistId = opt.getAttribute('data-district-id');
                if (!distId || optDistId === distId) {
                    opt.style.display = '';
                    if (opt.value === currentZoneVal) currentStillVisible = true;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (!currentStillVisible && currentZoneVal) {
                zoneSelect.value = '';
            }
        }

        if (divisionSelect) {
            divisionSelect.addEventListener('change', filterDistricts);
            filterDistricts();
        }

        if (districtSelect) {
            districtSelect.addEventListener('change', filterZones);
            filterZones();
        }
    });
</script>
@endpush
