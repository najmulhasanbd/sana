@extends('admin.layouts.master')

@section('title', 'পাসওয়ার্ড পরিবর্তন (Change Password)')

@section('content')
    <div class="container-fluid p-3 p-md-4">
        
        <!-- Page Header & Breadcrumb -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">
                    <i class="fa-solid fa-key text-primary me-2"></i>পাসওয়ার্ড পরিবর্তন
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    আপনার অ্যাকাউন্টের নিরাপত্তা নিশ্চিত করতে একটি শক্তিশালী ও নিরাপদ পাসওয়ার্ড ব্যবহার করুন
                </p>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 px-3 py-2 bg-white rounded-pill shadow-sm" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">ড্যাশবোর্ড</a></li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">পাসওয়ার্ড পরিবর্তন</li>
                </ol>
            </nav>
        </div>

        <!-- Main Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" 
                             style="width: 44px; height: 44px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                            <i class="fa-solid fa-shield-halved fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark">অ্যাকাউন্ট সিকিউরিটি ক্রেডেনশিয়াল</h5>
                            <small class="text-muted">বর্তমান পাসওয়ার্ড দিয়ে নতুন পাসওয়ার্ড নিশ্চিত করুন</small>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.change.update') }}" id="passwordChangeForm">
                        @csrf
                        @method('PUT')

                        <div class="card-body p-3 p-md-4">
                            <!-- Current Password -->
                            <div class="mb-3.5">
                                <label for="current_password" class="form-label fw-semibold text-secondary">
                                    বর্তমান পাসওয়ার্ড (Current Password) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 border-end-0 ps-0 @if($errors->updatePassword->has('current_password')) is-invalid @endif" 
                                           id="current_password" 
                                           name="current_password" 
                                           placeholder="বর্তমান পাসওয়ার্ড" 
                                           autocomplete="current-password" 
                                           required>
                                    <button class="btn btn-outline-light border border-start-0 text-muted px-3" 
                                            type="button" 
                                            onclick="togglePasswordVisibility('current_password', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                @if($errors->updatePassword->has('current_password'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        {{ $errors->updatePassword->first('current_password') }}
                                    </div>
                                @endif
                            </div>

                            <!-- New Password -->
                            <div class="mb-3.5">
                                <label for="password" class="form-label fw-semibold text-secondary">
                                    নতুন পাসওয়ার্ড (New Password) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 border-end-0 ps-0 @if($errors->updatePassword->has('password')) is-invalid @endif" 
                                           id="password" 
                                           name="password" 
                                           placeholder="নতুন পাসওয়ার্ড (কমপক্ষে ৮ অক্ষর)" 
                                           autocomplete="new-password" 
                                           required>
                                    <button class="btn btn-outline-light border border-start-0 text-muted px-3" 
                                            type="button" 
                                            onclick="togglePasswordVisibility('password', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                @if($errors->updatePassword->has('password'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        {{ $errors->updatePassword->first('password') }}
                                    </div>
                                @endif
                                <small class="text-muted d-block mt-1">
                                    <i class="fa-solid fa-circle-info text-primary me-1"></i>
                                    পাসওয়ার্ডে বড় হাতের অক্ষর, ছোট হাতের অক্ষর, সংখ্যা ও স্পেশাল ক্যারেক্টার ব্যবহার করা ভালো।
                                </small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold text-secondary">
                                    নতুন পাসওয়ার্ড নিশ্চিত করুন (Confirm Password) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="fa-solid fa-check-double"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 border-end-0 ps-0 @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="নতুন পাসওয়ার্ড নিশ্চিত করুন" 
                                           autocomplete="new-password" 
                                           required>
                                    <button class="btn btn-outline-light border border-start-0 text-muted px-3" 
                                            type="button" 
                                            onclick="togglePasswordVisibility('password_confirmation', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                                @if($errors->updatePassword->has('password_confirmation'))
                                    <div class="text-danger small mt-1">
                                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                                        {{ $errors->updatePassword->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer with Spinner Button -->
                        <div class="card-footer bg-light p-3 p-md-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="text-muted small">
                                <i class="fa-solid fa-lock text-success me-1"></i>
                                পরিবর্তন সম্পন্ন হলে নতুন পাসওয়ার্ড দিয়ে লগইন নিশ্চিত করুন।
                            </div>

                            <button type="submit" 
                                    class="btn-submit-with-spinner btn btn-primary px-4 py-2.5 rounded-pill shadow-sm fw-bold d-flex align-items-center gap-2"
                                    data-loading-text="পাসওয়ার্ড আপডেট হচ্ছে...">
                                <span class="btn-spinner d-none spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="btn-content d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-shield-check"></i>
                                    <span>পাসওয়ার্ড আপডেট করুন</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
