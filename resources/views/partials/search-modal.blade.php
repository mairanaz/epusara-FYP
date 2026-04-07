<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.dashboard') }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo RTB"
                 style="height: 50px; width: auto; object-fit: contain;">
            <span style="font-size: 22px; font-weight: 800; color: #ffffff;">E-Pusara</span>
        </a>
    </div>

    @php
        $khairatOpen = request()->routeIs('admin.khairat.*') || request()->routeIs('admin.profile.*');
        $deathReportOpen = request()->routeIs('admin.death-reports.*');
    @endphp

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">

                <li class="slide__category">
                    <span class="category-name">Admin</span>
                </li>

                {{-- Dashboard --}}
                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}"
                       class="side-menu__item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                {{-- Pengurusan Khairat --}}
                <li class="slide has-sub {{ $khairatOpen ? 'open' : '' }}">
                    <a href="javascript:void(0);"
                       class="side-menu__item {{ $khairatOpen ? 'active' : '' }}">
                        <i class="bx bx-folder side-menu__icon"></i>
                        <span class="side-menu__label">Pengurusan Khairat</span>
                        <i class="fe fe-chevron-right side-menu__angle"></i>
                    </a>

                    <ul class="slide-menu child1" style="{{ $khairatOpen ? 'display:block;' : '' }}">
                        <li class="slide">
                            <a href="{{ route('admin.khairat.members.index') }}"
                               class="side-menu__item {{ request()->routeIs('admin.khairat.members.*') ? 'active' : '' }}">
                                Senarai Ahli
                            </a>
                        </li>

                        <li class="slide">
                            <a href="{{ route('admin.khairat.dependents.index') }}"
                               class="side-menu__item {{ request()->routeIs('admin.khairat.dependents.*') ? 'active' : '' }}">
                                Senarai Tanggungan
                            </a>
                        </li>

                        <li class="slide">
                            <a href="{{ route('admin.profile.index') }}"
                               class="side-menu__item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                Permohonan Keahlian
                            </a>
                        </li>

                        <li class="slide">
                            <a href="{{ route('admin.khairat.fees.index') }}"
                               class="side-menu__item {{ request()->routeIs('admin.khairat.fees.*') ? 'active' : '' }}">
                                Senarai Yuran
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Laporan Kematian --}}
                <li class="slide">
                    <a href="{{ route('admin.death-reports.index') }}"
                       class="side-menu__item {{ $deathReportOpen ? 'active' : '' }}">
                        <i class="bx bx-notepad side-menu__icon"></i>
                        <span class="side-menu__label">Laporan Kematian</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>