@extends('admin.layouts.master')

@section('title', 'কর্মচারী প্রোফাইল ও লেজার - ' . $employee->name)

@section('content')
<div class="w-100 pb-4">
    <!-- Top Action Bar (Screen Only) -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 d-print-none">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-address-book text-primary"></i>
                <span>কর্মচারীর ব্যক্তিগত হিসাব ও লেজার</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}" class="text-decoration-none text-muted">কর্মচারী তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $employee->name }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employee.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
            <button type="button" 
                    class="btn btn-outline-warning text-dark rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#employeeAdvanceModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                <span>অগ্রিম টাকা দিন</span>
            </button>
            <a href="{{ route('admin.employee-salary.create', ['employee_id' => $employee->id]) }}" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-money-bill-wave"></i>
                <span>বেতন পরিশোধ করুন</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-print"></i>
                <span>লেজার প্রিন্ট করুন</span>
            </button>
        </div>
    </div>

    <!-- Employee Profile Info & Summary Stat Row -->
    <div class="row g-3 mb-4">
        <!-- Profile Info Card -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                         style="width: 58px; height: 58px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); font-size: 1.4rem;">
                        {{ mb_substr($employee->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0.5">{{ $employee->name }}</h5>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-light text-primary border px-2 py-0.5 rounded-pill" style="font-size: 0.78rem;">
                                <i class="fa-solid fa-briefcase me-1"></i>{{ $employee->designation_name }}
                            </span>
                            <span class="badge {{ $employee->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">
                                {{ $employee->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3">
                    <div class="row g-2" style="font-size: 0.85rem;">
                        <div class="col-6">
                            <span class="text-muted d-block small">মোবাইল নম্বর:</span>
                            <strong class="text-dark">{{ $employee->mobile ?: '—' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">যোগদানের তারিখ:</span>
                            <strong class="text-dark">{{ $employee->joining_date ? $employee->joining_date->format('d M, Y') : '—' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">এনআইডি নম্বর:</span>
                            <strong class="text-dark">{{ $employee->nid ?: '—' }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block small">মাসিক মূল বেতন:</span>
                            <strong class="text-primary">৳{{ number_format($employee->monthly_salary, 2) }}</strong>
                        </div>
                        <div class="col-12 mt-2">
                            <span class="text-muted d-block small">ঠিকানা:</span>
                            <span class="text-dark">{{ $employee->address ?: '—' }}</span>
                        </div>
                        @if($employee->note)
                            <div class="col-12 mt-1">
                                <span class="text-muted d-block small">নোট:</span>
                                <span class="text-secondary small">{{ $employee->note }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Balance Stats Cards -->
        <div class="col-12 col-lg-7">
            <div class="row g-3 h-100">
                <!-- Advance Balance -->
                <div class="col-12 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">বর্তমান বকেয়া অগ্রিম</small>
                            <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">খোরকী ব্যালেন্স</span>
                        </div>
                        <h3 class="fw-bold mb-1 text-warning-emphasis">৳{{ number_format($employee->advance_balance, 2) }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            মোট অগ্রিম নেওয়া: ৳{{ number_format($employee->total_advance, 2) }} | কর্তন: ৳{{ number_format($employee->total_advance_deducted, 2) }}
                        </small>
                    </div>
                </div>

                <!-- Total Net Salary Paid -->
                <div class="col-12 col-sm-6">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">সর্বমোট পরিশোধিত বেতন</small>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-0.5" style="font-size: 0.7rem;">{{ $employee->salaries->count() }}টি মাস পরিশোধ</span>
                        </div>
                        <h3 class="fw-bold mb-1 text-success">৳{{ number_format($employee->total_salary_paid, 2) }}</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">
                            নিট বেতন পরিশোধের সমষ্টি
                        </small>
                    </div>
                </div>

                <!-- Quick Help / Information Box -->
                <div class="col-12">
                    <div class="alert alert-info border-0 rounded-4 p-3 mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info fs-5 text-info"></i>
                        <span class="small text-dark">
                            <strong>টিপস:</strong> অগ্রিম টাকা দেওয়া থাকলে তা মাস শেষে বেতন দেওয়ার সময় সর্বোচ্চ মূল বেতনের সমপরিমাণ কর্তন করা যায়। কর্তন কখনোই মোট আয়ের বেশি হবে না, তাই বেতন বা ব্যালেন্স কখনোই <strong>মাইনাস (-)</strong> হবে না।
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs: Salary Payment History & Advance History -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs border-0 px-3 pt-2" id="employeeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold py-2.5 px-4 d-flex align-items-center gap-2" 
                            id="salaries-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#salaries-pane" 
                            type="button" 
                            role="tab" 
                            style="font-size: 0.88rem;">
                        <i class="fa-solid fa-money-bill-wave text-success"></i>
                        <span>বেতন পরিশোধের খাতা ({{ $employee->salaries->count() }}টি)</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold py-2.5 px-4 d-flex align-items-center gap-2" 
                            id="advances-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#advances-pane" 
                            type="button" 
                            role="tab" 
                            style="font-size: 0.88rem;">
                        <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                        <span>অগ্রিম ও খোরকী খাতা ({{ $employee->advances->count() }}টি)</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content" id="employeeTabContent">
            <!-- TAB 1: SALARIES -->
            <div class="tab-pane fade show active p-3 p-md-4" id="salaries-pane" role="tabpanel" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">মাসিক বেতন পরিশোধের পূর্ণাঙ্গ রেকর্ড</h6>
                    <a href="{{ route('admin.employee-salary.create', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold d-print-none">
                        <i class="fa-solid fa-plus me-1"></i>নতুন বেতন পরিশোধ
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light table-light">
                            <tr>
                                <th class="ps-3 py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">বেতনের মাস</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পরিশোধের তারিখ</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মূল বেতন</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">বোনাস</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end text-warning-emphasis" style="font-size: 0.75rem;">অগ্রিম কর্তন (-)</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">নিট পরিশোধ</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মাধ্যম</th>
                                <th class="pe-3 py-2.5 text-secondary text-uppercase fw-semibold text-end d-print-none" style="font-size: 0.75rem; width: 120px;">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->salaries as $sal)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark" style="font-size: 0.88rem;">
                                        {{ $sal->salary_month_name }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ $sal->payment_date ? $sal->payment_date->format('d M, Y') : '—' }}
                                    </td>
                                    <td class="text-end fw-semibold text-secondary" style="font-size: 0.85rem;">
                                        ৳{{ number_format($sal->base_salary, 2) }}
                                    </td>
                                    <td class="text-end text-muted small">
                                        {{ $sal->bonus_amount > 0 ? '+ ৳' . number_format($sal->bonus_amount, 2) : '—' }}
                                    </td>
                                    <td class="text-end fw-bold text-warning-emphasis" style="font-size: 0.85rem;">
                                        {{ $sal->advance_deduction > 0 ? '- ৳' . number_format($sal->advance_deduction, 2) : '—' }}
                                    </td>
                                    <td class="text-end fw-bold text-success" style="font-size: 0.95rem;">
                                        ৳{{ number_format($sal->net_salary, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">
                                            {{ $sal->payment_method }}
                                        </span>
                                    </td>
                                    <td class="pe-3 text-end d-print-none">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.employee-salary.show', $sal) }}" 
                                               class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1" 
                                               title="রসিদ প্রিন্ট করুন"
                                               style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-receipt me-1"></i>রসিদ
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-danger btn-delete-salary" 
                                                    data-action="{{ route('admin.employee-salary.destroy', $sal) }}"
                                                    data-month="{{ $sal->salary_month_name }}"
                                                    title="বাতিল করুন"
                                                    style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        এই কর্মচারীর কোনো বেতন পরিশোধের রেকর্ড পাওয়া যায়নি।
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: ADVANCES -->
            <div class="tab-pane fade p-3 p-md-4" id="advances-pane" role="tabpanel" tabindex="0">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0">অগ্রিম ও খোরকী গ্রহণের হিসাব</h6>
                    <button type="button" 
                            class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1.5 fw-semibold d-print-none" 
                            data-bs-toggle="modal" 
                            data-bs-target="#employeeAdvanceModal">
                        <i class="fa-solid fa-plus me-1"></i>নতুন অগ্রিম দিন
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light table-light">
                            <tr>
                                <th class="ps-3 py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">তারিখ</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">অগ্রিমের পরিমাণ</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মাধ্যম</th>
                                <th class="py-2.5 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মন্তব্য / কারণ</th>
                                <th class="pe-3 py-2.5 text-secondary text-uppercase fw-semibold text-end d-print-none" style="font-size: 0.75rem; width: 80px;">অ্যাকশন</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->advances as $idx => $adv)
                                <tr>
                                    <td class="ps-3 text-muted small">{{ $idx + 1 }}</td>
                                    <td class="fw-semibold text-dark" style="font-size: 0.85rem;">
                                        {{ $adv->date ? $adv->date->format('d M, Y') : '—' }}
                                    </td>
                                    <td class="text-end fw-bold text-warning-emphasis" style="font-size: 0.95rem;">
                                        ৳{{ number_format($adv->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.75rem;">
                                            {{ $adv->payment_method }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $adv->note ?: '—' }}
                                    </td>
                                    <td class="pe-3 text-end d-print-none">
                                        <button type="button" 
                                                class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-danger btn-delete-advance" 
                                                data-action="{{ route('admin.employee-advance.destroy', $adv) }}"
                                                data-amount="{{ number_format($adv->amount, 2) }}"
                                                title="বাতিল করুন"
                                                style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        এই কর্মচারীর কোনো অগ্রিম গ্রহণের রেকর্ড নেই।
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: ADD ADVANCE FOR THIS EMPLOYEE ================= -->
<div class="modal fade" id="employeeAdvanceModal" tabindex="-1" aria-labelledby="employeeAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">অগ্রিম টাকা দিন - {{ $employee->name }}</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">বর্তমান অগ্রিম ব্যালেন্স: ৳{{ number_format($employee->advance_balance, 2) }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.employee-advance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                প্রদানের তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="date" 
                                   value="{{ date('Y-m-d') }}" 
                                   class="form-control" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                টাকার পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   step="0.01" 
                                   min="1" 
                                   name="amount" 
                                   class="form-control fw-bold text-warning-emphasis" 
                                   placeholder="যেমন: ২০০০" 
                                   required 
                                   style="font-size: 0.88rem;">
                            <small class="text-muted" style="font-size: 0.72rem;">* মাইনাস (-) মান গ্রহণযোগ্য নয়</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                প্রদানের মাধ্যম <span class="text-danger">*</span>
                            </label>
                            <select name="payment_method" class="form-select" required style="font-size: 0.88rem;">
                                <option value="Cash" selected>ক্যাশ (Cash)</option>
                                <option value="bKash">বিকাশ (bKash)</option>
                                <option value="Nagad">নগদ (Nagad)</option>
                                <option value="Bank">ব্যাংক ট্রান্সফার (Bank)</option>
                                <option value="Other">অন্যান্য</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="note" 
                                   class="form-control" 
                                   placeholder="যেমন: পরিবারের খরচের জন্য অগ্রিম..." 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-warning text-dark px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="সংরক্ষণ হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>অগ্রিম নিশ্চিত করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Action Forms for SweetAlert2 -->
<form id="deleteGenericForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // Delete Salary with SweetAlert2
    $(document).on('click', '.btn-delete-salary', function() {
        const actionUrl = $(this).data('action');
        const month = $(this).data('month') || '';

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `${month} মাসের এই বেতন পরিশোধ রেকর্ডটি মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, বাতিল করুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteGenericForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });

    // Delete Advance with SweetAlert2
    $(document).on('click', '.btn-delete-advance', function() {
        const actionUrl = $(this).data('action');
        const amount = $(this).data('amount') || '০.০০';

        Swal.fire({
            title: 'অগ্রিম বাতিল করবেন?',
            text: `৳${amount} টাকার এই অগ্রিম রেকর্ডটি স্থায়ীভাবে মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteGenericForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
