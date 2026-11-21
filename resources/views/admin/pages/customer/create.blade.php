@extends('admin.layouts.master')

@section('title', 'নতুন কাস্টমার যোগ করুন')

@section('content')
<!-- Page Header & Breadcrumb -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">
            <i class="fa-solid fa-user-plus text-primary me-2"></i>নতুন কাস্টমার যোগ
        </h4>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">
            বিক্রয়ের পূর্বেই পোনা বা মাছের ক্রেতার নাম, মোবাইল ও এলাকাভিত্তিক তথ্য যুক্ত করুন
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}" class="text-decoration-none text-muted">কাস্টমার তালিকা</a></li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">নতুন কাস্টমার</li>
            </ol>
        </nav>
        <a href="{{ route('admin.customer.index') }}" class="btn btn-light border rounded-pill px-3 py-2 text-secondary fw-semibold shadow-sm d-flex align-items-center gap-1.5" style="font-size: 0.85rem;">
            <i class="fa-solid fa-arrow-left"></i>
            <span>তালিকায় ফিরে যান</span>
        </a>
    </div>
</div>

<!-- Form Card (Full Width) -->
<div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden w-100">
    <div class="card-header bg-white border-bottom p-3 p-md-4">
        <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
            <i class="fa-solid fa-address-card text-primary"></i>
            <span>কাস্টমারের তথ্যাবলী</span>
        </h5>
    </div>

    <div class="card-body p-3 p-md-4">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3" role="alert">
                <div class="d-flex align-items-center mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i>
                    <strong class="text-danger">নিম্নোক্ত ত্রুটিগুলো সংশোধন করুন:</strong>
                </div>
                <ul class="mb-0 ps-4 text-danger" style="font-size: 0.88rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.customer.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <!-- Customer Name -->
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        কাস্টমারের নাম <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" 
                               class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="যেমন: মোঃ রফিকুল ইসলাম" 
                               required>
                    </div>
                    @error('name')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mobile -->
                <div class="col-12 col-md-6">
                    <label for="mobile" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        মোবাইল নম্বর <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-phone"></i>
                        </span>
                        <input type="text" 
                               class="form-control bg-light border-start-0 @error('mobile') is-invalid @enderror" 
                               id="mobile" 
                               name="mobile" 
                               value="{{ old('mobile') }}" 
                               placeholder="যেমন: 017xxxxxxxx" 
                               required>
                    </div>
                    @error('mobile')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-12 col-md-6">
                    <label for="email" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        ইমেইল ঠিকানা <small class="text-muted">(ঐচ্ছিক)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="যেমন: customer@gmail.com">
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        বর্তমান স্ট্যাটাস
                    </label>
                    <div class="d-flex align-items-center gap-4 pt-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   role="switch" 
                                   id="statusSwitch" 
                                   name="status" 
                                   value="1" 
                                   {{ old('status', '1') == '1' ? 'checked' : '' }} 
                                   style="width: 2.5rem; height: 1.3rem; cursor: pointer;">
                            <label class="form-check-label ms-2 fw-semibold text-success" for="statusSwitch" id="statusLabel">
                                সক্রিয় (Active)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Location: Division, District, Zone -->
                <div class="col-12 col-md-4">
                    <label for="divisionSelect" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        বিভাগ (Division) <small class="text-muted">(ঐচ্ছিক)</small>
                    </label>
                    <select name="division_id" id="divisionSelect" class="select2 form-select" data-placeholder="-- বিভাগ নির্বাচন করুন --">
                        <option value="">-- বিভাগ নির্বাচন করুন --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('division_id')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="districtSelect" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        জেলা (District) <small class="text-muted">(ঐচ্ছিক)</small>
                    </label>
                    <select name="district_id" id="districtSelect" class="select2 form-select" data-placeholder="-- জেলা নির্বাচন করুন --">
                        <option value="">-- জেলা নির্বাচন করুন --</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist->id }}" data-division-id="{{ $dist->division_id }}" {{ old('district_id') == $dist->id ? 'selected' : '' }}>
                                {{ $dist->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label for="zoneSelect" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        জোন / উপজেলা (Zone) <small class="text-muted">(ঐচ্ছিক)</small>
                    </label>
                    <select name="zone_id" id="zoneSelect" class="select2 form-select" data-placeholder="-- জোন নির্বাচন করুন --">
                        <option value="">-- জোন নির্বাচন করুন --</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}" data-district-id="{{ $z->district_id }}" {{ old('zone_id') == $z->id ? 'selected' : '' }}>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('zone_id')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label for="address" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        ঠিকানা (Address) <small class="text-muted">(গ্রাম/মহল্লা, বাজার, ঘের বা খামারের অবস্থান)</small>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="fa-solid fa-location-dot"></i>
                        </span>
                        <input type="text" 
                               class="form-control bg-light border-start-0 @error('address') is-invalid @enderror" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}" 
                               placeholder="যেমন: দক্ষিণ পুঁইজালা, শ্রীউলা">
                    </div>
                    @error('address')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Note -->
                <div class="col-12">
                    <label for="note" class="form-label fw-semibold text-dark" style="font-size: 0.88rem;">
                        বিশেষ মন্তব্য বা নোট <small class="text-muted">(ঐচ্ছিক)</small>
                    </label>
                    <textarea class="form-control bg-light @error('note') is-invalid @enderror" 
                              id="note" 
                              name="note" 
                              rows="3" 
                              placeholder="কাস্টমার সম্পর্কিত কোনো তথ্য থাকলে লিখুন...">{{ old('note') }}</textarea>
                    @error('note')
                        <div class="text-danger mt-1" style="font-size: 0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button with Spinner -->
                <div class="col-12 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-light border rounded-pill px-4 py-2 text-secondary fw-semibold">
                        বাতিল
                    </a>
                    <button type="submit" 
                            class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm d-flex align-items-center gap-2 btn-submit-with-spinner" 
                            data-loading-text="কাস্টমার সংরক্ষণ হচ্ছে...">
                        <i class="fa-solid fa-check"></i>
                        <span>কাস্টমার সংরক্ষণ করুন</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const statusSwitch = document.getElementById('statusSwitch');
        const statusLabel = document.getElementById('statusLabel');

        if (statusSwitch && statusLabel) {
            statusSwitch.addEventListener('change', function() {
                if (this.checked) {
                    statusLabel.textContent = 'সক্রিয় (Active)';
                    statusLabel.className = 'form-check-label ms-2 fw-semibold text-success';
                } else {
                    statusLabel.textContent = 'নিষ্ক্রিয় (Inactive)';
                    statusLabel.className = 'form-check-label ms-2 fw-semibold text-secondary';
                }
            });
        }

        // Cascading Division -> District -> Zone
        const $division = $('#divisionSelect');
        const $district = $('#districtSelect');
        const $zone = $('#zoneSelect');

        const districtOptions = $district.find('option').clone();
        const zoneOptions = $zone.find('option').clone();

        $division.on('change', function() {
            const divId = $(this).val();

            $district.empty().append('<option value="">-- জেলা নির্বাচন করুন --</option>');
            $zone.empty().append('<option value="">-- জোন নির্বাচন করুন --</option>');

            if (divId) {
                districtOptions.each(function() {
                    const $opt = $(this);
                    if ($opt.val() && $opt.data('division-id') == divId) {
                        $district.append($opt.clone());
                    }
                });
            } else {
                districtOptions.each(function() {
                    if ($(this).val()) {
                        $district.append($(this).clone());
                    }
                });
            }
            $district.val('').trigger('change.select2');
            $zone.val('').trigger('change.select2');
        });

        $district.on('change', function() {
            const distId = $(this).val();

            $zone.empty().append('<option value="">-- জোন নির্বাচন করুন --</option>');

            if (distId) {
                zoneOptions.each(function() {
                    const $opt = $(this);
                    if ($opt.val() && $opt.data('district-id') == distId) {
                        $zone.append($opt.clone());
                    }
                });
            } else {
                zoneOptions.each(function() {
                    if ($(this).val()) {
                        $zone.append($(this).clone());
                    }
                });
            }
            $zone.val('').trigger('change.select2');
        });
    });
</script>
@endpush
