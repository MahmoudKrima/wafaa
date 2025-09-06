<div class="footer-area theme_right" data-background="{{ asset('front/assets/img/footer-bg.jpg') }}">
        <div class="container">
          
            <div class="footer-bottom row">
                <div class="col-lg-6 col-md-5">
                    <span>© WM EXPRESS 2025 | جميع الحقوق محفوظة</span>
                </div>
                <div class="col-lg-6 col-md-7 text-md-end">
                    <ul>
                        <li><a href="{{ route('front.terms') }}">{{__('admin.terms_description')}}</a></li>
                        <li><a href="{{ route('front.privacy') }}">{{__('admin.privacy_description')}}</a></li>
                    </ul>
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