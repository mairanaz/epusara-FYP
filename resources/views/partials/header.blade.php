{{-- resources/views/partials/header.blade.php --}}
<header class="app-header">

    <div class="main-header-container container-fluid">

        {{-- Start::header-content-left --}}
        <div class="header-content-left">

            <div class="header-element">
                <div class="horizontal-logo">
                    <a href="{{ auth()->check() ? (auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard')) : url('/') }}"
                    class="header-logo d-flex align-items-center">
                        <img src="{{ asset('assets/images/logo_rtb.jpg') }}"
                            alt="Logo RTB"
                            style="height: 42px; width: 42px; object-fit: contain;">
                    </a>
                </div>
            </div>

            <div class="header-element">
                <a aria-label="Hide Sidebar"
                    id="sidebar-toggle"
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

                        {{-- Fullscreen --}}
            <div class="header-element header-fullscreen">
                <a onclick="openFullscreen();" href="javascript:void(0);" class="header-link">
                    <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
                    <i class="bx bx-exit-fullscreen full-screen-close header-link-icon d-none"></i>
                </a>
            </div>

            @auth
                {{-- Guided Tour Button --}}
                <div class="header-element">
                    <button type="button"
                            id="btnPageTour"
                            class="header-link epusara-guide-btn"
                            title="Panduan Halaman"
                            aria-label="Buka panduan halaman">
                        <i class="bx bx-help-circle header-link-icon"></i>
                        <span class="d-none d-lg-inline">Panduan</span>
                    </button>
                </div>

                {{-- Profile --}}
                <div class="header-element dropdown">
                    <a href="javascript:void(0);" class="header-link dropdown-toggle"
                       id="mainHeaderProfile"
                       data-bs-toggle="dropdown"
                       data-bs-auto-close="outside"
                       aria-expanded="false">

                        <div class="d-flex align-items-center">
                            <div class="me-sm-2 me-0">
                                <img src="{{ auth()->user()->avatar_url ?? asset('assets/images/faces/9.jpg') }}"
                                     alt="img" width="32" height="32" class="rounded-circle">
                            </div>

                            <div class="d-sm-block d-none">
                                <p class="fw-semibold mb-0 lh-1">
                                    {{ auth()->user()->name ?? auth()->user()->email ?? 'User' }}
                                </p>
                                <span class="op-7 fw-normal d-block fs-11">
                                    {{ auth()->user()->role === 'admin' ? 'Admin' : 'User' }}
                                </span>
                            </div>
                        </div>
                    </a>

                    <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end"
                        aria-labelledby="mainHeaderProfile">
                        <li>
                            <a class="dropdown-item d-flex" href="#">
                                <i class="ti ti-user-circle fs-18 me-2 op-7"></i>Profile
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

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
            @endauth

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