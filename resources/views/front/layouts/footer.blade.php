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
        
        
        <!---------->
        <div style="background-color:#ffffff45;height:1px;"></div>
        <div class="row">
       
        <div class="col-xl-12 col-lg-12 text-start">
            <div class="social-area" style="text-align:center;margin:20px 0;">
                
                <p style="text-align:center;color:#ffffff;margin:15px 0;">تابعنا على شبكات التواصل الإجتماعي</p>
                
                @if(isset(app('settings')['facebook']) && app('settings')['facebook'] != null)
                <a href="{{app('settings')['facebook']}}"><i class="fa-brands fa-facebook-f"></i></a>
                @endif
                
                @if(isset(app('settings')['instagram']) && app('settings')['instagram'] != null)
                <a href="{{app('settings')['instagram']}}"><i class="fa-brands fa-instagram"></i></a>
                @endif
                
                @if(isset(app('settings')['twitter']) && app('settings')['twitter'] != null)
                <a href="{{app('settings')['twitter']}}"><i class="fa-brands fa-twitter"></i></a>
                @endif
                
                @if(isset(app('settings')['tiktok']) && app('settings')['tiktok'] != null)
                <a href="{{app('settings')['tiktok']}}"><i class="fa-brands fa-tiktok"></i></a>
                @endif
                
                @if(isset(app('settings')['snapchat']) && app('settings')['snapchat'] != null)
                <a href="{{app('settings')['snapchat']}}"><i class="fa-brands fa-snapchat"></i></a>
                @endif
            </div>
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
