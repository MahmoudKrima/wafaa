@extends('front.layouts.app')
@section('title', __('admin.home_page'))

@section('content')
<div id="home-3" class="homepage-slides owl-carousel">
    @foreach($sliders as $slider)
    <div class="single-slide-item d-flex align-items-center"
        data-background="{{ displayImage($slider->image)}}">
        <div class="overlay"></div>
        <div class="hero-area-content">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12 wow fadeInUp animated" data-wow-delay=".2s">
                        <div class="section-title">
                            @if($slider->subtitle)
                            <h6 class="text-white">{{ $slider->subtitle}}</h6>
                            @endif
                            <h1 class="text-white">{{ $slider->title}}</h1>
                        </div>
                        <p class="text-white">{{ $slider->description }}</p>
                        @if($slider->button_text && $slider->button_url)
                        <a href="{{ $slider->button_url }}" target="_blank" class="theme-btn mt-40">{{ $slider->button_text }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div id="about" class="faq-section section-padding theme_right" style="padding-top:150px;">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6 wow fadeInUp animated" data-wow-delay="200ms">
                <div class="section-title">
                    <h6>{{ $about->subtitle}}</h6>
                    <h2>{{ $about->title}} <span>{{ app('settings')['app_name_' . assetLang()] }}</span></h2>
                </div>
                <div class="faq-wrap mt-0">
                    <div class="cp-custom-accordion mt-60">
                        <div class="accordions" id="accordionExample">
                            @foreach($aboutItems as $index => $item)
                            <div class="accordion-items">
                                <h2 class="accordion-header" id="heading{{ $index + 1 }}">
                                    <button class="accordion-buttons {{ $index == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index + 1 }}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index + 1 }}">
                                        <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $item->title }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $index + 1 }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index + 1 }}" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        {{ $item->description }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 wow fadeInRight animated" data-wow-delay="200ms">
                <div class="faq-img-wrap">
                    <img src="{{displayImage($about->image) }}" alt="{{ $about->title }}" style="float:left;">
                </div>
            </div>
        </div>
    </div>
</div>


<div id="clients" class="about-section gray-bg theme_right" style="padding-top:120px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-9 col-lg-9 wow fadeInUp animated" data-wow-delay="300ms">
                <div class="about-content-wrap">
                    <div class="section-title">
                        <h2><span>شركاء النجاح</span></h2>
                    </div>
                    <p>في دبليو إام ، نفخر بشراكتنا مع رواد الصناعة لتقديم أفضل خدمات الشحن والتوصيل. تعاوننا معهم يمنح عملائنا خيارات متعددة وحلول شحن متكاملة، مما يُعزز من قدرتنا على تلبية متطلباتكم بكفاءة وفعالية.</p>
                </div>
            </div>

            <!-- Clients Section  -->
            <div class="client-section section-padding pt-4" style="direction:ltr;">
                <div class="container">
                    <div class="row">
                        <div class="client-wrapper owl-carousel">
                            <div class="single-client-item">
                                <img src="{{ asset('front/assets/img/clients/1.png') }}" alt="">
                            </div>
                            <div class="single-client-item">
                                <img src="{{ asset('front/assets/img/clients/2.png') }}" alt="">
                            </div>
                            <div class="single-client-item">
                                <img src="{{ asset('front/assets/img/clients/3.png') }}" alt="">
                            </div>
                            <div class="single-client-item">
                                <img src="{{ asset('front/assets/img/clients/4.png') }}" alt="">
                            </div>
                            <div class="single-client-item">
                                <img src="{{ asset('front/assets/img/clients/5.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>



<!-- Service Section  -->
<div id="services" class="service-section section-padding pb-60 theme-bg theme_right">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-xl-9 col-lg-9 wow fadeInUp animated" data-wow-delay="200ms">
                <div class="section-title mb-0">
                    <h2> <span>الخدمات التي نقدمها</span></h2>
                </div>

                <p class="text-white wow fadeInDown animated mt-3" data-wow-delay="400ms">
                    نعمل في دبليو إم اكسبرس على تقديم تجربة شحن استثنائية لكافة عملاءنا، مع تركيز على الكفاءة، السرعة، والموثوقية.
                </p>

            </div>

        </div>
        <div class="row mt-100">

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="200ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/1.png') }}" alt="">
                    </div>
                    <h4>الشفافية والمصداقية</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="400ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/2.png') }}" alt="">
                    </div>
                    <h4>أسعار مخفضة</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="600ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/3.png') }}" alt="">
                    </div>
                    <h4>دعم فني على مدار الساعة</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="200ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/1.png') }}" alt="">
                    </div>
                    <h4>الشفافية والمصداقية</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="400ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/2.png') }}" alt="">
                    </div>
                    <h4>أسعار مخفضة</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>

            <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="600ms">
                <div class="single-service-wrap">
                    <div class="service-icon">
                        <img src="{{ asset('front/assets/img/service/3.png') }}" alt="">
                    </div>
                    <h4>دعم فني على مدار الساعة</h4>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. </p>
                </div>
            </div>



        </div>
    </div>
</div>


<!-- Testimonial Section  -->
<div id="opinions" id="testimonial-2" class="testimonial-section-two gray-bg section-padding theme_right" style="padding-top:30px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5 col-lg-5 wow fadeInUp animated" data-wow-delay="200ms">
                <div class="section-title">
                    <h2><span>أراء عملائنا</span></h2>
                </div>
                <p class="pr-85">
                    هذا النص هو مثال لنص يمكن أن يستبدل في نفس المساحة، لقد تم توليد هذا النص من مولد النص العربى، حيث يمكنك أن تولد مثل هذا النص أو العديد من النصوص الأخرى إضافة إلى زيادة عدد الحروف التى يولدها التطبيق.
                </p>
            </div>
            <div class="col-xl-7 col-lg-7">
                <div class="testimonial-wrap owl-carousel">


                    <div class="single-testimonial-item">
                        <div class="testimonial-author">

                            <h4>احمد رضا<span>Marketing Manager</span></h4>
                        </div>
                        <p>Transport is the movement of people goods or ani from one place to another. It plays the crucial role in connecting Tr the movement of people goods or animals from one place t another. It plays the crucial role in connecting loren ipsum </p>
                        <div class="rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>


                    <div class="single-testimonial-item">
                        <div class="testimonial-author">

                            <h4>Jane Cooper<span>Marketing Manager</span></h4>
                        </div>
                        <p>Transport is the movement of people goods or ani from one place to another. It plays the crucial role in connecting Tr the movement of people goods or animals from one place t another. It plays the crucial role in connecting loren ipsum </p>
                        <div class="rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                    </div>


                </div>
            </div>
        </div>


        <!-- Funfacts Section  -->
        <div class="row justify-content-center mt-5" style="padding-top:23px;">
            <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                <div class="single-funfact-wrap">
                    <div class="funfact-icon">
                        <img src="{{ asset('front/assets/img/funfacts/1.png') }}" alt="">
                    </div>
                    <h2><span class="odometer" data-count="200">000</span>+</h2>
                    <p>Team Member</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                <div class="single-funfact-wrap">
                    <div class="funfact-icon">
                        <img src="{{ asset('front/assets/img/funfacts/2.png') }}" alt="">
                    </div>
                    <h2><span class="odometer" data-count="300">000</span>+</h2>
                    <p>Winning Award</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                <div class="single-funfact-wrap">
                    <div class="funfact-icon">
                        <img src="{{ asset('front/assets/img/funfacts/3.png') }}" alt="">
                    </div>
                    <h2><span class="odometer" data-count="250">000</span>+</h2>
                    <p>Complete Project</p>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                <div class="single-funfact-wrap">
                    <div class="funfact-icon">
                        <img src="{{ asset('front/assets/img/funfacts/4.png') }}" alt="">
                    </div>
                    <h2><span class="odometer" data-count="20">00</span>+</h2>
                    <p>Client Review</p>
                </div>
            </div>
        </div>
        <!-- Funfacts Section  -->

    </div>
</div>

<!-- contact Section  -->
<div id="contact" class="appointment-section section-padding theme_right" style="padding-top:200px;">
    <div class="container">
        <div class="appointment-inner">
            <div class="row">
                <div class="col-xl-5 col-lg-5">
                    <div class="section-title">
                        <h2 class="text-white wow fadeInDown animated" data-wow-delay="400ms">تواصل معنا الان</h2>
                    </div>
                    <p class="text-white wow fadeInUp animated" data-wow-delay="200ms">
                        نحن هنا لمساعدتك وخدمتك عملينا العزيز على مدار الساعة. </p>
                    <div class="contact-wrap">
                        <div class="icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h6>رقم الجوال</h6>
                            <p>01016202064</p>
                        </div>

                        <div class="icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h6>رقم الجوال</h6>
                            <p>01016202064</p>
                        </div>

                    </div>


                </div>
                <div class="col-xl-7 col-lg-7 wow fadeInDown animated" data-wow-delay="400ms">
                    <div class="apppointment-form-wrap white-bg">
                        <h2>طلب المساعدة</h2>
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" placeholder="الاسم الاول">
                                </div>
                                <div class="col-md-6">
                                    <input type="text" placeholder="الاسم الاخير">
                                </div>
                                <div class="col-md-6">
                                    <input type="email" placeholder="البريد الالكتروني">
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" placeholder="رقم الجوال">
                                </div>
                                <div class="col-md-12">
                                    <textarea name="message" cols="30" rows="10" placeholder="اكتب رسالتك هنا"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <input type="submit" value="إرســال">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- FAQ Section  -->
<div id="faqs" class="faq-section section-padding theme_right">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-8 text-center">
                <div class="section-title wow fadeInUp animated" data-wow-delay="200ms">
                    <h2><span>الأسئلة الشائعة</span></h2>
                </div>
                <div class="faq-wrap">
                    <div class="cp-custom-accordion mt-60">
                        <div class="accordions" id="accordionExample">
                            <div class="accordion-items">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-buttons" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <span>01</span>ما هي شركة دبليو إم للشحن ؟.
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        هي شركة تربط شركات الشحن بالتجار والشركات والمؤسسات عن طريق توفير بوليصات الشحن بأسعار منافسة ومميزات حصرية
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-items">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-buttons collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <span>02</span>What does the category encompass?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        It is a long established fact t a reader will be distracted by the mabn readable content of ajlijkl page when looking at its layout. Lorem Ipsum is simpl
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-items">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-buttons collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <span>03</span>Is mental health support logistics category?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        It is a long established fact t a reader will be distracted by the mabn readable content of ajlijkl page when looking at its layout. Lorem Ipsum is simpl
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- login feature Section  -->
<div class="about-section gray-bg section-padding theme_right">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-6 wow fadeInLeft animated" data-wow-delay="200ms">
                <div class="about-img-wrap">
                    <img src="{{ asset('front/assets/img/about/about-1.png') }}" alt="">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 wow fadeInUp animated" data-wow-delay="300ms">
                <div class="about-content-wrap">
                    <div class="section-title">
                        <h2>ليه تختار منصة دبليو إم اكسبريس ؟</h2>
                    </div>
                    <p> نجمع بين الأسعار التنافسية والشراكات القوية، مع شبكة شحن واسعة النطاق تغطي كافة البلدان، وخدمة عملاء استثنائية لتتبع شحنتك بدقة وتقدم خيارات دفع متعددة وآمنة، كل هذا متاح على مدار الساعة لتلبية احتياجاتك.</p>
                </div>
                <a href="about.html" class="bordered-btn mt-40">تسجيل الدخول<i class="fa-solid fa-arrow-left"></i></a>
            </div>
        </div>
    </div>
</div>

@endsection