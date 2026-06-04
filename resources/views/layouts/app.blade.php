<!DOCTYPE html>
<html lang="en" dir="ltr"
      data-nav-layout="vertical"
      data-theme-mode="light"
      data-header-styles="light"
      data-menu-styles="dark"
      data-toggled="close">

<head>
    @include('partials.head')

    {{-- Driver.js - Tour Guide System --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.css">

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

    /*
|--------------------------------------------------------------------------
| e-Pusara Guided Tour - Onboarding Style
|--------------------------------------------------------------------------
*/

.epusara-tour-popover.driver-popover {
        width: 420px;
    max-width: calc(100vw - 28px);
    min-width: 390px;
    padding: 0;
    border: 0;
    border-radius: 10px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 16px 45px rgba(15, 23, 42, 0.24);
    font-family: inherit;
}

/* Sembunyikan close X asal Driver.js */
.epusara-tour-popover .driver-popover-close-btn {
    display: none !important;
}

/* Tajuk */
.epusara-tour-popover .driver-popover-title {
    margin: 0;
    padding: 42px 20px 8px;
    font-size: 19px;
    line-height: 1.35;
    font-weight: 600;
    color: #1f2937;
}

/* Penerangan */
.epusara-tour-popover .driver-popover-description {
    margin: 0;
    min-height: 72px;
    padding: 0 20px 16px;
    font-size: 13px;
    line-height: 1.55;
    color: #4b5563;
}

/* Footer Driver.js */
.epusara-tour-popover .driver-popover-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 12px 12px;
    margin: 0;
    border-top: 0;
    background: #ffffff;
}

/* Sembunyikan progress text asal kerana kita guna titik + progress bar */
.epusara-tour-popover .driver-popover-progress-text {
    display: none !important;
}

/* Container button */
.epusara-tour-popover .driver-popover-navigation-btns {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

/* Semua button navigation */
.epusara-tour-popover .driver-popover-navigation-btns button {
    min-width: 112px;
    padding: 11px 14px;
    border-radius: 7px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
    text-shadow: none !important;
    box-shadow: none !important;
    opacity: 1 !important;
    transition: background-color 0.18s ease, border-color 0.18s ease, color 0.18s ease;
}

/* Button Sebelumnya - aktif */
.epusara-tour-popover button.driver-popover-prev-btn:not(:disabled) {
    border: 1px solid #334e68 !important;
    background: #334e68 !important;
    color: #ffffff !important;
}

.epusara-tour-popover button.driver-popover-prev-btn:not(:disabled):hover {
    border-color: #243b53 !important;
    background: #243b53 !important;
    color: #ffffff !important;
}

/* Button Seterusnya / Selesai - aktif */
.epusara-tour-popover button.driver-popover-next-btn:not(:disabled),
.epusara-tour-popover button.driver-popover-done-btn:not(:disabled) {
    margin-left: auto;
    border: 1px solid #0ea5e9 !important;
    background: #0ea5e9 !important;
    color: #ffffff !important;
}

.epusara-tour-popover button.driver-popover-next-btn:not(:disabled):hover,
.epusara-tour-popover button.driver-popover-done-btn:not(:disabled):hover {
    border-color: #0284c7 !important;
    background: #0284c7 !important;
    color: #ffffff !important;
}

/* Button yang belum boleh ditekan */
.epusara-tour-popover .driver-popover-navigation-btns button:disabled {
    cursor: not-allowed !important;
    border: 1px solid #d1d5db !important;
    background: #f1f5f9 !important;
    color: #64748b !important;
    opacity: 1 !important;
}

/* Pastikan text button tidak dipudarkan oleh style asal template / Driver.js */
.epusara-tour-popover .driver-popover-navigation-btns button * {
    color: inherit !important;
    opacity: 1 !important;
}

/* Sembunyikan ruang button disabled pada step pertama jika dikehendaki */
.epusara-tour-popover .driver-popover-prev-btn:disabled {
    visibility: hidden;
}
/* Arrow popup */
.epusara-tour-popover .driver-popover-arrow {
    border-color: #ffffff;
}

/* Butang Langkau custom */
.epusara-tour-skip {
    position: absolute;
    top: 13px;
    right: 16px;
    padding: 0;
    border: 0;
    background: transparent;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}

.epusara-tour-skip:hover {
    color: #0ea5e9;
}

/* Ruang dots + progress bar */
.epusara-tour-indicator {
    padding: 0 12px 0;
}

/* Titik indikator */
.epusara-tour-dots {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 5px;
    padding: 4px 0 14px;
}

.epusara-tour-dot {
    display: block;
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d1d5db;
}

.epusara-tour-dot.is-active {
    width: 14px;
    background: #94a3b8;
}

/* Track progress */
.epusara-tour-progress-track {
    width: 100%;
    height: 5px;
    overflow: hidden;
    border-radius: 999px;
    background: #e5e7eb;
}

.epusara-tour-progress-fill {
    height: 100%;
    border-radius: 999px;
    background: #0ea5e9;
    transition: width 0.25s ease;
}

/* Highlight item pada screen */
.driver-active-element {
    border-radius: 10px !important;
}

/* Paparan telefon */
@media (max-width: 575px) {
    .epusara-tour-popover.driver-popover {
        width: calc(100vw - 24px);
        min-width: 0;
    }

    .epusara-tour-popover .driver-popover-title {
        padding: 40px 16px 8px;
        font-size: 17px;
    }

    .epusara-tour-popover .driver-popover-description {
        min-height: 0;
        padding: 0 16px 16px;
    }

    .epusara-tour-popover .driver-popover-navigation-btns button {
        min-width: 98px;
    }
}

    .app-header .header-content-right {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }

    .app-header .header-content-right > .header-element {
        display: flex !important;
        align-items: center !important;
        height: 100% !important;
    }

    .app-header .header-content-right .header-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    
    .epusara-guide-btn {
        width: auto !important;
        height: 38px !important;
        min-height: 38px !important;
        margin: 0 4px !important;
        padding: 0 13px 0 11px !important;

        border: 1px solid #e2e8f0 !important;
        border-radius: 999px !important;
        background: #f8fafc !important;
        color: #475569 !important;

        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        align-self: center !important;
        vertical-align: middle !important;
        gap: 7px !important;

        font-size: 13px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        white-space: nowrap !important;

        cursor: pointer !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
    }

    .epusara-guide-btn .header-link-icon {
        width: auto !important;
        height: auto !important;
        font-size: 18px !important;
        color: #64748b !important;
        transition: color 0.2s ease !important;
    }

    .epusara-guide-btn span {
        color: inherit !important;
        font-size: 13px !important;
        font-weight: 600 !important;
    }

    /* Hover */
    .epusara-guide-btn:hover {
        border-color: #bfdbfe !important;
        background: #eff6ff !important;
        color: #2563eb !important;
    }

    .epusara-guide-btn:hover .header-link-icon {
        color: #2563eb !important;
    }

    /* Focus ketika keyboard navigation */
    .epusara-guide-btn:focus {
        outline: none !important;
        border-color: #93c5fd !important;
        background: #eff6ff !important;
        color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    }

    /* Paparan tablet / telefon: icon sahaja */
    @media (max-width: 991.98px) {
        .epusara-guide-btn {
            width: 38px !important;
            min-width: 38px !important;
            height: 38px !important;
            padding: 0 !important;
            margin: 0 2px !important;
            border-radius: 50% !important;
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

    {{-- Driver.js - Tour Guide System --}}
    <script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        /*
        |--------------------------------------------------------------------------
        | e-Pusara Tour Helper
        |--------------------------------------------------------------------------
        | Digunakan oleh semua modul untuk menghasilkan indicator titik,
        | progress bar dan butang Langkau dalam popup Driver.js.
        */

        window.updateEpusaraTourPopover = function (driverObj, currentIndex, totalSteps) {
            const popover = document.querySelector('.epusara-tour-popover.driver-popover');

            if (!popover) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Butang Langkau
            |--------------------------------------------------------------------------
            */
            let skipButton = popover.querySelector('.epusara-tour-skip');

            if (!skipButton) {
                skipButton = document.createElement('button');
                skipButton.type = 'button';
                skipButton.className = 'epusara-tour-skip';
                skipButton.textContent = 'Langkau';

                skipButton.addEventListener('click', function () {
                    driverObj.destroy();
                });

                popover.appendChild(skipButton);
            }

            /*
            |--------------------------------------------------------------------------
            | Indicator Titik dan Progress Bar
            |--------------------------------------------------------------------------
            */
            let indicator = popover.querySelector('.epusara-tour-indicator');

            if (!indicator) {
                indicator = document.createElement('div');
                indicator.className = 'epusara-tour-indicator';

                const footer = popover.querySelector('.driver-popover-footer');

                if (footer) {
                    popover.insertBefore(indicator, footer);
                } else {
                    popover.appendChild(indicator);
                }
            }

            const dots = Array.from({ length: totalSteps }, function (_, index) {
                const activeClass = index === currentIndex ? ' is-active' : '';
                return '<span class="epusara-tour-dot' + activeClass + '"></span>';
            }).join('');

            const percentage = ((currentIndex + 1) / totalSteps) * 100;

            indicator.innerHTML =
                '<div class="epusara-tour-dots">' + dots + '</div>' +
                '<div class="epusara-tour-progress-track">' +
                    '<div class="epusara-tour-progress-fill" style="width: ' + percentage + '%;"></div>' +
                '</div>';
        };
    </script>

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
    @stack('scripts')
</body>
</html>