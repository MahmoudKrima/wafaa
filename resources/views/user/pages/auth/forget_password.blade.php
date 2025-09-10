<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{ app('settings')['app_name_' . assetLang()] }} | {{ __('admin.forget_password') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ displayImage(app('settings')['fav_icon']) }}" />

    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    @if (App::getLocale() === 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
    @endif

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

            <div class="header-container">
                <header style="background-color:#eeebeb59 !important;border:none !important;display:flex;justify-content:flex-start;align-items:center;padding:10px 25px; !important;">
                    <div> {{__('admin.choose_lang')}} </div>
                    <ul class="navbar-item flex-row navbar-dropdown" style="list-style:none; margin:0; padding:0;">
                        <li class="nav-item dropdown language-dropdown more-dropdown">

                            <div class="dropdown custom-dropdown-icon">
                                <a class="dropdown-toggle btn" href="#" role="button" id="customDropdown" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false"
                                   style="display:flex; align-items:center; gap:6px; padding:4px 8px;">
                                    <img src="{{ asset('assets_' . assetLang()) }}/assets/img/{{ app()->getLocale() }}.png"
                                         alt="flag"
                                         style="width:24px; height:auto; object-fit:cover; border-radius:3px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round"
                                         style="margin-{{ App::getLocale()==='ar' ? 'right' : 'left' }}:4px;">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </a>

                                <div class="dropdown-menu {{ App::getLocale()==='ar' ? 'dropdown-menu-left' : 'dropdown-menu-right' }} animated fadeInUp"
                                     aria-labelledby="customDropdown" style="min-width:130px; padding:4px 0;">
                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                        @if ($localeCode == app()->getLocale()) @continue @endif
                                        <a class="dropdown-item d-flex align-items-center"
                                           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
                                           style="gap:6px; padding:6px 10px;">
                                            <img src="{{ asset('assets_' . assetLang()) }}/assets/img/{{ $localeCode }}.png"
                                                 alt="flag"
                                                 style="width:20px; height:auto; object-fit:cover; border-radius:3px;">
                                            <span dir="auto">{{ $properties['native'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    </ul>
                </header>
            </div>

            <div style="text-align:center;">
                <img src="{{ displayImage(app('settings')['logo']) }}" style="height:120px;width:120px;">
            </div>


            <div class="form-form-wrap">
                <div class="form-container" style="min-height:auto !important;">
                    <div class="form-content">

                        <h1 class="">{{ __('admin.Password Recovery') }}</h1>
                        <p class="signup-link">{{ __('admin.Enter your email and instructions will sent to you!') }}
                        </p>
                        <form class="text-left" method="POST" action="{{ route('user.auth.forgetPassword') }}">
                            @csrf
                            <div class="form">

                                <div id="email-field" class="field-wrapper input">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-at-sign">
                                        <circle cx="12" cy="12" r="4"></circle>
                                        <path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"></path>
                                    </svg>
                                    <input id="email" name="email" type="text" value="{{ old('email') }}"
                                        placeholder="{{ __('admin.email') }}">
                                </div>
                                <div class="d-sm-flex justify-content-between">
                                    <div class="field-wrapper">
                                        <button type="submit" class="btn btn-primary"
                                            value="">{{ __('admin.reset') }}</button>
                                    </div>
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
</body>

</html>
