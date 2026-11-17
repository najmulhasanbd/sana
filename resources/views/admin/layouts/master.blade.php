<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Admin Dashboard') | {{ config('app.name', 'সানা ফিশারিজ') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}" />
  @stack('styles')

  <style>
    body {
      background-color: #f4f7fe;
      font-family: "Inter", sans-serif;
    }

    .cursor-pointer {
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="page">
    @include('admin.layouts.header')

    @include('admin.layouts.sidebar')
    <main class="main-content app-content">
      @yield('content')
    </main>
    <!-- Bootstrap Toast Container (compatible with template main.js) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090;">
      <div id="liveToast" class="toast border-0 shadow-lg rounded-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-white border-0 py-2">
          <div class="rounded-circle me-2 d-flex align-items-center justify-content-center"
            style="width: 24px; height: 24px; background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%); color: white;">
            <i class="fa-solid fa-bell" style="font-size: 0.7rem;"></i>
          </div>
          <strong class="me-auto text-dark" style="font-size: 0.88rem;">সানা ফিশারিজ</strong>
          <small class="text-muted">এখনই</small>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-light rounded-bottom py-2.5 px-3" id="toastMessage" style="font-size: 0.85rem;">
          New notification received!
        </div>
      </div>
    </div>
  </div>
  
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Laravel Custom Toastr (najmulhasanbd/laravel-custom-toastr) -->
  <x-custom-toastr position="top-right" duration="4000" />

  @if(session('success'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.customToastr === 'function') {
          window.customToastr('Success', @json(session('success')), 'সফল');
        }
      });
    </script>
  @endif
  @if(session('error'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.customToastr === 'function') {
          window.customToastr('Error', @json(session('error')), 'ত্রুটি');
        }
      });
    </script>
  @endif
  @if(session('warning'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.customToastr === 'function') {
          window.customToastr('Warning', @json(session('warning')), 'সতর্কতা');
        }
      });
    </script>
  @endif
  @if(session('info'))
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.customToastr === 'function') {
          window.customToastr('Info', @json(session('info')), 'তথ্য');
        }
      });
    </script>
  @endif

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="{{ asset('admin/js/main.js') }}"></script>

  <!-- Global Submit Button Spinner Trait -->
  <x-submit-spinner-script />

  @stack('scripts')
</body>

</html>