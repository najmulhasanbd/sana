@extends('admin.layouts.master')

@section('title', 'দৈনিক খরচ খাতা (Daily Expenses)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-receipt text-primary"></i>
                <span>দৈনিক প্রাতিষ্ঠানিক খরচ খাতা (Daily Expenses)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">হিসাব ও ব্যয়</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">দৈনিক খরচ</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.expense-category.index') }}" class="btn btn-light border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-tags text-primary"></i>
                <span>খরচের খাতসমূহ</span>
            </a>
            <a href="{{ route('admin.report.profit-loss') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-chart-pie"></i>
                <span>লাভ-ক্ষতি রিপোর্ট</span>
            </a>
            <button type="button" 
                    class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#createExpenseModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন খরচ লিখুন</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Today's Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-calendar-day fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">আজকের মোট খরচ</small>
                        <h4 class="fw-bold mb-0 text-dark">৳ {{ number_format($todayAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- This Month's Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-calendar-days fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">চলতি মাসের খরচ</small>
                        <h4 class="fw-bold mb-0 text-primary">৳ {{ number_format($thisMonthAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtered Total Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
                        <i class="fa-solid fa-filter-circle-dollar fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">ফিল্টারকৃত খরচ ({{ $filteredCount }} টি)</small>
                        <h4 class="fw-bold mb-0 text-danger">৳ {{ number_format($filteredTotalAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- All-Time Total Expense -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                        <i class="fa-solid fa-wallet fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট প্রাতিষ্ঠানিক খরচ</small>
                        <h4 class="fw-bold mb-0 text-secondary">৳ {{ number_format($totalAllTimeAmount, 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-3 p-md-4">
            <!-- Quick Date Preset Chips -->
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-3 border-bottom">
                <span class="text-secondary fw-semibold small me-1"><i class="fa-regular fa-clock me-1"></i>দ্রুত ফিল্টার:</span>
                @php
                    $presets = [
                        'all' => 'সব সময়',
                        'today' => 'আজকে',
                        'yesterday' => 'গতকাল',
                        'this_week' => 'চলতি সপ্তাহ',
                        'this_month' => 'চলতি মাস',
                        'last_month' => 'গত মাস',
                        'this_year' => 'চলতি বছর',
                    ];
                @endphp
                @foreach($presets as $key => $label)
                    <a href="{{ route('admin.expense.index', array_merge(request()->except(['date_preset', 'from_date', 'to_date', 'page']), ['date_preset' => $key])) }}" 
                       class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ $datePreset === $key ? 'btn-primary' : 'btn-light border text-secondary' }}"
                       style="font-size: 0.8rem;">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Detailed Filter Form -->
            <form action="{{ route('admin.expense.index') }}" method="GET" class="row g-2 align-items-end">
                <input type="hidden" name="date_preset" value="{{ $datePreset === 'all' ? '' : ($fromDate || $toDate ? 'custom' : $datePreset) }}">

                <!-- Search Input -->
                <div class="col-12 col-md-3">
                    <label class="form-label text-secondary small fw-semibold mb-1">কীওয়ার্ড দিয়ে খুঁজুন</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="খরচের শিরোনাম বা নোট..." 
                               style="font-size: 0.85rem;">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label text-secondary small fw-semibold mb-1">খরচের খাত</label>
                    <select name="category_id" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সব খাত --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label text-secondary small fw-semibold mb-1">পেমেন্ট মাধ্যম</label>
                    <select name="payment_method" class="form-select bg-light" style="font-size: 0.85rem;">
                        <option value="">-- সব মাধ্যম --</option>
                        <option value="Cash" {{ $paymentMethod === 'Cash' ? 'selected' : '' }}>নগদ (Cash)</option>
                        <option value="bKash" {{ $paymentMethod === 'bKash' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                        <option value="Nagad" {{ $paymentMethod === 'Nagad' ? 'selected' : '' }}>নগদ (Nagad)</option>
                        <option value="Bank" {{ $paymentMethod === 'Bank' ? 'selected' : '' }}>ব্যাংক (Bank)</option>
                        <option value="Cheque" {{ $paymentMethod === 'Cheque' ? 'selected' : '' }}>চেক (Cheque)</option>
                        <option value="Other" {{ $paymentMethod === 'Other' ? 'selected' : '' }}>অন্যান্য (Other)</option>
                    </select>
                </div>

                <!-- From Date -->
                <div class="col-6 col-md-2">
                    <label class="form-label text-secondary small fw-semibold mb-1">হতে (তারিখ)</label>
                    <input type="date" 
                           name="from_date" 
                           value="{{ $fromDate ?? '' }}" 
                           class="form-control bg-light" 
                           style="font-size: 0.85rem;">
                </div>

                <!-- To Date -->
                <div class="col-6 col-md-2">
                    <label class="form-label text-secondary small fw-semibold mb-1">পর্যন্ত (তারিখ)</label>
                    <input type="date" 
                           name="to_date" 
                           value="{{ $toDate ?? '' }}" 
                           class="form-control bg-light" 
                           style="font-size: 0.85rem;">
                </div>

                <!-- Buttons -->
                <div class="col-12 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold shadow-sm" title="ফিল্টার প্রয়োগ করুন" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search', 'category_id', 'payment_method', 'from_date', 'to_date']) || $datePreset !== 'all')
                        <a href="{{ route('admin.expense.index') }}" class="btn btn-light border py-2 px-2.5 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Expense Data Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 50px;">#</th>
                        <th class="py-3" style="width: 120px;">তারিখ</th>
                        <th class="py-3">খরচের খাত</th>
                        <th class="py-3">বিবরণ / শিরোনাম</th>
                        <th class="py-3">পেমেন্ট মাধ্যম</th>
                        <th class="py-3 text-end">পরিমাণ (৳)</th>
                        <th class="pe-4 py-3 text-end" style="width: 120px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">
                                {{ $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage() }}
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">
                                    {{ $expense->date ? $expense->date->format('d M, Y') : '—' }}
                                </div>
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    {{ $expense->date ? $expense->date->translatedFormat('l') : '' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                    <i class="fa-solid fa-tag me-1"></i>{{ $expense->category?->name ?? 'অনির্দিষ্ট খাত' }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $expense->title ?: '—' }}</div>
                                @if($expense->note)
                                    <small class="text-muted d-block text-truncate" style="max-width: 320px; font-size: 0.78rem;">
                                        {{ $expense->note }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1">
                                    <i class="fa-solid fa-money-bill-wave me-1 text-muted"></i>{{ $expense->payment_method }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">
                                ৳ {{ number_format((float) $expense->amount, 2) }}
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-2 text-primary edit-expense-btn" 
                                            title="সম্পাদনা"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editExpenseModal"
                                            data-id="{{ $expense->id }}"
                                            data-category-id="{{ $expense->expense_category_id }}"
                                            data-date="{{ $expense->date ? $expense->date->format('Y-m-d') : '' }}"
                                            data-amount="{{ $expense->amount }}"
                                            data-payment-method="{{ $expense->payment_method }}"
                                            data-title="{{ $expense->title ?? '' }}"
                                            data-note="{{ $expense->note ?? '' }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('admin.expense.destroy', $expense->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই খরচের এন্ট্রিটি মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-2 text-danger" title="মুছে ফেলুন">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-receipt fs-2 mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-1 fw-semibold">কোনো খরচের হিসাব পাওয়া যায়নি।</p>
                                    <small class="text-muted">নতুন খরচ যুক্ত করতে উপরে "নতুন খরচ লিখুন" বাটনে ক্লিক করুন।</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($expenses->isNotEmpty())
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="5" class="ps-4 py-3 text-end text-secondary">এই পাতার মোট খরচ:</td>
                            <td class="py-3 text-end text-danger fs-6">
                                ৳ {{ number_format($expenses->sum('amount'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ================= CREATE EXPENSE MODAL ================= -->
<div class="modal fade" id="createExpenseModal" tabindex="-1" aria-labelledby="createExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">নতুন খরচ লিখুন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">প্রতিদিনের প্রাতিষ্ঠানিক খরচের ভাউচার এন্ট্রি</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.expense.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Date -->
                        <div class="col-12 col-sm-6">
                            <label for="createExpenseDate" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                খরচের তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="createExpenseDate" 
                                   name="date" 
                                   value="{{ old('date', date('Y-m-d')) }}" 
                                   class="form-control @error('date') is-invalid @enderror" 
                                   required 
                                   style="font-size: 0.88rem;">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="col-12 col-sm-6">
                            <label for="createExpenseCategory" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                খরচের খাত <span class="text-danger">*</span>
                            </label>
                            <select id="createExpenseCategory" 
                                    name="expense_category_id" 
                                    class="form-select @error('expense_category_id') is-invalid @enderror" 
                                    required 
                                    style="font-size: 0.88rem;">
                                <option value="">-- খাত নির্বাচন করুন --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-sm-6">
                            <label for="createExpenseAmount" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                টাকার পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">৳</span>
                                <input type="number" 
                                       step="0.01" 
                                       id="createExpenseAmount" 
                                       name="amount" 
                                       value="{{ old('amount') }}" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       placeholder="0.00" 
                                       required 
                                       style="font-size: 0.88rem;">
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-12 col-sm-6">
                            <label for="createExpenseMethod" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পেমেন্ট মাধ্যম <span class="text-danger">*</span>
                            </label>
                            <select id="createExpenseMethod" 
                                    name="payment_method" 
                                    class="form-select @error('payment_method') is-invalid @enderror" 
                                    required 
                                    style="font-size: 0.88rem;">
                                <option value="Cash" {{ old('payment_method') === 'Cash' ? 'selected' : '' }}>নগদ (Cash)</option>
                                <option value="bKash" {{ old('payment_method') === 'bKash' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                                <option value="Nagad" {{ old('payment_method') === 'Nagad' ? 'selected' : '' }}>নগদ (Nagad)</option>
                                <option value="Bank" {{ old('payment_method') === 'Bank' ? 'selected' : '' }}>ব্যাংক ট্রান্সফার (Bank)</option>
                                <option value="Cheque" {{ old('payment_method') === 'Cheque' ? 'selected' : '' }}>চেক (Cheque)</option>
                                <option value="Other" {{ old('payment_method') === 'Other' ? 'selected' : '' }}>অন্যান্য (Other)</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="col-12">
                            <label for="createExpenseTitle" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                শিরোনাম / বিবরণ (ঐচ্ছিক)
                            </label>
                            <input type="text" 
                                   id="createExpenseTitle" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   placeholder="যেমন: হ্যাচারি বিদ্যুৎ বিল পরিশোধ, পলি ও গ্যাস কেনা..." 
                                   style="font-size: 0.88rem;">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Note -->
                        <div class="col-12">
                            <label for="createExpenseNote" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                বিস্তারিত নোট / ভাউচার রেফারেন্স (ঐচ্ছিক)
                            </label>
                            <textarea id="createExpenseNote" 
                                      name="note" 
                                      rows="2" 
                                      class="form-control" 
                                      placeholder="প্রয়োজনীয় অতিরিক্ত তথ্য..." 
                                      style="font-size: 0.88rem;">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="সংরক্ষণ হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= EDIT EXPENSE MODAL ================= -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">খরচ সম্পাদনা</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">খরচের পরিমাণ বা বিবরণ সংশোধন করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editExpenseForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Date -->
                        <div class="col-12 col-sm-6">
                            <label for="editExpenseDate" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                খরচের তারিখ <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   id="editExpenseDate" 
                                   name="date" 
                                   class="form-control" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- Category -->
                        <div class="col-12 col-sm-6">
                            <label for="editExpenseCategory" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                খরচের খাত <span class="text-danger">*</span>
                            </label>
                            <select id="editExpenseCategory" 
                                    name="expense_category_id" 
                                    class="form-select" 
                                    required 
                                    style="font-size: 0.88rem;">
                                <option value="">-- খাত নির্বাচন করুন --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-12 col-sm-6">
                            <label for="editExpenseAmount" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                টাকার পরিমাণ (৳) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0">৳</span>
                                <input type="number" 
                                       step="0.01" 
                                       id="editExpenseAmount" 
                                       name="amount" 
                                       class="form-control" 
                                       required 
                                       style="font-size: 0.88rem;">
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-12 col-sm-6">
                            <label for="editExpenseMethod" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                পেমেন্ট মাধ্যম <span class="text-danger">*</span>
                            </label>
                            <select id="editExpenseMethod" 
                                    name="payment_method" 
                                    class="form-select" 
                                    required 
                                    style="font-size: 0.88rem;">
                                <option value="Cash">নগদ (Cash)</option>
                                <option value="bKash">বিকাশ (bKash)</option>
                                <option value="Nagad">নগদ (Nagad)</option>
                                <option value="Bank">ব্যাংক ট্রান্সফার (Bank)</option>
                                <option value="Cheque">চেক (Cheque)</option>
                                <option value="Other">অন্যান্য (Other)</option>
                            </select>
                        </div>

                        <!-- Title -->
                        <div class="col-12">
                            <label for="editExpenseTitle" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                শিরোনাম / বিবরণ
                            </label>
                            <input type="text" 
                                   id="editExpenseTitle" 
                                   name="title" 
                                   class="form-control" 
                                   style="font-size: 0.88rem;">
                        </div>

                        <!-- Note -->
                        <div class="col-12">
                            <label for="editExpenseNote" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                বিস্তারিত নোট / ভাউচার রেফারেন্স
                            </label>
                            <textarea id="editExpenseNote" 
                                      name="note" 
                                      rows="2" 
                                      class="form-control" 
                                      style="font-size: 0.88rem;"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="আপডেট হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-check me-1"></i>আপডেট করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.edit-expense-btn');
        const editForm = document.getElementById('editExpenseForm');
        const editDateInput = document.getElementById('editExpenseDate');
        const editCategorySelect = document.getElementById('editExpenseCategory');
        const editAmountInput = document.getElementById('editExpenseAmount');
        const editMethodSelect = document.getElementById('editExpenseMethod');
        const editTitleInput = document.getElementById('editExpenseTitle');
        const editNoteInput = document.getElementById('editExpenseNote');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const categoryId = this.getAttribute('data-category-id');
                const date = this.getAttribute('data-date');
                const amount = this.getAttribute('data-amount');
                const method = this.getAttribute('data-payment-method');
                const title = this.getAttribute('data-title');
                const note = this.getAttribute('data-note');

                editForm.action = "{{ url('admin/expenses') }}/" + id;
                editDateInput.value = date;
                editCategorySelect.value = categoryId;
                editAmountInput.value = amount;
                editMethodSelect.value = method;
                editTitleInput.value = title;
                editNoteInput.value = note;
            });
        });
    });
</script>
@endpush
@endsection
