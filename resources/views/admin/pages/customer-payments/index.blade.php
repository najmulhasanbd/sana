@extends('admin.layouts.master')

@section('title', 'বকেয়া জমার ভাউচার তালিকা (Customer Money Receipts)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-primary"></i>
                <span>বকেয়া জমার ভাউচার ও রসিদ তালিকা</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}" class="text-decoration-none text-muted">কাস্টমার তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">জমা ভাউচারসমূহ</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.customer-due.index') }}" class="btn btn-outline-warning rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-file-lines"></i>
                <span>বকেয়া শিট</span>
            </a>
            <button type="button" 
                    class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    onclick="openCollectDueModal()">
                <i class="fa-solid fa-plus-circle"></i>
                <span>+ নতুন বকেয়া জমা ও ভাউচার তৈরি</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-file-invoice fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট রসিদ সংখ্যা</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_count']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-money-bill-wave fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট সংগৃহীত টাকা</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_collected'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের আদায়</small>
                        <h4 class="fw-bold mb-0 text-primary">৳{{ number_format($stats['today_collected'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.customer-payments.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="ভাউচার নং, কাস্টমারের নাম বা মোবাইল..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="customer_id" class="select2 form-select" data-placeholder="সকল কাস্টমার">
                        <option value="">সকল কাস্টমার</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ (string)$customerId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->mobile }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control" title="শুরুর তারিখ">
                </div>

                <div class="col-6 col-md-2">
                    <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control" title="শেষ তারিখ">
                </div>

                <div class="col-6 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm w-100" title="ফিল্টার">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if(!empty($search) || !empty($customerId) || !empty($fromDate) || !empty($toDate))
                        <a href="{{ route('admin.customer-payments.index') }}" class="btn btn-light border px-2.5 py-2 rounded-3 text-secondary" title="রিসেট">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="bg-light table-light">
                    <tr>
                        <th class="ps-4 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">ভাউচার নং ও তারিখ</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কাস্টমার তথ্য</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পূর্বের বকেয়া</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">আদায়কৃত টাকা</th>
                        <th class="text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">অবশিষ্ট বকেয়া</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মাধ্যম</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted" style="font-size: 0.85rem;">
                                {{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}
                            </td>
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
                            <td>
                                <div>
                                    <a href="{{ route('admin.customer.show', $p->customer) }}" class="fw-semibold text-dark text-decoration-none hover-primary">
                                        {{ $p->customer->name ?? '—' }}
                                    </a>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-phone me-1"></i>{{ $p->customer->mobile ?? '—' }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-muted" style="font-size: 0.88rem;">
                                ৳{{ number_format($p->previous_due, 2) }}
                            </td>
                            <td class="text-end fw-bold text-success fs-6" style="font-size: 0.95rem;">
                                ৳{{ number_format($p->amount, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $p->remaining_due > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 0.88rem;">
                                ৳{{ number_format($p->remaining_due, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.75rem;">
                                    {{ $p->payment_method }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.customer-payments.show', $p) }}" 
                                       class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-primary shadow-xs" 
                                       title="ভাউচার দেখুন ও প্রিন্ট করুন">
                                        <i class="fa-solid fa-print me-1"></i>ভাউচার
                                    </a>
                                    <form action="{{ route('admin.customer-payments.destroy', $p) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('আপনি কি নিশ্চিত যে ভাউচার {{ $p->voucher_no }} মুছে ফেলতে চান? এটি মুছে ফেললে কাস্টমারের বকেয়া আবার পূর্বের অবস্থায় ফেরত যাবে।');" 
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-light border rounded-circle text-danger p-0 d-inline-flex align-items-center justify-content-center" 
                                                style="width: 28px; height: 28px;" 
                                                title="ভাউচার মুছুন">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fa-solid fa-file-invoice text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="text-muted fw-bold">কোনো বকেয়া জমা ভাউচার পাওয়া যায়নি</h6>
                                    <p class="text-muted small mb-3">কাস্টমারের বকেয়া টাকা জমা করে নতুন ভাউচার তৈরি করতে নিচের বাটনে চাপ দিন</p>
                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-semibold shadow-sm" onclick="openCollectDueModal()">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন বকেয়া জমা
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

@include('admin.pages.customer-payments.partials.collect-modal')
@endsection
