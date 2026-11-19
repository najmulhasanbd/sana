@extends('admin.layouts.master')

@section('title', 'খরচের খাতসমূহ (Expense Categories)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-tags text-primary"></i>
                <span>খরচের খাতসমূহ (Expense Categories)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.expense.index') }}" class="text-decoration-none text-muted">দৈনিক খরচ</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">খরচের খাত</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.expense.index') }}" class="btn btn-light border rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-receipt text-primary"></i>
                <span>দৈনিক খরচ খাতা</span>
            </a>
            <button type="button" 
                    class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#createCategoryModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন খাত যোগ</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-layer-group fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট খাত</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সক্রিয় খাত</small>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['active']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                        <i class="fa-solid fa-pause fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">নিষ্ক্রিয় খাত</small>
                        <h4 class="fw-bold mb-0 text-secondary">{{ number_format($stats['inactive']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.expense-category.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="খাতের নাম বা বিবরণ দিয়ে খুঁজুন..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>খুঁজুন
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.expense-category.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">#</th>
                        <th class="py-3">খাতের নাম (Category Name)</th>
                        <th class="py-3">বিবরণ (Description)</th>
                        <th class="py-3 text-center">মোট এন্ট্রি</th>
                        <th class="py-3 text-end">মোট খরচ (৳)</th>
                        <th class="py-3 text-center">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-end" style="width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $category->name }}</div>
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.83rem;">
                                    {{ $category->description ?: '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border rounded-pill px-2.5 py-1">
                                    {{ number_format($category->expenses_count) }} টি
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                ৳ {{ number_format((float) ($category->total_expense_amount ?? 0), 2) }}
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.expense-category.toggle-status', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="border-0 bg-transparent p-0" title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন">
                                        @if($category->status)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 cursor-pointer">
                                                <i class="fa-solid fa-circle-check me-1"></i>সক্রিয়
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 cursor-pointer">
                                                <i class="fa-solid fa-circle-xmark me-1"></i>নিষ্ক্রিয়
                                            </span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-2 text-primary edit-category-btn" 
                                            title="সম্পাদনা"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editCategoryModal"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description ?? '' }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    @if($category->expenses_count == 0)
                                        <form action="{{ route('admin.expense-category.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই খরচের খাতটি মুছে ফেলতে চান?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-2 text-danger" title="মুছে ফেলুন">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light border rounded-2 text-muted" title="এন্ট্রি থাকায় মোছা যাবে না" disabled>
                                            <i class="fa-solid fa-trash-can opacity-50"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-folder-open fs-2 mb-2 text-secondary opacity-50"></i>
                                    <p class="mb-0">কোনো খরচের খাত পাওয়া যায়নি।</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ================= CREATE CATEGORY MODAL ================= -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">নতুন খরচের খাত যোগ করুন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">প্রতিষ্ঠানের নিয়মিত ব্যয়ের খাত তৈরি করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.expense-category.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="createCategoryName" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            খাতের নাম (Category Name) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input type="text" 
                                   id="createCategoryName" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="যেমন: বিদ্যুৎ বিল, পরিবহন ভাড়া, নাস্তা ও আপ্যায়ন..." 
                                   required 
                                   autofocus 
                                   style="font-size: 0.88rem;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="createCategoryDesc" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            সংক্ষিপ্ত বিবরণ (ঐচ্ছিক)
                        </label>
                        <textarea id="createCategoryDesc" 
                                  name="description" 
                                  rows="3" 
                                  class="form-control" 
                                  placeholder="এই খাতের খরচ সম্পর্কিত সংক্ষিপ্ত নোট..." 
                                  style="font-size: 0.88rem;">{{ old('description') }}</textarea>
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

<!-- ================= EDIT CATEGORY MODAL ================= -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">খরচের খাত সম্পাদনা</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">খাতের নাম ও বিবরণ পরিবর্তন করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            খাতের নাম <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input type="text" 
                                   id="editCategoryName" 
                                   name="name" 
                                   class="form-control" 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="editCategoryDesc" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            সংক্ষিপ্ত বিবরণ
                        </label>
                        <textarea id="editCategoryDesc" 
                                  name="description" 
                                  rows="3" 
                                  class="form-control" 
                                  style="font-size: 0.88rem;"></textarea>
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
        const editButtons = document.querySelectorAll('.edit-category-btn');
        const editForm = document.getElementById('editCategoryForm');
        const editNameInput = document.getElementById('editCategoryName');
        const editDescInput = document.getElementById('editCategoryDesc');

        editButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const desc = this.getAttribute('data-description');

                editForm.action = "{{ url('admin/expense-categories') }}/" + id;
                editNameInput.value = name;
                editDescInput.value = desc;
            });
        });
    });
</script>
@endpush
@endsection
