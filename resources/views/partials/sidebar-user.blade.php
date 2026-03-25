<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header d-flex align-items-center justify-content-center">
        <a href="{{ route('user.dashboard') }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo RTB"
                 style="height: 50px; width: auto; object-fit: contain;">
            <span style="font-size: 22px; font-weight: 800; color: #ffffff;">E-Pusara</span>
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">

                <li class="slide__category"><span class="category-name">User</span></li>

                <li class="slide">
                    <a href="{{ route('user.dashboard') }}" class="side-menu__item">
                        <i class="bx bx-home side-menu__icon"></i>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('user.profile.show') }}" class="side-menu__item">
                        <i class="bx bx-user side-menu__icon"></i>
                        <span class="side-menu__label">Profil</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('user.dependents.index') }}" class="side-menu__item">
                        <i class="bx bx-group side-menu__icon"></i>
                        <span class="side-menu__label">Tanggungan</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('user.payments.index') }}" class="side-menu__item">
                        <i class="bx bx-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Yuran</span>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>