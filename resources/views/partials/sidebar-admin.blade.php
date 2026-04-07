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
    @endphp

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column">
            <ul class="main-menu">

                <li class="slide__category">
                    <span class="category-name">Admin</span>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}"
                       class="side-menu__item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="javascript:void(0);"
                       class="side-menu__item khairat-toggle {{ $khairatOpen ? 'active' : '' }}"
                       id="khairatToggle">
                        <i class="bx bx-folder side-menu__icon"></i>
                        <span class="side-menu__label">Pengurusan Khairat</span>
                        <i class="bx bx-chevron-right side-menu__angle ms-auto {{ $khairatOpen ? 'rotate' : '' }}" id="khairatArrow"></i>
                    </a>

                    <ul class="khairat-submenu {{ $khairatOpen ? 'show' : '' }}" id="khairatSubmenu">
                        <li>
                            <a href="{{ route('admin.khairat.members.index') }}"
                               class="submenu-link {{ request()->routeIs('admin.khairat.members.*') ? 'active' : '' }}">
                                Senarai Ahli
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.khairat.dependents.index') }}"
                               class="submenu-link {{ request()->routeIs('admin.khairat.dependents.*') ? 'active' : '' }}">
                                Senarai Tanggungan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.profile.index') }}"
                               class="submenu-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                Permohonan Keahlian
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.khairat.fees.index') }}"
                               class="submenu-link {{ request()->routeIs('admin.khairat.fees.*') ? 'active' : '' }}">
                                Senarai Yuran
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.death-reports.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.death-reports.*') ? 'active' : '' }}">
                        <i class="bx bx-notepad side-menu__icon"></i>
                        <span class="side-menu__label">Laporan Kematian</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    .khairat-submenu {
        display: none;
        padding-left: 0;
        margin: 8px 0 0 0;
        list-style: none;
    }

    .khairat-submenu.show {
        display: block;
    }

    .khairat-submenu li {
        list-style: none;
    }

    .submenu-link {
        display: block;
        text-decoration: none;
        color: #cfd8ff;
        padding: 10px 16px 10px 54px;
        font-size: 14px;
        border-radius: 8px;
        margin: 4px 10px;
        transition: all 0.2s ease;
    }

    .submenu-link:hover {
        background-color: rgba(255,255,255,0.08);
        color: #ffffff;
    }

    .submenu-link.active {
        background-color: rgba(255,255,255,0.12);
        color: #ffffff;
        font-weight: 600;
    }

    .side-menu__angle {
        transition: transform 0.25s ease;
    }

    .side-menu__angle.rotate {
        transform: rotate(90deg);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('khairatToggle');
        const submenu = document.getElementById('khairatSubmenu');
        const arrow = document.getElementById('khairatArrow');

        if (toggle && submenu && arrow) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                submenu.classList.toggle('show');
                arrow.classList.toggle('rotate');
            });
        }
    });
</script>