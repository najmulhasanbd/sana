@extends('admin.layouts.master')

@section('title', 'বিভাগ তালিকা (Divisions)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-map text-primary"></i>
                <span>বিভাগ তালিকা (Divisions)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">লোকেশন ও অঞ্চল</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">বিভাগ</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.district.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-city"></i>
                <span>জেলা তালিকা</span>
            </a>
            <a href="{{ route('admin.division.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন বিভাগ যোগ</span>
            </a>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                        <i class="fa-solid fa-map fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট বিভাগ</small>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($stats['total']) }}</h4>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সক্রিয় বিভাগ</small>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($stats['active']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white flex-shrink-0"
                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="fa-solid fa-circle-xmark fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">নিষ্ক্রিয় বিভাগ</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['inactive']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.division.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="বিভাগের নাম দিয়ে খুঁজুন..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="select2 form-select" data-placeholder="সকল স্ট্যাটাস" style="font-size: 0.88rem;">
                        <option value="">সকল স্ট্যাটাস</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                        <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($status))
                        <a href="{{ route('admin.division.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">বিভাগের নাম</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem;">মোট জেলা</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem;">মোট জোন / থানা</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.75rem; width: 140px;">স্ট্যাটাস</th>
                        <th class="pe-4 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.75rem; width: 130px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($divisions as $index => $division)
                        <tr>
                            <td class="ps-4 text-muted fw-medium" style="font-size: 0.82rem;">
                                {{ $divisions->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10 flex-shrink-0"
                                         style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-map-location-dot" style="font-size: 0.85rem;"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">{{ $division->name }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.district.index', ['division_id' => $division->id]) }}" class="badge rounded-pill bg-info bg-opacity-10 text-info text-decoration-none px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-city me-1"></i>{{ $division->districts_count }} টি জেলা
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 fw-semibold" style="font-size: 0.78rem;">
                                    <i class="fa-solid fa-location-crosshairs me-1"></i>{{ $division->zones_count }} টি জোন
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-flex align-items-center p-0 m-0">
                                    <input class="form-check-input status-toggle cursor-pointer m-0" 
                                           type="checkbox" 
                                           role="switch" 
                                           data-url="{{ route('admin.division.toggle-status', $division) }}"
                                           {{ $division->status ? 'checked' : '' }}
                                           style="width: 2.3em; height: 1.25em;">
                                    <span class="status-badge ms-2 badge rounded-pill {{ $division->status ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-muted' }}" style="font-size: 0.72rem;">
                                        {{ $division->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                    </span>
                                </div>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.division.edit', $division) }}" 
                                       class="btn btn-sm btn-light border rounded-circle text-primary d-flex align-items-center justify-content-center" 
                                       title="এডিট করুন"
                                       style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-pen-to-square" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-light border rounded-circle text-danger d-flex align-items-center justify-content-center btn-delete" 
                                            data-id="{{ $division->id }}"
                                            data-name="{{ $division->name }}"
                                            data-form="delete-form-{{ $division->id }}"
                                            title="মুছে ফেলুন"
                                            style="width: 32px; height: 32px;">
                                        <i class="fa-regular fa-trash-can" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <form id="delete-form-{{ $division->id }}" 
                                          action="{{ route('admin.division.destroy', $division) }}" 
                                          method="POST" 
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                                        <i class="fa-solid fa-map-location-dot fs-3"></i>
                                    </div>
                                    <h6 class="fw-semibold text-secondary mb-1">কোনো বিভাগ পাওয়া যায়নি</h6>
                                    <p class="text-muted small mb-3">নতুন বিভাগ তৈরি করতে নিচের বাটনে ক্লিক করুন।</p>
                                    <a href="{{ route('admin.division.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন বিভাগ যোগ
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($divisions->hasPages())
            <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    মোট <strong>{{ $divisions->total() }}</strong> টির মধ্যে <strong>{{ $divisions->firstItem() }}</strong> - <strong>{{ $divisions->lastItem() }}</strong> দেখানো হচ্ছে
                </div>
                <div>
                    {{ $divisions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete Confirmation with SweetAlert2
    $(document).on('click', '.btn-delete', function() {
        const formId = $(this).data('form');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: `"${name}" বিভাগটি মুছে ফেলতে চান? এর অধীনে থাকা জেলা ও জোনসমূহও মুছে যেতে পারে!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    });

    // Fast Ajax Status Toggle
    $(document).on('change', '.status-toggle', function() {
        const $toggle = $(this);
        const url = $toggle.data('url');
        const $badge = $toggle.siblings('.status-badge');
        const originalChecked = !$toggle.is(':checked');

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success) {
                    if (response.status) {
                        $badge.removeClass('bg-secondary-subtle text-muted').addClass('bg-success-subtle text-success').text('সক্রিয়');
                    } else {
                        $badge.removeClass('bg-success-subtle text-success').addClass('bg-secondary-subtle text-muted').text('নিষ্ক্রিয়');
                    }
                    if (window.customToastr) {
                        window.customToastr('success', response.message || 'স্ট্যাটাস আপডেট হয়েছে', 'সফল');
                    }
                }
            },
            error: function() {
                $toggle.prop('checked', originalChecked);
                if (window.customToastr) {
                    window.customToastr('error', 'স্ট্যাটাস পরিবর্তন ব্যর্থ হয়েছে!', 'ত্রুটি');
                }
            }
        });
    });
});
</script>
@endpush
