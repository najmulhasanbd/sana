@extends('admin.layouts.master')

@section('title', 'বিক্রয় পণ্য তালিকা (Sale Products)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-primary"></i>
                <span>বিক্রয় পণ্য তালিকা (Sale Products)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">অর্ডার ও বিক্রয়</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বিক্রয় পণ্য</li>
                </ol>
            </nav>
        </div>
        <div>
            <!-- Add Product Modal Trigger Button -->
            <button type="button" 
                    class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#createSaleProductModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন বিক্রয় পণ্য যোগ</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Card -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-boxes-packing fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট বিক্রয় পণ্য</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalCount) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.sale-product.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="বিক্রয় পণ্যের নাম দিয়ে খুঁজুন..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>খুঁজুন
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.sale-product.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 70px;">#</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পণ্যের নাম (Product Name)</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">যোগ করার তারিখ</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 120px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ $products->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-success bg-success bg-opacity-10 flex-shrink-0"
                                         style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-fish" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.92rem;">
                                        {{ $product->name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="fa-regular fa-calendar me-1"></i>{{ $product->created_at ? $product->created_at->format('d M, Y') : '—' }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <!-- Edit Button (Modal Trigger) -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-circle text-primary d-flex align-items-center justify-content-center btn-edit-product" 
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-update-url="{{ route('admin.sale-product.update', $product) }}"
                                            title="সম্পাদনা করুন"
                                            style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-pen-to-square" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-circle text-danger d-flex align-items-center justify-content-center btn-delete-product" 
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->name }}"
                                            data-action="{{ route('admin.sale-product.destroy', $product) }}"
                                            title="মুছে ফেলুন"
                                            style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-trash-can" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                                        <i class="fa-solid fa-boxes-packing fs-3"></i>
                                    </div>
                                    <h6 class="fw-semibold text-secondary mb-1">কোনো বিক্রয় পণ্য পাওয়া যায়নি</h6>
                                    <p class="text-muted small mb-3">নতুন বিক্রয় পণ্য যোগ করতে নিচের বাটনে ক্লিক করুন।</p>
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createSaleProductModal">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন পণ্য যোগ
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>

<!-- ================= CREATE PRODUCT MODAL ================= -->
<div class="modal fade" id="createSaleProductModal" tabindex="-1" aria-labelledby="createSaleProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">নতুন বিক্রয় পণ্য যোগ করুন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">পণ্যের নাম দিয়ে সংরক্ষণ করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.sale-product.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="createProductName" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            পণ্যের নাম (Product Name) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input type="text" 
                                   id="createProductName" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="যেমন: বড় কাতলা মাছ, লাইভ রুই, ব্রিগেড মাছ..." 
                                   required 
                                   autofocus 
                                   style="font-size: 0.88rem;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

<!-- ================= EDIT PRODUCT MODAL ================= -->
<div class="modal fade" id="editSaleProductModal" tabindex="-1" aria-labelledby="editSaleProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">বিক্রয় পণ্য নাম সম্পাদনা</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">সংশোধিত নাম প্রদান করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editProductForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="editProductName" class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            পণ্যের নাম (Product Name) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-tag"></i>
                            </span>
                            <input type="text" 
                                   id="editProductName" 
                                   name="name" 
                                   value="" 
                                   class="form-control" 
                                   placeholder="যেমন: বড় কাতলা মাছ..." 
                                   required 
                                   style="font-size: 0.88rem;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" data-bs-dismiss="modal" style="font-size: 0.85rem;">
                        বাতিল
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="আপডেট হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-rotate me-1"></i>আপডেট করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteProductForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Open Edit Modal with prefilled data
    $(document).on('click', '.btn-edit-product', function() {
        const updateUrl = $(this).data('update-url');
        const name = $(this).data('name');

        $('#editProductForm').attr('action', updateUrl);
        $('#editProductName').val(name);

        const editModal = new bootstrap.Modal(document.getElementById('editSaleProductModal'));
        editModal.show();
    });

    // Delete Product Confirmation with SweetAlert2
    $(document).on('click', '.btn-delete-product', function() {
        const actionUrl = $(this).data('action');
        const name = $(this).data('name');

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `"${name}" পণ্যটি মুছে ফেলতে চান?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteProductForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });

    // If there were validation errors upon store, re-open create modal
    @if($errors->any())
        const createModal = new bootstrap.Modal(document.getElementById('createSaleProductModal'));
        createModal.show();
    @endif
});
</script>
@endpush
