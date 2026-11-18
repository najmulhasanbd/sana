@extends('admin.layouts.master')

@section('title', 'কর্মচারী পদবী ও কাজের ধরন (Designations)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-briefcase text-primary"></i>
                <span>কর্মচারী পদবী ও কাজের ধরন (Designations)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}" class="text-decoration-none text-muted">কর্মচারী ও বেতন</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">পদবী তালিকা</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.employee.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-users"></i>
                <span>কর্মচারী তালিকা</span>
            </a>
            <!-- Add Designation Modal Trigger Button -->
            <button type="button" 
                    class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" 
                    data-bs-target="#createDesignationModal"
                    style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন পদবী যোগ করুন</span>
            </button>
        </div>
    </div>

    <!-- Summary Statistics Card -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-briefcase fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট পদবী</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fa-solid fa-circle-check fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সক্রিয় পদবী</small>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['active']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                        <i class="fa-solid fa-circle-xmark fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">নিষ্ক্রিয় পদবী</small>
                        <h4 class="fw-bold mb-0 text-muted">{{ number_format($stats['inactive']) }} টি</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.designation.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="পদবীর নাম বা বিবরণ দিয়ে খুঁজুন..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>খুঁজুন
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.designation.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="ps-4 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">পদবীর নাম</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">কাজের সংক্ষিপ্ত বিবরণ</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 130px;">কর্মচারী সংখ্যা</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 110px;">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 140px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($designations as $index => $designation)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.85rem;">
                                {{ $designations->firstItem() + $index }}
                            </td>
                            <td>
                                <span class="fw-bold text-dark" style="font-size: 0.9rem;">
                                    {{ $designation->name }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    {{ $designation->description ?: '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-primary border px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-users me-1"></i>{{ $designation->employees_count }} জন
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.designation.toggle-status', $designation) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="badge border-0 {{ $designation->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill cursor-pointer" 
                                            style="font-size: 0.75rem;" 
                                            title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন">
                                        <i class="fa-solid {{ $designation->status ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1"></i>
                                        {{ $designation->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                    </button>
                                </form>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Edit Modal Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-secondary edit-designation-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editDesignationModal"
                                            data-id="{{ $designation->id }}"
                                            data-name="{{ $designation->name }}"
                                            data-description="{{ $designation->description }}"
                                            data-update-url="{{ route('admin.designation.update', $designation) }}"
                                            title="সম্পাদনা">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Button with SweetAlert2 -->
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-3 px-2 py-1 text-danger btn-delete-designation" 
                                            data-action="{{ route('admin.designation.destroy', $designation) }}"
                                            data-name="{{ $designation->name }}"
                                            title="মুছে ফেলুন">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fa-solid fa-briefcase fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                                    <h6 class="fw-bold mb-1">কোনো পদবী পাওয়া যায়নি</h6>
                                    <p class="small mb-3">কর্মচারীদের কাজের ধরন বা পদবী যোগ করে সহজে নির্বাচন করার সুবিধা নিন।</p>
                                    <button type="button" 
                                            class="btn btn-primary rounded-pill btn-sm px-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#createDesignationModal">
                                        <i class="fa-solid fa-plus me-1"></i>প্রথম পদবী যোগ করুন
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($designations->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    মোট {{ $designations->total() }} টির মধ্যে {{ $designations->firstItem() }} থেকে {{ $designations->lastItem() }} দেখানো হচ্ছে
                </small>
                <div>
                    {{ $designations->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- ================= CREATE DESIGNATION MODAL ================= -->
<div class="modal fade" id="createDesignationModal" tabindex="-1" aria-labelledby="createDesignationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-briefcase"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">নতুন পদবী / কাজের ধরন যোগ করুন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">কর্মচারী যোগ করার সময় এটি সরাসরি সিলেক্ট করা যাবে</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.designation.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            পদবীর নাম <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               placeholder="যেমন: নার্সিং স্টাফ, অপারেটর, ম্যানেজার..." 
                               required 
                               style="font-size: 0.88rem;">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            সংক্ষিপ্ত বিবরণ <small class="text-muted">(ঐচ্ছিক)</small>
                        </label>
                        <textarea name="description" 
                                  rows="2" 
                                  class="form-control" 
                                  placeholder="এই পদবীর দায়িত্ব বা কাজের সংক্ষিপ্ত বিবরণ..." 
                                  style="font-size: 0.85rem;"></textarea>
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

<!-- ================= EDIT DESIGNATION MODAL ================= -->
<div class="modal fade" id="editDesignationModal" tabindex="-1" aria-labelledby="editDesignationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light border-bottom py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                         style="width: 36px; height: 36px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">পদবী সম্পাদনা করুন</h6>
                        <small class="text-muted" style="font-size: 0.75rem;">পদবীর নাম ও বিবরণ আপডেট করুন</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editDesignationForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            পদবীর নাম <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="editDesignationName" 
                               class="form-control" 
                               required 
                               style="font-size: 0.88rem;">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            সংক্ষিপ্ত বিবরণ <small class="text-muted">(ঐচ্ছিক)</small>
                        </label>
                        <textarea name="description" 
                                  id="editDesignationDescription" 
                                  rows="2" 
                                  class="form-control" 
                                  style="font-size: 0.85rem;"></textarea>
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

<!-- Hidden Delete Form -->
<form id="deleteDesignationForm" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
$(document).ready(function() {
    $('.edit-designation-btn').on('click', function() {
        const url = $(this).data('update-url');
        const name = $(this).data('name');
        const description = $(this).data('description');

        $('#editDesignationForm').attr('action', url);
        $('#editDesignationName').val(name);
        $('#editDesignationDescription').val(description);
    });

    // Delete Confirmation with SweetAlert2
    $(document).on('click', '.btn-delete-designation', function() {
        const actionUrl = $(this).data('action');
        const name = $(this).data('name');

        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `পদবী "${name}" মুছে ফেলা হবে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                const $form = $('#deleteDesignationForm');
                $form.attr('action', actionUrl);
                $form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
