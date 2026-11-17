@extends('admin.layouts.master')

@section('title', 'হ্যাচারি সরবরাহকারী তালিকা')

@section('content')

    <!-- Page Header & Breadcrumb -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">
                <i class="fa-solid fa-truck-ramp-box text-primary me-2"></i>হ্যাচারি সরবরাহকারী তালিকা
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                পোনা ও মাছ সরবরাহকারী প্রতিষ্ঠানের তথ্য, যোগাযোগ নম্বর ও স্ট্যাটাস পরিচালনা করুন
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <nav aria-label="breadcrumb" class="d-none d-sm-block">
                <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item text-muted">খামারি ও হ্যাচারি</li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">সরবরাহকারী</li>
                </ol>
            </nav>
            <a href="{{ route('admin.supplier.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-plus"></i>
                <span>নতুন সরবরাহকারী</span>
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
                        <i class="fa-solid fa-users fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সর্বমোট সরবরাহকারী</small>
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
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">সক্রিয় সরবরাহকারী</small>
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
                        <i class="fa-solid fa-circle-pause fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">নিষ্ক্রিয় সরবরাহকারী</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ number_format($stats['inactive']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        
        <!-- Filter and Search Header -->
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <form action="{{ route('admin.supplier.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               class="form-control bg-light border-start-0" 
                               placeholder="নাম, মোবাইল বা ঠিকানা দিয়ে খুঁজুন..." 
                               style="font-size: 0.88rem;">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="status" class="form-select bg-light" style="font-size: 0.88rem;">
                        <option value="">সকল স্ট্যাটাস</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>সক্রিয় (Active)</option>
                        <option value="inactive" {{ ($status ?? '') === 'inactive' ? 'selected' : '' }}>নিষ্ক্রিয় (Inactive)</option>
                    </select>
                </div>

                <div class="col-6 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold shadow-sm flex-grow-1" style="font-size: 0.85rem;">
                        <i class="fa-solid fa-filter me-1"></i>ফিল্টার
                    </button>
                    @if(!empty($search) || !empty($status))
                        <a href="{{ route('admin.supplier.index') }}" class="btn btn-light border px-3 py-2 rounded-3 text-secondary" title="রিসেট" style="font-size: 0.85rem;">
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
                        <th class="ps-4 text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 60px;">#</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">সরবরাহকারী / হ্যাচারি</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মোবাইল নম্বর</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">ঠিকানা</th>
                        <th class="text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem;">মন্তব্য / নোট</th>
                        <th class="text-center text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 130px;">স্ট্যাটাস</th>
                        <th class="pe-4 text-end text-secondary text-uppercase fw-semibold" style="font-size: 0.75rem; width: 120px;">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="ps-4 fw-semibold text-muted" style="font-size: 0.85rem;">
                                {{ $loop->iteration + ($suppliers->currentPage() - 1) * $suppliers->perPage() }}
                            </td>
                            <td>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">{{ $supplier->name }}</h6>
                                    @if($supplier->email)
                                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                                            <i class="fa-regular fa-envelope me-1"></i>{{ $supplier->email }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="tel:{{ $supplier->mobile }}" class="text-decoration-none text-dark fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem;">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                                        <i class="fa-solid fa-phone me-1" style="font-size: 0.7rem;"></i>{{ $supplier->mobile }}
                                    </span>
                                </a>
                            </td>
                            <td>
                                <span class="text-secondary" style="font-size: 0.85rem;">
                                    {{ $supplier->address ?? '—' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted text-truncate d-inline-block" style="max-width: 200px; font-size: 0.82rem;" title="{{ $supplier->note }}">
                                    {{ $supplier->note ?? '—' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.supplier.toggle-status', $supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="badge border-0 rounded-pill px-3 py-1.5 cursor-pointer text-decoration-none {{ $supplier->status ? 'bg-success-subtle text-success border-success-subtle' : 'bg-secondary-subtle text-secondary' }}"
                                            title="স্ট্যাটাস পরিবর্তন করতে ক্লিক করুন"
                                            style="font-size: 0.75rem; font-weight: 600;">
                                        <i class="fa-solid {{ $supplier->status ? 'fa-check' : 'fa-xmark' }} me-1"></i>
                                        {{ $supplier->status ? 'সক্রিয়' : 'নিষ্ক্রিয়' }}
                                    </button>
                                </form>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.supplier.edit', $supplier) }}" 
                                       class="btn btn-sm btn-outline-primary border-0 rounded-circle" 
                                       title="সম্পাদনা করুন"
                                       style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger border-0 rounded-circle delete-supplier-btn" 
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->name }}"
                                            title="মুছে ফেলুন"
                                            style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-muted mb-3" style="width: 64px; height: 64px;">
                                        <i class="fa-solid fa-truck-ramp-box fs-3"></i>
                                    </div>
                                    <h6 class="text-dark fw-bold mb-1">কোনো সরবরাহকারী পাওয়া যায়নি</h6>
                                    <p class="text-muted mb-3" style="font-size: 0.85rem;">এখনও কোনো হ্যাচারি সরবরাহকারী যুক্ত করা হয়নি অথবা ফিল্টারের সাথে মিলছে না।</p>
                                    <a href="{{ route('admin.supplier.create') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-semibold">
                                        <i class="fa-solid fa-plus me-1"></i>নতুন সরবরাহকারী যোগ করুন
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        @if($suppliers->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted" style="font-size: 0.8rem;">
                    মোট {{ $suppliers->total() }} টির মধ্যে {{ $suppliers->firstItem() }} থেকে {{ $suppliers->lastItem() }} টি দেখানো হচ্ছে
                </small>
                <div>
                    {{ $suppliers->links() }}
                </div>
            </div>
        @endif
    </div>


<!-- Delete Form (Hidden) -->
<form id="deleteSupplierForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-supplier-btn');
        const deleteForm = document.getElementById('deleteSupplierForm');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const supplierId = this.getAttribute('data-id');
                const supplierName = this.getAttribute('data-name');
                const actionUrl = "{{ url('admin/suppliers') }}/" + supplierId;

                Swal.fire({
                    title: 'আপনি কি নিশ্চিত?',
                    text: `"${supplierName}" সরবরাহকারীর তথ্য স্থায়ীভাবে মুছে ফেলা হবে!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
                    cancelButtonText: 'বাতিল'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteForm.setAttribute('action', actionUrl);
                        deleteForm.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
