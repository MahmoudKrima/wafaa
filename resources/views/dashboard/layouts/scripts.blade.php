<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{ asset('assets_' . assetLang()) }}/assets/js/libs/jquery-3.1.1.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/bootstrap/js/popper.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/bootstrap/js/bootstrap.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/plugins/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/assets/js/app.js"></script>
<script>
    $(document).ready(function() {
        App.init();
    });
</script>
<script src="{{ asset('assets_' . assetLang()) }}/assets/js/custom.js"></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
<script src="{{ asset('vendor/toastr/build/toastr.min.js') }}"></script>
<!-- END GLOBAL MANDATORY SCRIPTS -->
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            cache: false,
            beforeSend: function(xhr) {
                xhr.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
                xhr.setRequestHeader('Pragma', 'no-cache');
                xhr.setRequestHeader('Expires', '0');
            }
        });
    });
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

    $('.btn-dlt').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');

        var url = form.attr('action');
        var method = form.attr('method');
        swal({
            title: '{{ __('admin.are_you_sure_delete') }}',
            icon: 'warning',
            buttons: ["{{ __('admin.no') }}", "{{ __('admin.yes') }}"],
        }).then(function(value) {
            if (value) {
                form.submit();
            }
        });
    });

    $('.btn-alert').on('click', function(e) {
        e.preventDefault();
        var form = $(this).closest('form');

        var url = form.attr('action');
        var method = form.attr('method');
        swal({
            title: '{{ __('admin.are_you_sure_update_status') }}',
            icon: 'warning',
            buttons: ["{{ __('admin.no') }}", "{{ __('admin.yes') }}"],
        }).then(function(value) {
            if (value) {
                form.submit();
            }
        });
    });
</script>
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
@stack('js')
<!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM SCRIPTS -->
