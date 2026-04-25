<!DOCTYPE html>
<html lang="en" dir="ltr"
      data-nav-layout="vertical"
      data-theme-mode="light"
      data-header-styles="light"
      data-menu-styles="dark"
      data-toggled="close">

<head>
    @include('partials.head')
</head>

<body class="d-flex flex-column min-vh-100">

    @include('partials.switcher')
    @include('partials.search-modal')

    {{-- Loader --}}
  {{-- <div id="loader"> -- }}  
        <img src="{{ asset('assets/images/media/loader.svg') }}" alt="">
   {{-- </div> --}} 

    <div class="page flex-grow-1">

        @include('partials.header')

        @auth
            @if(auth()->user()->role === 'admin')
                @include('partials.sidebar-admin')
            @else
                @include('partials.sidebar-user')
            @endif
        @endauth

        <div class="main-content app-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('partials.footer')

    </div>

    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill fs-20"></i></span>
    </div>
    {{-- <div id="responsive-overlay"></div> --}}

    @include('partials.scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>