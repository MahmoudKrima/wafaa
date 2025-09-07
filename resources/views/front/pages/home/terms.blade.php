@extends('front.layouts.app')
@php
    app()->setLocale('ar');
@endphp
@section('title', __('admin.terms'))

@section('content')

    <div class="hero-section section-padding"
        style="background: linear-gradient(135deg, #1362a9 0%, #1364a8 100%); padding-top: 60px; padding-bottom: 60px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 text-center">
                    <div class="hero-content">
                        <h1 class="text-white mb-4 wow fadeInUp animated" data-wow-delay="200ms">
                            <i class="fa-solid fa-file-contract me-3"></i>{{ __('admin.terms') }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="terms-section section-padding theme_right" style="padding:80px 50px 10px 50px;">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="terms-content-wrap">
                        @if($term && $term->term_description)
                            <div>
                                <div class="terms-text">
                                    {!! $term->getTranslation('term_description', app()->getLocale()) !!}
                                </div>
                                <div>
                                    <h3 class="mb-2"></h3>
                                    <p class="mb-0 opacity-75">
                                        {{ __('admin.last_updated') ?? 'آخر تحديث' }}:
                                        {{ $term->updated_at ? $term->updated_at->format('Y-m-d') : 'غير محدد' }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div>
                                <div class="no-terms-icon mb-4">
                                    <i class="fa-solid fa-file-circle-exclamation fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted mb-3">
                                    {{ __('admin.no_terms_available') ?? 'لا توجد شروط وأحكام متاحة حالياً' }}</h4>
                                <p class="text-muted mb-4">
                                    {{ __('admin.terms_coming_soon') ?? 'سيتم إضافة الشروط والأحكام قريباً' }}</p>
                                <a href="{{ route('front.home') }}"
                                    class="btn btn-primary btn-sm">{{ __('admin.back_to_home') ?? 'العودة للصفحة الرئيسية' }}
                                </a>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="about-section white-bg section-padding theme_right">

    </div>

    </div>

    <style>
        .terms-section .card {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .terms-section .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15) !important;
        }

        .terms-text {
            font-size: 18px;
            line-height: 1.8;
            color: #333;
            text-align: justify;
        }

        .terms-text h1,
        .terms-text h2,
        .terms-text h3,
        .terms-text h4,
        .terms-text h5,
        .terms-text h6 {
            color: #2c3e50;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .terms-text h1:first-child,
        .terms-text h2:first-child,
        .terms-text h3:first-child {
            margin-top: 0;
        }

        .terms-text p {
            margin-bottom: 15px;
        }

        .terms-text ul,
        .terms-text ol {
            margin-bottom: 15px;
            padding-right: 20px;
        }

        .terms-text li {
            margin-bottom: 8px;
        }

        .hero-section {
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="white" opacity="0.1"><polygon points="0,0 1000,100 1000,0"/></svg>') bottom center/cover no-repeat;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .terms-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .no-terms-icon {
            opacity: 0.6;
        }

        .contact-buttons .btn {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .contact-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .terms-section {
                padding: 25px 10px !important;
            }

            .hero-section {
                padding-top: 100px !important;
                padding-bottom: 60px !important;
            }

            .terms-text {
                font-size: 18px;
            }
        }
    </style>
@endsection