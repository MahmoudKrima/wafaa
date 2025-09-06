@php use App\Models\AdminSetting; @endphp
@extends('user.layouts.app')
@section('title', __('admin.dashboard'))

@push('css')
<link href="{{ asset('assets_' . assetLang()) }}/plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
<link href="{{ asset('assets_' . assetLang()) }}/assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" class="dashboard-analytics" />

<style>
    /* ===== Metric cards ===== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 576px) {
        .metrics-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
    }

    @media (min-width: 576px) and (max-width: 768px) {
        .metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) and (max-width: 992px) {
        .metrics-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 992px) {
        .metrics-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .metrics-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1920px) {
        .metrics-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .navbar .language-dropdown .custom-dropdown-icon a.dropdown-toggle:before, .navbar .navbar-item .nav-item.user-profile-dropdown .nav-link.user:before{
        top:17px !important;
    }

    .metric-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        min-height: 100px;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .metric-card .meta {
        flex: 1;
        min-width: 0;
    }

    .metric-card .label {
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
        margin: 0 0 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .metric-card .value {
        margin: 0;
        font-weight: 700;
        font-size: 50px;
        color: #1b6aab;
        line-height: 1.2;
        word-break: break-word;
    }

    .metric-card .icon {
        flex: 0 0 auto;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--icon-bg, #f3f4f6);
        color: var(--icon-fg, #374151);
        position: relative;
    }

    .metric-card .icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--icon-bg, #f3f4f6) 0%, rgba(255,255,255,0.8) 100%);
        z-index: -1;
    }

    .metric-card .icon i {
        font-size: 24px;
        z-index: 1;
    }

    /* Color variants */
    .is-primary {
        --icon-bg: #dbeafe;
        --icon-fg: #1d4ed8;
    }

    .is-success {
        --icon-bg: #dcfce7;
        --icon-fg: #16a34a;
    }

    .is-warning {
        --icon-bg: #fef3c7;
        --icon-fg: #d97706;
    }

    .is-danger {
        --icon-bg: #fee2e2;
        --icon-fg: #dc2626;
    }

    .is-info {
        --icon-bg: #dbeafe;
        --icon-fg: #2563eb;
    }

    .is-secondary {
        --icon-bg: #f3f4f6;
        --icon-fg: #6b7280;
    }

    .is-purple {
        --icon-bg: #e9d5ff;
        --icon-fg: #9333ea;
    }

    .is-orange {
        --icon-bg: #fed7aa;
        --icon-fg: #ea580c;
    }

    .is-slate {
        --icon-bg: #f1f5f9;
        --icon-fg: #475569;
    }

    /* RTL Support */
    [dir="rtl"] .metric-card {
        direction: rtl;
    }

    [dir="rtl"] .metric-card .meta {
        text-align: right;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .metric-card {
            padding: 20px;
            min-height: 90px;
        }

        .metric-card .value {
            font-size: 24px;
        }

        .metric-card .icon {
            width: 50px;
            height: 50px;
        }

        .metric-card .icon i {
            font-size: 20px;
        }
    }
</style>
@endpush

@section('content')
@php
$rtl = app()->getLocale() === 'ar' ? 'rtl' : 'ltr';
$st = $stats['status'] ?? [];
$localCount = data_get($stats, 'methods.local', 0);
$intlCount = data_get($stats, 'methods.international', 0);
$totalCount = (int) ($stats['total'] ?? 0);
@endphp

<div class="layout-px-spacing" dir="{{ $rtl }}">


    {{--Statistics--}}

    {{-- Wallet Balance --}}
    <div class="row layout-top-spacing">
        <div class="col-lg-4 col-md-6 col-sm-12 layout-spacing">
            <div class="widget widget-account-invoice-two">
                <div class="widget-content">
                    <div class="account-box">
                        <div class="info">
                            <h5 class="">{{ __('admin.wallet_balance') }}</h5>
                            <p style="font-size: 35px;margin-top: 20px;">{{ number_format($walletBalance, 2) }} {{ __('admin.currency_symbol') }}</p>
                        </div>
                        <div class="acc-action">
                            <div class="">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-credit-card"></i>
                                </a>
                            </div>
                            <a href="{{ route('user.transactions.create') }}">
                                <i class="fa fa-add"></i> {{__('admin.recharge_credit')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert">
       <strong> {{__('admin.statistics')}}</strong>
    </div>

    {{-- ===== Metrics as separate cards ===== --}}
    <div class="metrics-grid">

        {{-- Receivers --}}
        <div class="metric-card is-success">
            <div class="meta">
                <p class="label">{{ __('admin.receivers_count') }}</p>
                <p class="value">{{ number_format($receiversCount) }}</p>
            </div>
            <div class="icon"><i class="fa fa-user-friends"></i></div>
        </div>

        {{-- Total Shipments --}}
        <div class="metric-card is-info">
            <div class="meta">
                <p class="label">{{ __('admin.shipments_total') }}</p>
                <p class="value">{{ number_format($totalCount) }}</p>
            </div>
            <div class="icon"><i class="fa fa-shipping-fast"></i></div>
        </div>

        {{-- Local Shipments --}}
        <div class="metric-card is-info">
            <div class="meta">
                <p class="label">{{ __('admin.local_shipments') }}</p>
                <p class="value">{{ number_format($localCount) }}</p>
            </div>
            <div class="icon"><i class="fa fa-truck-arrow-right"></i></div>
        </div>

        {{-- International Shipments --}}
        <div class="metric-card is-primary">
            <div class="meta">
                <p class="label">{{ __('admin.international_shipments') }}</p>
                <p class="value">{{ number_format($intlCount) }}</p>
            </div>
            <div class="icon"><i class="fa fa-globe-europe"></i></div>
        </div>

        {{-- Status: Pending --}}
        <div class="metric-card is-warning">
            <div class="meta">
                <p class="label">{{ __('admin.pending_shipments') }}</p>
                <p class="value">{{ number_format($st['pending'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-list-check"></i></div>
        </div>

        {{-- Status: Processing --}}
        <div class="metric-card is-success">
            <div class="meta">
                <p class="label">{{ __('admin.processing_shipments') }}</p>
                <p class="value">{{ number_format($st['processing'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-refresh"></i></div>
        </div>

        {{-- Status: delivered --}}
        <div class="metric-card is-success">
            <div class="meta">
                <p class="label">{{ __('admin.delivered_shipments') }}</p>
                <p class="value">{{ number_format($st['delivered'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-check-circle"></i></div>
        </div>

        {{-- Status: returned --}}
        <div class="metric-card is-warning">
            <div class="meta">
                <p class="label">{{ __('admin.returned_shipments') }}</p>
                <p class="value">{{ number_format($st['returned'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-retweet"></i></div>
        </div>

        {{-- Status: Failed --}}
        <div class="metric-card is-danger">
            <div class="meta">
                <p class="label">{{ __('admin.failed_shipments') }}</p>
                <p class="value">{{ number_format($st['failed'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-window-close"></i></div>
        </div>

        {{-- Status: Cancel Request --}}
        <div class="metric-card is-danger">
            <div class="meta">
                <p class="label">{{ __('admin.cancel_request_shipments') }}</p>
                <p class="value">{{ number_format($st['cancelRequest'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-cancel"></i></div>
        </div>

        {{-- Status: Canceled --}}
        <div class="metric-card is-danger">
            <div class="meta">
                <p class="label">{{ __('admin.canceled_shipments') }}</p>
                <p class="value">{{ number_format($st['canceled'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="fa fa-window-close"></i></div>
        </div>

    </div>
    {{-- ===== /Metrics grid ===== --}}

</div>

@endsection

@push('js')
<script src="{{ asset('assets_' . assetLang()) }}/plugins/apex/apexcharts.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/assets/js/dashboard/dash_1.js"></script>
@endpush

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("contactData").style.display = "block";
    });
</script>
@php
    $user = auth()->user();
    $contacts = AdminSetting::where('admin_id', $user->created_by)
        ->select('email', 'phone', 'whatsapp')
        ->first();
@endphp
<div class="floating-button-menu menu-off" id="contactData" style="display: none;">
    <div class="floating-button-menu-links">

        <a href="tel:{{ $contacts->phone }}" class="text-decoration-none">
            <i class="fa fa-phone" style="margin:0 5px;"></i>
            {{ $contacts->phone ?? '—' }}
        </a>

        <a href="mailto:{{ $contacts->email }}" class="text-decoration-none">
            <i class="fa fa-envelope" style="margin:0 5px;"></i>
            {{ $contacts->email ?? '—' }}
        </a>
        @if($contacts->whatsapp)

            <a href="https://api.whatsapp.com/send/?phone=966{{$contacts->whatsapp}}&text&type=phone_number&app_absent=0" target="_blank" class="text-decoration-none">
                <i class="fa-brands fa-whatsapp" style="margin:0 5px;"></i>
                {{ $contacts->whatsapp }}
            </a>
        @else
            <span>—</span>
        @endif
    </div>
    <div class="floating-button-menu-label"><i class="fa fa-phone" style="font-size:25px;line-height:65px;"></i></div>
</div>
<div class="floating-button-menu-close"></div>
