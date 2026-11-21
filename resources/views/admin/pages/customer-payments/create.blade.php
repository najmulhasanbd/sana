@extends('admin.layouts.master')

@section('title', 'নতুন বকেয়া টাকা জমা ও ভাউচার তৈরি')

@section('content')
<div class="w-100 pb-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-hand-holding-dollar text-primary"></i>
                <span>নতুন বকেয়া টাকা জমা ও ভাউচার তৈরি</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.customer-payments.index') }}" class="text-decoration-none text-muted">ভাউচারসমূহ</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">নতুন জমা</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.customer-payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm" style="font-size: 0.85rem;">
            <i class="fa-solid fa-arrow-left me-1"></i>ভাউচার তালিকা
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <form action="{{ route('admin.customer-payments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            কাস্টমার নির্বাচন করুন <span class="text-danger">*</span>
                        </label>
                        <select name="customer_id" id="pageCustomerId" class="select2 form-select" required onchange="handleCustomerSelect(this)">
                            <option value="">কাস্টমার বাছাই করুন...</option>
                            @foreach($customers as $c)
                                @php $cDue = (float) ($c->total_due ?? 0); @endphp
                                <option value="{{ $c->id }}" 
                                        data-name="{{ $c->name }}" 
                                        data-mobile="{{ $c->mobile }}" 
                                        data-due="{{ $cDue }}"
                                        {{ (string)$selectedCustomerId === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->mobile }}) — বাকি: ৳{{ number_format($cDue, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-light border border-primary-subtle rounded-3 p-3 mb-3" id="pageCustomerBanner" style="display: none; background: #f0fdf4 !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold text-dark mb-0" id="pageCustomerName"></h6>
                                <small class="text-muted" id="pageCustomerMobile"></small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.72rem;">বর্তমান মোট বকেয়া</small>
                                <span class="fw-bold text-danger fs-5" id="pageCustomerDue">৳0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" step="0.01" min="1" name="amount" id="pageAmountInput" class="form-control fw-bold text-success fs-5" placeholder="0.00" required>
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
                            <input type="text" name="transaction_id" class="form-control" placeholder="e.g. TRX123456">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                নোট / বিবরণ <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" name="note" class="form-control" placeholder="টাকা আদায়ের বিবরণ বা রেফারেন্স...">
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" role="switch" name="send_sms" id="pageSendSmsCheck" value="1" checked>
                                <label class="form-check-label text-dark fw-semibold small" for="pageSendSmsCheck">
                                    <i class="fa-solid fa-paper-plane text-primary me-1"></i>কাস্টমারের মোবাইলে টাকা জমার রসিদ SMS পাঠান
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.customer-payments.index') }}" class="btn btn-light border px-4 py-2 rounded-3 text-secondary">
                            বাতিল
                        </a>
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="প্রসেসিং...">
                            <i class="fa-solid fa-check me-1"></i>জমা সম্পন্ন করুন ও ভাউচার দেখুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function handleCustomerSelect(select) {
    const bannerEl = document.getElementById('pageCustomerBanner');
    const nameTextEl = document.getElementById('pageCustomerName');
    const mobileTextEl = document.getElementById('pageCustomerMobile');
    const dueTextEl = document.getElementById('pageCustomerDue');
    const amountInput = document.getElementById('pageAmountInput');

    const selectedOption = select.options[select.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        if (bannerEl) bannerEl.style.display = 'none';
        if (amountInput) {
            amountInput.value = '';
            amountInput.removeAttribute('max');
        }
        return;
    }

    const name = selectedOption.getAttribute('data-name') || '';
    const mobile = selectedOption.getAttribute('data-mobile') || '';
    const due = parseFloat(selectedOption.getAttribute('data-due') || '0');

    if (bannerEl) bannerEl.style.display = 'block';
    if (nameTextEl) nameTextEl.textContent = name;
    if (mobileTextEl) mobileTextEl.textContent = 'মোবাইল: ' + (mobile || '—');
    if (dueTextEl) dueTextEl.textContent = '৳' + due.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (amountInput) {
        amountInput.max = due > 0 ? due : '';
        amountInput.value = due > 0 ? due : '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('pageCustomerId');
    if (sel && sel.value) {
        handleCustomerSelect(sel);
    }
});
</script>
@endsection
