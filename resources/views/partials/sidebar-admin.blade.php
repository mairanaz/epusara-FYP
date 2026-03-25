<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header d-flex align-items-center justify-content-center">
        <a href="{{ route('admin.dashboard') }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo RTB"
                 style="height: 50px; width: auto; object-fit: contain;">
            <span style="font-size: 22px; font-weight: 800; color: #ffffff;">E-Pusara</span>
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">

                <li class="slide__category"><span class="category-name">Admin</span></li>

                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard Admin</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="#" class="side-menu__item">
                        <i class="bx bx-cog side-menu__icon"></i>
                        <span class="side-menu__label">Pengurusan</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>