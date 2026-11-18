@extends('admin.layouts.master')

@section('title', 'মাসিক বেতন পরিশোধের তালিকা')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-money-bill-wave text-primary"></i>
                <span>মাসিক বেতন পরিশোধের তালিকা</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}" class="text-decoration-none text-muted">কর্মচারী ও বেতন</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বেতন পরিশোধ তালিকা</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.employee.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-users"></i>
                <span>কর্মচারী তালিকা</span>
            </a>
            <a href="{{ route('admin.employee-salary.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন বেতন পরিশোধ করুন</span>
            </a>
        </div>
    </div>

    <!-- Monthly Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-receipt fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">পরিশোধ সংখ্যা</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_payments']) }} টি</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">নির্বাচিত মাসে বেতন প্রদান</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-money-bill-transfer fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট পরিশোধিত বেতন</small>
                        <h4 class="fw-bold mb-0 text-success">৳{{ number_format($stats['total_paid'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">নিট পরিশোধকৃত টাকা</small>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">অগ্রিম কর্তন</small>
                        <h4 class="fw-bold mb-0 text-warning">৳{{ number_format($stats['total_deductions'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">বেতন থেকে সমন্বয়কৃত অগ্রিম</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fa-solid fa-gift fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট বোনাস</small>
                        <h4 class="fw-bold mb-0 text-dark">৳{{ number_format($stats['total_bonus'], 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.72rem;">অতিরিক্ত বোনাস প্রদান</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.employee-salary.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Search text -->
                <div class="col-12 col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               class="form-control border-start-0 bg-light" 
                               placeholder="কর্মচারীর নাম, মোবাইল দিয়ে খুঁজুন..." 
                               style="font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Month Filter -->
                <div class="col-12 col-md-3">
                    <input type="month" 
                           name="salary_month" 
                           value="{{ $salaryMonth }}" 
                           class="form-control bg-light" 
                           style="font-size: 0.85rem;"
                           title="বেতনের মাস নির্বাচন করুন">
                </div>

                <!-- Employee Filter -->
                <div class="col-12 col-md-3">
                    <select name="employee_id" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সকল কর্মচারী --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit & Reset -->
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($employeeId) || !empty($salaryMonth))
                        <a href="{{ route('admin.employee-salary.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কর্মচারী</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">বেতনের মাস</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পরিশোধের তারিখ</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">মূল বেতন</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">বোনাস</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end text-warning-emphasis" style="font-size: 0.75rem;">অগ্রিম কর্তন (-)</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem;">নিট প্রদেয় বেতন</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মাধ্যম</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 130px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $index => $sal)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ $salaries->firstItem() + $index }}
                            </td>
                            <td>
                                <div>
                                    <a href="{{ route('admin.employee.show', $sal->employee_id) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size: 0.88rem;">
                                        {{ $sal->employee?->name ?? '—' }}
                                    </a>
                                    <small class="text-muted" style="font-size: 0.72rem;">
                                        {{ $sal->employee?->designation }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill" style="font-size: 0.78rem;">
                                    {{ $sal->salary_month_name }}
                                </span>
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
                            <td class="pe-4 text-end">
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
                                            data-name="{{ $sal->employee?->name }}"
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
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-money-bill-slash fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                    <h6 class="fw-bold mb-1">কোনো বেতন পরিশোধের রেকর্ড পাওয়া যায়নি</h6>
                                    <p class="small mb-3">মাস শেষ হলে কর্মচারীদের বেতন পরিশোধ এন্ট্রি করে রসিদ প্রিন্ট করুন।</p>
                                    <a href="{{ route('admin.employee-salary.create') }}" class="btn btn-primary rounded-pill btn-sm px-3">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন বেতন পরিশোধ করুন
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($salaries->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    মোট {{ $salaries->total() }} টির মধ্যে {{ $salaries->firstItem() }} থেকে {{ $salaries->lastItem() }} দেখানো হচ্ছে
                </small>
                <div>
                    {{ $salaries->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
<!-- Hidden Delete Form -->
<form id="deleteSalaryForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-delete-salary', function() {
        const actionUrl = $(this).data('action');
        const name = $(this).data('name') || 'কর্মচারী';
        const month = $(this).data('month') || '';

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `"${name}"-এর ${month} মাসের বেতন পরিশোধ রেকর্ডটি মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, বাতিল করুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteSalaryForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
