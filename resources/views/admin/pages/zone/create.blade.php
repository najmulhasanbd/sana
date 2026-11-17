@extends('admin.layouts.master')

@section('title', 'নতুন জোন / উপজেলা যোগ (Add Zone)')

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation & Action -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-plus text-primary"></i>
                <span>নতুন জোন / উপজেলা যোগ (Add Zone)</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.zone.index') }}" class="text-decoration-none text-muted">জোন / উপজেলা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">নতুন জোন</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.zone.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>জোন তালিকায় ফিরে যান</span>
            </a>
        </div>
    </div>

    <!-- Form Card (Full Width) -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden w-100">
        <div class="card-header bg-white border-bottom p-3 p-md-4">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-pen text-primary"></i>
                <span>জোনের বিবরণ</span>
            </h6>
        </div>

        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.zone.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <!-- District Selection (Select2) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            জেলা নির্বাচন করুন (Select District) <span class="text-danger">*</span>
                        </label>
                        <select name="district_id" class="select2 form-select @error('district_id') is-invalid @enderror" data-placeholder="-- জেলা নির্বাচন করুন --" required>
                            <option value="">-- জেলা নির্বাচন করুন --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }} ({{ $district->division?->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Zone Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            জোন / উপজেলার নাম (Zone Name) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="উদাহরণ: Trishal, Dhanmondi, Savar..." 
                                   required 
                                   style="font-size: 0.88rem;">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-12">
                        <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                            স্ট্যাটাস (Status)
                        </label>
                        <div class="pt-1">
                            <div class="form-check form-switch d-flex align-items-center gap-2">
                                <input class="form-check-input cursor-pointer" 
                                       type="checkbox" 
                                       role="switch" 
                                       name="status" 
                                       id="statusSwitch" 
                                       value="1" 
                                       {{ old('status', '1') == '1' ? 'checked' : '' }} 
                                       style="width: 2.5em; height: 1.3em;">
                                <label class="form-check-label cursor-pointer fw-semibold text-dark mb-0" for="statusSwitch" style="font-size: 0.88rem;">
                                    সক্রিয় (Active)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.zone.index') }}" class="btn btn-light border px-4 py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
                        বাতিল
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="সংরক্ষণ হচ্ছে..." style="font-size: 0.85rem;">
                        <i class="fa-solid fa-save me-1"></i>জোন সংরক্ষণ করুন
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
