@extends('admin.layouts.master')

@section('title', 'বিক্রয় চালান - ' . $sale->invoice_no)

@section('content')
<div class="w-100 pb-4">
    <!-- Top Action Bar (screen only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-receipt text-primary"></i>
                <span>বিক্রয় চালান বিবরণী</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.sales.index') }}" class="text-decoration-none text-muted">বিক্রয় তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $sale->invoice_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
            @if($sale->due_amount > 0)
                <button type="button" 
                        class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addPaymentModal"
                        style="font-size: 0.85rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>বকেয়া আদায় / পরিশোধ</span>
                </button>
            @endif
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট চালান</span>
            </button>
        </div>
    </div>

    {{-- ===== SCREEN VIEW (non-print) ===== --}}
    <div class="d-print-none">
        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden p-4 p-md-5 mb-4">
            {{-- Invoice Header --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-4 pb-4 border-bottom">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" 
                             style="width: 42px; height: 42px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                            <i class="fa-solid fa-fish fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0">{{ config('app.name', 'সানা ফিশারিজ') }}</h4>
                            <small class="text-muted" style="font-size: 0.78rem;">মাছের ছানা ও আধুনিক পোনা বাজার</small>
                        </div>
                    </div>
                    <p class="text-muted small mb-0"><i class="fa-solid fa-location-dot me-1 text-danger"></i>ময়মনসিংহ, বাংলাদেশ</p>
                </div>
                <div class="text-md-end">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold fs-6 mb-2 d-block">
                        চালান নং: {{ $sale->invoice_no }}
                    </span>
                    <div class="text-muted small"><strong>তারিখ:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d M, Y') : '—' }}</div>
                    <div class="text-muted small"><strong>তৈরির সময়:</strong> {{ $sale->created_at ? $sale->created_at->format('h:i A') : '—' }}</div>
                </div>
            </div>

            {{-- Customer & Status --}}
            <div class="row g-4 py-4 border-bottom">
                <div class="col-12 col-md-6">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">ক্রেতা / কাস্টমার তথ্য</small>
                    <h5 class="fw-bold text-dark mb-1">{{ $sale->customer?->name ?? '—' }}</h5>
                    <p class="text-secondary small mb-1"><i class="fa-solid fa-phone text-muted me-1.5"></i>{{ $sale->customer?->mobile ?? '—' }}</p>
                    @if($sale->customer?->email)
                        <p class="text-secondary small mb-1"><i class="fa-solid fa-envelope text-muted me-1.5"></i>{{ $sale->customer->email }}</p>
                    @endif
                    <p class="text-muted small mb-0">
                        <i class="fa-solid fa-location-dot text-muted me-1.5"></i>
                        {{ collect([$sale->customer?->zone?->name, $sale->customer?->district?->name, $sale->customer?->division?->name])->filter()->implode(', ') ?: ($sale->customer?->address ?? 'ঠিকানা উল্লেখ নেই') }}
                    </p>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">পরিশোধ অবস্থা</small>
                    <div class="mb-2">
                        @if($sale->payment_status === 'paid')
                            <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1.5 fw-bold fs-6">
                                <i class="fa-solid fa-circle-check me-1"></i>সম্পূর্ণ পরিশোধিত (Paid)
                            </span>
                        @elseif($sale->payment_status === 'partial')
                            <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1.5 fw-bold fs-6">
                                <i class="fa-solid fa-clock-rotate-left me-1"></i>আংশিক বকেয়া (Partial)
                            </span>
                        @else
                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1.5 fw-bold fs-6">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>সম্পূর্ণ বকেয়া (Due)
                            </span>
                        @endif
                    </div>
                    @if($sale->due_amount > 0)
                        <p class="text-danger fw-bold mb-0">অবশিষ্ট বকেয়া: ৳{{ number_format($sale->due_amount, 2) }}</p>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="py-4 border-bottom">
                <h6 class="fw-bold text-dark mb-3">বিক্রয়কৃত মাছ ও পণ্যের বিবরণ</h6>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light table-light">
                            <tr>
                                <th class="py-2.5 ps-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পণ্যের নাম</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem;">পরিমাণ</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem;">একক</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">একক দর (৳)</th>
                                <th class="py-2.5 pe-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মোট মূল্য (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                                <tr>
                                    <td class="ps-3 text-muted fw-medium">{{ $index + 1 }}</td>
                                    <td><span class="fw-bold text-dark">{{ $item->product?->name ?? '—' }}</span></td>
                                    <td class="text-center fw-semibold text-secondary">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $item->unit }}</span></td>
                                    <td class="text-end fw-semibold text-secondary">৳{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="pe-3 text-end fw-bold text-dark">৳{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Financial Summary --}}
            <div class="row g-4 py-4 border-bottom">
                <div class="col-12 col-md-6">
                    @if($sale->note)
                        <div class="p-3 rounded-3 bg-light border">
                            <small class="text-muted fw-bold d-block mb-1">মন্তব্য:</small>
                            <p class="text-secondary small mb-0">{{ $sale->note }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-semibold">সর্বমোট মূল্য:</span>
                            <span class="fw-bold text-dark fs-6">৳{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fw-semibold">মোট আদায় / পরিশোধ:</span>
                            <span class="fw-bold text-success fs-6">৳{{ number_format($sale->paid_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <span class="fw-bold text-dark">অবশিষ্ট বকেয়া:</span>
                            <span class="fw-bold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-success' }} fs-5">
                                ৳{{ number_format($sale->due_amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            <div class="pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                        <span>পরিশোধের ইতিহাস (Payment History)</span>
                    </h6>
                    @if($sale->due_amount > 0)
                        <button type="button" 
                                class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold" 
                                data-bs-toggle="modal" 
                                data-bs-target="#addPaymentModal">
                            <i class="fa-solid fa-plus me-1"></i>বকেয়া জমা
                        </button>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-sm">
                        <thead class="bg-light table-light">
                            <tr>
                                <th class="ps-3 py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 50px;">#</th>
                                <th class="py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">পরিশোধের তারিখ</th>
                                <th class="py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">পরিশোধের মাধ্যম</th>
                                <th class="py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">ট্রানজেকশন আইডি</th>
                                <th class="py-2 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">জমা টাকা</th>
                                <th class="pe-3 py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">নোট</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sale->payments as $pIdx => $payment)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $pIdx + 1 }}</td>
                                    <td class="text-dark fw-medium">{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : '—' }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-light border text-dark px-2.5 py-1">
                                            <i class="fa-solid fa-credit-card me-1 text-primary"></i>{{ $payment->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $payment->transaction_id ?? '—' }}</td>
                                    <td class="text-end fw-bold text-success">৳{{ number_format($payment->amount, 2) }}</td>
                                    <td class="pe-3 text-muted small">{{ $payment->note ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">এখনও কোনো পরিশোধ রেকর্ড নেই।</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== PRINT-ONLY INVOICE (A4 professional layout) ===== --}}
    <div id="printableInvoice" style="display: none;">
        {{-- Company Header --}}
        <div style="display:flex; justify-content:space-between; align-items:flex-start; border-bottom: 3px solid #0284c7; padding-bottom: 14px; margin-bottom: 16px;">
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:52px; height:52px; background:linear-gradient(135deg,#0284c7 0%,#0d9488 100%); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="32" height="32" fill="white">
                        <path d="M60 32c-6-8-16-14-28-14C18 18 8 26 4 32c4 6 14 14 28 14 12 0 22-6 28-14zM32 42a10 10 0 1 1 0-20 10 10 0 0 1 0 20z"/>
                        <circle cx="35" cy="30" r="3" fill="#0d9488"/>
                        <path d="M4 32 C0 28 0 36 4 32z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:21px; font-weight:800; color:#0284c7; line-height:1.1; letter-spacing:-0.5px;">{{ config('app.name', 'সানা ফিশারিজ') }}</div>
                    <div style="font-size:11px; color:#0d9488; font-weight:600; margin-top:2px;">🐟 মাছের পোনা ও হ্যাচারি সরবরাহকারী</div>
                    <div style="font-size:10px; color:#777; margin-top:2px;">📍 ময়মনসিংহ, বাংলাদেশ &nbsp;|&nbsp; 🌐 মৎস্য চাষের বিশ্বস্ত অংশীদার</div>
                </div>
            </div>
            <div style="text-align:right;">
                <div style="display:inline-block; font-size:12px; font-weight:700; color:#fff; background:linear-gradient(135deg,#0284c7,#0d9488); padding:5px 16px; border-radius:6px; letter-spacing:0.8px;">
                    🐠 বিক্রয় চালান
                </div>
                <div style="font-size:12px; margin-top:7px; color:#111;"><strong>চালান নং:</strong> <span style="color:#0284c7; font-weight:700;">{{ $sale->invoice_no }}</span></div>
                <div style="font-size:10.5px; color:#555;"><strong>তারিখ:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d M, Y') : '—' }}</div>
                <div style="font-size:10.5px; color:#555;"><strong>মুদ্রণ সময়:</strong> {{ $sale->created_at ? $sale->created_at->format('h:i A') : '—' }}</div>
            </div>
        </div>

        {{-- Customer & Status --}}
        <div style="display:flex; justify-content:space-between; gap:20px; margin-bottom:16px;">
            <div style="flex:1; background:#f0f9ff; border:1px solid #bae6fd; border-radius:7px; padding:10px 14px;">
                <div style="font-size:9.5px; text-transform:uppercase; font-weight:700; color:#666; letter-spacing:0.8px; margin-bottom:5px;">ক্রেতা / কাস্টমার তথ্য</div>
                <div style="font-size:14px; font-weight:700; color:#111; margin-bottom:3px;">{{ $sale->customer?->name ?? '—' }}</div>
                <div style="font-size:11px; color:#444;">📞 {{ $sale->customer?->mobile ?? '—' }}</div>
                @if($sale->customer?->email)
                    <div style="font-size:11px; color:#444;">✉ {{ $sale->customer->email }}</div>
                @endif
                @php
                    $sLoc = collect([$sale->customer?->zone?->name, $sale->customer?->district?->name, $sale->customer?->division?->name])->filter()->implode(', ') ?: ($sale->customer?->address ?? '');
                @endphp
                @if($sLoc)
                    <div style="font-size:11px; color:#555; margin-top:1px;">📍 {{ $sLoc }}</div>
                @endif
            </div>
            <div style="text-align:right; min-width:190px;">
                <div style="font-size:9.5px; text-transform:uppercase; font-weight:700; color:#666; letter-spacing:0.8px; margin-bottom:7px;">পরিশোধ অবস্থা</div>
                @if($sale->payment_status === 'paid')
                    <div style="display:inline-block; background:#dcfce7; color:#16a34a; border:1.5px solid #86efac; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:700;">✓ সম্পূর্ণ পরিশোধিত</div>
                @elseif($sale->payment_status === 'partial')
                    <div style="display:inline-block; background:#fef9c3; color:#ca8a04; border:1.5px solid #fde047; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:700;">⚡ আংশিক বকেয়া</div>
                @else
                    <div style="display:inline-block; background:#fee2e2; color:#dc2626; border:1.5px solid #fca5a5; border-radius:20px; padding:4px 12px; font-size:11px; font-weight:700;">✕ সম্পূর্ণ বকেয়া</div>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div style="margin-bottom:16px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#0284c7; letter-spacing:0.5px; margin-bottom:7px; border-left:3.5px solid #0284c7; padding-left:8px;">বিক্রয়কৃত মাছ ও পণ্যের বিবরণ</div>
            <table style="width:100%; border-collapse:collapse; font-size:11.5px;">
                <thead>
                    <tr style="background:#0284c7; color:#fff;">
                        <th style="padding:7px 8px; text-align:left; width:35px;">#</th>
                        <th style="padding:7px 8px; text-align:left;">পণ্যের নাম</th>
                        <th style="padding:7px 8px; text-align:center; width:95px;">পরিমাণ</th>
                        <th style="padding:7px 8px; text-align:center; width:65px;">একক</th>
                        <th style="padding:7px 8px; text-align:right; width:100px;">একক দর (৳)</th>
                        <th style="padding:7px 12px 7px 8px; text-align:right; width:110px;">মোট মূল্য (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $i => $item)
                        <tr style="background:{{ $i % 2 === 0 ? '#f8fafc' : '#fff' }}; border-bottom:1px solid #e2e8f0;">
                            <td style="padding:6px 8px; color:#666;">{{ $i + 1 }}</td>
                            <td style="padding:6px 8px; font-weight:600; color:#111;">{{ $item->product?->name ?? '—' }}</td>
                            <td style="padding:6px 8px; text-align:center; color:#444;">{{ number_format($item->quantity, 2) }}</td>
                            <td style="padding:6px 8px; text-align:center; color:#555;">{{ $item->unit }}</td>
                            <td style="padding:6px 8px; text-align:right; color:#444;">{{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding:6px 12px 6px 8px; text-align:right; font-weight:700; color:#111;">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Financial Summary (right aligned) --}}
        <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
            <table style="min-width:250px; border-collapse:collapse; font-size:11.5px; border:1.5px solid #e2e8f0; border-radius:7px; overflow:hidden;">
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <td style="padding:7px 14px; color:#555; font-weight:600;">সর্বমোট মূল্য:</td>
                    <td style="padding:7px 14px; text-align:right; font-weight:700; color:#111;">৳{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0;">
                    <td style="padding:7px 14px; color:#555; font-weight:600;">মোট আদায়:</td>
                    <td style="padding:7px 14px; text-align:right; font-weight:700; color:#16a34a;">৳{{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr style="background:{{ $sale->due_amount > 0 ? '#fee2e2' : '#dcfce7' }};">
                    <td style="padding:9px 14px; font-weight:700; color:#111;">অবশিষ্ট বকেয়া:</td>
                    <td style="padding:9px 14px; text-align:right; font-size:14px; font-weight:800; color:{{ $sale->due_amount > 0 ? '#dc2626' : '#16a34a' }};">৳{{ number_format($sale->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($sale->note)
        <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:8px 12px; margin-bottom:16px; font-size:10.5px; color:#555;">
            <strong style="color:#92400e;">মন্তব্য:</strong> {{ $sale->note }}
        </div>
        @endif

        {{-- Payment History --}}
        @if($sale->payments->isNotEmpty())
        <div style="margin-bottom:16px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; color:#334155; letter-spacing:0.5px; margin-bottom:7px; border-left:3.5px solid #334155; padding-left:8px;">আদায় ও পরিশোধের ইতিহাস</div>
            <table style="width:100%; border-collapse:collapse; font-size:11px;">
                <thead>
                    <tr style="background:#334155; color:#fff;">
                        <th style="padding:6px 8px; text-align:left; width:35px;">#</th>
                        <th style="padding:6px 8px; text-align:left;">তারিখ</th>
                        <th style="padding:6px 8px; text-align:left;">মাধ্যম</th>
                        <th style="padding:6px 8px; text-align:left;">ট্রানজেকশন আইডি</th>
                        <th style="padding:6px 12px 6px 8px; text-align:right; width:110px;">আদায়কৃত (৳)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->payments as $pIdx => $payment)
                        <tr style="background:{{ $pIdx % 2 === 0 ? '#f8fafc' : '#fff' }}; border-bottom:1px solid #e2e8f0;">
                            <td style="padding:5px 8px; color:#666;">{{ $pIdx + 1 }}</td>
                            <td style="padding:5px 8px; color:#333;">{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : '—' }}</td>
                            <td style="padding:5px 8px; color:#333;">{{ $payment->payment_method }}</td>
                            <td style="padding:5px 8px; color:#666;">{{ $payment->transaction_id ?? '—' }}</td>
                            <td style="padding:5px 12px 5px 8px; text-align:right; font-weight:700; color:#16a34a;">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Footer with 50px signature space --}}
        <div style="margin-top:28px; border-top:1px dashed #cbd5e1; padding-top:14px; display:flex; justify-content:space-between; align-items:flex-end;">
            <div style="font-size:9.5px; color:#888; line-height:1.8;">
                <div style="color:#0284c7; font-weight:700; font-size:10px; margin-bottom:3px;">🐟 সানা ফিশারিজ</div>
                <div>মৎস্য বিক্রয়ের বিশ্বস্ত চালান নথি</div>
                <div>🖨 কম্পিউটার প্রিন্টেড — স্বাক্ষরের প্রয়োজন নেই</div>
                <div>মুদ্রণের সময়: {{ now()->format('h:i A, d M Y') }}</div>
            </div>
            <div style="text-align:center; font-size:10.5px; color:#555;">
                <div style="height:50px;"></div>
                <div style="border-top:1.5px solid #555; padding-top:5px; min-width:160px;">অনুমোদনকারীর স্বাক্ষর</div>
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
                        <h6 class="modal-title fw-bold text-dark mb-0">বকেয়া টাকা আদায় / পরিশোধ</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">চালান: {{ $sale->invoice_no }}</small>
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
                                পরিশোধের তারিখ <span class="text-danger">*</span>
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
                                পরিশোধের মাধ্যম <span class="text-danger">*</span>
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
                                   placeholder="যেমন: TRX12345" 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="note" 
                                   class="form-control" 
                                   placeholder="যেমন: কিস্তি বা বকেয়া আদায়" 
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

<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 12mm 14mm 14mm 14mm;
    }
    html, body {
        background: #fff !important;
        font-family: 'Inter', sans-serif !important;
        font-size: 12px !important;
        color: #111 !important;
    }
    .app-sidebar, .app-header, header, .toast-container,
    .d-print-none, #addPaymentModal {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    .w-100.pb-4 {
        padding: 0 !important;
    }
    #printableInvoice {
        display: block !important;
    }
    table { page-break-inside: auto; }
    tr { page-break-inside: avoid; }
}
</style>
@endsection
