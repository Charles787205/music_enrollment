<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Muzic\'s Kool') - {{ config('app.name', 'Muzic\'s Kool') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Custom Styles -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* Floating particles animation */
    .auth-gradient::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle at 20% 50%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
        radial-gradient(circle at 40% 80%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
      animation: particles 20s linear infinite;
      z-index: -1;
      pointer-events: none;
    }

    @keyframes particles {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.8;
      }
    }

    /* Glass morphism effect */
    .auth-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border-radius: 20px;
      padding: 1px;
      background: linear-gradient(45deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
      mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
      mask-composite: exclude;
      z-index: -1;
      pointer-events: none;
    }

    /* Enhanced focus states */
    .glass-input:focus {
      outline: none;
      border-color: #667eea !important;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1),
        0 0 20px rgba(102, 126, 234, 0.2) !important;
    }

    /* Button hover effects */
    .btn-gradient:hover {
      box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4) !important;
    }

    /* Smooth transitions for all interactive elements */
    .floating-label input,
    .floating-label label,
    .btn,
    .form-check-input {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Custom checkbox styling */
    .form-check-input:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    .form-check-input:focus {
      border-color: #667eea;
      outline: 0;
      box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
  </style>
</head>

<body>
  <div id="app">
    @yield('content')
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JavaScript for enhanced interactions -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Enhanced floating label animation
      const floatingInputs = document.querySelectorAll('.floating-label input');

      floatingInputs.forEach(input => {
        // Check if input has value on load
        if (input.value.trim() !== '') {
          input.classList.add('has-value');
        }

        input.addEventListener('blur', function () {
          if (this.value.trim() !== '') {
            this.classList.add('has-value');
          } else {
            this.classList.remove('has-value');
          }
        });

        input.addEventListener('focus', function () {
          this.classList.add('is-focused');
        });

        input.addEventListener('blur', function () {
          this.classList.remove('is-focused');
        });
      });

      // Add ripple effect to buttons
      const buttons = document.querySelectorAll('.btn-gradient');
      buttons.forEach(button => {
        button.addEventListener('click', function (e) {
          const ripple = document.createElement('span');
          const rect = this.getBoundingClientRect();
          const size = Math.max(rect.width, rect.height);
          const x = e.clientX - rect.left - size / 2;
          const y = e.clientY - rect.top - size / 2;

          ripple.style.width = ripple.style.height = size + 'px';
          ripple.style.left = x + 'px';
          ripple.style.top = y + 'px';
          ripple.classList.add('ripple');

          this.appendChild(ripple);

          setTimeout(() => {
            ripple.remove();
          }, 600);
        });
      });
    });
  </script>

  <style>
    .ripple {
      position: absolute;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      pointer-events: none;
      transform: scale(0);
      animation: ripple-animation 0.6s linear;
    }

    @keyframes ripple-animation {
      to {
        transform: scale(4);
        opacity: 0;
      }
    }

    .floating-label input.has-value+label,
    .floating-label input.is-focused+label {
      transform: translateY(-0.5rem) translateX(-0.25rem) scale(0.85);
      color: #667eea !important;
    }
  </style>
</body>

</html>