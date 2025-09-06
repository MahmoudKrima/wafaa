@extends('user.layouts.app')
@section('title', __('admin.shippings'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.shippings') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div style="display:flex;flex-direction: row;justify-content: space-between;">
                        <div style="margin: 15px 15px 0 15px;">
                            <h4 class="mb-0" style="text-wrap-mode:nowrap !important;">
                                <i class="fas fa-check-circle text-primary me-2" style="margin:0 5px;"></i>{{ __('admin.package_details') }}
                            </h4>
                        </div>
                        <div style="margin: 25px 15px 0 20px;" id="hide-when-print" class="no-print">
                            <button onclick="window.print()" class="btn btn-outline-dark">
                                <i class="fa fa-print"></i> {{ __('admin.print_file') }}
                            </button>
                        </div>
                    </div>

                </div>
                <div class="widget-content widget-content-area">
                    <div id="step-7" class="step-content" style="margin-top:0 !important;">
                        <div class="row">
                            <div class="col-12">
                                <div id="step7-errors" class="mb-3"></div>

                                <!----Sender and company details--->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white"
                                                style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-user-tie"
                                                        style="margin:0 5px;"></i>{{ __('admin.sender_information') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <p>
                                                    <strong>{{ __('admin.name') . ' : ' }}</strong>
                                                    <span id="sender-name-preview">{{ $senderName }}</span>
                                                </p>
                                                <p>
                                                    <strong>{{ __('admin.phone') . ' : ' }}</strong>
                                                    <span id="sender-phone-preview">{{ $senderPhone }}</span>
                                                </p>
                                                <p>
                                                    <strong>{{ __('admin.city') . ' : ' }}</strong>
                                                    <span id="sender-city-preview">{{ $senderCity }}</span>
                                                </p>
                                                <p>
                                                    <strong>{{ __('admin.address') . ' : ' }}</strong>
                                                    <span id="sender-address-preview">{{ $senderAddress }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white"
                                                style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;"><i
                                                        class="fa fa-house-flood-water-circle-arrow-right"
                                                        style="margin:0 5px;"></i>{{ __('admin.shipping_company_details') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12"
                                                        style="display:flex;justify-content:space-between;">
                                                        <div>
                                                            <img id="company-logo-preview"
                                                                src="{{ data_get($company, 'logoUrl', '') }}"
                                                                class="me-3"
                                                                style="width:80px;height:60px;object-fit:contain;"
                                                                alt="">
                                                            <div>
                                                                <h6 id="company-name-preview"
                                                                    style="margin:5px 15px;" class="mb-1">
                                                                    {{ data_get($company, 'name', __('admin.company_name')) }}
                                                                </h6>
                                                                <small id="company-service-preview"
                                                                    style="margin:5px 15px;" class="text-muted">
                                                                    {{ data_get($company, 'serviceName', __('admin.service_type')) }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <span class="badge bg-success fs-6" id="shipping-method-preview">
                                                                {{ __('admin.' . ($shipment['method'] ?? 'local')) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @php
                                $receiversList = (array) data_get($shipment, 'receivers', []);
                                if (empty($receiversList) && !empty($receiver)) {
                                $receiversList = [$receiver];
                                }
                                $rcCount = is_countable($receiversList) ? count($receiversList) : 0;
                                @endphp

                                <!----receivers and shipment details--->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white"
                                                style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-user-friends" style="margin:0 5px;"></i>{{ __('admin.receivers_info') }}
                                                    (<span id="receivers-count-preview">{{ $rcCount }}</span>)
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div id="receivers-summary-container">
                                                    @forelse($receiversList as $idx => $rcv)
                                                    @php
                                                    $rName = data_get($rcv, 'fullName', '—');
                                                    $rPhone = data_get($rcv, 'phone', '—');
                                                    $rPhone1 = data_get($rcv, 'phone1', '—');
                                                    $rStreet = data_get($rcv, 'address.street', data_get($rcv, 'street', '—'));
                                                    $rCity = data_get($rcv, 'address.cityName') ??
                                                    data_get($rcv, 'city_name') ??
                                                    ($receiverCityName ?? '—');

                                                    $rCountry = data_get($rcv, 'address.countryName') ??
                                                    data_get($rcv, 'country_name') ??
                                                    ($receiverCountryName ?? '—');
                                                    @endphp
                                                    <div class="card mb-2">
                                                        <div class="card-body">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                                <div>
                                                                    <p class="mb-1">
                                                                        <strong>{{ __('admin.receiver') }}#{{ $idx + 1 }} : </strong>
                                                                        <span class="text-muted">{{ $rName }}</span>
                                                                    </p>
                                                                    <p>
                                                                        <strong>{{ __('admin.phone') }} : </strong>
                                                                        <span class="text-muted">{{ $rPhone }}</span>
                                                                    </p>
                                                                    <p>
                                                                        <strong>{{ __('admin.country') }} : </strong>
                                                                        <span class="text-muted">{{ $rCountry }}</span>
                                                                    </p>
                                                                    <p>
                                                                        <strong>{{ __('admin.city') }} : </strong>
                                                                        <span class="text-muted">{{ $rCity }}</span>
                                                                    </p>
                                                                    <p>
                                                                        <strong>{{ __('admin.address') }} : </strong>
                                                                        <span class="text-muted">{{ $rStreet }}</span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @empty
                                                    <div
                                                        class="text-muted">{{ __('admin.no_receivers_found') }}</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white"
                                                style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;">
                                                    <i class="fa fa-truck-fast" style="margin:0 5px;"></i>{{ __('admin.package_details') }}
                                                </h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong>{{ __('admin.package_type') . ': ' }}</strong><span id="package-type-preview">{{ __('admin.' . ($shipment['type'] ?? 'box')) }}</span>
                                                        </p>
                                                        <p>
                                                            <strong>{{ __('admin.package_count') . ': ' }}</strong><span id="package-count-preview">{{ $packagesCount }}</span>
                                                        </p>
                                                        <p>
                                                            <strong>{{ __('admin.weight_kg') . ': ' }}</strong><span id="package-weight-preview">{{ number_format($weight, 2) }}</span> {{ __('admin.kg') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p>
                                                            <strong>{{ __('admin.length_summary') . ': ' }}</strong><span id="package-length-preview">{{ $length }}</span> {{ __('admin.cm') }}
                                                        </p>
                                                        <p>
                                                            <strong>{{ __('admin.width_summary') . ': ' }}</strong><span id="package-width-preview">{{ $width }}</span> {{ __('admin.cm') }}
                                                        </p>
                                                        <p>
                                                            <strong>{{ __('admin.height_summary') . ': ' }}</strong><span id="package-height-preview">{{ $height }}</span> {{ __('admin.cm') }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="mt-3">
                                                            <p>
                                                                <strong>{{ __('admin.package_description') . ': ' }}</strong>
                                                            </p>
                                                            <p id="package-notes-preview" class="text-muted">
                                                                {{ $packageDescription !== '' ? $packageDescription : __('admin.no_special_notes') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!----shipment price and cost details--->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-info-circle" style="margin:0 5px;"></i>{{ __('admin.shipment_price_details') }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-3 text-black">{{ __('admin.shipping_price_per_receiver') }}:</strong>
                                                        <div class="text-muted" id="price-base-per-receiver">{{ number_format($shippingFee, 2) }} {{ __('admin.currency_symbol') }}</div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-1 text-black">{{ __('admin.extra_weight_per_receiver') }}:</strong>
                                                        <div class="text-muted" id="price-extra-per-receiver">
                                                            {{ number_format($extraWeightPerReceiver, 2) }} {{ __('admin.currency_symbol') }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="small mb-3 text-muted mt-0" id="extra-weight-note">
                                                            {{ $extraWeightPerReceiver > 0 ? '' : __('admin.no_extra_weight') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-3 text-black">{{ __('admin.shippment_type') }}:</strong>
                                                        <div class="mb-0 text-muted" id="payment-method-preview">
                                                            {{ $shipment['isCod'] ? __('admin.cash_on_delivery_shippment') : __('admin.normal_shipment') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-1 text-black">{{ __('admin.cod_fee_per_receiver') }}:</strong>
                                                        <div class="mb-0 text-muted" id="price-cod-per-receiver">
                                                            {{ number_format($codPerReceiver, 2) }} {{ __('admin.currency_symbol') }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card mb-4" style="border-radius:15px;">
                                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-money-bill" style="margin:0 5px;"></i>{{ __('admin.payment_details') }}</h5>
                                            </div>
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-3 text-black">{{ __('admin.shipping_fee') }}:</strong>
                                                        <div class="mb-0 text-primary" id="shipping-fee-preview">{{ number_format($shippingFee, 2) }} {{ __('admin.currency_symbol') }}</div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-1 text-black">{{ __('admin.extra_fees') }}:</strong>
                                                        <div class="mb-0 text-primary" id="extra-fees-preview">{{ number_format($shippingFee, 2) }} {{ __('admin.currency_symbol') }}</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="small mb-3 text-muted mt-0">{{__('admin.extra_fess_desc') }}</div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-3 text-black">{{ __('admin.cod_fee') }}:</strong>
                                                        <div class="mb-0 text-primary" id="cod-fees-preview">{{ number_format($codFee, 2) }} {{ __('admin.currency_symbol') }}</div>
                                                    </div>
                                                </div>

                                                @php
                                                $displayCount = $rcCount;
                                                $displayPerReceiver = $displayCount > 0 ? ($total / $displayCount) : 0;
                                                @endphp

                                                <div class="row">
                                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                                        <strong class="mb-1 text-primary">{{ __('admin.total_amount') }}:</strong>
                                                        <div class="h6 mb-0 text-primary" id="total-amount-preview">{{ number_format($total, 2) }} {{ __('admin.currency_symbol') }}</div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span class="small mb-3 text-muted mt-0" id="receivers-count-display">{{ $displayCount }}</span> {{ __('admin.receivers') }}
                                                        ×
                                                        <span class="small mb-3 text-muted mt-0" id="per-receiver-total">{{ number_format($displayPerReceiver, 2) }}</span> {{ __('admin.per_receiver') }}
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
            </div>
        </div>
    </div>
</div>
@endsection