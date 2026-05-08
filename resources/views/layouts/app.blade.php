<!DOCTYPE html>
<html lang="en" dir="ltr"
      data-nav-layout="vertical"
      data-theme-mode="light"
      data-header-styles="light"
      data-menu-styles="dark"
      data-toggled="close">

<head>
    @include('partials.head')

<style>
    /* Sidebar spacing ikut template */
    .app-sidebar .main-sidebar {
        padding-left: 3px !important;
        padding-right: 3px !important;
    }

    .app-sidebar .main-menu {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .app-sidebar .main-menu > .slide {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .app-sidebar .side-menu__item {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    /* Elak menu item gerak-gerak bila hover/active */
    .app-sidebar .side-menu__item,
    .app-sidebar .side-menu__item:hover,
    .app-sidebar .side-menu__item:focus,
    .app-sidebar .side-menu__item:active,
    .app-sidebar .slide,
    .app-sidebar .slide:hover,
    .app-sidebar .slide:focus,
    .app-sidebar .slide:active {
        transform: none !important;
    }

    @media (max-width: 991px) {
        .app-sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            transform: translateX(-100%) !important;
            width: 280px !important;
            height: 100vh !important;
            z-index: 9999 !important;
            transition: transform 0.3s ease !important;
            background: #111827 !important;
            overflow-y: auto !important;
        }

        .app-sidebar.mobile-open {
            transform: translateX(0) !important;
        }

        .sidebar-overlay {
            display: none !important;
            position: fixed !important;
            inset: 0 !important;
            background: rgba(15, 23, 42, 0.45) !important;
            z-index: 9998 !important;
        }

        .sidebar-overlay.mobile-open {
            display: block !important;
        }

        .main-content,
        .app-header {
            margin-left: 0 !important;
        }
    }
</style>

</head>

<body class="d-flex flex-column min-vh-100">

    @include('partials.switcher')
    @include('partials.search-modal')

    {{-- 
    <div id="loader">
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
    </div>
    --}}

    <div class="page flex-grow-1">

        @include('partials.header')

       @auth
            @if(request()->is('admin') || request()->is('admin/*'))
                @include('partials.sidebar-admin')
            @else
                @include('partials.sidebar-user')
            @endif
        @endauth

        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('partials.footer')

    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    {{-- <div id="responsive-overlay"></div> --}}

    @include('partials.scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebarOverlay');

            const userRole = "{{ strtolower(trim(auth()->user()->role ?? 'user')) }}";

            let sidebar = null;

            if (userRole === 'admin') {
                sidebar = document.getElementById('sidebar-admin');
            } else {
                sidebar = document.getElementById('sidebar-user');
            }

            if (!toggle || !sidebar) {
                console.log('Toggle atau sidebar tidak dijumpai');
                console.log('Role:', userRole);
                console.log('Sidebar:', sidebar);
                return;
            }

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                sidebar.classList.toggle('mobile-open');

                if (overlay) {
                    overlay.classList.toggle('mobile-open');
                }
            });

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('mobile-open');
                });
            }

            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    sidebar.classList.remove('mobile-open');

                    if (overlay) {
                        overlay.classList.remove('mobile-open');
                    }
                });
            });
        });
    </script>
</body>
</html>