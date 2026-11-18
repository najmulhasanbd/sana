@extends('admin.layouts.master')

@section('title', 'সকল ক্রয়ের তালিকা (Purchases)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-cart-flatbed text-primary"></i>
                <span>সকল ক্রয়ের তালিকা (Purchases)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">ক্রয় ও হিসাব</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">ক্রয় তালিকা</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <!-- <a href="{{ route('admin.purchase-product.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>ক্রয় পণ্য</span>
            </a> -->
            <a href="{{ route('admin.purchase.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন ক্রয় এন্ট্রি</span>
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
                        <i class="fa-solid fa-file-invoice fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট ক্রয় চালান</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_count']) }} টি</h4>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট ক্রয় মূল্য</small>
                        <h4 class="fw-bold mb-0 text-dark">৳{{ number_format($stats['total_amount'], 2) }}</h4>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট পরিশোধিত</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_paid'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট বকেয়া</small>
                        <h4 class="fw-bold mb-0 text-danger">৳{{ number_format($stats['total_due'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header with Select2 Search -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.purchase.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="চালান নম্বর, নাম বা মোবাইল..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <select name="supplier_id" class="select2 form-select" data-placeholder="সকল সরবরাহকারী">
                        <option value="">সকল সরবরাহকারী</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ (string)$supplierId === (string)$sup->id ? 'selected' : '' }}>
                                {{ $sup->name }} — {{ $sup->mobile }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-sm-3 col-md-2">
                    <select name="payment_status" class="select2 form-select" data-placeholder="পরিশোধ স্ট্যাটাস">
                        <option value="">সকল স্ট্যাটাস</option>
                        <option value="paid" {{ ($paymentStatus ?? '') === 'paid' ? 'selected' : '' }}>পরিশোধিত (Paid)</option>
                        <option value="partial" {{ ($paymentStatus ?? '') === 'partial' ? 'selected' : '' }}>আংশিক (Partial)</option>
                        <option value="due" {{ ($paymentStatus ?? '') === 'due' ? 'selected' : '' }}>বকেয়া (Due)</option>
                    </select>
                </div>

                <div class="col-6 col-sm-3 col-md-2">
                    <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control" title="হতে তারিখ" style="font-size: 0.85rem;">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($paymentStatus) || !empty($supplierId) || !empty($fromDate) || !empty($toDate))
                        <a href="{{ route('admin.purchase.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">SL</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">চালান নম্বর</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">তারিখ</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">সরবরাহকারী</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পণ্যসমূহ</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মোট মূল্য</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">পরিশোধিত</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">বকেয়া</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 110px;">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $index => $purchase)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ $purchases->firstItem() + $index }}
                            </td>
                            <td>
                                <a href="{{ route('admin.purchase.show', $purchase) }}" class="fw-bold text-primary text-decoration-none d-flex align-items-center gap-1.5" style="font-size: 0.88rem;">
                                    <i class="fa-solid fa-receipt text-muted" style="font-size: 0.8rem;"></i>
                                    <span>{{ $purchase->invoice_no }}</span>
                                </a>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $purchase->purchase_date ? $purchase->purchase_date->format('d M, Y') : '—' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $productNames = $purchase->items->map(fn($i) => $i->product?->name)->filter();
                                @endphp
                                @if($productNames->isEmpty())
                                    <span class="text-muted small">—</span>
                                @else
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($productNames as $pName)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1" style="font-size: 0.72rem;">
                                                🐟 {{ $pName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">
                                        {{ $purchase->supplier?->name ?? '—' }}
                                    </span>
                                    <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-phone" style="font-size: 0.65rem;"></i>{{ $purchase->supplier?->mobile ?? '—' }}
                                    </small>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-dark" style="font-size: 0.88rem;">
                                ৳{{ number_format($purchase->total_amount, 2) }}
                            </td>
                            <td class="text-end fw-semibold text-success" style="font-size: 0.88rem;">
                                ৳{{ number_format($purchase->paid_amount, 2) }}
                            </td>
                            <td class="text-end fw-bold {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}" style="font-size: 0.88rem;">
                                ৳{{ number_format($purchase->due_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($purchase->payment_status === 'paid')
                                    <span class="badge rounded-pill bg-success-subtle text-success px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle-check me-1"></i>পরিশোধিত
                                    </span>
                                @elseif($purchase->payment_status === 'partial')
                                    <span class="badge rounded-pill bg-warning-subtle text-warning px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-clock-rotate-left me-1"></i>আংশিক
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>বকেয়া
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- View Invoice Button -->
                                    <a href="{{ route('admin.purchase.show', $purchase) }}" 
                                       class="btn btn-sm btn-light border rounded-circle text-primary d-flex align-items-center justify-content-center" 
                                       title="চালান বিস্তারিত দেখুন"
                                       style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>

                                    <!-- Due Payment Button (Only if due > 0) -->
                                    @if($purchase->due_amount > 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-light border rounded-circle text-success d-flex align-items-center justify-content-center btn-pay-due" 
                                                data-id="{{ $purchase->id }}"
                                                data-invoice="{{ $purchase->invoice_no }}"
                                                data-supplier="{{ $purchase->supplier?->name }}"
                                                data-due="{{ $purchase->due_amount }}"
                                                data-action="{{ route('admin.purchase.payment.store', $purchase) }}"
                                                title="বকেয়া পরিশোধ করুন"
                                                style="width: 32px; height: 32px;">
                                            <i class="fa-solid fa-hand-holding-dollar" style="font-size: 0.85rem;"></i>
                                        </button>
                                    @endif

                                    <!-- Delete Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-circle text-danger d-flex align-items-center justify-content-center btn-delete-purchase" 
                                            data-id="{{ $purchase->id }}"
                                            data-invoice="{{ $purchase->invoice_no }}"
                                            data-action="{{ route('admin.purchase.destroy', $purchase) }}"
                                            title="মুছে ফেলুন"
                                            style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-trash-can" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                                        <i class="fa-solid fa-cart-flatbed fs-3"></i>
                                    </div>
                                    <h6 class="fw-semibold text-secondary mb-1">কোনো ক্রয়ের রেকর্ড পাওয়া যায়নি</h6>
                                    <p class="text-muted small mb-3">নতুন ক্রয় এন্ট্রি করতে নিচের বাটনে ক্লিক করুন।</p>
                                    <a href="{{ route('admin.purchase.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন ক্রয় এন্ট্রি
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $purchases->links() }}
            </div>
        @endif
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
                        <small class="text-muted" style="font-size: 0.75rem;" id="modalPaymentSubtitle">চালান নম্বর</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="addPaymentForm" action="" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Due Notice Banner -->
                    <div class="alert alert-warning border-0 rounded-3 p-3 mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">বর্তমান অবশিষ্ট বকেয়া</small>
                            <span class="fw-bold text-danger fs-5" id="modalDueAmountDisplay">৳0.00</span>
                        </div>
                        <span class="badge bg-danger rounded-pill px-2.5 py-1">বকেয়া প্রযোজ্য</span>
                    </div>

                    <div class="row g-3">
                        <!-- Payment Date -->
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

                        <!-- Amount -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পরিশোধিত টাকা (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   min="1" 
                                   id="modalPaymentAmountInput" 
                                   name="amount" 
                                   class="form-control fw-bold text-success" 
                                   placeholder="0.00" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- Payment Method -->
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

                        <!-- Transaction ID -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                ট্রানজেকশন আইডি / রেফারেন্স <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   class="form-control" 
                                   placeholder="যেমন: TRX92819..." 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- Note -->
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
                        <i class="fa-solid fa-check me-1"></i>বকেয়া পরিশোধ নিশ্চিত করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deletePurchaseForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Open Due Payment Modal
    $(document).on('click', '.btn-pay-due', function() {
        const actionUrl = $(this).data('action');
        const invoice = $(this).data('invoice');
        const supplier = $(this).data('supplier');
        const due = parseFloat($(this).data('due')) || 0;

        $('#addPaymentForm').attr('action', actionUrl);
        $('#modalPaymentSubtitle').text(`চালান: ${invoice} • ${supplier}`);
        $('#modalDueAmountDisplay').text(`৳${due.toFixed(2)}`);
        $('#modalPaymentAmountInput').attr('max', due).val(due);

        const paymentModal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
        paymentModal.show();
    });

    // Delete Purchase Confirmation with SweetAlert2
    $(document).on('click', '.btn-delete-purchase', function() {
        const actionUrl = $(this).data('action');
        const invoice = $(this).data('invoice');

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `চালান "${invoice}" স্থায়ীভাবে মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deletePurchaseForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });
});
</script>
@endpush
