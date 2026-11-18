@extends('admin.layouts.master')

@section('title', 'কর্মচারীর তথ্য সম্পাদনা - ' . $employee->name)

@section('content')
<div class="w-100 pb-4">
    <!-- Header Navigation -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-user-pen text-primary"></i>
                <span>কর্মচারীর তথ্য সম্পাদনা</span>
            </h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.employee.index') }}" class="text-decoration-none text-muted">কর্মচারী তালিকা</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">{{ $employee->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.employee.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="fa-solid fa-arrow-left"></i>
                <span>তালিকায় ফিরুন</span>
            </a>
        </div>
    </div>

    <!-- Edit Employee Card -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-id-card text-primary"></i>
                        <span>কর্মচারীর তথ্য আপডেট করুন</span>
                    </h6>
                </div>

                <form action="{{ route('admin.employee.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <!-- Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    কর্মচারীর পূর্ণ নাম <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       value="{{ old('name', $employee->name) }}" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       required 
                                       style="font-size: 0.88rem;">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Designation Selection -->
                            <div class="col-12 col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-semibold text-secondary mb-0" style="font-size: 0.85rem;">
                                        পদবী / কাজের ধরন <span class="text-danger">*</span>
                                    </label>
                                    <a href="{{ route('admin.designation.index') }}" target="_blank" class="text-primary text-decoration-none small" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-plus me-0.5"></i>নতুন পদবী তৈরি
                                    </a>
                                </div>
                                <select name="designation_id" id="designationSelect" class="form-select select2 @error('designation_id') is-invalid @enderror" required style="font-size: 0.88rem;">
                                    <option value="">-- পদবী নির্বাচন করুন --</option>
                                    @foreach($designations as $d)
                                        <option value="{{ $d->id }}" {{ old('designation_id', $employee->designation_id) == $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('designation_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mobile -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    মোবাইল নম্বর <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="text" 
                                       name="mobile" 
                                       value="{{ old('mobile', $employee->mobile) }}" 
                                       class="form-control @error('mobile') is-invalid @enderror" 
                                       style="font-size: 0.88rem;">
                                @error('mobile')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Monthly Salary (cannot be negative) -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    মাসিক মূল বেতন (৳) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       step="0.01" 
                                       min="0" 
                                       name="monthly_salary" 
                                       value="{{ old('monthly_salary', $employee->monthly_salary) }}" 
                                       class="form-control fw-bold text-dark @error('monthly_salary') is-invalid @enderror" 
                                       required 
                                       style="font-size: 0.88rem;">
                                <small class="text-muted" style="font-size: 0.72rem;">* বেতন কখনোই ঋণাত্মক বা মাইনাস (-) হবে না</small>
                                @error('monthly_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Joining Date -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    যোগদানের তারিখ <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="date" 
                                       name="joining_date" 
                                       value="{{ old('joining_date', $employee->joining_date ? $employee->joining_date->format('Y-m-d') : '') }}" 
                                       class="form-control @error('joining_date') is-invalid @enderror" 
                                       style="font-size: 0.88rem;">
                                @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- NID -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    জাতীয় পরিচয়পত্র নম্বর (NID) <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <input type="text" 
                                       name="nid" 
                                       value="{{ old('nid', $employee->nid) }}" 
                                       class="form-control @error('nid') is-invalid @enderror" 
                                       style="font-size: 0.88rem;">
                                @error('nid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    স্থায়ী / বর্তমান ঠিকানা <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <textarea name="address" 
                                          rows="2" 
                                          class="form-control @error('address') is-invalid @enderror" 
                                          style="font-size: 0.85rem;">{{ old('address', $employee->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Status Switch -->
                            <div class="col-12">
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="employeeStatus" {{ $employee->status ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark" for="employeeStatus" style="font-size: 0.85rem;">
                                        কর্মচারী সক্রিয় (Active) রাখুন
                                    </label>
                                </div>
                            </div>

                            <!-- Note -->
                            <div class="col-12">
                                <label class="form-label fw-semibold text-secondary" style="font-size: 0.85rem;">
                                    অতিরিক্ত মন্তব্য বা নোট <small class="text-muted">(ঐচ্ছিক)</small>
                                </label>
                                <textarea name="note" 
                                          rows="2" 
                                          class="form-control" 
                                          style="font-size: 0.85rem;">{{ old('note', $employee->note) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.employee.index') }}" class="btn btn-light border px-4 py-2 rounded-3 text-secondary" style="font-size: 0.85rem;">
                            বাতিল
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm btn-submit-with-spinner" data-loading-text="আপডেট হচ্ছে..." style="font-size: 0.85rem;">
                            <i class="fa-solid fa-check me-1"></i>তথ্য সংরক্ষণ করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
