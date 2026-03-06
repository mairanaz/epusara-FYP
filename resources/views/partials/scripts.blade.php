{{-- resources/views/partials/scripts.blade.php --}}

{{-- Popper + Bootstrap --}}
{{-- <script src="{{ asset('assets/libs/@popperjs/core/umd/popper.min.js') }}"></script> --}}
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

{{-- Defaultmenu / Sidebar --}}
<script src="{{ asset('assets/js/defaultmenu.min.js') }}"></script>

{{-- Simplebar + Waves (UI) --}}
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>

{{-- Sticky header / layout helpers --}}
<script src="{{ asset('assets/js/sticky.js') }}"></script>

{{-- (OPTIONAL tapi banyak template perlukan) --}}
<script src="{{ asset('assets/libs/@simonwep/pickr/pickr.es5.min.js') }}"></script>
<script src="{{ asset('assets/js/color-picker.js') }}"></script>

{{-- Dashboard libs (ikut page yang kau guna) --}}
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

{{-- Main custom --}}
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/custom-switcher.min.js') }}"></script>

{{-- Kalau template ada file dashboard JS (contoh: index.js), letak bawah sekali --}}
{{-- <script src="{{ asset('assets/js/sales-dashboard.js') }}"></script> --}}