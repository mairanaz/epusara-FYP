<aside class="app-sidebar sticky" id="sidebar">
    <div class="main-sidebar-header d-flex align-items-center justify-content-center">
        @php
            $user = auth()->user();
            $profile = $user->profile ?? null;
            $profileStatus = $profile->status_permohonan ?? null;

            if ($user->account_type === 'tanggungan') {
                $homeRoute = route('dependent.dashboard');
            } elseif ($user->account_type === null) {
                $homeRoute = route('user.profile.create.step1');
            } else {
                $homeRoute = route('user.dashboard');
            }
        @endphp

        <a href="{{ $homeRoute }}" class="header-logo d-flex align-items-center gap-2 text-decoration-none">
            <img src="{{ asset('assets/images/logo_rtb.jpg') }}" alt="Logo RTB"
                 style="height: 50px; width: auto; object-fit: contain;">
            <span style="font-size: 22px; font-weight: 800; color: #ffffff;">E-Pusara</span>
        </a>
    </div>

    <div class="main-sidebar" id="sidebar-scroll">
        <nav class="main-menu-container nav nav-pills flex-column sub-open">
            <ul class="main-menu">

                @if($user->account_type === null)
                    <li class="slide__category"><span class="category-name">Pengguna Baharu</span></li>

                    <li class="slide">
                        <a href="{{ route('user.dashboard') }}"
                           class="side-menu__item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="bx bx-home side-menu__icon"></i>
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('user.profile.create.step1') }}"
                           class="side-menu__item {{ request()->routeIs('user.profile.create.step1') || request()->routeIs('user.profile.create.step2') || request()->routeIs('user.profile.create.step3') || request()->routeIs('user.profile.create.step4') ? 'active' : '' }}">
                            <i class="bx bx-user side-menu__icon"></i>
                            <span class="side-menu__label">Lengkapkan Profil</span>
                        </a>
                    </li>

                @elseif($user->account_type === 'tanggungan')
                    <li class="slide__category"><span class="category-name">Tanggungan</span></li>

                    <li class="slide">
                        <a href="{{ route('dependent.dashboard') }}"
                           class="side-menu__item {{ request()->routeIs('dependent.dashboard') ? 'active' : '' }}">
                            <i class="bx bx-home side-menu__icon"></i>
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('user.profile.show') }}"
                           class="side-menu__item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                            <i class="bx bx-user side-menu__icon"></i>
                            <span class="side-menu__label">Maklumat Diri</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('dependent.main-member') }}"
                           class="side-menu__item {{ request()->routeIs('dependent.main-member') ? 'active' : '' }}">
                            <i class="bx bx-id-card side-menu__icon"></i>
                            <span class="side-menu__label">Ahli Utama</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('death-report.create') }}"
                           class="side-menu__item {{ request()->routeIs('death-report.create') ? 'active' : '' }}">
                            <i class="bx bx-file side-menu__icon"></i>
                            <span class="side-menu__label">Lapor Kematian</span>
                        </a>
                    </li>

                @elseif($user->account_type === 'utama')
                    <li class="slide__category"><span class="category-name">Ahli</span></li>

                    <li class="slide">
                        <a href="{{ route('user.dashboard') }}"
                           class="side-menu__item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <i class="bx bx-home side-menu__icon"></i>
                            <span class="side-menu__label">Dashboard</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('user.profile.show') }}"
                           class="side-menu__item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                            <i class="bx bx-user side-menu__icon"></i>
                            <span class="side-menu__label">Profil</span>
                        </a>
                    </li>

                    <li class="slide">
                        <a href="{{ route('user.dependents.index') }}"
                           class="side-menu__item {{ request()->routeIs('user.dependents.*') ? 'active' : '' }}">
                            <i class="bx bx-group side-menu__icon"></i>
                            <span class="side-menu__label">Tanggungan</span>
                        </a>
                    </li>

                    @if(in_array($profileStatus, ['approved', 'active']))
                        <li class="slide">
                            <a href="{{ route('user.payments.index') }}"
                               class="side-menu__item {{ request()->routeIs('user.payments.*') ? 'active' : '' }}">
                                <i class="bx bx-credit-card side-menu__icon"></i>
                                <span class="side-menu__label">Yuran</span>
                            </a>
                        </li>
                    @endif

                    <li class="slide">
                        <a href="{{ route('death-report.create') }}"
                           class="side-menu__item {{ request()->routeIs('death-report.create') ? 'active' : '' }}">
                            <i class="bx bx-file side-menu__icon"></i>
                            <span class="side-menu__label">Lapor Kematian</span>
                        </a>
                    </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>