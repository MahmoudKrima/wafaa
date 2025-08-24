<!-- Popper JS -->
<script src="{{ asset('front/assets/js/popper.min.js') }}"></script>
<!-- Bootstrap JS -->
<script src="{{ asset('front/assets/js/bootstrap.min.js') }}"></script>
<!-- Wow JS -->
<script src="{{ asset('front/assets/js/wow.min.js') }}"></script>
<!-- Way Points JS -->
<script src="{{ asset('front/assets/js/jquery.waypoints.min.js') }}"></script>
<!-- Counter Up JS -->
<script src="{{ asset('front/assets/js/jquery.counterup.min.js') }}"></script>
<!-- Owl Carousel JS -->
<script src="{{ asset('front/assets/js/owl.carousel.min.js') }}"></script>
<!-- Slick Slider JS -->
<script src="{{ asset('front/assets/js/slick.min.js') }}"></script>
<!-- Magnific Popup JS -->
<script src="{{ asset('front/assets/js/magnific-popup.min.js') }}"></script>
<!-- Sticky JS -->
<script src="{{ asset('front/assets/js/jquery.sticky.js') }}"></script>
<!-- Nice Select JS -->
<script src="{{ asset('front/assets/js/jquery.nice-select.min.js') }}"></script>
<!-- Appear JS -->
<script src="{{ asset('front/assets/js/jquery.appear.min.js') }}"></script>
<!-- Odometer JS -->
<script src="{{ asset('front/assets/js/odometer.min.js') }}"></script>
<!-- Back To Top JS -->
<script src="{{ asset('front/assets/js/backToTop.js') }}"></script>
<!-- Metis Menu JS -->
<script src="{{ asset('front/assets/js/metismenu.js') }}"></script>
<!-- Main JS -->
<script src="{{ asset('front/assets/js/main.js') }}"></script>

<script src="{{ asset('vendor/toastr/build/toastr.min.js') }}"></script>

<script>
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    @if (session()->has('Success'))
        toastr.success('{{ session()->get('Success') }}');
    @endif
    @if (session()->has('Error'))
        toastr.error('{{ session()->get('Error') }}');
    @endif

    @if (session()->has('Warn'))
        toastr.warning('{{ session()->get('Warn') }}');
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            toastr.error('{{ $error }}');
        @endforeach
    @endif

</script>

@stack('js')