<!DOCTYPE html>
<html lang="en" dir="ltr">
@include('partials.head')

<body>
  <div class="page">
    @include('partials.header')
    @include('partials.sidebar')

    <div class="main-content app-content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>

    @include('partials.footer')
  </div>

  @include('partials.scripts')
  @stack('scripts')
</body>
</html>