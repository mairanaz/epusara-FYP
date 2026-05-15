<aside class="app-sidebar sticky" id="sidebar-admin" data-sidebar-role="admin">
    <div class="main-sidebar-header d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.dashboard') }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb-removebg-preview.png') }}" alt="Logo RTB"
                 style="height: 50px; width: auto; object-fit: contain;">
            <span style="font-size: 22px; font-weight: 800; color: #ffffff;">E-Pusara</span>
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column">
            <ul class="main-menu">

                {{-- UTAMA --}}
                <li class="slide__category">
                    <span class="category-name">Utama</span>
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
                       class="side-menu__item khairat-toggle {{ request()->routeIs('admin.khairat.members.*') || request()->routeIs('admin.khairat.dependents.*') || request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                       id="khairatToggle">
                        <i class="bx bx-folder side-menu__icon"></i>
                        <span class="side-menu__label">Pengurusan Khairat</span>
                        <i class="bx bx-chevron-right side-menu__angle ms-auto" id="khairatArrow"></i>
                    </a>

                    <ul class="khairat-submenu" id="khairatSubmenu">
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
                                Permohonan Ahli
                            </a>
                        </li>
                    </ul>
                </li>


                {{-- MENU OPERASI --}}
                <li class="slide__category">
                    <span class="category-name">Menu Operasi</span>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.death-reports.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.death-reports.*') ? 'active' : '' }}">
                        <i class="bx bx-notepad side-menu__icon"></i>
                        <span class="side-menu__label">Pengesahan Kematian</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.grave-orders.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.grave-orders.*') ? 'active' : '' }}">
                        <i class="bx bx-building-house side-menu__icon"></i>
                        <span class="side-menu__label">Tempahan Kepukan</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.burial-map.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.burial-map.*') ? 'active' : '' }}">
                        <i class="bx bx-map side-menu__icon"></i>
                        <span class="side-menu__label">Peta Lot Kubur</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.burial-records.index') }}"
                    class="side-menu__item {{ request()->routeIs('admin.burial-records.*') ? 'active' : '' }}">
                        <i class="bx bx-map-pin side-menu__icon"></i>
                        <span class="side-menu__label">Rekod Kubur</span>
                    </a>
                </li>


                {{-- LAPORAN / STATISTIK --}}
                <li class="slide__category">
                    <span class="category-name">Laporan / Statistik</span>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.khairat.fees.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.khairat.fees.*') ? 'active' : '' }}">
                        <i class="bx bx-receipt side-menu__icon"></i>
                        <span class="side-menu__label">Rekod Yuran</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.reports.grave-orders.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.reports.grave-orders.*') ? 'active' : '' }}">
                        <i class="bx bx-bar-chart-alt-2 side-menu__icon"></i>
                        <span class="side-menu__label">Laporan Kepukan</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

<style>
    /*
    |--------------------------------------------------------------------------
    | Sidebar Submenu - Pengurusan Khairat
    |--------------------------------------------------------------------------
    */

    .khairat-submenu {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transform: translateY(-6px);
        padding-left: 0;
        margin: 8px 0 0 0;
        list-style: none;
        will-change: max-height, opacity, transform;
        transition:
            max-height 0.26s cubic-bezier(0.22, 1, 0.36, 1),
            opacity 0.20s ease,
            transform 0.20s ease;
    }

    .khairat-submenu.show {
        max-height: 500px;
        opacity: 1;
        transform: translateY(0);
    }

    .khairat-submenu li {
        list-style: none;
    }

    .submenu-link {
        position: relative;
        display: block;
        text-decoration: none;
        color: #cfd8ff;
        padding: 10px 16px 10px 54px;
        font-size: 14px;
        border-radius: 8px;
        margin: 4px 10px;
        transition:
            background-color 0.18s ease,
            color 0.18s ease,
            transform 0.16s ease;
    }

    .submenu-link::before {
        content: "";
        position: absolute;
        left: 34px;
        top: 50%;
        width: 5px;
        height: 5px;
        border: 1px solid rgba(255,255,255,0.6);
        border-radius: 50%;
        transform: translateY(-50%);
    }

    .submenu-link.active::before {
        border-color: #ffffff;
        background-color: #ffffff;
    }

    .submenu-link:hover {
        background-color: rgba(255,255,255,0.08);
        color: #ffffff;
        transform: translateX(2px);
    }

    .submenu-link.active {
        background-color: rgba(255,255,255,0.12);
        color: #ffffff;
        font-weight: 600;
    }

    .side-menu__angle {
        transition: transform 0.26s cubic-bezier(0.22, 1, 0.36, 1);
        will-change: transform;
    }

    .side-menu__angle.rotate {
        transform: rotate(90deg);
    }

    html[data-khairat-open="true"] #khairatSubmenu {
        max-height: 500px;
        opacity: 1;
        transform: translateY(0);
    }

    html[data-khairat-open="true"] #khairatArrow {
        transform: rotate(90deg);
    }

    .khairat-toggle {
        transition: background-color 0.18s ease, color 0.18s ease;
    }


    /*
    |--------------------------------------------------------------------------
    | Sidebar Category Spacing
    |--------------------------------------------------------------------------
    */

    #sidebar-admin .slide__category {
        margin: 18px 0 8px 0;
        padding: 0 20px;
    }

    #sidebar-admin .slide__category:first-child {
        margin-top: 8px;
    }

    #sidebar-admin .category-name {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.45);
    }

    #sidebar-admin .main-menu {
        padding-bottom: 24px;
    }

    #sidebar-admin .main-menu > .slide {
        margin-bottom: 4px;
    }
</style>

<script>
(function () {
    const isKhairatPage =
        "{{ request()->routeIs('admin.khairat.members.*') || request()->routeIs('admin.khairat.dependents.*') || request()->routeIs('admin.profile.*') ? 'true' : 'false' }}" === "true";

    const savedState = localStorage.getItem('khairatSubmenuOpen');

    if (isKhairatPage) {
        document.documentElement.setAttribute('data-khairat-open', 'true');
        localStorage.setItem('khairatSubmenuOpen', 'true');
    } else {
        document.documentElement.setAttribute(
            'data-khairat-open',
            savedState === 'true' ? 'true' : 'false'
        );
    }
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('khairatToggle');
    const submenu = document.getElementById('khairatSubmenu');
    const arrow = document.getElementById('khairatArrow');

    if (!toggle || !submenu || !arrow) return;

    const isKhairatPage =
        "{{ request()->routeIs('admin.khairat.members.*') || request()->routeIs('admin.khairat.dependents.*') || request()->routeIs('admin.profile.*') ? 'true' : 'false' }}" === "true";

    const applyState = (isOpen) => {
        submenu.classList.toggle('show', isOpen);
        arrow.classList.toggle('rotate', isOpen);
        document.documentElement.setAttribute('data-khairat-open', isOpen ? 'true' : 'false');
        localStorage.setItem('khairatSubmenuOpen', isOpen ? 'true' : 'false');
    };

    if (isKhairatPage) {
        applyState(true);
    } else {
        const savedState = localStorage.getItem('khairatSubmenuOpen') === 'true';
        applyState(savedState);
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();

        const isOpen = !submenu.classList.contains('show');
        applyState(isOpen);
    });
});
</script>