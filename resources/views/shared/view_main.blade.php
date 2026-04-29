<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('style/main-screen.css') }}">
        <link rel="stylesheet" href="{{ asset('style/container.css') }}">
        <link rel="stylesheet" href="{{ asset('style/others.css') }}">
        <link rel="stylesheet" href="{{ asset('style/container2.css') }}">
        <link rel="stylesheet" href="{{ asset('style/container3.css') }}">
    </head>

    <body>
        <!-- Dark overlay for mobile sidebar -->
        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <div class="layout">

            <!-- SIDEBAR -->
            <div class="nav-bar" id="sidebar">
                @include('shared.nav-bar')
            </div>

            <!-- RIGHT SIDE -->
            <div class="right-side">
                <!-- HEADER -->
                <div class="header">
                    <button id="menu-toggle" class="menu-toggle">☰</button>
                    @include('shared.header')
                </div>

                <!-- MAIN CONTENT -->
                <div class="main-content">            
                    @yield('content')
                </div>
            </div>

        </div>

        @stack('pet-info')
        @stack('scripts')

        <script>
            (function () {
                const sidebar  = document.getElementById('sidebar');
                const toggle   = document.getElementById('menu-toggle');
                const overlay  = document.getElementById('sidebar-overlay');
                const MOBILE   = () => window.innerWidth <= 768;

                function openMobile() {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('active');
                }

                function closeMobile() {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                }

                toggle.addEventListener('click', function () {
                    if (MOBILE()) {
                        // On mobile: toggle the overlay sidebar
                        sidebar.classList.contains('mobile-open') ? closeMobile() : openMobile();
                    } else {
                        // On desktop: original collapse behaviour
                        sidebar.classList.toggle('collapsed');
                    }
                });

                // Tap overlay to close sidebar on mobile
                overlay.addEventListener('click', closeMobile);

                // If screen resizes from mobile → desktop, clean up mobile classes
                window.addEventListener('resize', function () {
                    if (!MOBILE()) {
                        closeMobile();
                    }
                });
            })();
        </script>

    </body>
</html>