<div class="footer-area theme_right" data-background="{{ asset('front/assets/img/footer-bg.jpg') }}">
        <div class="container">

            <div class="footer-bottom row">
                <div class="col-lg-4 col-md-4">
                    <span>© {{app('settings')['app_name_' . assetLang()]}} {{date('Y')}} | {{ __('user.all_rights_reserved') }}</span>
                </div>
                <div class="col-lg-4 col-md-4">
                    <ul>
                        <li><a href="{{ route('front.terms') }}">{{__('admin.terms_description')}}</a></li>
                        <li><a href="{{ route('front.policy') }}">{{__('admin.privacy_description')}}</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4">
                    <span>  مؤسسة بسنت سويت التجارية - السجل التجاري : 4030442974  </span>
                </div>
            </div>
            
        </div>
    </div>


    <div class="search-popup">
        <span class="search-back-drop"></span>

        <div class="search-inner">
            <div class="container">
                <div class="logo">
                    <a class="navbar-brand" href="{{ route('front.home') }}"><img src="{{ displayImage(app('settings')['logo']) }}" alt=""></a>
                </div>
                <button class="close-search"><span class="la la-times"></span></button>

            </div>
        </div>
    </div>


    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
