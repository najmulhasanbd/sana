@extends('admin.layouts.master')

@section('title', 'কাস্টমার বকেয়া শিট (Customer Due Sheet)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action (Screen only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>কাস্টমার বকেয়া শিট (Customer Due Sheet)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">হিসাব ও রিপোর্ট</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বকেয়া শিট</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-users"></i>
                <span>সকল কাস্টমার</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট বকেয়া শিট</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards (Screen only) -->
    <div class="row g-3 mb-4 d-print-none">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট বকেয়া কাস্টমার</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_customers']) }} জন</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fa-solid fa-money-bill-wave fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট বিক্রয় মূল্য</small>
                        <h4 class="fw-bold mb-0 text-dark">৳{{ number_format($stats['total_sales'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট আদায় / পরিশোধ</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_paid'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট বকেয়া পাওনা</small>
                        <h4 class="fw-bold mb-0 text-danger">৳{{ number_format($stats['total_due'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Header (Screen only) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 d-print-none">
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.customer-due.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="filter_applied" value="1">
                <!-- Search by Name or Mobile -->
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="কাস্টমারের নাম বা মোবাইল..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <!-- Division Filter -->
                <div class="col-6 col-md-2">
                    <select name="division_id" id="divisionFilter" class="select2 form-select" data-placeholder="সকল বিভাগ">
                        <option value="">সকল বিভাগ</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ (string)$divisionId === (string)$div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- District Filter -->
                <div class="col-6 col-md-2">
                    <select name="district_id" id="districtFilter" class="select2 form-select" data-placeholder="সকল জেলা">
                        <option value="">সকল জেলা</option>
                        @foreach($districts as $dist)
                            @if(empty($divisionId) || (string)$divisionId === (string)$dist->division_id)
                                <option value="{{ $dist->id }}" data-division-id="{{ $dist->division_id }}" {{ (string)$districtId === (string)$dist->id ? 'selected' : '' }}>
                                    {{ $dist->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Zone Filter -->
                <div class="col-6 col-md-2">
                    <select name="zone_id" id="zoneFilter" class="select2 form-select" data-placeholder="সকল জোন / থানা">
                        <option value="">সকল জোন / থানা</option>
                        @foreach($zones as $zn)
                            @php
                                $znDistrict = $districts->firstWhere('id', $zn->district_id);
                                $matchesDivision = empty($divisionId) || ($znDistrict && (string)$znDistrict->division_id === (string)$divisionId);
                                $matchesDistrict = empty($districtId) || (string)$districtId === (string)$zn->district_id;
                            @endphp
                            @if($matchesDivision && $matchesDistrict)
                                <option value="{{ $zn->id }}" data-district-id="{{ $zn->district_id }}" {{ (string)$zoneId === (string)$zn->id ? 'selected' : '' }}>
                                    {{ $zn->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Action & Reset -->
                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($divisionId) || !empty($districtId) || !empty($zoneId) || !$groupByArea || !$dueOnly)
                        <a href="{{ route('admin.customer-due.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

                <!-- Options Checkboxes -->
                <div class="col-12 d-flex flex-wrap align-items-center gap-4 pt-2">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="group_by_area" id="groupByAreaCheck" value="1" {{ $groupByArea ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label fw-semibold text-dark small" for="groupByAreaCheck">
                            <i class="fa-solid fa-layer-group text-primary me-1"></i>এলাকা ভাগ ভাগ করে আলাদা সাজান (Group by Area)
                        </label>
                    </div>

                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="due_only" id="dueOnlyCheck" value="1" {{ $dueOnly ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label fw-semibold text-danger small" for="dueOnlyCheck">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>শুধু যাদের বকেয়া আছে তাদের দেখান
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== SCREEN VIEW (non-print) ===== --}}
    <div class="d-print-none">
        @if($customers->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center">
                <div class="py-4">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-circle-check fs-3 text-success"></i>
                    </div>
                    <h5 class="fw-semibold text-secondary mb-1">কোনো বকেয়া কাস্টমার পাওয়া যায়নি</h5>
                    <p class="text-muted small">নির্বাচিত এলাকা বা সার্চ ক্রাইটেরিয়ায় কোনো বকেয়া নেই অথবা ফিল্টার পরিবর্তন করে দেখুন।</p>
                </div>
            </div>
        @elseif($groupByArea && $groupedCustomers)
            {{-- Grouped by Area on Screen --}}
            @foreach($groupedCustomers as $areaName => $areaCustomers)
                @php
                    $areaTotalSales = $areaCustomers->sum('total_sales');
                    $areaTotalPaid = $areaCustomers->sum('total_paid');
                    $areaTotalDue = $areaCustomers->sum('total_due');
                @endphp
                <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                    <div class="card-header bg-light border-bottom p-3 p-md-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white" 
                                 style="width: 34px; height: 34px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                                <i class="fa-solid fa-location-dot" style="font-size: 0.85rem;"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $areaName }}</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">কাস্টমার: {{ $areaCustomers->count() }} জন</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted small">মোট বিক্রয়: <strong>৳{{ number_format($areaTotalSales, 2) }}</strong></span>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold" style="font-size: 0.85rem;">
                                এলাকা বকেয়া: ৳{{ number_format($areaTotalDue, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="bg-white">
                                <tr>
                                    <th class="ps-4 py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 50px;">#</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">কাস্টমারের নাম</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">মোবাইল নম্বর</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">নির্দিষ্ট ঠিকানা</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">সর্বমোট ক্রয়</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">মোট আদায়</th>
                                    <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">বকেয়া টাকা</th>
                                    <th class="pe-4 py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 120px;">অ্যাকশন</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($areaCustomers as $idx => $cust)
                                    <tr>
                                        <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                        <td>
                                            <a href="{{ route('admin.customer.show', $cust) }}" class="fw-bold text-dark text-decoration-none" title="লেজার দেখুন">
                                                {{ $cust->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                                <i class="fa-solid fa-phone me-1" style="font-size: 0.65rem;"></i>{{ $cust->mobile }}
                                            </span>
                                        </td>
                                        <td><span class="text-muted small">{{ $cust->address ?: '—' }}</span></td>
                                        <td class="text-end fw-semibold text-dark">৳{{ number_format($cust->total_sales ?? 0, 2) }}</td>
                                        <td class="text-end fw-semibold text-success">৳{{ number_format($cust->total_paid ?? 0, 2) }}</td>
                                        <td class="text-end fw-bold text-danger fs-6">৳{{ number_format($cust->total_due ?? 0, 2) }}</td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('admin.customer.show', $cust) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary fw-medium" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-receipt me-1"></i>লেজার
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light table-light border-top">
                                <tr>
                                    <td colspan="4" class="ps-4 fw-bold text-dark">এই এলাকার সর্বমোট (Subtotal):</td>
                                    <td class="text-end fw-bold text-dark">৳{{ number_format($areaTotalSales, 2) }}</td>
                                    <td class="text-end fw-bold text-success">৳{{ number_format($areaTotalPaid, 2) }}</td>
                                    <td class="text-end fw-bold text-danger">৳{{ number_format($areaTotalDue, 2) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            {{-- Flat Table on Screen --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light table-light">
                            <tr>
                                <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 50px;">#</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কাস্টমারের নাম</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মোবাইল নম্বর</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">এলাকা / জোন</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">সর্বমোট ক্রয়</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মোট পরিশোধ</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">বকেয়া টাকা</th>
                                <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 120px;">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $idx => $cust)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.customer.show', $cust) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $cust->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1">
                                            <i class="fa-solid fa-phone me-1" style="font-size: 0.65rem;"></i>{{ $cust->mobile }}
                                        </span>
                                    </td>
                                    <td><span class="text-muted small">{{ $cust->location_text }}</span></td>
                                    <td class="text-end fw-semibold text-dark">৳{{ number_format($cust->total_sales ?? 0, 2) }}</td>
                                    <td class="text-end fw-semibold text-success">৳{{ number_format($cust->total_paid ?? 0, 2) }}</td>
                                    <td class="text-end fw-bold text-danger fs-6">৳{{ number_format($cust->total_due ?? 0, 2) }}</td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.customer.show', $cust) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary fw-medium" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-receipt me-1"></i>লেজার
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light table-light border-top">
                            <tr>
                                <td colspan="4" class="ps-4 fw-bold text-dark fs-6">সর্বমোট সমষ্টি (Grand Total):</td>
                                <td class="text-end fw-bold text-dark fs-6">৳{{ number_format($stats['total_sales'], 2) }}</td>
                                <td class="text-end fw-bold text-success fs-6">৳{{ number_format($stats['total_paid'], 2) }}</td>
                                <td class="text-end fw-bold text-danger fs-5">৳{{ number_format($stats['total_due'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- ===== PRINT-ONLY DUE SHEET (A4 High Density Landscape/Portrait) ===== --}}
    <div id="printableDueSheet" style="display: none;">
        {{-- Company Header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom: 3px solid #0284c7; padding-bottom: 12px; margin-bottom: 14px;">
            <div style="display:flex; align-items:flex-start; gap:10px;">
                <div style="width:48px; height:48px; background:linear-gradient(135deg,#0284c7 0%,#0d9488 100%); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="28" height="28" fill="white">
                        <path d="M60 32c-6-8-16-14-28-14C18 18 8 26 4 32c4 6 14 14 28 14 12 0 22-6 28-14zM32 42a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"/>
                        <circle cx="35" cy="30" r="3" fill="#0d9488"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:20px; font-weight:800; color:#0284c7; line-height:1.1;">{{ config('app.name', 'সানা ফিশারিজ') }}</div>
                    <div style="font-size:10.5px; color:#0d9488; font-weight:600; margin-top:2px;">মাছের পোনা ও হ্যাচারি সরবরাহকারী • কাস্টমার বকেয়া কালেকশন শিট</div>
                    <div style="font-size:9.5px; color:#666;">ময়মনসিংহ, বাংলাদেশ</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="display:inline-block; font-size:12px; font-weight:700; color:#fff; background:#dc2626; padding:4px 14px; border-radius:5px; letter-spacing:0.5px;">
                    বকেয়া আদায়ের শিট
                </div>
                <div style="font-size:10.5px; margin-top:5px; color:#222;"><strong>তারিখ:</strong> {{ now()->format('d M, Y') }}</div>
                <div style="font-size:9.5px; color:#666;">মুদ্রণের সময়: {{ now()->format('h:i A') }}</div>
            </div>
        </div>

        {{-- Filter Notice & Summary Strip --}}
        <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:8px 12px; margin-bottom:14px; font-size:11px;">
            <div>
                <strong>ফিল্টার এলাকা:</strong> 
                {{ collect([
                    $zones->firstWhere('id', $zoneId)?->name,
                    $districts->firstWhere('id', $districtId)?->name,
                    $divisions->firstWhere('id', $divisionId)?->name,
                ])->filter()->implode(', ') ?: 'সার্বিক সকল এলাকা' }}
                @if($search) (সার্চ: "{{ $search }}") @endif
            </div>
            <div style="display:flex; gap:16px;">
                <span>মোট কাস্টমার: <strong>{{ number_format($stats['total_customers']) }} জন</strong></span>
                <span>মোট বিক্রয়: <strong>৳{{ number_format($stats['total_sales'], 2) }}</strong></span>
                <span>মোট বকেয়া: <strong style="color:#dc2626; font-size:12px;">৳{{ number_format($stats['total_due'], 2) }}</strong></span>
            </div>
        </div>

        @if($groupByArea && $groupedCustomers)
            @php $globalSerial = 1; @endphp
            @foreach($groupedCustomers as $areaName => $areaCustomers)
                @php
                    $areaTotalSales = $areaCustomers->sum('total_sales');
                    $areaTotalPaid = $areaCustomers->sum('total_paid');
                    $areaTotalDue = $areaCustomers->sum('total_due');
                @endphp
                <div style="margin-bottom:14px; page-break-inside:avoid;">
                    <div style="background:#0284c7; color:#fff; padding:5px 10px; font-size:11px; font-weight:700; border-radius:4px 4px 0 0; display:flex; justify-content:space-between;">
                        <span>📍 এলাকা: {{ $areaName }} ({{ $areaCustomers->count() }} জন)</span>
                        <span>এলাকা বকেয়া: ৳{{ number_format($areaTotalDue, 2) }}</span>
                    </div>
                    <table style="width:100%; border-collapse:collapse; font-size:10.5px; border:1px solid #cbd5e1;">
                        <thead>
                            <tr style="background:#f1f5f9; border-bottom:1px solid #cbd5e1;">
                                <th style="padding:5px; text-align:left; width:28px; border-right:1px solid #cbd5e1;">#</th>
                                <th style="padding:5px 8px; text-align:left; border-right:1px solid #cbd5e1;">কাস্টমারের নাম</th>
                                <th style="padding:5px 8px; text-align:left; width:95px; border-right:1px solid #cbd5e1;">মোবাইল</th>
                                <th style="padding:5px 8px; text-align:left; border-right:1px solid #cbd5e1;">ঠিকানা</th>
                                <th style="padding:5px 8px; text-align:right; width:80px; border-right:1px solid #cbd5e1;">মোট ক্রয় (৳)</th>
                                <th style="padding:5px 8px; text-align:right; width:75px; border-right:1px solid #cbd5e1;">পরিশোধ (৳)</th>
                                <th style="padding:5px 8px; text-align:right; width:90px; border-right:1px solid #cbd5e1; background:#fee2e2; color:#dc2626;">বকেয়া (৳)</th>
                                <th style="padding:5px 8px; text-align:center; width:90px; border-right:1px solid #cbd5e1;">আদায় নোট</th>
                                <th style="padding:5px 8px; text-align:center; width:80px;">স্বাক্ষর</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($areaCustomers as $cIdx => $cust)
                                <tr style="background:{{ $cIdx % 2 === 0 ? '#fff' : '#f8fafc' }}; border-bottom:1px solid #e2e8f0;">
                                    <td style="padding:4px 5px; border-right:1px solid #e2e8f0;">{{ $globalSerial++ }}</td>
                                    <td style="padding:4px 8px; font-weight:700; color:#111; border-right:1px solid #e2e8f0;">{{ $cust->name }}</td>
                                    <td style="padding:4px 8px; border-right:1px solid #e2e8f0;">{{ $cust->mobile }}</td>
                                    <td style="padding:4px 8px; color:#555; border-right:1px solid #e2e8f0;">{{ $cust->address ?: '—' }}</td>
                                    <td style="padding:4px 8px; text-align:right; border-right:1px solid #e2e8f0;">{{ number_format($cust->total_sales ?? 0, 2) }}</td>
                                    <td style="padding:4px 8px; text-align:right; border-right:1px solid #e2e8f0; color:#16a34a;">{{ number_format($cust->total_paid ?? 0, 2) }}</td>
                                    <td style="padding:4px 8px; text-align:right; font-weight:800; color:#dc2626; border-right:1px solid #e2e8f0; background:#fef2f2;">৳{{ number_format($cust->total_due ?? 0, 2) }}</td>
                                    <td style="padding:4px 8px; border-right:1px solid #e2e8f0;"></td>
                                    <td style="padding:4px 8px;"></td>
                                </tr>
                            @endforeach
                            <tr style="background:#f1f5f9; font-weight:700; border-top:1.5px solid #cbd5e1;">
                                <td colspan="4" style="padding:5px 8px; text-align:right;">{{ $areaName }} মোট:</td>
                                <td style="padding:5px 8px; text-align:right;">৳{{ number_format($areaTotalSales, 2) }}</td>
                                <td style="padding:5px 8px; text-align:right; color:#16a34a;">৳{{ number_format($areaTotalPaid, 2) }}</td>
                                <td style="padding:5px 8px; text-align:right; color:#dc2626; background:#fee2e2;">৳{{ number_format($areaTotalDue, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        @else
            <table style="width:100%; border-collapse:collapse; font-size:10.5px; border:1px solid #cbd5e1; margin-bottom:14px;">
                <thead>
                    <tr style="background:#0284c7; color:#fff;">
                        <th style="padding:5px; text-align:left; width:28px;">#</th>
                        <th style="padding:5px 8px; text-align:left;">কাস্টমারের নাম</th>
                        <th style="padding:5px 8px; text-align:left; width:95px;">মোবাইল</th>
                        <th style="padding:5px 8px; text-align:left;">এলাকা / ঠিকানা</th>
                        <th style="padding:5px 8px; text-align:right; width:80px;">মোট ক্রয় (৳)</th>
                        <th style="padding:5px 8px; text-align:right; width:75px;">পরিশোধ (৳)</th>
                        <th style="padding:5px 8px; text-align:right; width:90px; background:#dc2626;">বকেয়া (৳)</th>
                        <th style="padding:5px 8px; text-align:center; width:90px;">আদায় নোট</th>
                        <th style="padding:5px 8px; text-align:center; width:80px;">স্বাক্ষর</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $cIdx => $cust)
                        <tr style="background:{{ $cIdx % 2 === 0 ? '#fff' : '#f8fafc' }}; border-bottom:1px solid #e2e8f0;">
                            <td style="padding:4px 5px; border-right:1px solid #e2e8f0;">{{ $cIdx + 1 }}</td>
                            <td style="padding:4px 8px; font-weight:700; color:#111; border-right:1px solid #e2e8f0;">{{ $cust->name }}</td>
                            <td style="padding:4px 8px; border-right:1px solid #e2e8f0;">{{ $cust->mobile }}</td>
                            <td style="padding:4px 8px; color:#555; border-right:1px solid #e2e8f0;">{{ $cust->location_text }}</td>
                            <td style="padding:4px 8px; text-align:right; border-right:1px solid #e2e8f0;">{{ number_format($cust->total_sales ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; text-align:right; border-right:1px solid #e2e8f0; color:#16a34a;">{{ number_format($cust->total_paid ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; text-align:right; font-weight:800; color:#dc2626; border-right:1px solid #e2e8f0; background:#fef2f2;">৳{{ number_format($cust->total_due ?? 0, 2) }}</td>
                            <td style="padding:4px 8px; border-right:1px solid #e2e8f0;"></td>
                            <td style="padding:4px 8px;"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Grand Total Summary Box --}}
        <div style="display:flex; justify-content:flex-end; margin-bottom:20px; page-break-inside:avoid;">
            <table style="min-width:280px; border-collapse:collapse; font-size:11px; border:2px solid #0284c7; border-radius:6px; overflow:hidden;">
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <td style="padding:6px 12px; color:#555; font-weight:600;">মোট বকেয়া কাস্টমার:</td>
                    <td style="padding:6px 12px; text-align:right; font-weight:700;">{{ number_format($stats['total_customers']) }} জন</td>
                </tr>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <td style="padding:6px 12px; color:#555; font-weight:600;">সর্বমোট বিক্রয়:</td>
                    <td style="padding:6px 12px; text-align:right; font-weight:700;">৳{{ number_format($stats['total_sales'], 2) }}</td>
                </tr>
                <tr style="background:#fee2e2;">
                    <td style="padding:8px 12px; font-weight:800; color:#111; font-size:12px;">সর্বমোট বকেয়া পাওনা:</td>
                    <td style="padding:8px 12px; text-align:right; font-size:13px; font-weight:900; color:#dc2626;">৳{{ number_format($stats['total_due'], 2) }}</td>
                </tr>
            </table>
        </div>

        {{-- Signature Blocks --}}
        <div style="margin-top:40px; display:flex; justify-content:space-between; align-items:flex-end; page-break-inside:avoid;">
            <div style="text-align:center; font-size:10px; color:#555;">
                <div style="height:35px;"></div>
                <div style="border-top:1.5px solid #555; padding-top:4px; min-width:140px;">কালেক্টর / আদায়কারীর স্বাক্ষর</div>
            </div>
            <div style="text-align:center; font-size:10px; color:#555;">
                <div style="height:35px;"></div>
                <div style="border-top:1.5px solid #555; padding-top:4px; min-width:140px;">ম্যানেজারের স্বাক্ষর</div>
            </div>
            <div style="text-align:center; font-size:10px; color:#555;">
                <div style="height:35px;"></div>
                <div style="border-top:1.5px solid #555; padding-top:4px; min-width:140px;">অনুমোদনকারীর স্বাক্ষর</div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm 12mm 12mm 12mm;
    }
    html, body {
        background: #fff !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 11px !important;
        color: #111 !important;
    }
    .app-sidebar, .app-header, header, .toast-container, .d-print-none {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    .w-100.pb-4 {
        padding: 0 !important;
    }
    #printableDueSheet {
        display: block !important;
    }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const $division = $('#divisionFilter');
    const $district = $('#districtFilter');
    const $zone = $('#zoneFilter');

    // Preloaded location datasets from server
    const allDistricts = @json($districts->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'division_id' => $d->division_id]));
    const allZones = @json($zones->map(fn($z) => ['id' => $z->id, 'name' => $z->name, 'district_id' => $z->district_id]));

    function reinitSelect2($el, placeholder) {
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: placeholder,
            allowClear: true,
            dropdownParent: $(document.body)
        });
    }

    function updateDistricts(divId, selectedDistrictId) {
        $district.empty().append('<option value="">সকল জেলা</option>');

        const filtered = divId 
            ? allDistricts.filter(d => String(d.division_id) === String(divId))
            : allDistricts;

        filtered.forEach(function(d) {
            const isSel = selectedDistrictId && String(d.id) === String(selectedDistrictId);
            $district.append(new Option(d.name, d.id, isSel, isSel));
        });

        reinitSelect2($district, 'সকল জেলা');
    }

    function updateZones(distId, divId, selectedZoneId) {
        $zone.empty().append('<option value="">সকল জোন / থানা</option>');

        let filtered = [];
        if (distId) {
            filtered = allZones.filter(z => String(z.district_id) === String(distId));
        } else if (divId) {
            const divDistrictIds = allDistricts
                .filter(d => String(d.division_id) === String(divId))
                .map(d => d.id);
            filtered = allZones.filter(z => divDistrictIds.includes(z.district_id));
        } else {
            filtered = allZones;
        }

        filtered.forEach(function(z) {
            const isSel = selectedZoneId && String(z.id) === String(selectedZoneId);
            $zone.append(new Option(z.name, z.id, isSel, isSel));
        });

        reinitSelect2($zone, 'সকল জোন / থানা');
    }

    // When Division changes: filter districts and zones accordingly
    $division.on('change', function() {
        const divId = $(this).val();
        updateDistricts(divId, '');
        updateZones('', divId, '');
    });

    // When District changes: filter zones accordingly
    $district.on('change', function() {
        const distId = $(this).val();
        const divId = $division.val();
        updateZones(distId, divId, '');
    });

    // On page load: sync options if division is pre-selected
    const currentDivId = $division.val();
    const currentDistId = '{{ $districtId ?? "" }}';
    const currentZoneId = '{{ $zoneId ?? "" }}';

    if (currentDivId) {
        updateDistricts(currentDivId, currentDistId);
        updateZones(currentDistId, currentDivId, currentZoneId);
    }
});
</script>
@endpush
