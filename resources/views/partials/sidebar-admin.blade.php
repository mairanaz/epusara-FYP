<aside class="app-sidebar sticky epusara-admin-sidebar" id="sidebar-admin" data-sidebar-role="admin">
   <div class="main-sidebar-header epusara-sidebar-logo d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.dashboard') }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb-removebg-preview.png') }}"
                alt="Logo RTB"
                class="epusara-admin-logo-img">
            <span class="epusara-admin-brand">E-Pusara</span>
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

                {{-- Laporan Ahli --}}
                <li class="slide">
                    <a href="{{ route('admin.reports.members.index') }}"
                    class="side-menu__item {{ request()->routeIs('admin.reports.members.*') ? 'active' : '' }}">
                        <i class="bx bx-group side-menu__icon"></i>
                        <span class="side-menu__label">Laporan Ahli</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.khairat.fees.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.khairat.fees.*') ? 'active' : '' }}">
                        <i class="bx bx-receipt side-menu__icon"></i>
                        <span class="side-menu__label">Rekod Yuran</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('admin.reports.deaths.index') }}"
                       class="side-menu__item {{ request()->routeIs('admin.reports.deaths.*') ? 'active' : '' }}">
                        <i class="bx bx-user-x side-menu__icon"></i>
                        <span class="side-menu__label">Laporan Kematian</span>
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
/* =========================================================
   FIX SIDEBAR COMPACT - ePusara Admin
========================================================= */

#sidebar-admin.epusara-admin-sidebar {
    background: #003f3a !important;
    border-right: 0 !important;
    box-shadow: 4px 0 18px rgba(15, 23, 42, 0.08);
    height: 100vh !important;
    overflow: hidden !important;
}

/* Header logo jangan terlalu tinggi */
#sidebar-admin .main-sidebar-header {
    height: 88px !important;
    background: #003f3a !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10) !important;
    padding: 10px 14px !important;
}

#sidebar-admin .epusara-admin-logo-img {
    height: 54px !important;
    width: 54px !important;
    object-fit: contain !important;
}

#sidebar-admin .epusara-admin-brand {
    font-size: 22px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    line-height: 1 !important;
    white-space: nowrap !important;
}

/* Bahagian menu boleh scroll tapi scrollbar disorok */
#sidebar-admin .main-sidebar {
    background: #003f3a !important;
    height: calc(100vh - 88px) !important;
    max-height: calc(100vh - 88px) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    padding: 14px 0 20px !important;
    scrollbar-width: none !important;
}

#sidebar-admin .main-sidebar::-webkit-scrollbar {
    width: 0 !important;
    display: none !important;
}

#sidebar-admin .main-menu {
    padding: 0 0 18px !important;
    margin: 0 !important;
    background: #003f3a !important;
}

/* Kecilkan jarak category */
#sidebar-admin .slide__category {
    margin: 14px 0 6px !important;
    padding: 0 22px !important;
}

#sidebar-admin .slide__category:first-child {
    margin-top: 0 !important;
}

#sidebar-admin .category-name {
    display: block;
    color: rgba(209, 250, 229, 0.55) !important;
    font-size: 10px !important;
    font-weight: 800 !important;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

/* Kecilkan item menu */
#sidebar-admin .slide {
    margin: 0 !important;
    padding: 0 12px !important;
    background: transparent !important;
}

#sidebar-admin .side-menu__item {
    position: relative;
    width: 100% !important;
    min-height: 38px !important;
    margin: 0 !important;
    padding: 9px 12px !important;
    border-radius: 10px !important;
    background: transparent !important;
    color: #d1fae5 !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    transition: background-color 0.18s ease, color 0.18s ease;
}

#sidebar-admin .side-menu__item:hover {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #ffffff !important;
}

#sidebar-admin .side-menu__item.active {
    background: #0f766e !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: inset 4px 0 0 #99f6e4;
}

#sidebar-admin .side-menu__icon {
    width: 20px !important;
    min-width: 20px !important;
    height: 20px !important;
    margin: 0 !important;
    color: inherit !important;
    font-size: 18px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}

#sidebar-admin .side-menu__label {
    color: inherit !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    line-height: 1.15 !important;
    white-space: normal !important;
}

#sidebar-admin .side-menu__angle {
    color: inherit !important;
    font-size: 16px !important;
    transition: transform 0.22s ease;
}

#sidebar-admin .side-menu__angle.rotate {
    transform: rotate(90deg);
}

/* Submenu lebih compact */
#sidebar-admin .khairat-submenu {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-4px);
    padding-left: 0;
    margin: 3px 0 0 0;
    list-style: none;
    transition:
        max-height 0.22s ease,
        opacity 0.18s ease,
        transform 0.18s ease;
}

#sidebar-admin .khairat-submenu.show {
    max-height: 180px;
    opacity: 1;
    transform: translateY(0);
}

#sidebar-admin .khairat-submenu li {
    list-style: none;
}

#sidebar-admin .submenu-link {
    position: relative;
    display: block;
    text-decoration: none;
    color: rgba(209, 250, 229, 0.82) !important;
    padding: 7px 10px 7px 40px !important;
    font-size: 12.8px !important;
    font-weight: 500;
    line-height: 1.2 !important;
    border-radius: 8px;
    margin: 2px 8px 2px 10px;
    background: transparent !important;
}

#sidebar-admin .submenu-link::before {
    content: "";
    position: absolute;
    left: 23px;
    top: 50%;
    width: 5px;
    height: 5px;
    border: 1px solid rgba(209, 250, 229, 0.70);
    border-radius: 50%;
    transform: translateY(-50%);
}

#sidebar-admin .submenu-link:hover {
    background-color: rgba(16, 185, 129, 0.16) !important;
    color: #ffffff !important;
}

#sidebar-admin .submenu-link.active {
    background-color: rgba(20, 184, 166, 0.28) !important;
    color: #ffffff !important;
    font-weight: 700;
}

#sidebar-admin .submenu-link.active::before {
    border-color: #99f6e4;
    background-color: #99f6e4;
}

/* Auto open hanya bila page khairat */
html[data-khairat-open="true"] #khairatSubmenu {
    max-height: 180px;
    opacity: 1;
    transform: translateY(0);
}

html[data-khairat-open="true"] #khairatArrow {
    transform: rotate(90deg);
}

@media (max-width: 991px) {
    #sidebar-admin.epusara-admin-sidebar {
        width: 280px !important;
        background: #003f3a !important;
    }
}
</style>

<script>
(function () {
    const isKhairatPage =
        "{{ request()->routeIs('admin.khairat.members.*') || request()->routeIs('admin.khairat.dependents.*') || request()->routeIs('admin.profile.*') ? 'true' : 'false' }}" === "true";

    const savedState = localStorage.getItem('khairatSubmenuOpen');

    let shouldOpen = false;

    if (savedState === null) {
        shouldOpen = isKhairatPage;
    } else {
        shouldOpen = savedState === 'true';
    }

    document.documentElement.setAttribute('data-khairat-open', shouldOpen ? 'true' : 'false');
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

    const savedState = localStorage.getItem('khairatSubmenuOpen');

    const applyState = (isOpen) => {
        submenu.classList.toggle('show', isOpen);
        arrow.classList.toggle('rotate', isOpen);
        document.documentElement.setAttribute('data-khairat-open', isOpen ? 'true' : 'false');
        localStorage.setItem('khairatSubmenuOpen', isOpen ? 'true' : 'false');
    };

    if (savedState === null) {
        applyState(isKhairatPage);
    } else {
        applyState(savedState === 'true');
    }

    toggle.addEventListener('click', function (e) {
        e.preventDefault();

        const isOpen = !submenu.classList.contains('show');
        applyState(isOpen);
    });
});
</script>