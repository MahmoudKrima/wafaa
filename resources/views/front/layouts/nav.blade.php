<div id="shikor_preloader">
    <div id="shikor_circle_loader"></div>
    <div class="shikor_loader_logo"><img src="{{ asset('front/assets/img/preload.png') }}" alt="Preload"></div>
</div>


<div id="home" class="header-top-area secondary-bg">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <span><i class="fa-solid fa-envelope"></i>{{app('settings')['email']}}</span>
                <span><i class="fa-solid fa-location-dot"></i>{{app('settings')['address_' . assetLang()]}}</span>
                {{--<span><i class="fa-solid fa-clock"></i>{{__('admin.working_hours')}}</span>--}}
            </div>
            <div class="col-xl-4 col-lg-4 text-start">
                <div class="social-area">
                    <a href="{{app('settings')['facebook']}}"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="{{app('settings')['instagram']}}"><i class="fa-brands fa-instagram"></i></a>
                    <a href="{{app('settings')['twitter']}}"><i class="fa-brands fa-twitter"></i></a>
                    <a href="{{app('settings')['tiktok']}}"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="{{app('settings')['snapchat']}}"><i class="fa-brands fa-snapchat"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Header Area  -->

<div id="header-sticky" class="header-area">
    <div class="navigation">
        <div class="container">
            <div class="header-inner-box">

                <div class="logo">
                    <a href="{{ route('front.home') }}"><img src="{{ displayImage(app('settings')['logo']) }}" style="width:90px;height:90px;"></a>
                </div>

                <div class="main-menu d-none d-lg-inline-block">
                    <ul>

                        @if(isRoute(['front.terms','front.policy']))
                            <li><a href="{{ route('front.home') }}#home">{{__('admin.home')}}</a></li>

                            <li><a href="{{ route('front.home') }}#about">{{__('admin.about_us')}}</a></li>

                            <li><a href="{{ route('front.home') }}#clients">{{__('admin.our_clients')}}</a></li>

                            <li><a href="{{ route('front.home') }}#services">{{__('admin.services')}}</a></li>

                            <li><a href="{{ route('front.home') }}#opinions">{{__('admin.opinions')}}</a></li>

                            <li><a href="{{ route('front.home') }}#contact">{{__('admin.contact')}}</a></li>

                            <li><a href="{{ route('front.home') }}#faqs">{{__('admin.faqs')}}</a></li>
                        @else
                            <li><a href="#home">{{__('admin.home')}}</a></li>

                            <li><a href="#about">{{__('admin.about_us')}}</a></li>

                            <li><a href="#clients">{{__('admin.our_clients')}}</a></li>

                            <li><a href="#services">{{__('admin.services')}}</a></li>

                            <li><a href="#opinions">{{__('admin.opinions')}}</a></li>

                            <li><a href="#contact">{{__('admin.contact')}}</a></li>

                            <li><a href="#faqs">{{__('admin.faqs')}}</a></li>
                        @endif

                    </ul>
                </div>

                <div class="mobile-nav-bar d-block col-6 d-lg-none">
                    <div class="mobile-nav-wrap">
                        <div id="hamburger">
                            <i class="las la-bars"></i>
                        </div>
                        <div class="mobile-nav">
                            <button type="button" class="close-nav">
                                <i class="las la-times-circle"></i>
                            </button>
                            <nav class="sidebar-nav">
                                <ul class="metismenu" id="mobile-menu">

                                    @if(isRoute(['front.terms','front.policy']))
                                        <li><a href="{{ route('front.home') }}#home">{{__('admin.home')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#about">{{__('admin.about_us')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#clients">{{__('admin.our_clients')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#services">{{__('admin.services')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#opinions">{{__('admin.opinions')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#contact">{{__('admin.contact')}}</a></li>

                                        <li><a href="{{ route('front.home') }}#faqs">{{__('admin.faqs')}}</a></li>
                                    @else
                                        <li><a href="#home">{{__('admin.home')}}</a></li>

                                        <li><a href="#about">{{__('admin.about_us')}}</a></li>

                                        <li><a href="#clients">{{__('admin.our_clients')}}</a></li>

                                        <li><a href="#services">{{__('admin.services')}}</a></li>

                                        <li><a href="#opinions">{{__('admin.opinions')}}</a></li>

                                        <li><a href="#contact">{{__('admin.contact')}}</a></li>

                                        <li><a href="#faqs">{{__('admin.faqs')}}</a></li>
                                    @endif

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="header-right d-none d-lg-block">
                    <div class="contact-icon-wrap">
                        {{--<i class="fa-brands fa-whatsapp" style="font-size:30px;"></i>
                        <div class="contact-info">
                            <p>{{__('admin.whatsapp')}}</p>
                            <p><b>{{app('settings')['whatsapp']}}</b></p>
                        </div>--}}
                    </div>
                </div>

                
            </div>
        </div>
    </div>
</div>
