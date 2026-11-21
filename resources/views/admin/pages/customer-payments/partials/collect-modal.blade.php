<!-- ================= DIRECT CUSTOMER DUE COLLECTION MODAL ================= -->
<div class="modal fade" id="collectCustomerDueModal" tabindex="-1" aria-labelledby="collectCustomerDueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0" id="collectCustomerDueModalLabel">বকেয়া টাকা জমা / আদায় (মানি রিসিট)</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">মোট বকেয়ার ওপর সরাসরি জমা ও ভাউচার রসিদ তৈরি</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.customer-payments.store') }}" method="POST" id="collectCustomerDueForm">
                @csrf
                <div class="modal-body p-4">
                    <!-- Customer Selection or Display -->
                    <div class="mb-3" id="customerSelectGroup">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            কাস্টমার নির্বাচন করুন <span class="text-danger">*</span>
                        </label>
                        <select name="customer_id" id="modalCustomerId" class="select2 form-select" required onchange="handleModalCustomerChange(this)">
                            <option value="">কাস্টমার বাছাই করুন...</option>
                            @if(isset($customers))
                                @foreach($customers as $c)
                                    @php
                                        $cDue = (float) ($c->total_due ?? ($c->total_due_amount ?? 0));
                                    @endphp
                                    <option value="{{ $c->id }}" 
                                            data-name="{{ $c->name }}" 
                                            data-mobile="{{ $c->mobile }}" 
                                            data-due="{{ $cDue }}">
                                        {{ $c->name }} ({{ $c->mobile }}) — বাকি: ৳{{ number_format($cDue, 2) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Customer Info & Due Alert Banner -->
                    <div class="alert alert-light border border-primary-subtle rounded-3 p-3 mb-3" id="modalCustomerInfoBanner" style="display: none; background: #f0fdf4 !important;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold text-dark mb-0" id="modalCustomerNameText">কাস্টমারের নাম</h6>
                                <small class="text-muted" id="modalCustomerMobileText">মোবাইল: -</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.72rem;">বর্তমান মোট বকেয়া</small>
                                <span class="fw-bold text-danger fs-5" id="modalCustomerDueText">৳0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                জমার তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="payment_date" 
                                   id="modalPaymentDate" 
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
                                   name="amount" 
                                   id="modalPaymentAmount" 
                                   class="form-control fw-bold text-success fs-5" 
                                   placeholder="0.00" 
                                   required 
                                   style="font-size: 1rem;">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পরিশোধের মাধ্যম <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" id="modalPaymentMethod" class="select2 form-select" required>
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
                                   placeholder="e.g. TRX123456" 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                নোট / বিবরণ <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="note" 
                                   class="form-control" 
                                   placeholder="টাকা আদায়ের বিবরণ বা রেফারেন্স..." 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- SMS Checkbox -->
                        <div class="col-12">
                            <div class="form-check form-switch pt-1">
                                <input class="form-check-input" type="checkbox" role="switch" name="send_sms" id="modalSendSmsCheck" value="1" checked>
                                <label class="form-check-label text-dark fw-semibold small" for="modalSendSmsCheck">
                                    <i class="fa-solid fa-paper-plane text-primary me-1"></i>কাস্টমারের মোবাইলে টাকা জমার রসিদ SMS পাঠান
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="প্রসেসিং..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>জমা সম্পন্ন করুন ও ভাউচার দেখুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCollectDueModal(customerId, customerName, customerMobile, totalDue) {
    const modalEl = document.getElementById('collectCustomerDueModal');
    if (!modalEl) return;

    const selectEl = document.getElementById('modalCustomerId');
    const bannerEl = document.getElementById('modalCustomerInfoBanner');
    const nameTextEl = document.getElementById('modalCustomerNameText');
    const mobileTextEl = document.getElementById('modalCustomerMobileText');
    const dueTextEl = document.getElementById('modalCustomerDueText');
    const amountInput = document.getElementById('modalPaymentAmount');

    if (customerId) {
        if (selectEl) {
            selectEl.value = customerId;
            if (window.jQuery && $(selectEl).data('select2')) {
                $(selectEl).trigger('change');
            }
        }
        if (bannerEl) bannerEl.style.display = 'block';
        if (nameTextEl) nameTextEl.textContent = customerName || 'কাস্টমার';
        if (mobileTextEl) mobileTextEl.textContent = 'মোবাইল: ' + (customerMobile || '—');
        if (dueTextEl) dueTextEl.textContent = '৳' + Number(totalDue || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (amountInput) {
            amountInput.max = totalDue > 0 ? totalDue : '';
            amountInput.value = totalDue > 0 ? totalDue : '';
        }
    } else {
        if (bannerEl) bannerEl.style.display = 'none';
        if (amountInput) {
            amountInput.value = '';
            amountInput.removeAttribute('max');
        }
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function handleModalCustomerChange(select) {
    const bannerEl = document.getElementById('modalCustomerInfoBanner');
    const nameTextEl = document.getElementById('modalCustomerNameText');
    const mobileTextEl = document.getElementById('modalCustomerMobileText');
    const dueTextEl = document.getElementById('modalCustomerDueText');
    const amountInput = document.getElementById('modalPaymentAmount');

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
</script>
