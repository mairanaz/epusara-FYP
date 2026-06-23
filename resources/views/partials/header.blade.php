{{-- resources/views/partials/header.blade.php --}}
<header class="app-header">

    <div class="main-header-container container-fluid">

        {{-- Start::header-content-left --}}
        <div class="header-content-left d-flex align-items-center">

            {{-- Sidebar Toggle --}}
            <div class="header-element">
                <a aria-label="Toggle Sidebar"
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
                <a href="javascript:void(0);" class="header-link layout-setting" title="Tukar tema">
                    <span class="light-layout">
                        <i class="bx bx-moon header-link-icon"></i>
                    </span>
                    <span class="dark-layout">
                        <i class="bx bx-sun header-link-icon"></i>
                    </span>
                </a>
            </div>

    

            {{-- Fullscreen --}}
            <div class="header-element header-fullscreen">
                <a id="fullscreenToggle"
                   href="javascript:void(0);"
                   class="header-link"
                   title="Skrin penuh">
                    <i class="bx bx-fullscreen full-screen-open header-link-icon"></i>
                    <i class="bx bx-exit-fullscreen full-screen-close header-link-icon d-none"></i>
                </a>
            </div>

            @auth
                {{-- Guided Tour Button - sembunyi untuk admin sahaja --}}
                @if(auth()->user()->role !== 'admin')
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
                @endif

                {{-- Profile --}}
                <div class="header-element dropdown">
                    <a href="javascript:void(0);"
                       class="header-link dropdown-toggle"
                       id="mainHeaderProfile"
                       data-bs-toggle="dropdown"
                       data-bs-auto-close="outside"
                       aria-expanded="false">

                        <div class="d-flex align-items-center">
                            <div class="me-sm-2 me-0">
                                @php
                                    $authUser = auth()->user();
                                    $profileInitial = strtoupper(substr($authUser->name ?? $authUser->email ?? 'U', 0, 1));
                                @endphp

                               @php
                                    $authUser = auth()->user();
                                    $profileInitial = strtoupper(substr($authUser->name ?? $authUser->email ?? 'U', 0, 1));
                                    $profileAvatar = $authUser->avatar ?? null;
                                @endphp

                                @if(!empty($profileAvatar))
                                    <img src="{{ $profileAvatar }}"
                                        alt="Profil"
                                        width="36"
                                        height="36"
                                        class="rounded-circle epusara-profile-img"
                                        style="object-fit: cover;">
                                @else
                                    <div class="epusara-profile-initial">
                                        {{ $profileInitial }}
                                    </div>
                                @endif
                            </div>

                            <div class="d-sm-block d-none">
                                <p class="fw-semibold mb-0 lh-1">
                                    {{ auth()->user()->name ?? auth()->user()->email ?? 'User' }}
                                </p>
                                <span class="op-7 fw-normal d-block fs-11">
                                    @if(auth()->user()->role === 'admin')
                                        Pentadbir
                                    @elseif(auth()->user()->account_type === 'utama')
                                        Ahli Utama
                                    @elseif(auth()->user()->account_type === 'tanggungan')
                                        Tanggungan
                                    @else
                                        Pengguna
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>

                    <ul class="main-header-dropdown dropdown-menu pt-0 header-profile-dropdown dropdown-menu-end"
                        aria-labelledby="mainHeaderProfile">

                        <li>
                            <a class="dropdown-item d-flex"
                               href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.profile.show') }}">
                                <i class="ti ti-user-circle fs-18 me-2 op-7"></i>
                                Profil
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex w-100">
                                    <i class="ti ti-logout fs-18 me-2 op-7"></i>
                                    Log Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth


        </div>
        {{-- End::header-content-right --}}

    </div>
</header>