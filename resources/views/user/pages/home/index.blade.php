@extends('user.layouts.app')
@section('title', __('admin.dashboard'))

@push('css')
<link href="{{ asset('assets_' . assetLang()) }}/plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
<link href="{{ asset('assets_' . assetLang()) }}/assets/css/dashboard/dash_1.css" rel="stylesheet" type="text/css" class="dashboard-analytics" />

<style>
    /* ===== Metric cards ===== */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
            grid-template-columns: repeat(4, 1fr);
        }
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
        font-size: 28px;
        color: #111827;
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

    {{-- ===== Metrics as separate cards ===== --}}
    <div class="metrics-grid">

        {{-- Wallet Balance --}}
        <div class="metric-card is-primary">
            <div class="meta">
                <p class="label">{{ __('admin.wallet_balance') }}</p>
                <p class="value">{{ number_format($walletBalance, 2) }} {{ __('admin.currency_symbol') }}</p>
            </div>
            <div class="icon"><i class="feather feather-credit-card"></i></div>
        </div>

        {{-- Receivers --}}
        <div class="metric-card is-success">
            <div class="meta">
                <p class="label">{{ __('admin.receivers') }}</p>
                <p class="value">{{ number_format($receiversCount) }}</p>
            </div>
            <div class="icon"><i class="feather feather-users"></i></div>
        </div>

        {{-- Total Shipments --}}
        <div class="metric-card is-purple">
            <div class="meta">
                <p class="label">{{ __('admin.shipments_total') }}</p>
                <p class="value">{{ number_format($totalCount) }}</p>
            </div>
            <div class="icon"><i class="feather feather-package"></i></div>
        </div>

        {{-- Local Shipments --}}
        <div class="metric-card is-info">
            <div class="meta">
                <p class="label">{{ __('admin.local') }}</p>
                <p class="value">{{ number_format($localCount) }}</p>
            </div>
            <div class="icon"><i class="feather feather-truck"></i></div>
        </div>

        {{-- International Shipments --}}
        <div class="metric-card is-secondary">
            <div class="meta">
                <p class="label">{{ __('admin.international') }}</p>
                <p class="value">{{ number_format($intlCount) }}</p>
            </div>
            <div class="icon"><i class="feather feather-globe"></i></div>
        </div>

        {{-- Status: Pending --}}
        <div class="metric-card is-warning">
            <div class="meta">
                <p class="label">{{ __('admin.pending') }}</p>
                <p class="value">{{ number_format($st['pending'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="feather feather-clock"></i></div>
        </div>

        {{-- Status: Processing --}}
        <div class="metric-card is-success">
            <div class="meta">
                <p class="label">{{ __('admin.processing') }}</p>
                <p class="value">{{ number_format($st['processing'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="feather feather-activity"></i></div>
        </div>

        {{-- Status: Failed --}}
        <div class="metric-card is-danger">
            <div class="meta">
                <p class="label">{{ __('admin.failed') }}</p>
                <p class="value">{{ number_format($st['failed'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="feather feather-x-circle"></i></div>
        </div>

        {{-- Status: Cancel Request --}}
        <div class="metric-card is-orange">
            <div class="meta">
                <p class="label">{{ __('admin.cancelrequest') }}</p>
                <p class="value">{{ number_format($st['cancelRequest'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="feather feather-alert-triangle"></i></div>
        </div>

        {{-- Status: Canceled --}}
        <div class="metric-card is-slate">
            <div class="meta">
                <p class="label">{{ __('admin.canceled') }}</p>
                <p class="value">{{ number_format($st['canceled'] ?? 0) }}</p>
            </div>
            <div class="icon"><i class="feather feather-slash"></i></div>
        </div>

    </div>
    {{-- ===== /Metrics grid ===== --}}

</div>
@endsection

@push('js')
<script src="{{ asset('assets_' . assetLang()) }}/plugins/apex/apexcharts.min.js"></script>
<script src="{{ asset('assets_' . assetLang()) }}/assets/js/dashboard/dash_1.js"></script>
@endpush