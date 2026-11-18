@extends('admin.layouts.master')

@section('title', 'সকল কর্মচারী ও স্টাফ তালিকা')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-users-gear text-primary"></i>
                <span>সকল কর্মচারী ও স্টাফ তালিকা</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">কর্মচারী ও বেতন</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">কর্মচারী তালিকা</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" 
                    class="btn btn-outline-warning text-dark rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#quickAdvanceModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                <span>অগ্রিম টাকা দিন</span>
            </button>
            <a href="{{ route('admin.employee-salary.create') }}" class="btn btn-outline-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-money-bill-wave"></i>
                <span>বেতন পরিশোধ করুন</span>
            </a>
            <a href="{{ route('admin.employee.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-user-plus"></i>
                <span>নতুন কর্মচারী যোগ করুন</span>
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
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট সক্রিয় কর্মচারী</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['active']) }} জন</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">সর্বমোট: {{ $stats['total'] }} জন</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মাসিক মোট বেতন বাজেট</small>
                        <h4 class="fw-bold mb-0 text-dark">৳{{ number_format($stats['monthly_payroll'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">সকল সক্রিয় কর্মচারীর মূল বেতন</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-hand-holding-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">বর্তমান বকেয়া অগ্রিম</small>
                        <h4 class="fw-bold mb-0 text-warning">৳{{ number_format($stats['advance_balance'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">কর্মচারীদের কাছে পাওনা অগ্রিম</small>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট পরিশোধিত বেতন</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_salary_paid'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">এখন পর্যন্ত মোট বিতরণকৃত বেতন</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.employee.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               class="form-control border-start-0 bg-light" 
                               placeholder="কর্মচারীর নাম, মোবাইল নম্বর বা পদবী দিয়ে খুঁজুন..." 
                               style="font-size: 0.85rem;">
                    </div>
                </div>

                <div class="col-12 col-md-3">
                    <select name="status" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সকল স্ট্যাটাস --</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($status))
                        <a href="{{ route('admin.employee.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কর্মচারীর তথ্য</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পদবী</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মাসিক বেতন</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">বকেয়া অগ্রিম</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 110px;">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 220px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $index => $employee)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-xs flex-shrink-0" 
                                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); font-size: 0.9rem;">
                                        {{ mb_substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.employee.show', $employee) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.9rem;">
                                            {{ $employee->name }}
                                        </a>
                                        @if($employee->mobile)
                                            <small class="text-muted d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                                <i class="fa-solid fa-phone" style="font-size: 0.65rem;"></i>{{ $employee->mobile }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-briefcase text-secondary me-1"></i>{{ $employee->designation_name }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark" style="font-size: 0.9rem;">
                                ৳{{ number_format($employee->monthly_salary, 2) }}
                            </td>
                            <td class="text-end">
                                @if($employee->advance_balance > 0)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.82rem;">
                                        <i class="fa-solid fa-hand-holding-dollar me-1"></i>৳{{ number_format($employee->advance_balance, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted small">৳০.০০</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.employee.toggle-status', $employee) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="badge border-0 {{ $employee->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill cursor-pointer" 
                                            style="font-size: 0.75rem;" 
                                            title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন">
                                        <i class="fa-solid {{ $employee->status ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1"></i>
                                        {{ $employee->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                    </button>
                                </form>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Quick Advance Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-warning text-dark rounded-3 px-2 py-1 open-advance-modal" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#quickAdvanceModal"
                                            data-id="{{ $employee->id }}"
                                            data-name="{{ $employee->name }}"
                                            data-salary="{{ $employee->monthly_salary }}"
                                            data-balance="{{ $employee->advance_balance }}"
                                            title="অগ্রিম টাকা দিন"
                                            style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-hand-holding-dollar text-warning"></i>
                                        <span class="d-none d-lg-inline ms-1">অগ্রিম</span>
                                    </button>

                                    <!-- Pay Salary Button -->
                                    <a href="{{ route('admin.employee-salary.create', ['employee_id' => $employee->id]) }}" 
                                       class="btn btn-sm btn-outline-success rounded-3 px-2 py-1" 
                                       title="বেতন পরিশোধ করুন"
                                       style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-money-bill-wave"></i>
                                        <span class="d-none d-lg-inline ms-1">বেতন</span>
                                    </a>

                                    <!-- View Profile & Ledger -->
                                    <a href="{{ route('admin.employee.show', $employee) }}" 
                                       class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-primary" 
                                       title="হিসাবের খাতা / লেজার"
                                       style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.employee.edit', $employee) }}" 
                                       class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-secondary" 
                                       title="এডিট"
                                       style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <!-- Delete with SweetAlert2 -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-danger btn-delete-employee" 
                                            data-action="{{ route('admin.employee.destroy', $employee) }}"
                                            data-name="{{ $employee->name }}"
                                            title="মুছে ফেলুন"
                                            style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-users-slash fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                    <h6 class="fw-bold mb-1">কোনো কর্মচারী পাওয়া যায়নি</h6>
                                    <p class="small mb-3">নতুন কর্মচারী যুক্ত করে তার মাসিক বেতন ও অগ্রিমের হিসাব সংরক্ষণ করুন।</p>
                                    <a href="{{ route('admin.employee.create') }}" class="btn btn-primary rounded-pill btn-sm px-3">
                                        <i class="fa-solid fa-plus me-1"></i>প্রথম কর্মচারী যোগ করুন
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    মোট {{ $employees->total() }} জনের মধ্যে {{ $employees->firstItem() }} থেকে {{ $employees->lastItem() }} দেখানো হচ্ছে
                </small>
                <div>
                    {{ $employees->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ================= QUICK ADVANCE MODAL ================= -->
<div class="modal fade" id="quickAdvanceModal" tabindex="-1" aria-labelledby="quickAdvanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">কর্মচারীকে অগ্রিম টাকা দিন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">অগ্রিম টাকা মাস শেষে বেতনের সময় কেটে রাখা যাবে</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.employee-advance.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Employee Select -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                কর্মচারী নির্বাচন করুন <span class="text-danger">*</span>
                            </label>
                            <select name="employee_id" id="modalEmployeeSelect" class="form-select select2" required style="font-size: 0.88rem;">
                                <option value="">-- কর্মচারী নির্বাচন করুন --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" data-balance="{{ $emp->advance_balance }}">
                                        {{ $emp->name }} ({{ $emp->designation }}) - বর্তমান অগ্রিম: ৳{{ number_format($emp->advance_balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
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

                        <!-- Amount (min: 1, cannot be negative) -->
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                অগ্রিমের পরিমাণ (৳) <span class="text-danger">*</span>
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

                        <!-- Payment Method -->
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

                        <!-- Note -->
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                মন্তব্য / নোট <small class="text-muted">(ঐচ্ছিক)</small>
                            </label>
                            <input type="text" 
                                   name="note" 
                                   class="form-control" 
                                   placeholder="যেমন: পারিবারিক চিকিৎসার জন্য খোরকী অগ্রিম..." 
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

<!-- Hidden Delete Employee Form -->
<form id="deleteEmployeeForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
$(document).ready(function() {
    // If user clicked row's "+ অগ্রিম" button, auto-select that employee in modal
    $('.open-advance-modal').on('click', function() {
        const empId = $(this).data('id');
        if (empId) {
            $('#modalEmployeeSelect').val(empId).trigger('change');
        }
    });

    // Delete Employee Confirmation with SweetAlert2
    $(document).on('click', '.btn-delete-employee', function() {
        const actionUrl = $(this).data('action');
        const name = $(this).data('name');

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `কর্মচারী "${name}" এবং তার সমস্ত রেকর্ড স্থায়ীভাবে মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteEmployeeForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
