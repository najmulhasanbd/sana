@extends('admin.layouts.master')

@section('title', 'ক্যাশ ও আয়-ব্যয় খাতা (Cash Book Ledger)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-wallet text-success"></i>
                <span>ক্যাশ ও আয়-ব্যয় খাতা (Cash Book Ledger)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">হিসাব ও ব্যয়</li>
                    <li class="breadcrumb-item active text-success fw-semibold" aria-current="page">ক্যাশ ও আয়-ব্যয় খাতা</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <!-- Add Deposit Button -->
            <button type="button" 
                    class="btn btn-success rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    onclick="openCreateModal('deposit')"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-circle-plus"></i>
                <span>টাকা জমা করুন (Cash In)</span>
            </button>

            <!-- Add Expense Button -->
            <button type="button" 
                    class="btn btn-danger rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    onclick="openCreateModal('expense')"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-circle-minus"></i>
                <span>খরচ লিখুন (Cash Out)</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Filtered Total Deposit -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-arrow-down-left fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট জমা (Cash In)</small>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">জমা</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-success mt-1">৳ {{ number_format($totalDeposit, 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">সর্বমোট আজ পর্যন্ত: ৳ {{ number_format($lifetimeDeposit, 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtered Total Expense -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                        <i class="fa-solid fa-arrow-up-right fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">মোট খরচ (Cash Out)</small>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.65rem;">খরচ</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-danger mt-1">৳ {{ number_format($totalExpense, 2) }}</h4>
                        <small class="text-muted" style="font-size: 0.75rem;">সর্বমোট আজ পর্যন্ত: ৳ {{ number_format($lifetimeExpense, 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Net Balance / Profit -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 position-relative overflow-hidden" 
                 style="border-left: 4px solid {{ $netBalance >= 0 ? '#0284c7' : '#e11d48' }} !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 52px; height: 52px; background: {{ $netBalance >= 0 ? 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)' : 'linear-gradient(135deg, #e11d48 0%, #be123c 100%)' }};">
                        <i class="fa-solid fa-scale-balanced fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">ক্যাশ উদ্বৃত্ত / ব্যালেন্স (Balance)</small>
                            <span class="badge {{ $netBalance >= 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}" style="font-size: 0.65rem;">
                                {{ $netBalance >= 0 ? 'উদ্বৃত্ত (Surplus)' : 'ঘাটতি (Deficit)' }}
                            </span>
                        </div>
                        <h4 class="fw-bold mb-0 {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }} mt-1">
                            ৳ {{ number_format($netBalance, 2) }}
                        </h4>
                        <small class="text-muted" style="font-size: 0.75rem;">সর্বমোট ক্যাশ হাতে আছে: ৳ {{ number_format($lifetimeBalance, 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('admin.cash-book.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <!-- Search Input -->
                    <div class="col-12 col-md-3">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.78rem;">খাত / বিবরণ দিয়ে খুঁজুন</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control bg-light border-start-0" 
                                   placeholder="খাত, নোট বা রেফারেন্স..." 
                                   value="{{ $search }}"
                                   style="font-size: 0.85rem;">
                        </div>
                    </div>

                    <!-- Type Filter -->
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.78rem;">লেনদেনের ধরণ</label>
                        <select name="type" class="form-select bg-light" style="font-size: 0.85rem;">
                            <option value="">সব ধরণ</option>
                            <option value="deposit" {{ $type === 'deposit' ? 'selected' : '' }}>🟢 জমা (Cash In)</option>
                            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>🔴 খরচ (Cash Out)</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.78rem;">খাত / উদ্দেশ্য</label>
                        <select name="category" class="form-select bg-light" style="font-size: 0.85rem;">
                            <option value="">সকল খাত</option>
                            @foreach($existingCategories as $cat)
                                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Preset Filter -->
                    <div class="col-6 col-md-2">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.78rem;">সময়সীমা (Preset)</label>
                        <select name="date_preset" id="datePresetSelect" class="form-select bg-light" style="font-size: 0.85rem;">
                            <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>আজকের</option>
                            <option value="yesterday" {{ $datePreset === 'yesterday' ? 'selected' : '' }}>গতকালের</option>
                            <option value="this_week" {{ $datePreset === 'this_week' ? 'selected' : '' }}>চলতি সপ্তাহ</option>
                            <option value="this_month" {{ $datePreset === 'this_month' ? 'selected' : '' }}>চলতি মাস</option>
                            <option value="last_month" {{ $datePreset === 'last_month' ? 'selected' : '' }}>গত মাস</option>
                            <option value="this_year" {{ $datePreset === 'this_year' ? 'selected' : '' }}>চলতি বছর</option>
                            <option value="custom" {{ $datePreset === 'custom' ? 'selected' : '' }}>নির্দিষ্ট তারিখ...</option>
                            <option value="all" {{ $datePreset === 'all' ? 'selected' : '' }}>সব সময়</option>
                        </select>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-6 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1 rounded-3 fw-semibold shadow-sm" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-filter me-1"></i> ফিল্টার
                        </button>
                        <a href="{{ route('admin.cash-book.index') }}" class="btn btn-light border rounded-3 fw-semibold text-muted" title="ফিল্টার রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>

                    <!-- Custom Date Range Row (Shown when custom is selected) -->
                    <div class="col-12 {{ $datePreset === 'custom' ? '' : 'd-none' }}" id="customDateRow">
                        <div class="row g-2 pt-2 border-top">
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.75rem;">হতে তারিখ (From)</label>
                                <input type="date" name="from_date" class="form-control bg-light" value="{{ $fromDate }}" style="font-size: 0.85rem;">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.75rem;">পর্যন্ত তারিখ (To)</label>
                                <input type="date" name="to_date" class="form-control bg-light" value="{{ $toDate }}" style="font-size: 0.85rem;">
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.75rem;">পেমেন্ট মাধ্যম</label>
                                <select name="payment_method" class="form-select bg-light" style="font-size: 0.85rem;">
                                    <option value="">সব মাধ্যম</option>
                                    <option value="নগদ" {{ $paymentMethod === 'নগদ' ? 'selected' : '' }}>নগদ (Cash)</option>
                                    <option value="বিকাশ" {{ $paymentMethod === 'বিকাশ' ? 'selected' : '' }}>বিকাশ (bKash)</option>
                                    <option value="নগদ (Nagad)" {{ $paymentMethod === 'নগদ (Nagad)' ? 'selected' : '' }}>নগদ (Nagad)</option>
                                    <option value="রকেট" {{ $paymentMethod === 'রকেট' ? 'selected' : '' }}>রকেট (Rocket)</option>
                                    <option value="ব্যাংক" {{ $paymentMethod === 'ব্যাংক' ? 'selected' : '' }}>ব্যাংক (Bank)</option>
                                    <option value="অন্যান্য" {{ $paymentMethod === 'অন্যান্য' ? 'selected' : '' }}>অন্যান্য</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table Card (Clean & Focused on Important Columns) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-0 d-flex align-items-center justify-content-between">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-list-check text-primary"></i>
                <span>ক্যাশ লেনদেনের সারসংক্ষেপ (মোট এন্ট্রি: {{ $transactions->total() }} টি)</span>
            </h6>
            <small class="text-muted d-none d-md-inline" style="font-size: 0.78rem;">
                <i class="fa-regular fa-hand-pointer me-1"></i> বিস্তারিত তথ্যের জন্য লাইনের <i class="fa-solid fa-eye text-info mx-1"></i> আইকনে চাপুন
            </small>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-muted border-top border-bottom" style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="ps-4 text-center" style="width: 50px;">#</th>
                        <th style="min-width: 110px;">তারিখ</th>
                        <th style="min-width: 100px;" class="text-center">ধরণ</th>
                        <th style="min-width: 220px;">খাত / উদ্দেশ্য</th>
                        <th style="min-width: 140px;" class="text-end">টাকার পরিমাণ</th>
                        <th class="pe-4 text-center" style="width: 130px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $index => $item)
                        <tr>
                            <!-- SL -->
                            <td class="ps-4 text-center text-muted fw-semibold">
                                {{ $transactions->firstItem() + $index }}
                            </td>

                            <!-- Date -->
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->date ? $item->date->format('d/m/Y') : '-' }}</span>
                                <br>
                                <small class="text-muted" style="font-size: 0.72rem;">{{ $item->date ? $item->date->format('l') : '' }}</small>
                            </td>

                            <!-- Type -->
                            <td class="text-center">
                                @if($item->isDeposit())
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-arrow-down-left me-1"></i> জমা
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-arrow-up-right me-1"></i> খরচ
                                    </span>
                                @endif
                            </td>

                            <!-- Category & Subtle Payment Info -->
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span class="fw-bold text-dark">{{ $item->category }}</span>
                                    <span class="badge bg-light text-muted border px-2 py-0.5 rounded" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-wallet me-1 text-secondary" style="font-size: 0.65rem;"></i>{{ $item->payment_method }}
                                    </span>
                                </div>
                                @if($item->note)
                                    <small class="text-muted text-truncate d-block mt-0.5" style="max-width: 320px; font-size: 0.75rem;" title="{{ $item->note }}">
                                        {{ Str::limit($item->note, 45) }}
                                    </small>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="text-end">
                                <span class="fw-bold fs-6 {{ $item->isDeposit() ? 'text-success' : 'text-danger' }}">
                                    {{ $item->isDeposit() ? '+' : '-' }}৳ {{ number_format($item->amount, 2) }}
                                </span>
                            </td>

                            <!-- Actions (View Modal, Edit Modal, Delete) -->
                            <td class="pe-4 text-center">
                                <div class="d-inline-flex gap-1">
                                    <!-- View Details Modal Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-2 text-info" 
                                            title="বিস্তারিত তথ্য দেখুন"
                                            onclick='openViewModal(@json($item), "{{ $item->creator?->name ?? 'অজানা' }}", "{{ $item->created_at ? $item->created_at->format('d/m/Y h:i A') : '-' }}")'>
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <!-- Edit Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-2 text-primary" 
                                            title="সম্পাদনা করুন"
                                            onclick='openEditModal(@json($item))'>
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.cash-book.destroy', $item->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('আপনি কি নিশ্চিত যে এই {{ $item->isDeposit() ? 'জমা' : 'খরচ' }} এন্ট্রিটি মুছে ফেলতে চান?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-sm btn-light border rounded-2 text-danger" 
                                                title="মুছে ফেলুন">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                        <i class="fa-solid fa-wallet fs-2 text-secondary"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">কোনো লেনদেনের হিসাব পাওয়া যায়নি</h6>
                                    <p class="mb-3" style="font-size: 0.82rem;">ক্যাশ খাতা সম্পূর্ণ স্বাধীন। নতুন জমা বা খরচ যুক্ত করতে উপরের বাটন ব্যবহার করুন।</p>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-success rounded-pill px-3" onclick="openCreateModal('deposit')">
                                            <i class="fa-solid fa-circle-plus me-1"></i> টাকা জমা করুন
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" onclick="openCreateModal('expense')">
                                            <i class="fa-solid fa-circle-minus me-1"></i> খরচ লিখুন
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    দেখানো হচ্ছে {{ $transactions->firstItem() }} থেকে {{ $transactions->lastItem() }} (সর্বমোট {{ $transactions->total() }} টি)
                </small>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ========================================== -->
<!-- VIEW CASH BOOK DETAILS MODAL (POPUP)       -->
<!-- ========================================== -->
<div class="modal fade" id="viewCashBookModal" tabindex="-1" aria-labelledby="viewCashBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="viewCashBookModalLabel">
                    <i class="fa-solid fa-circle-info text-primary"></i>
                    <span>লেনদেনের পূর্ণাঙ্গ বিবরণ</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Amount Banner Card -->
                <div class="p-3 rounded-4 mb-3 text-center" id="viewBannerBg" style="background-color: #ecfdf5;">
                    <div class="mb-2">
                        <span class="badge px-3 py-1.5 rounded-pill fw-semibold" id="viewTypeBadge" style="font-size: 0.8rem;">
                            <!-- Type badge -->
                        </span>
                    </div>
                    <h2 class="fw-bold mb-1" id="viewAmountDisplay">৳ 0.00</h2>
                    <small class="text-muted fw-semibold" id="viewDateDisplay">-</small>
                </div>

                <!-- Detailed Information List -->
                <div class="list-group list-group-flush rounded-3 border">
                    <!-- Category / Purpose -->
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.82rem;">
                            <i class="fa-solid fa-tag me-1 text-primary"></i> খাত / উদ্দেশ্য:
                        </span>
                        <span class="fw-bold text-dark" id="viewCategoryDisplay" style="font-size: 0.9rem;">-</span>
                    </div>

                    <!-- Payment Method -->
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.82rem;">
                            <i class="fa-solid fa-credit-card me-1 text-primary"></i> পেমেন্ট মাধ্যম:
                        </span>
                        <span class="badge bg-light text-dark border px-2.5 py-1" id="viewPaymentMethodDisplay" style="font-size: 0.82rem;">-</span>
                    </div>

                    <!-- Reference No -->
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.82rem;">
                            <i class="fa-solid fa-hashtag me-1 text-primary"></i> রেফারেন্স / ভাউচার নং:
                        </span>
                        <span class="fw-semibold text-primary" id="viewReferenceDisplay" style="font-size: 0.85rem;">-</span>
                    </div>

                    <!-- Recorded By (User) -->
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.82rem;">
                            <i class="fa-regular fa-user me-1 text-primary"></i> এন্ট্রি করেছেন:
                        </span>
                        <span class="text-dark fw-semibold" id="viewCreatorDisplay" style="font-size: 0.82rem;">-</span>
                    </div>

                    <!-- Created At Timestamp -->
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="text-muted fw-semibold" style="font-size: 0.82rem;">
                            <i class="fa-regular fa-clock me-1 text-primary"></i> এন্ট্রির সময়:
                        </span>
                        <span class="text-muted" id="viewCreatedAtDisplay" style="font-size: 0.8rem;">-</span>
                    </div>

                    <!-- Note / Description -->
                    <div class="list-group-item py-2.5 px-3 bg-light-subtle">
                        <span class="text-muted fw-semibold d-block mb-1" style="font-size: 0.82rem;">
                            <i class="fa-regular fa-comment-dots me-1 text-primary"></i> বিস্তারিত নোট / বিবরণ:
                        </span>
                        <div class="p-2.5 bg-white rounded-2 border text-dark" id="viewNoteDisplay" style="font-size: 0.85rem; min-height: 48px; white-space: pre-wrap; line-height: 1.5;">-</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold d-flex align-items-center gap-1 shadow-sm" onclick="editFromViewModal()">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>সম্পাদনা করুন</span>
                </button>
                <button type="button" class="btn btn-light rounded-3 px-4 py-2 text-muted fw-semibold" data-bs-dismiss="modal">বন্ধ করুন</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- CREATE CASH BOOK TRANSACTION MODAL         -->
<!-- ========================================== -->
<div class="modal fade" id="createCashBookModal" tabindex="-1" aria-labelledby="createCashBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="{{ route('admin.cash-book.store') }}" method="POST" id="createCashBookForm">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="createCashBookModalLabel">
                        <i class="fa-solid fa-circle-plus text-success" id="createModalIcon"></i>
                        <span id="createModalTitle">নতুন জমা / খরচ যুক্ত করুন</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Type Selector (Radio Toggle) -->
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold mb-2" style="font-size: 0.8rem;">লেনদেনের ধরণ নির্ধারণ করুন *</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="type" id="createTypeDeposit" value="deposit" checked onchange="handleTypeChange('create', 'deposit')">
                            <label class="btn btn-outline-success flex-fill py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="createTypeDeposit">
                                <i class="fa-solid fa-circle-plus"></i> টাকা জমা (Cash In)
                            </label>

                            <input type="radio" class="btn-check" name="type" id="createTypeExpense" value="expense" onchange="handleTypeChange('create', 'expense')">
                            <label class="btn btn-outline-danger flex-fill py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="createTypeExpense">
                                <i class="fa-solid fa-circle-minus"></i> খরচ (Cash Out)
                            </label>
                        </div>
                    </div>

                    <!-- Category (Khath) with Datalist Suggestions -->
                    <div class="mb-3">
                        <label for="createCategoryInput" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">
                            খাত / উদ্দেশ্য * <small class="text-primary">(টাইপ করুন বা তালিকা থেকে বেছে নিন)</small>
                        </label>
                        <input type="text" 
                               name="category" 
                               id="createCategoryInput" 
                               list="createCategoryList" 
                               class="form-control rounded-3" 
                               placeholder="যেমন: মূলধন জমা, লেবার খরচ, বিদ্যুৎ বিল..." 
                               required>
                        <datalist id="createCategoryList">
                            <!-- Populated via JS based on type -->
                        </datalist>
                    </div>

                    <!-- Date & Amount Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">তারিখ *</label>
                            <input type="date" name="date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">টাকার পরিমাণ (৳) *</label>
                            <input type="number" name="amount" step="0.01" min="0.01" class="form-control rounded-3 fw-bold" placeholder="0.00" required>
                        </div>
                    </div>

                    <!-- Payment Method & Reference No Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">পেমেন্ট মাধ্যম *</label>
                            <select name="payment_method" class="form-select rounded-3" required>
                                <option value="নগদ" selected>নগদ (Cash)</option>
                                <option value="বিকাশ">বিকাশ (bKash)</option>
                                <option value="নগদ (Nagad)">নগদ (Nagad)</option>
                                <option value="রকেট">রকেট (Rocket)</option>
                                <option value="ব্যাংক">ব্যাংক (Bank)</option>
                                <option value="চেক">চেক (Cheque)</option>
                                <option value="অন্যান্য">অন্যান্য</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">রেফারেন্স / ভাউচার নং</label>
                            <input type="text" name="reference_no" class="form-control rounded-3" placeholder="ঐচ্ছিক">
                        </div>
                    </div>

                    <!-- Note / Description -->
                    <div class="mb-2">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">বিবরণ / নোট (ঐচ্ছিক)</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="প্রয়োজনীয় কোনো মন্তব্য বা বিবরণ থাকলে লিখুন..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 text-muted fw-semibold" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" 
                            class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" 
                            data-loading-text="সংরক্ষণ হচ্ছে..."
                            id="createSubmitBtn">
                        <i class="fa-solid fa-check me-1"></i> সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- EDIT CASH BOOK TRANSACTION MODAL           -->
<!-- ========================================== -->
<div class="modal fade" id="editCashBookModal" tabindex="-1" aria-labelledby="editCashBookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form action="" method="POST" id="editCashBookForm">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="editCashBookModalLabel">
                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                        <span>লেনদেনের তথ্য সম্পাদনা</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Type Selector (Radio Toggle) -->
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold mb-2" style="font-size: 0.8rem;">লেনদেনের ধরণ *</label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="type" id="editTypeDeposit" value="deposit" onchange="handleTypeChange('edit', 'deposit')">
                            <label class="btn btn-outline-success flex-fill py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="editTypeDeposit">
                                <i class="fa-solid fa-circle-plus"></i> টাকা জমা (Cash In)
                            </label>

                            <input type="radio" class="btn-check" name="type" id="editTypeExpense" value="expense" onchange="handleTypeChange('edit', 'expense')">
                            <label class="btn btn-outline-danger flex-fill py-2 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2" for="editTypeExpense">
                                <i class="fa-solid fa-circle-minus"></i> খরচ (Cash Out)
                            </label>
                        </div>
                    </div>

                    <!-- Category (Khath) with Datalist Suggestions -->
                    <div class="mb-3">
                        <label for="editCategoryInput" class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">
                            খাত / উদ্দেশ্য *
                        </label>
                        <input type="text" 
                               name="category" 
                               id="editCategoryInput" 
                               list="editCategoryList" 
                               class="form-control rounded-3" 
                               required>
                        <datalist id="editCategoryList">
                            <!-- Populated via JS based on type -->
                        </datalist>
                    </div>

                    <!-- Date & Amount Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">তারিখ *</label>
                            <input type="date" name="date" id="editDateInput" class="form-control rounded-3" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">টাকার পরিমাণ (৳) *</label>
                            <input type="number" name="amount" id="editAmountInput" step="0.01" min="0.01" class="form-control rounded-3 fw-bold" required>
                        </div>
                    </div>

                    <!-- Payment Method & Reference No Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">পেমেন্ট মাধ্যম *</label>
                            <select name="payment_method" id="editPaymentMethodSelect" class="form-select rounded-3" required>
                                <option value="নগদ">নগদ (Cash)</option>
                                <option value="বিকাশ">বিকাশ (bKash)</option>
                                <option value="নগদ (Nagad)">নগদ (Nagad)</option>
                                <option value="রকেট">রকেট (Rocket)</option>
                                <option value="ব্যাংক">ব্যাংক (Bank)</option>
                                <option value="চেক">চেক (Cheque)</option>
                                <option value="অন্যান্য">অন্যান্য</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">রেফারেন্স / ভাউচার নং</label>
                            <input type="text" name="reference_no" id="editReferenceNoInput" class="form-control rounded-3">
                        </div>
                    </div>

                    <!-- Note / Description -->
                    <div class="mb-2">
                        <label class="form-label text-muted fw-semibold mb-1" style="font-size: 0.8rem;">বিবরণ / নোট (ঐচ্ছিক)</label>
                        <textarea name="note" id="editNoteInput" class="form-control rounded-3" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light rounded-3 px-3 py-2 text-muted fw-semibold" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" 
                            class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" 
                            data-loading-text="আপডেট হচ্ছে...">
                        <i class="fa-solid fa-check me-1"></i> পরিবর্তন সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const defaultCategories = @json($defaultCategories);
    const existingCategories = @json($existingCategories);
    let currentViewItem = null;

    // Toggle custom date range in filter
    document.getElementById('datePresetSelect')?.addEventListener('change', function () {
        const customRow = document.getElementById('customDateRow');
        if (this.value === 'custom') {
            customRow.classList.remove('d-none');
        } else {
            customRow.classList.add('d-none');
        }
    });

    // Populate Category Datalist dynamically based on selected type
    function updateCategoryDatalist(mode, type) {
        const datalist = document.getElementById(mode + 'CategoryList');
        if (!datalist) return;

        datalist.innerHTML = '';
        const presets = defaultCategories[type] || [];
        
        // Add defaults
        presets.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            datalist.appendChild(opt);
        });

        // Add any existing unique categories that aren't already included
        existingCategories.forEach(cat => {
            if (!presets.includes(cat)) {
                const opt = document.createElement('option');
                opt.value = cat;
                datalist.appendChild(opt);
            }
        });
    }

    // Handle Type change in Create or Edit Modal
    function handleTypeChange(mode, type) {
        updateCategoryDatalist(mode, type);

        if (mode === 'create') {
            const title = document.getElementById('createModalTitle');
            const icon = document.getElementById('createModalIcon');
            const submitBtn = document.getElementById('createSubmitBtn');

            if (type === 'deposit') {
                title.textContent = 'টাকা জমা লিখুন (Cash In)';
                icon.className = 'fa-solid fa-circle-plus text-success';
                submitBtn.className = 'btn btn-success px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner';
            } else {
                title.textContent = 'খরচ লিখুন (Cash Out)';
                icon.className = 'fa-solid fa-circle-minus text-danger';
                submitBtn.className = 'btn btn-danger px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner';
            }
        }
    }

    // Open Create Modal pre-configured for Deposit or Expense
    function openCreateModal(type) {
        const depositRadio = document.getElementById('createTypeDeposit');
        const expenseRadio = document.getElementById('createTypeExpense');

        if (type === 'deposit') {
            depositRadio.checked = true;
            handleTypeChange('create', 'deposit');
        } else {
            expenseRadio.checked = true;
            handleTypeChange('create', 'expense');
        }

        const modalEl = document.getElementById('createCashBookModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    // Open View Details Modal (Popup)
    function openViewModal(item, creatorName, createdAtFormatted) {
        currentViewItem = item;
        const isDeposit = item.type === 'deposit';

        const bannerBg = document.getElementById('viewBannerBg');
        const typeBadge = document.getElementById('viewTypeBadge');
        const amountDisplay = document.getElementById('viewAmountDisplay');
        const dateDisplay = document.getElementById('viewDateDisplay');
        const categoryDisplay = document.getElementById('viewCategoryDisplay');
        const paymentDisplay = document.getElementById('viewPaymentMethodDisplay');
        const referenceDisplay = document.getElementById('viewReferenceDisplay');
        const noteDisplay = document.getElementById('viewNoteDisplay');
        const creatorDisplay = document.getElementById('viewCreatorDisplay');
        const createdAtDisplay = document.getElementById('viewCreatedAtDisplay');

        if (isDeposit) {
            bannerBg.style.backgroundColor = '#ecfdf5';
            typeBadge.className = 'badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-semibold';
            typeBadge.innerHTML = '<i class="fa-solid fa-arrow-down-left me-1"></i> টাকা জমা (Cash In)';
            amountDisplay.className = 'fw-bold mb-1 text-success';
            amountDisplay.textContent = '+৳ ' + Number(item.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            bannerBg.style.backgroundColor = '#fef2f2';
            typeBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-semibold';
            typeBadge.innerHTML = '<i class="fa-solid fa-arrow-up-right me-1"></i> খরচ (Cash Out)';
            amountDisplay.className = 'fw-bold mb-1 text-danger';
            amountDisplay.textContent = '-৳ ' + Number(item.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        dateDisplay.textContent = 'লেনদেনের তারিখ: ' + (item.date ? item.date.substring(0, 10) : '-');
        categoryDisplay.textContent = item.category || '-';
        paymentDisplay.textContent = item.payment_method || 'নগদ';
        referenceDisplay.textContent = item.reference_no || 'কোনো রেফারেন্স নং নেই';
        creatorDisplay.textContent = creatorName || 'অজানা';
        createdAtDisplay.textContent = createdAtFormatted || '-';
        noteDisplay.textContent = item.note || 'কোনো বিবরণ বা মন্তব্য লেখা নেই।';

        const modalEl = document.getElementById('viewCashBookModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    // Switch from View Modal to Edit Modal directly
    function editFromViewModal() {
        if (!currentViewItem) return;
        const viewModalEl = document.getElementById('viewCashBookModal');
        bootstrap.Modal.getInstance(viewModalEl)?.hide();
        openEditModal(currentViewItem);
    }

    // Open Edit Modal with transaction data
    function openEditModal(item) {
        const form = document.getElementById('editCashBookForm');
        form.action = "{{ url('admin/cash-books') }}/" + item.id;

        const isDeposit = item.type === 'deposit';
        document.getElementById('editTypeDeposit').checked = isDeposit;
        document.getElementById('editTypeExpense').checked = !isDeposit;

        handleTypeChange('edit', item.type);

        document.getElementById('editCategoryInput').value = item.category || '';
        document.getElementById('editDateInput').value = item.date ? item.date.substring(0, 10) : '';
        document.getElementById('editAmountInput').value = item.amount || '';
        document.getElementById('editPaymentMethodSelect').value = item.payment_method || 'নগদ';
        document.getElementById('editReferenceNoInput').value = item.reference_no || '';
        document.getElementById('editNoteInput').value = item.note || '';

        const modalEl = document.getElementById('editCashBookModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    // Initialize datalists on page load
    document.addEventListener('DOMContentLoaded', function () {
        updateCategoryDatalist('create', 'deposit');
        updateCategoryDatalist('edit', 'deposit');
    });
</script>
@endpush
