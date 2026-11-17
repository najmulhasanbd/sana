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

<body class="sidebar-collapsed">
  <div class="page">
    @include('admin.layouts.header')

    @include('admin.layouts.sidebar')

    @yield('content')
  </div>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Toast Container -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="liveToast" class="toast border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header bg-white border-0">
        <div class="rounded me-2 d-flex align-items-center justify-content-center"
          style="width: 20px; height: 20px; background-color: #6366f1; color: white;">
          <i class="fa-solid fa-bell" style="font-size: 0.6rem;"></i>
        </div>
        <strong class="me-auto" style="color: #2b3674;">System</strong>
        <small class="text-muted">Just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body bg-light rounded-bottom" id="toastMessage">
        New notification received!
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="{{ asset('admin/js/main.js') }}"></script>

  <!-- Global Submit Button Spinner Trait -->
  <x-submit-spinner-script />

  @stack('scripts')
  <script>
    $(document).ready(function () {
      // Trigger Massive Confetti Animation on Page Load
      if (typeof confetti === 'function') {
        setTimeout(function() {
          var count = 350;
          var defaults = { 
            origin: { y: 0.6 }, 
            zIndex: 10000, 
            colors: ['#6366f1', '#a855f7', '#10b981', '#f59e0b', '#ec4899', '#3b82f6', '#ef4444'] 
          };

          function fire(particleRatio, opts) {
            confetti(Object.assign({}, defaults, opts, {
              particleCount: Math.floor(count * particleRatio)
            }));
          }

          fire(0.25, { spread: 26, startVelocity: 55 });
          fire(0.2, { spread: 60 });
          fire(0.35, { spread: 100, decay: 0.91, scalar: 1.2 });
          fire(0.1, { spread: 120, startVelocity: 25, decay: 0.92, scalar: 1.5 });
          fire(0.1, { spread: 140, startVelocity: 45 });
        }, 300); 
      }
    });
  </script>
</body>

</html>