{{-- resources/views/partials/header.blade.php --}}
<header class="app-header">

    <div class="main-header-container container-fluid">

        {{-- Start::header-content-left --}}
        <div class="header-content-left">

            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}"
                       class="header-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-logo.png') }}" alt="logo" class="desktop-logo">
                        <img src="{{ asset('assets/images/brand-logos/toggle-logo.png') }}" alt="logo" class="toggle-logo">
                        <img src="{{ asset('assets/images/brand-logos/desktop-dark.png') }}" alt="logo" class="desktop-dark">
                        <img src="{{ asset('assets/images/brand-logos/toggle-dark.png') }}" alt="logo" class="toggle-dark">
                        <img src="{{ asset('assets/images/brand-logos/desktop-white.png') }}" alt="logo" class="desktop-white">
                        <img src="{{ asset('assets/images/brand-logos/toggle-white.png') }}" alt="logo" class="toggle-white">
                    </a>
                </div>
            </div>

            <div class="header-element">
                <a aria-label="Hide Sidebar"
                   class="sidemenu-toggle header-link animated-arrow hor-toggle horizontal-navtoggle"
                   data-bs-toggle="sidebar"
                   href="javascript:void(0);">
                    <span></span>
                </a>
            </div>

        </div>
        {{-- End::header-content-left --}}

        {{-- Start::header-content-right --}}
        <div class="header-content-right">

            {{-- Search modal trigger --}}
            <div class="header-element header-search">
                <a href="javascript:void(0);" class="header-link switcher-icon"
                    data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                    <i class="bx bx-cog header-link-icon"></i>
                </a>
            </div>

            {{-- Country selector (optional demo) --}}
            <div class="header-element country-selector">
                <a href="javascript:void(0);" class="header-link dropdown-toggle"
                   data-bs-auto-close="outside" data-bs-toggle="dropdown">
                    <img src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt="img" class="rounded-circle header-link-icon">
                </a>

                <ul class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                            <span class="avatar avatar-xs lh-1 me-2">
                                <img src="{{ asset('assets/images/flags/us_flag.jpg') }}" alt="img">
                            </span>
                            English
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                            <span class="avatar avatar-xs lh-1 me-2">
                                <img src="{{ asset('assets/images/flags/spain_flag.jpg') }}" alt="img">
                            </span>
                            Spanish
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);">
                            <span class="avatar avatar-xs lh-1 me-2">
                                <img src="{{ asset('assets/images/flags/french_flag.jpg') }}" alt="img">
                            </span>
                            French
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Theme mode --}}
            <div class="header-element header-theme-mode">
                <a href="javascript:void(0);" class="header-link layout-setting">
                    <span class="light-layout">
                        <i class="bx bx-moon header-link-icon"></i>
                    </span>
                    <span class="dark-layout">
                        <i class="bx bx-sun header-link-icon"></i>
                    </span>
                </a>
            </div>

            {{-- Cart (demo) - kalau tak nak boleh buang block ni --}}
            <div class="header-element cart-dropdown">
                <a href="javascript:void(0);" class="header-link dropdown-toggle"
                   data-bs-auto-close="outside" data-bs-toggle="dropdown">
                    <i class="bx bx-cart header-link-icon"></i>
                    <span class="badge bg-primary rounded-pill header-icon-badge" id="cart-icon-badge">5</span>
                </a>

                <div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0 fs-17 fw-semibold">Cart Items</p>
                            <span class="badge bg-success-transparent" id="cart-data">5 Items</span>
                        </div>
                    </div>
                    <div><hr class="dropdown-divider"></div>

                    <ul class="list-unstyled mb-0" id="header-cart-items-scroll">
                        <li class="dropdown-item">
                            <div class="d-flex align-items-start cart-dropdown-item">
                                <img src="{{ asset('assets/images/ecommerce/jpg/1.jpg') }}" alt="img"
                                     class="avatar avatar-sm avatar-rounded br-5 me-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start justify-content-between mb-0">
                                        <div class="mb-0 fs-13 text-dark fw-semibold">
                                            <a href="javascript:void(0);">SomeThing Phone</a>
                                        </div>
                                        <div>
                                            <span class="text-black mb-1">$1,299.00</span>
                                            <a href="javascript:void(0);" class="header-cart-remove float-end dropdown-item-close">
                                                <i class="ti ti-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="min-w-fit-content d-flex align-items-start justify-content-between">
                                        <ul class="header-product-item d-flex">
                                            <li>Metallic Blue</li>
                                            <li>6gb Ram</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="p-3 empty-header-item border-top">
                        <div class="d-grid">
                            <a href="javascript:void(0);" class="btn btn-primary">Proceed to checkout</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="header-element notifications-dropdown">
                <a href="javascript:void(0);" class="header-link dropdown-toggle"
                   data-bs-toggle="dropdown" data-bs-auto-close="outside"
                   id="messageDropdown" aria-expanded="false">
                    <i class="bx bx-bell header-link-icon"></i>
                    <span class="badge bg-secondary rounded-pill header-icon-badge pulse pulse-secondary"
                          id="notification-icon-badge">5</span>
                </a>

                <div class="main-header-dropdown dropdown-menu dropdown-menu-end" data-popper-placement="none">
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0 fs-17 fw-semibold">Notifications</p>
                            <span class="badge bg-secondary-transparent" id="notifiation-data">5 Unread</span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>

                    <ul class="list-unstyled mb-0" id="header-notification-scroll">
                        <li class="dropdown-item">
                            <div class="d-flex align-items-start">
                                <div class="pe-2">
                                    <span class="avatar avatar-md bg-primary-transparent avatar-rounded">
                                        <i class="ti ti-gift fs-18"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 fw-semibold"><a href="javascript:void(0);">Notification Example</a></p>
                                        <span class="text-muted fw-normal fs-12 header-notification-text">Contoh notifikasi.</span>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="min-w-fit-content text-muted me-1 dropdown-item-close1">
                                            <i class="ti ti-x fs-16"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div class="p-3 empty-header-item1 border-top">
                        <div class="d-grid">
                            <a href="javascript:void(0);" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Shortcuts --}}
            <div class="header-element header-shortcuts-dropdown">
                <a href="javascript:void(0);" class="header-link dropdown-toggle"
                   data-bs-toggle="dropdown" data-bs-auto-close="outside"
                   id="notificationDropdown" aria-expanded="false">
                    <i class="bx bx-grid-alt header-link-icon"></i>
                </a>

                <div class="main-header-dropdown header-shortcuts-dropdown dropdown-menu pb-0 dropdown-menu-end"
                     aria-labelledby="notificationDropdown">
                    <div class="p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0 fs-17 fw-semibold">Related Apps</p>
                        </div>
                    </div>
                    <div class="dropdown-divider mb-0"></div>

                    <div class="main-header-shortcuts p-2" id="header-shortcut-scroll">
                        <div class="row g-2">
                            <div class="col-4">
                                <a href="javascript:void(0);">
                                    <div class="text-center p-3 related-app">
                                        <span class="avatar avatar-sm avatar-rounded">
                                            <img src="{{ asset('assets/images/apps/google.png') }}" alt="">
                                        </span>
                                        <span class="d-block fs-12">Google</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="javascript:void(0);">
                                    <div class="text-center p-3 related-app">
                                        <span class="avatar avatar-sm avatar-rounded">
                                            <img src="{{ asset('assets/images/apps/calender.png') }}" alt="">
                                        </span>
                                        <span class="d-block fs-12">Calendar</span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="javascript:void(0);">
                                    <div class="text-center p-3 related-app">
                                        <span class="avatar avatar-sm avatar-rounded">
                                            <img src="{{ asset('assets/images/apps/translate.png') }}" alt="">
                                        </span>
                                        <span class="d-block fs-12">Translate</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border-top">
                        <div class="d-grid">
                            <a href="javascript:void(0);" class="btn btn-primary">View All</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Fullscreen --}}
            <div class="header-element header-fullscreen">
                <a onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                    <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
                    <i class="bx bx-exit-fullscreen full-screen-close header-link-icon d-none"></i>
                </a>
            </div>

            {{-- Profile --}}
            <div class="header-element">
                <a href="javascript:void(0);" class="header-link dropdown-toggle"
                   id="mainHeaderProfile" data-bs-toggle="dropdown"
                   data-bs-auto-close="outside" aria-expanded="false">
                    <div class="d-flex align-items-center">
                        <div class="me-sm-2 me-0">
                            <img src="{{ auth()->user()->avatar_url ?? asset('assets/images/faces/9.jpg') }}"
                                 alt="img" width="32" height="32" class="rounded-circle">
                        </div>
                        <div class="d-sm-block d-none">
                            <p class="fw-semibold mb-0 lh-1">{{ auth()->user()->name ?? auth()->user()->email ?? 'User' }}</p>
                            <span class="op-7 fw-normal d-block fs-11">
                                {{ auth()->user()->role === 'admin' ? 'Admin' : 'User' }}
                            </span>
                        </div>
                    </div>
                </a>

                <ul class="main-header-dropdown dropdown-menu pt-0 overflow-hidden header-profile-dropdown dropdown-menu-end"
                    aria-labelledby="mainHeaderProfile">
                    <li>
                        <a class="dropdown-item d-flex" href="#">
                            <i class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex" href="#">
                            <i class="ti ti-adjustments-horizontal fs-18 me-2 op-7"></i>Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex w-100">
                                <i class="ti ti-logout fs-18 me-2 op-7"></i>Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            {{-- Switcher button (⚙️) --}}
            <div class="header-element">
                <a href="javascript:void(0);" class="header-link switcher-icon"
                   data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas">
                    <i class="bx bx-cog header-link-icon"></i>
                </a>
            </div>

        </div>
        {{-- End::header-content-right --}}

    </div>
</header>