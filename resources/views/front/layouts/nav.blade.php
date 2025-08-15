<div id="shikor_preloader">
    <div id="shikor_circle_loader"></div>
    <div class="shikor_loader_logo"><img src="{{ asset('front/assets/img/preload.png') }}" alt="Preload"></div>
</div>

<!-- Header Top Area -->

<div id="home" class="header-top-area secondary-bg">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-8">
                <span><i class="fa-solid fa-envelope"></i>info@example.com</span>
                <span><i class="fa-solid fa-location-dot"></i>المملكة العربية السعودية ، جدة</span>
                <span><i class="fa-solid fa-clock"></i>السبت : الخميس</span>
            </div>
            <div class="col-xl-4 col-lg-4 text-start">
                <div class="social-area">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
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
                    <a href="{{ route('front.home') }}"><img src="{{ asset('front/assets/img/logo.png') }}" alt=""></a>
                </div>

                <div class="main-menu d-none d-lg-inline-block">
                    <ul>

                        <li><a href="#home">الرئيسية</a></li>

                        <li><a href="#about">من نحن</a></li>

                        <li><a href="#clients">عملائنا</a></li>

                        <li><a href="#services">خدماتنا</a></li>

                        <li><a href="#opinions">أراء العملاء</a></li>

                        <li><a href="#contact">تواصل معنا</a></li>

                        <li><a href="#faqs">الأسئلة الشائعة</a></li>
                    </ul>
                </div>

                <div class="mobile-nav-bar d-block col-6 d-lg-none">
                    <div class="mobile-nav-wrap">
                        <div id="hamburger">
                            <i class="las la-bars"></i>
                        </div>
                        <!-- mobile menu - responsive menu  -->
                        <div class="mobile-nav">
                            <button type="button" class="close-nav">
                                <i class="las la-times-circle"></i>
                            </button>
                            <nav class="sidebar-nav">
                                <ul class="metismenu" id="mobile-menu">

                                    <li><a href="#home">الرئيسية</a></li>

                                    <li><a href="#about">من نحن</a></li>

                                    <li><a href="#clients">عملائنا</a></li>

                                    <li><a href="#services">خدماتنا</a></li>

                                    <li><a href="#opinions">أراء العملاء</a></li>

                                    <li><a href="#contact">تواصل معنا</a></li>

                                    <li><a href="#faqs">الأسئلة الشائعة</a></li>

                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="header-right d-none d-lg-block">
                    <div class="contact-icon-wrap">
                        <img src="{{ asset('front/assets/img/chat.png') }}" alt="">
                        <div class="contact-info">
                            <p>واتساب</p>
                            <p><b>+201016202064</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>