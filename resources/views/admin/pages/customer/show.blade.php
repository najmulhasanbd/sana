@extends('admin.layouts.master')

@section('title', $customer->name . ' - কাস্টমার লেজার ও হিসাব')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action (Screen only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-user-tag text-primary"></i>
                <span>{{ $customer->name }} - লেজার ও হিসাব বিবরণী</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}" class="text-decoration-none text-muted">কাস্টমার তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $customer->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customer.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>কাস্টমার তালিকা</span>
            </a>
            <a href="{{ route('admin.customer-due.index', ['search' => $customer->mobile]) }}" class="btn btn-outline-warning rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>বকেয়া শিটে দেখুন</span>
            </a>
            @if($stats['total_due'] > 0)
                <button type="button" 
                        class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                        onclick="openCollectDueModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->mobile }}', {{ $stats['total_due'] }})"
                        style="font-size: 0.85rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>বকেয়া টাকা জমা / আদায়</span>
                </button>
            @endif
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>লেজার প্রিন্ট করুন</span>
            </button>
        </div>
    </div>

    <!-- On-Screen Customer Card & Stats (Screen only) -->
    <div class="row g-3 mb-4 d-print-none">
        <!-- Customer Profile Card -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 54px; height: 54px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-user fs-4"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $customer->name }}</h5>
                        <span class="badge {{ $stats['total_due'] > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle' }} rounded-pill px-2.5 py-0.5 mt-1 fw-semibold" style="font-size: 0.72rem;">
                            {{ $stats['total_due'] > 0 ? 'বকেয়া আছে' : 'পরিশোধিত' }}
                        </span>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 0.86rem;">
                        <i class="fa-solid fa-phone text-primary" style="width: 18px;"></i>
                        <span class="text-dark fw-semibold">{{ $customer->mobile }}</span>
                    </div>
                    @if($customer->email)
                        <div class="d-flex align-items-center gap-2 mb-2 text-muted" style="font-size: 0.86rem;">
                            <i class="fa-regular fa-envelope text-primary" style="width: 18px;"></i>
                            <span>{{ $customer->email }}</span>
                        </div>
                    @endif
                    <div class="d-flex align-items-start gap-2 mb-2 text-muted" style="font-size: 0.86rem;">
                        <i class="fa-solid fa-location-dot text-primary mt-1" style="width: 18px;"></i>
                        <div>
                            <span class="text-dark fw-semibold d-block">{{ $customer->location_text }}</span>
                            @if($customer->address && $customer->address !== $customer->location_text)
                                <small class="text-muted">{{ $customer->address }}</small>
                            @endif
                        </div>
                    </div>
                    @if($customer->note)
                        <div class="d-flex align-items-start gap-2 mt-2 pt-2 border-top text-muted" style="font-size: 0.84rem;">
                            <i class="fa-regular fa-note-sticky text-secondary mt-1" style="width: 18px;"></i>
                            <span class="fst-italic">{{ $customer->note }}</span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-top">
                    @if($stats['total_due'] > 0)
                        <button type="button" 
                                class="btn btn-success w-100 rounded-pill py-2 fw-semibold mb-2 shadow-sm" 
                                onclick="openCollectDueModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->mobile }}', {{ $stats['total_due'] }})"
                                style="font-size: 0.85rem;">
                            <i class="fa-solid fa-hand-holding-dollar me-1"></i>বকেয়া টাকা জমা / আদায়
                        </button>
                    @endif
                    <a href="{{ route('admin.sales.create') }}" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-cart-plus me-1"></i>এই কাস্টমারকে নতুন বিক্রয়
                    </a>
                </div>
            </div>
        </div>

        <!-- Lifetime Balance Stats Cards -->
        <div class="col-12 col-lg-8">
            <div class="row g-3 h-100">
                <div class="col-6 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                                <i class="fa-solid fa-file-invoice fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট চালান সংখ্যা</small>
                                <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_invoices']) }} টি</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                                <i class="fa-solid fa-cart-shopping fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট ক্রয় মূল্য</small>
                                <h4 class="fw-bold mb-0 text-dark">৳{{ number_format($stats['total_purchased'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <i class="fa-solid fa-circle-check fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট পরিশোধ</small>
                                <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_paid'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                                 style="width: 48px; height: 48px; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);">
                                <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">বর্তমান মোট বকেয়া</small>
                                <h4 class="fw-bold mb-0 text-danger">৳{{ number_format($stats['total_due'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Screen Sales & Invoices List (Screen only) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4 d-print-none">
        <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-receipt text-primary"></i>
                    <span>বিক্রয় চালান তালিকা (Sales Invoices)</span>
                </h5>
                <small class="text-muted">এই কাস্টমারের সকল বিক্রয় চালান ও বকেয়া হিসাব</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light table-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">চালান নং ও তারিখ</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পোনা / মাছ আইটেম</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মোট মূল্য</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পরিশোধ</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">বকেয়া</th>
                        <th class="text-center text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 120px;">স্ট্যাটাস</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted" style="font-size: 0.85rem;">
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('admin.sales.show', $sale) }}" class="text-decoration-none fw-bold text-primary hover-underline" style="font-size: 0.9rem;">
                                        {{ $sale->invoice_no }}
                                    </a>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fa-regular fa-calendar me-1"></i>{{ $sale->sale_date->format('d M, Y') }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @forelse($sale->items as $item)
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">
                                            <i class="fa-solid fa-fish text-primary me-1"></i>{{ $item->product_name }} ({{ number_format($item->quantity) }} {{ $item->unit }})
                                        </span>
                                    @empty
                                        <span class="text-muted small">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-dark" style="font-size: 0.88rem;">
                                ৳{{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td class="text-end fw-semibold text-success" style="font-size: 0.88rem;">
                                ৳{{ number_format($sale->paid_amount, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $sale->due_amount > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 0.9rem;">
                                ৳{{ number_format($sale->due_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($sale->due_amount <= 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        পরিশোধিত
                                    </span>
                                @elseif($sale->paid_amount > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        আংশিক বকেয়া
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                        সম্পূর্ণ বকেয়া
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.sales.show', $sale) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary shadow-xs" title="চালান দেখুন ও প্রিন্ট করুন">
                                    <i class="fa-solid fa-eye me-1"></i>চালান
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa-solid fa-receipt text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted fw-bold">এখনও কোন বিক্রয় চালান তৈরি হয়নি</h6>
                                    <p class="text-muted small mb-3">এই কাস্টমারের নামে প্রথম চালান তৈরি করতে নিচের বাটনে ক্লিক করুন</p>
                                    <a href="{{ route('admin.sales.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-sm">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন বিক্রয় চালান
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Screen Payment Vouchers List (Screen only) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4 d-print-none">
        <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-success"></i>
                    <span>বকেয়া জমার ভাউচার ও রসিদসমূহ (Payment Receipts)</span>
                </h5>
                <small class="text-muted">এই কাস্টমারের সরাসরি মোট বাকি জমার সকল অফিশিয়াল ভাউচার</small>
            </div>
            @if($stats['total_due'] > 0)
                <button type="button" 
                        class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-semibold shadow-sm" 
                        onclick="openCollectDueModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->mobile }}', {{ $stats['total_due'] }})">
                    <i class="fa-solid fa-plus me-1"></i>নতুন বকেয়া জমা
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light table-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">ভাউচার নং ও তারিখ</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পূর্বের বকেয়া</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">আদায়কৃত টাকা</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">অবশিষ্ট বকেয়া</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মাধ্যম</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মন্তব্য</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted" style="font-size: 0.85rem;">{{ $loop->iteration }}</td>
                            <td>
                                <div>
                                    <a href="{{ route('admin.customer-payments.show', $p) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $p->voucher_no }}
                                    </a>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fa-regular fa-calendar me-1"></i>{{ $p->payment_date->format('d M, Y') }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-muted" style="font-size: 0.88rem;">
                                ৳{{ number_format($p->previous_due, 2) }}
                            </td>
                            <td class="text-end fw-bold text-success fs-6">
                                ৳{{ number_format($p->amount, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $p->remaining_due > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 0.88rem;">
                                ৳{{ number_format($p->remaining_due, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.72rem;">
                                    {{ $p->payment_method }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ $p->note ?: '—' }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.customer-payments.show', $p) }}" 
                                   class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary shadow-xs" 
                                   title="ভাউচার স্লিপ দেখুন ও প্রিন্ট করুন">
                                    <i class="fa-solid fa-print me-1"></i>ভাউচার
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">
                                এই কাস্টমারের কোনো সরাসরি বকেয়া জমার ভাউচার তৈরি হয়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- DEDICATED PRINTABLE A4 CUSTOMER STATEMENT (Print Only) -->
    <div class="d-none d-print-block" id="printableCustomerStatement">
        <style>
            @media print {
                @page {
                    size: A4 portrait;
                    margin: 12mm 10mm;
                }
                body {
                    background: #ffffff !important;
                    color: #000000 !important;
                    font-size: 11pt !important;
                }
                .app-header, .app-sidebar, .sidebar, nav, .d-print-none, .btn, footer {
                    display: none !important;
                }
                .statement-print-table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                }
                .statement-print-table th, .statement-print-table td {
                    border: 1px solid #1e293b !important;
                    padding: 5px 8px !important;
                    font-size: 9.5pt !important;
                }
                .statement-print-table th {
                    background-color: #f1f5f9 !important;
                    font-weight: bold !important;
                    color: #0f172a !important;
                }
            }
        </style>

        <!-- Company Header -->
        <div style="border-bottom: 2px solid #0f172a; padding-bottom: 8px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <h2 style="margin: 0; font-size: 20pt; font-weight: 800; color: #0284c7; letter-spacing: -0.5px;">সানা ফিশারিজ ও হ্যাচারি</h2>
                <div style="font-size: 9.5pt; color: #475569; margin-top: 2px;">
                    উন্নত জাতের পোনা উৎপাদক ও পাইকারি মাছ বিক্রেতা
                </div>
                <div style="font-size: 9pt; color: #64748b; margin-top: 2px;">
                    প্রধান কার্যালয় ও হ্যাচারি পয়েন্ট | মোবাইল: ০১৭০০-০০০০০০, ০১৮০০-০০০০০০
                </div>
            </div>
            <div style="text-align: right;">
                <div style="display: inline-block; background: #0f172a; color: #ffffff; padding: 4px 14px; font-size: 11pt; font-weight: bold; border-radius: 4px; text-transform: uppercase;">
                    কাস্টমার লেজার বিবরণী
                </div>
                <div style="font-size: 8.5pt; color: #64748b; margin-top: 4px;">
                    প্রিন্টের তারিখ: {{ now()->format('d M, Y - h:i A') }}
                </div>
            </div>
        </div>

        <!-- Customer Profile Info Block -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 14px; padding: 8px 12px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 4px;">
            <div>
                <div style="font-size: 9pt; color: #64748b; text-transform: uppercase; font-weight: bold;">কাস্টমারের বিবরণ:</div>
                <div style="font-size: 13pt; font-weight: bold; color: #0f172a;">{{ $customer->name }}</div>
                <div style="font-size: 9.5pt; color: #334155;"><strong>মোবাইল:</strong> {{ $customer->mobile }}</div>
                @if($customer->email)
                    <div style="font-size: 9pt; color: #64748b;"><strong>ইমেইল:</strong> {{ $customer->email }}</div>
                @endif
            </div>
            <div style="text-align: right;">
                <div style="font-size: 9pt; color: #64748b; text-transform: uppercase; font-weight: bold;">এলাকা ও ঠিকানা:</div>
                <div style="font-size: 10.5pt; font-weight: bold; color: #0f172a;">{{ $customer->location_text }}</div>
                @if($customer->address && $customer->address !== $customer->location_text)
                    <div style="font-size: 9pt; color: #475569;">{{ $customer->address }}</div>
                @endif
            </div>
        </div>

        <!-- Statement Summary Grid -->
        <div style="display: flex; gap: 8px; margin-bottom: 14px;">
            <div style="flex: 1; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background: #f8fafc;">
                <div style="font-size: 8.5pt; color: #64748b;">মোট চালান</div>
                <div style="font-size: 12pt; font-weight: bold; color: #0f172a;">{{ number_format($stats['total_invoices']) }} টি</div>
            </div>
            <div style="flex: 1; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background: #f8fafc;">
                <div style="font-size: 8.5pt; color: #64748b;">মোট বিক্রয় মূল্য</div>
                <div style="font-size: 12pt; font-weight: bold; color: #0f172a;">৳{{ number_format($stats['total_purchased'], 2) }}</div>
            </div>
            <div style="flex: 1; border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; background: #f8fafc;">
                <div style="font-size: 8.5pt; color: #64748b;">মোট পরিশোধ</div>
                <div style="font-size: 12pt; font-weight: bold; color: #059669;">৳{{ number_format($stats['total_paid'], 2) }}</div>
            </div>
            <div style="flex: 1; border: 2px solid #dc2626; padding: 6px 10px; text-align: center; background: #fef2f2;">
                <div style="font-size: 8.5pt; color: #dc2626; font-weight: bold;">বর্তমান মোট বকেয়া</div>
                <div style="font-size: 13pt; font-weight: 800; color: #dc2626;">৳{{ number_format($stats['total_due'], 2) }}</div>
            </div>
        </div>

        <!-- Invoices Ledger Table -->
        <div style="margin-bottom: 20px;">
            <div style="font-size: 10pt; font-weight: bold; color: #0f172a; margin-bottom: 6px;">চালান বিবরণী ও পরিশোধ ইতিহাস:</div>
            <table class="statement-print-table">
                <thead>
                    <tr>
                        <th style="width: 35px; text-align: center;">#</th>
                        <th style="width: 100px;">চালান নং</th>
                        <th style="width: 90px;">তারিখ</th>
                        <th>আইটেম বিবরণ</th>
                        <th style="width: 95px; text-align: right;">চালান মূল্য</th>
                        <th style="width: 95px; text-align: right;">জমা / পরিশোধ</th>
                        <th style="width: 95px; text-align: right;">বকেয়া</th>
                        <th style="width: 80px; text-align: center;">স্ট্যাটাস</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td style="text-align: center;">{{ $loop->iteration }}</td>
                            <td style="font-weight: bold;">{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                            <td>
                                @foreach($sale->items as $it)
                                    <div>{{ $it->product_name }} ({{ number_format($it->quantity) }} {{ $it->unit }})</div>
                                @endforeach
                            </td>
                            <td style="text-align: right; font-weight: bold;">৳{{ number_format($sale->total_amount, 2) }}</td>
                            <td style="text-align: right; color: #059669; font-weight: bold;">৳{{ number_format($sale->paid_amount, 2) }}</td>
                            <td style="text-align: right; color: {{ $sale->due_amount > 0 ? '#dc2626' : '#475569' }}; font-weight: bold;">
                                ৳{{ number_format($sale->due_amount, 2) }}
                            </td>
                            <td style="text-align: center; font-size: 8.5pt;">
                                {{ $sale->due_amount <= 0 ? 'পরিশোধিত' : 'বকেয়া' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 12px; color: #64748b;">
                                কোন চালান পাওয়া যায়নি।
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="4" style="text-align: right; font-weight: bold;">সর্বমোট:</td>
                        <td style="text-align: right; font-weight: bold;">৳{{ number_format($stats['total_purchased'], 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: #059669;">৳{{ number_format($stats['total_paid'], 2) }}</td>
                        <td style="text-align: right; font-weight: 800; color: #dc2626;">৳{{ number_format($stats['total_due'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signature Block -->
        <div style="margin-top: 50px; display: flex; justify-content: space-between; padding-top: 10px;">
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px dashed #475569; padding-top: 4px; font-size: 9pt; font-weight: bold;">
                    কাস্টমারের স্বাক্ষর
                </div>
            </div>
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px dashed #475569; padding-top: 4px; font-size: 9pt; font-weight: bold;">
                    হিসাবরক্ষক / ক্যাশিয়ার
                </div>
            </div>
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px dashed #475569; padding-top: 4px; font-size: 9pt; font-weight: bold;">
                    কর্তৃপক্ষের স্বাক্ষর
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.pages.customer-payments.partials.collect-modal')
@endsection
