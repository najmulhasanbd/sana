@extends('admin.layouts.master')

@section('title', 'ক্রয় চালান - ' . $purchase->invoice_no)

@section('content')
<div class="w-100 pb-4">
    <!-- Top Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-receipt text-primary"></i>
                <span>ক্রয় চালান বিবরণী</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.purchase.index') }}" class="text-decoration-none text-muted">ক্রয় তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $purchase->invoice_no }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.purchase.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
            @if($purchase->due_amount > 0)
                <button type="button" 
                        class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                        data-bs-toggle="modal" 
                        data-bs-target="#addPaymentModal"
                        style="font-size: 0.85rem;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>বকেয়া পরিশোধ করুন</span>
                </button>
            @endif
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>প্রিন্ট চালান</span>
            </button>
        </div>
    </div>

    <!-- Printable Invoice Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden p-4 p-md-5 mb-4" id="printableInvoice">
        <!-- Invoice Header -->
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
                <p class="text-muted small mb-0">
                    <i class="fa-solid fa-location-dot me-1 text-danger"></i>ময়মনসিংহ, বাংলাদেশ
                </p>
            </div>

            <div class="text-md-end">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold fs-6 mb-2">
                    চালান নং: {{ $purchase->invoice_no }}
                </span>
                <div class="text-muted small">
                    <strong>তারিখ:</strong> {{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : '—' }}
                </div>
                <div class="text-muted small">
                    <strong>তৈরির সময়:</strong> {{ $purchase->created_at ? $purchase->created_at->format('h:i A') : '—' }}
                </div>
            </div>
        </div>

        <!-- Supplier & Payment Status Details -->
        <div class="row g-4 py-4 border-bottom">
            <div class="col-12 col-md-6">
                <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">সরবরাহকারীর তথ্য</small>
                <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier?->name ?? '—' }}</h5>
                <p class="text-secondary small mb-1">
                    <i class="fa-solid fa-phone text-muted me-1.5"></i>{{ $purchase->supplier?->mobile ?? '—' }}
                </p>
                @if($purchase->supplier?->email)
                    <p class="text-secondary small mb-1">
                        <i class="fa-solid fa-envelope text-muted me-1.5"></i>{{ $purchase->supplier->email }}
                    </p>
                @endif
                <p class="text-muted small mb-0">
                    <i class="fa-solid fa-location-dot text-muted me-1.5"></i>
                    {{ collect([$purchase->supplier?->zone?->name, $purchase->supplier?->district?->name, $purchase->supplier?->division?->name])->filter()->implode(', ') ?: ($purchase->supplier?->address ?? 'ঠিকানা উল্লেখ নেই') }}
                </p>
            </div>

            <div class="col-12 col-md-6 text-md-end">
                <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">পরিশোধ অবস্থা</small>
                <div class="mb-2">
                    @if($purchase->payment_status === 'paid')
                        <span class="badge rounded-pill bg-success-subtle text-success px-3 py-1.5 fw-bold fs-6">
                            <i class="fa-solid fa-circle-check me-1"></i>সম্পূর্ণ পরিশোধিত (Paid)
                        </span>
                    @elseif($purchase->payment_status === 'partial')
                        <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-1.5 fw-bold fs-6">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i>আংশিক বকেয়া (Partial)
                        </span>
                    @else
                        <span class="badge rounded-pill bg-danger-subtle text-danger px-3 py-1.5 fw-bold fs-6">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>সম্পূর্ণ বকেয়া (Due)
                        </span>
                    @endif
                </div>
                @if($purchase->due_amount > 0)
                    <p class="text-danger fw-bold mb-0">
                        অবশিষ্ট বকেয়া: ৳{{ number_format($purchase->due_amount, 2) }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Purchased Items Table -->
        <div class="py-4 border-bottom">
            <h6 class="fw-bold text-dark mb-3">ক্রয়কৃত পণ্যের বিবরণ</h6>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-light table-light">
                        <tr>
                            <th class="py-2.5 ps-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                            <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পণ্যের নাম (Product Name)</th>
                            <th class="py-2.5 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem;">পরিমাণ (Quantity)</th>
                            <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">একক দর (Unit Price)</th>
                            <th class="py-2.5 pe-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মোট মূল্য</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                            <tr>
                                <td class="ps-3 text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $item->product?->name ?? '—' }}</span>
                                </td>
                                <td class="text-center fw-semibold text-secondary">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-end fw-semibold text-secondary">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="pe-3 text-end fw-bold text-dark">৳{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="row g-4 py-4 border-bottom">
            <div class="col-12 col-md-6">
                @if($purchase->note)
                    <div class="p-3 rounded-3 bg-light border">
                        <small class="text-muted fw-bold d-block mb-1">মন্তব্য বা বিশেষ নোট:</small>
                        <p class="text-secondary small mb-0">{{ $purchase->note }}</p>
                    </div>
                @endif
            </div>

            <div class="col-12 col-md-6">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-semibold">সর্বমোট মূল্য:</span>
                        <span class="fw-bold text-dark fs-6">৳{{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted fw-semibold">মোট পরিশোধিত:</span>
                        <span class="fw-bold text-success fs-6">৳{{ number_format($purchase->paid_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2 border-top">
                        <span class="fw-bold text-dark">অবশিষ্ট বকেয়া:</span>
                        <span class="fw-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-success' }} fs-5">
                            ৳{{ number_format($purchase->due_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History (পরিশোধের ইতিহাস ও মাধ্যম) -->
        <div class="pt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-primary"></i>
                    <span>পরিশোধের ইতিহাস ও মাধ্যম (Payment History)</span>
                </h6>
                @if($purchase->due_amount > 0)
                    <button type="button" 
                            class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fw-semibold d-print-none" 
                            data-bs-toggle="modal" 
                            data-bs-target="#addPaymentModal">
                        <i class="fa-solid fa-plus me-1"></i>বকেয়া পরিশোধ
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
                            <th class="py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">ট্রানজেকশন আইডি / রেফারেন্স</th>
                            <th class="py-2 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem;">পরিশোধিত টাকা</th>
                            <th class="pe-3 py-2 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem;">নোট</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->payments as $pIdx => $payment)
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
                        <h6 class="modal-title fw-bold text-dark mb-0">বকেয়া টাকা পরিশোধ</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">চালান: {{ $purchase->invoice_no }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.purchase.payment.store', $purchase) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">বর্তমান অবশিষ্ট বকেয়া</small>
                            <span class="fw-bold text-danger fs-5">৳{{ number_format($purchase->due_amount, 2) }}</span>
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
                                পরিশোধের পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   min="1" 
                                   max="{{ $purchase->due_amount }}" 
                                   value="{{ $purchase->due_amount }}" 
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
                                   placeholder="যেমন: কিস্তি বা বকেয়া পরিশোধ" 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="প্রসেসিং..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>পরিশোধ সম্পন্ন করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background-color: #fff !important;
    }
    .app-sidebar, header, .app-header, .toast-container, .d-print-none {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    #printableInvoice {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
