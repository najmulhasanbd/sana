@extends('admin.layouts.master')

@section('title', 'কর্মচারীর বেতন পরিশোধ করুন')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-success"></i>
                <span>কর্মচারীর বেতন পরিশোধ করুন</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}" class="text-decoration-none text-muted">কর্মচারী ও বেতন</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বেতন পরিশোধ</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.employee-salary.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>বেতন তালিকায় ফিরুন</span>
            </a>
        </div>
    </div>

    <!-- Main Payment Form Card -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-invoice-dollar text-success"></i>
                        <span>মাসিক বেতন ও অগ্রিম সমন্বয় ফরম</span>
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                        <i class="fa-solid fa-shield-halved me-1"></i>শূন্য বা পজিটিভ হিসাব (মাইনাস-প্রটেক্টেড)
                    </span>
                </div>

                <form action="{{ route('admin.employee-salary.store') }}" method="POST" id="salaryForm">
                    @csrf
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- 1. Employee Selection -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    কর্মচারী নির্বাচন করুন <span class="text-danger">*</span>
                                </label>
                                <select name="employee_id" id="employeeSelect" class="form-select select2" required style="font-size: 0.88rem;">
                                    <option value="">-- কর্মচারী নির্বাচন করুন --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ (old('employee_id', $selectedEmployeeId) == $emp->id) ? 'selected' : '' }}>
                                            {{ $emp->name }} ({{ $emp->designation }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 2. Salary Month -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    বেতনের মাস <span class="text-danger">*</span>
                                </label>
                                <input type="month" 
                                       name="salary_month" 
                                       value="{{ old('salary_month', $currentMonth) }}" 
                                       class="form-control @error('salary_month') is-invalid @enderror" 
                                       required 
                                       style="font-size: 0.88rem;">
                                @error('salary_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 3. Payment Date -->
                            <div class="col-12 col-md-3">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    পরিশোধের তারিখ <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       name="payment_date" 
                                       value="{{ old('payment_date', date('Y-m-d')) }}" 
                                       class="form-control @error('payment_date') is-invalid @enderror" 
                                       required 
                                       style="font-size: 0.88rem;">
                                @error('payment_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Real-time Status Card of Selected Employee -->
                            <div class="col-12" id="employeeInfoBox" style="display: none;">
                                <div class="p-3 rounded-4 border bg-light d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 38px; height: 38px; background: #0284c7;">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" id="infoEmpName">—</span>
                                            <small class="text-muted" id="infoEmpDesignation">—</small>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <div class="text-end">
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">নির্ধারিত মূল বেতন</small>
                                            <strong class="text-primary" id="infoEmpSalary">৳০.০০</strong>
                                        </div>
                                        <div class="border-start ps-3 text-end">
                                            <small class="text-muted d-block" style="font-size: 0.72rem;">বর্তমান বকেয়া অগ্রিম</small>
                                            <strong class="text-warning-emphasis" id="infoEmpAdvance">৳০.০০</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Base Salary (editable, defaults to employee monthly_salary) -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    চলতি মাসের মূল বেতন (৳) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       id="baseSalaryInput" 
                                       name="base_salary" 
                                       value="{{ old('base_salary', 0) }}" 
                                       class="form-control fw-bold text-dark @error('base_salary') is-invalid @enderror" 
                                       required 
                                       style="font-size: 0.95rem;">
                                <small class="text-muted" style="font-size: 0.72rem;">* কর্মচারীর নির্ধারিত মাসিক বেতন</small>
                                @error('base_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 5. Bonus Amount -->
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    বোনাস / অতিরিক্ত ভাতা (৳) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       id="bonusInput" 
                                       name="bonus_amount" 
                                       value="{{ old('bonus_amount', 0) }}" 
                                       class="form-control fw-bold text-success @error('bonus_amount') is-invalid @enderror" 
                                       placeholder="০.০০" 
                                       style="font-size: 0.95rem;">
                                <small class="text-muted" style="font-size: 0.72rem;">* ঈদ বা বিশেষ কাজের বোনাস</small>
                                @error('bonus_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 6. Advance Deduction (Smart Clamped) -->
                            <div class="col-12 col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-secondary mb-0" style="font-size: 0.85rem;">
                                        অগ্রিম কর্তন (৳) <small class="text-muted">(ঐচ্ছিক)</small>
                                    </label>
                                    <button type="button" id="btnMaxDeduct" class="btn btn-link p-0 text-decoration-none small text-warning-emphasis fw-bold" style="font-size: 0.75rem;">
                                        সর্বোচ্চ কর্তন
                                    </button>
                                </div>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       id="advanceDeductionInput" 
                                       name="advance_deduction" 
                                       value="{{ old('advance_deduction', 0) }}" 
                                       class="form-control fw-bold text-warning-emphasis @error('advance_deduction') is-invalid @enderror" 
                                       placeholder="০.০০" 
                                       style="font-size: 0.95rem;">
                                <small class="text-muted d-block" id="deductionHelpText" style="font-size: 0.72rem;">
                                    * বকেয়া অগ্রিম থেকে কেটে নেওয়া হবে
                                </small>
                                @error('advance_deduction')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- 7. REAL-TIME CALCULATION SUMMARY BANNER (GUARANTEES NEVER MINUS) -->
                            <div class="col-12 mt-4">
                                <div class="card border-2 border-primary-subtle rounded-4 bg-light p-3 p-md-4">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-12 col-md-8">
                                            <div class="row g-2" style="font-size: 0.85rem;">
                                                <div class="col-6">
                                                    <span class="text-muted">গ্রস আয় (বেতন + বোনাস):</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <strong class="text-dark" id="calcGrossSalary">৳০.০০</strong>
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-muted">অগ্রিম কর্তন (-):</span>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <strong class="text-warning-emphasis" id="calcAdvanceDeduction">- ৳০.০০</strong>
                                                </div>
                                                <div class="col-6 border-top pt-1">
                                                    <span class="text-muted">পরবর্তী মাসে অবশিষ্ট অগ্রিম:</span>
                                                </div>
                                                <div class="col-6 text-end border-top pt-1">
                                                    <strong class="text-secondary" id="calcRemainingAdvance">৳০.০০</strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-4 text-center border-start-md ps-md-4">
                                            <small class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.75rem; letter-spacing: 0.5px;">চূড়ান্ত প্রদেয় নিট বেতন</small>
                                            <h2 class="fw-bolder text-success mb-1" id="calcNetSalary">৳০.০০</h2>
                                            <span class="badge bg-success text-white rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                                <i class="fa-solid fa-check me-1"></i>হাতে প্রদেয় টাকা
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 8. Payment Method -->
                            <div class="col-12 col-md-6 mt-3">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    পরিশোধের মাধ্যম <span class="text-danger">*</span>
                                </label>
                                <select name="payment_method" class="form-select" required style="font-size: 0.88rem;">
                                    <option value="Cash" selected>ক্যাশ (Cash)</option>
                                    <option value="bKash">বিকাশ (bKash)</option>
                                    <option value="Nagad">নগদ (Nagad)</option>
                                    <option value="Rocket">রকেট (Rocket)</option>
                                    <option value="Bank">ব্যাংক ট্রান্সফার (Bank)</option>
                                    <option value="Other">অন্যান্য</option>
                                </select>
                            </div>

                            <!-- 9. Note -->
                            <div class="col-12 col-md-6 mt-3">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="text" 
                                       name="note" 
                                       value="{{ old('note') }}" 
                                       class="form-control" 
                                       placeholder="বেতন পরিশোধ সংক্রান্ত বিশেষ নোট..." 
                                       style="font-size: 0.88rem;">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.employee-salary.index') }}" class="btn btn-light border px-4 py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
                            বাতিল
                        </a>
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-bold shadow-sm btn-submit-with-spinner" data-loading-text="বেতন পরিশোধ হচ্ছে..." style="font-size: 0.88rem;">
                            <i class="fa-solid fa-check-double me-1"></i>বেতন পরিশোধ নিশ্চিত করুন ও রসিদ তৈরি করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Preloaded employees mapping from server
    const employeeData = @json($employeeData);

    const $empSelect = $('#employeeSelect');
    const $baseSalary = $('#baseSalaryInput');
    const $bonus = $('#bonusInput');
    const $deduction = $('#advanceDeductionInput');
    const $btnMaxDeduct = $('#btnMaxDeduct');

    // UI elements
    const $infoBox = $('#employeeInfoBox');
    const $infoName = $('#infoEmpName');
    const $infoDesig = $('#infoEmpDesignation');
    const $infoSalary = $('#infoEmpSalary');
    const $infoAdvance = $('#infoEmpAdvance');

    const $calcGross = $('#calcGrossSalary');
    const $calcDeduction = $('#calcAdvanceDeduction');
    const $calcRemainingAdvance = $('#calcRemainingAdvance');
    const $calcNet = $('#calcNetSalary');
    const $deductionHelpText = $('#deductionHelpText');

    let currentAdvanceBalance = 0;

    function formatTk(val) {
        return '৳' + Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function onEmployeeChange() {
        const empId = $empSelect.val();
        if (empId && employeeData[empId]) {
            const emp = employeeData[empId];
            currentAdvanceBalance = Number(emp.advance_balance || 0);

            $infoName.text(emp.name);
            $infoDesig.text(emp.designation);
            $infoSalary.text(formatTk(emp.monthly_salary));
            $infoAdvance.text(formatTk(currentAdvanceBalance));
            $infoBox.slideDown(150);

            // Auto fill base salary if not edited
            $baseSalary.val(emp.monthly_salary);

            // If employee has advance, suggest deducting advance up to monthly salary
            const maxDeductible = Math.min(currentAdvanceBalance, Number(emp.monthly_salary));
            $deduction.val(maxDeductible);
        } else {
            currentAdvanceBalance = 0;
            $infoBox.slideUp(150);
            $baseSalary.val(0);
            $deduction.val(0);
        }
        recalculate();
    }

    function recalculate() {
        let base = Math.max(0, parseFloat($baseSalary.val()) || 0);
        let bonus = Math.max(0, parseFloat($bonus.val()) || 0);
        let gross = base + bonus;

        // SAFE CLAMP RULE: "jno minus - na hoi"
        // Maximum deduction that can be made cannot exceed:
        // 1) The available advance balance
        // 2) The total gross salary earned
        const maxAllowedDeduction = Math.min(currentAdvanceBalance, gross);

        let requestedDeduction = parseFloat($deduction.val()) || 0;

        // Auto clamp if user typed more than maxAllowedDeduction
        if (requestedDeduction > maxAllowedDeduction) {
            requestedDeduction = maxAllowedDeduction;
            $deduction.val(requestedDeduction);
        }
        if (requestedDeduction < 0) {
            requestedDeduction = 0;
            $deduction.val(0);
        }

        // Net salary strictly >= 0 (cannot be negative)
        const netSalary = Math.max(0, gross - requestedDeduction);

        // Remaining advance strictly >= 0 (cannot be negative)
        const remainingAdvance = Math.max(0, currentAdvanceBalance - requestedDeduction);

        // Update live breakdown
        $calcGross.text(formatTk(gross));
        $calcDeduction.text('- ' + formatTk(requestedDeduction));
        $calcRemainingAdvance.text(formatTk(remainingAdvance));
        $calcNet.text(formatTk(netSalary));

        // Update help text
        if (currentAdvanceBalance > 0) {
            $deductionHelpText.html(`<span class="text-warning-emphasis">সর্বোচ্চ কর্তনযোগ্য: <strong>${formatTk(maxAllowedDeduction)}</strong> (মোট বকেয়া অগ্রিম: ${formatTk(currentAdvanceBalance)})</span>`);
        } else {
            $deductionHelpText.text('* এই কর্মচারীর কোনো বকেয়া অগ্রিম নেই');
        }
    }

    $empSelect.on('change', onEmployeeChange);
    $baseSalary.on('input', recalculate);
    $bonus.on('input', recalculate);
    $deduction.on('input', recalculate);

    $btnMaxDeduct.on('click', function() {
        let base = Math.max(0, parseFloat($baseSalary.val()) || 0);
        let bonus = Math.max(0, parseFloat($bonus.val()) || 0);
        let gross = base + bonus;
        let maxAllowedDeduction = Math.min(currentAdvanceBalance, gross);
        $deduction.val(maxAllowedDeduction);
        recalculate();
    });

    // Trigger on initial load
    if ($empSelect.val()) {
        onEmployeeChange();
    }
});
</script>
@endpush
@endsection
