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
  <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Select2 CSS & Bootstrap 5 Theme -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}" />
  <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}" />
  @stack('styles')

  <style>
    /* Remove number input spinner arrows globally across all browsers */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none !important;
      margin: 0 !important;
    }
    input[type=number] {
      -moz-appearance: textfield !important;
      appearance: textfield !important;
    }

    body {
      background-color: #f4f7fe;
      font-family: 'Inter', 'Hind Siliguri', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
      font-size: 0.88rem;
      color: #334155;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    /* Select2 Custom Theme Polish */
    .select2-container--bootstrap-5 .select2-selection {
      min-height: 42px;
      padding: 0.375rem 0.75rem;
      border: 1px solid #dee2e6;
      border-radius: 0.5rem;
      font-size: 0.88rem;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
      border-color: #0284c7;
      box-shadow: 0 0 0 0.2rem rgba(2, 132, 199, 0.15);
    }
    .select2-container--bootstrap-5 .select2-dropdown {
      border-color: #e2e8f0;
      border-radius: 0.5rem;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
      font-size: 0.88rem;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field {
      border-radius: 0.375rem;
      border: 1px solid #cbd5e1;
      padding: 0.4rem 0.75rem;
      font-size: 0.85rem;
    }
    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
      background-color: #0284c7;
      color: #fff;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option[aria-disabled="true"],
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__option--disabled {
      color: #94a3b8 !important;
      background-color: #f8fafc !important;
      cursor: not-allowed !important;
      opacity: 0.7 !important;
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
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Laravel Custom Toastr (najmulhasanbd/laravel-custom-toastr) -->
  <x-custom-toastr position="top-right" duration="4000" />

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="{{ asset('admin/js/main.js') }}"></script>

  <!-- Global Submit Button Spinner Trait -->
  <x-submit-spinner-script />

  <!-- Global Select2 Initialization -->
  <script>
    (function($) {
      'use strict';
      window.initSelect2 = function(scope) {
        var $context = scope ? $(scope) : $(document);
        $context.find('select.select2, select:not(.no-select2)').each(function() {
          var $this = $(this);
          if (!$this.hasClass('select2-hidden-accessible')) {
            var placeholder = $this.data('placeholder') || $this.attr('placeholder');
            if (!placeholder) {
              var $firstOption = $this.find('option[value=""]').first();
              if ($firstOption.length && $firstOption.text().trim() !== '') {
                placeholder = $firstOption.text().trim();
              } else {
                placeholder = '-- নির্বাচন করুন / Select --';
              }
            }
            $this.select2({
              theme: 'bootstrap-5',
              width: '100%',
              placeholder: placeholder,
              allowClear: $this.data('allow-clear') !== undefined ? Boolean($this.data('allow-clear')) : true,
              dropdownParent: $this.closest('.modal').length ? $this.closest('.modal') : $(document.body)
            });
          }
        });
      };

      $(document).ready(function() {
        window.initSelect2();
      });
    })(jQuery);
  </script>

  @stack('scripts')
</body>

</html>