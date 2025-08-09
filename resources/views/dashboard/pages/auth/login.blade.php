<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{ app('settings')['app_name_' . assetLang()] }} | {{ __('admin.login') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ displayImage(app('settings')['fav_icon']) }}" />
    <link href="https://fonts.googleapis.com/css?family=Quicksand:400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets_' . assetLang()) }}/bootstrap/css/bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets_' . assetLang()) }}/assets/css/plugins.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets_' . assetLang()) }}/assets/css/authentication/form-1.css" rel="stylesheet"
        type="text/css" />
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets_' . assetLang()) }}/assets/css/forms/theme-checkbox-radio.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets_' . assetLang()) }}/assets/css/forms/switches.css">
    <link rel="stylesheet" href="{{ asset('vendor/toastr/build/toastr.min.css') }}">
</head>

<body class="form">


    <div class="form-container">
        <div class="form-form">
            <div class="form-form-wrap">
                <div class="form-container">
                    <div class="form-content">

                        <h1 class="">{{ __('admin.login_to') . " " }}<span
                                class="brand-name">{{ app('settings')['app_name_' . assetLang()] }}</span></h1>
                        <form class="text-left" method="POST" action="{{ route('admin.auth.login') }}">
                            @csrf
                            <div class="form">

                                <div id="username-field" class="field-wrapper input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-at-sign">
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                    </svg>
                                    <input id="phone" name="phone" type="phone" class="form-control"
                                        placeholder="{{ __('admin.phone') }}" value="{{ old('phone') }}">
                                </div>

                                <div id="password-field" class="field-wrapper input mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-lock">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2">
                                        </rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                    <input id="password" name="password" type="password" class="form-control"
                                        placeholder="{{ __('admin.password') }}">
                                </div>
                                <div class="d-sm-flex justify-content-between">
                                    <div class="field-wrapper toggle-pass">
                                        <p class="d-inline-block">{{ __('admin.show_password') }}</p>
                                        <label class="switch s-primary">
                                            <input type="checkbox" id="toggle-password" class="d-none">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                    <div class="field-wrapper">
                                        <button type="submit" class="btn btn-primary"
                                            value="">{{ __('admin.login') }}</button>
                                    </div>

                                </div>


                                <div class="field-wrapper">
                                    <a href="{{ route('admin.auth.forgetPasswordForm') }}"
                                        class="forgot-pass-link">{{ __('admin.forget_password') }}</a>
                                </div>

                            </div>
                        </form>
                        <p class="terms-conditions">{{ __('admin.Copyright') }} © {{date('Y')}}
                            {{ __('admin.all_rights_reserved') }}. <a
                                href="#">{{app('settings')['app_name_' . assetLang()]}}</a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
        <div class="form-image">
            <div class="l-image">
            </div>
        </div>
    </div>


    <script src="{{ asset('assets_' . assetLang()) }}/assets/js/libs/jquery-3.1.1.min.js"></script>
    <script src="{{ asset('assets_' . assetLang()) }}/bootstrap/js/popper.min.js"></script>
    <script src="{{ asset('assets_' . assetLang()) }}/bootstrap/js/bootstrap.min.js"></script>

    <script src="{{ asset('assets_' . assetLang()) }}/assets/js/authentication/form-1.js"></script>
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

        @if(session()->has('Success'))
        toastr.success('{{ session()->get('
            Success ') }}');
        @endif
        @if(session()->has('Error'))
        toastr.error('{{ session()->get('
            Error ') }}');
        @endif

        @if(session()->has('Warn'))
        toastr.warning('{{ session()->get('
            Warn ') }}');
        @endif

        @if($errors->any())
        @foreach($errors->all() as $error)
        toastr.error('{{ $error }}');
        @endforeach
        @endif
    </script>
</body>

</html>