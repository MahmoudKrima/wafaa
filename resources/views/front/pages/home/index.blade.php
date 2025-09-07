@extends('front.layouts.app')
@php
    app()->setLocale('ar');
@endphp
@section('title', __('admin.home'))

@section('content')

    <div id="home-3" class="homepage-slides owl-carousel">
        @foreach($sliders as $slider)
            <div class="single-slide-item d-flex align-items-center" data-background="{{ displayImage($slider->image)}}">
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
                                    <a href="{{ $slider->button_url }}" target="_blank"
                                        class="theme-btn mt-40">{{ $slider->button_text }}</a>
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
                                            <button class="accordion-buttons {{ $index == 0 ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $index + 1 }}"
                                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse{{ $index + 1 }}">
                                                <span>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $item->title }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $index + 1 }}"
                                            class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                            aria-labelledby="heading{{ $index + 1 }}" data-bs-parent="#accordionExample">
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
                            <h2><span>{{ __('admin.partners') }}</span></h2>
                        </div>
                        <p>{{ __('admin.partners_description') }}</p>
                    </div>
                </div>

                <!-- Clients Section  -->
                <div class="client-section section-padding pt-4" style="direction:ltr;margin-top:35px;">
                    <div class="container">
                        <div class="row">
                            <div class="client-wrapper owl-carousel">
                                @foreach($partners as $partner)
                                    <div class="single-client-item">
                                        <img src="{{ displayImage($partner->image) }}" alt="partner"
                                            style="width:100px;height:90px;">
                                    </div>
                                @endforeach
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
                        <h2> <span>{{__('admin.services_provided')}}</span></h2>
                    </div>

                    <p class="text-white wow fadeInDown animated mt-3" data-wow-delay="400ms"
                        style="color:#1c1d1e !important;">
                        {{__('admin.services_description')}}
                    </p>

                </div>

            </div>
            <div class="row mt-100">

                @foreach($services as $service)
                    <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp animated" data-wow-delay="200ms">
                        <div class="single-service-wrap">
                            <div class="service-icon">
                                <img src="{{ displayImage($service->image) }}" alt="" style="height:55px;width:55px;">
                            </div>
                            <h4>{{ $service->title }}</h4>
                            <p>{{ $service->description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>


    <!-- Testimonial Section  -->
    <div id="opinions" id="testimonial-2" class="testimonial-section-two gray-bg section-padding theme_right"
        style="padding-top:30px;">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-xl-5 col-lg-5 wow fadeInUp animated" data-wow-delay="200ms">
                    <div class="section-title">
                        <h2><span>{{ __('admin.testimonials') }}</span></h2>
                    </div>
                    <p class="pr-85" style="line-height:1.8">
                        {{ __('admin.testimonials_description') }}
                    </p>
                </div>
                <div class="col-xl-7 col-lg-7">
                    <div class="testimonial-wrap owl-carousel">

                        @foreach($testimonials as $testimonial)
                            <div class="single-testimonial-item">
                                <div class="testimonial-author">

                                    <h4>{{ $testimonial->name }}<span>{{ $testimonial->job_title }}</span></h4>
                                </div>
                                <p>{{ $testimonial->review }}</p>
                                <div class="rating">
                                    @for($i = 0; $i < $testimonial->rate; $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>


            <!-- Funfacts Section  -->
            <div class="row justify-content-center mt-5" style="padding-top:23px;">
                <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                    <div class="single-funfact-wrap">
                        <h2><span style="direction: ltr !important;" class="odometer"
                                data-count="{{ $statistics['totalAdmins'] }}">000</span></h2>
                        <p>{{ __('admin.team_member') }}</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                    <div class="single-funfact-wrap">
                        <h2><span style="direction: ltr !important;" class="odometer"
                                data-count="{{$statistics['totalShippingCompanies']}}">000</span></h2>
                        <p>{{ __('admin.shipping_companies') }}</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                    <div class="single-funfact-wrap">
                        <h2><span style="direction: ltr !important;" class="odometer"
                                data-count="{{$statistics['totalShippments']}}">000</span></h2>
                        <p>{{ __('admin.shippments') }}</p>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 text-center">
                    <div class="single-funfact-wrap">
                        <h2><span style="direction: ltr !important;" class="odometer"
                                data-count="{{$statistics['totalUsers']}}">000</span></h2>
                        <p>{{ __('admin.clients') }}</p>
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
                            <h2 class="text-white wow fadeInDown animated" data-wow-delay="400ms">
                                {{ __('admin.contact_us_now') }}
                            </h2>
                        </div>
                        <p class="text-white wow fadeInUp animated" data-wow-delay="200ms"
                            style="color:#1c1d1e !important;">
                            {{ __('admin.contact_us_description') }}
                        </p>
                        <div class="contact-wrap">
                            <div class="icon">
                                <i class="fa fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h6>{{__('admin.phone')}}</h6>
                                <p>{{app('settings')['phone']}}</p>
                            </div>

                            <div class="icon">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="contact-details">
                                <h6>{{__('admin.whatsapp_number')}}</h6>
                                <p>{{app('settings')['whatsapp']}}</p>
                            </div>

                        </div>


                    </div>
                    <div class="col-xl-7 col-lg-7 wow fadeInDown animated" data-wow-delay="400ms">
                        <div class="apppointment-form-wrap white-bg">
                            <form method="post" action="{{ route('front.contact.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="first_name" placeholder="{{__('admin.first_name')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="last_name" placeholder="{{__('admin.last_name')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" placeholder="{{__('admin.email')}}">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="tel" name="phone" placeholder="{{__('admin.phone')}}">
                                    </div>
                                    <div class="col-md-12">
                                        <textarea name="message" cols="30" rows="10" name="message"
                                            placeholder="{{ __('admin.write_your_message') }}"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <input type="submit" value="{{ __('admin.send') }}">
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
                        <h2><span>{{ __('admin.faqs') }}</span></h2>
                    </div>
                    <div class="faq-wrap">
                        <div class="cp-custom-accordion mt-60">
                            <div class="accordions" id="accordionExample">
                                @foreach($faqs as $i => $faq)
                                    <div class="accordion-items">
                                        <h2 class="accordion-header" id="headingOne{{ $faq->id }}">
                                            <button class="accordion-buttons {{ $i == 0 ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapseOne{{ $faq->id }}"
                                                aria-expanded="{{$i == 0 ? 'true' : 'false'}}"
                                                aria-controls="collapseOne{{ $faq->id }}">
                                                <span>{{ $i < 10 ? '0' . ($i + 1) : ($i + 1) }}</span>{{ $faq->question }}
                                            </button>
                                        </h2>
                                        <div id="collapseOne{{ $faq->id }}"
                                            class="accordion-collapse collapse {{ $i == 0 ? 'show' : '' }}"
                                            aria-labelledby="headingOne{{ $faq->id }}" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                {{ $faq->answer }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
                        <p> نجمع بين الأسعار التنافسية والشراكات القوية، مع شبكة شحن واسعة النطاق تغطي كافة البلدان، وخدمة
                            عملاء استثنائية لتتبع شحنتك بدقة وتقدم خيارات دفع متعددة وآمنة، كل هذا متاح على مدار الساعة
                            لتلبية احتياجاتك.</p>
                    </div>
                    @if(!auth()->guard('web')->check() && !auth()->guard('admin')->check())
                        <a href="{{ route('user.auth.loginForm') }}" class="bordered-btn mt-40">
                            {{__('admin.login')}} <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection