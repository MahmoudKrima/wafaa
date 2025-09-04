@extends('dashboard.layouts.app')
@section('title', __('admin.update_user'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('admin.users') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.update_user') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.update_user') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nameArInput" class="text-dark">{{ __('admin.name_ar') }}</label>
                                            <input id="nameArInput" type="text" name="name_ar"
                                                placeholder="{{ __('admin.name_ar') }}" class="form-control"
                                                value="{{ old('name_ar', $user->getTranslation('name', 'ar')) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nameEnInput" class="text-dark">{{ __('admin.name_en') }}</label>
                                            <input id="nameEnInput" type="text" name="name_en"
                                                placeholder="{{ __('admin.name_en') }}" class="form-control"
                                                value="{{ old('name_en', $user->getTranslation('name', 'en')) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phoneInput" class="text-dark">{{ __('admin.phone') }}</label>
                                            <input id="phoneInput" type="number" placeholder="05XXXXXXXX"
                                                name="phone" placeholder="{{ __('admin.phone') }}"
                                                class="form-control" value="{{ old('phone', $user->phone) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="additionalPhoneInput" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                            <input id="additionalPhoneInput" type="text" placeholder="05XXXXXXXX"
                                                name="additional_phone" placeholder="{{ __('admin.additional_phone') }}"
                                                class="form-control" value="{{ old('additional_phone', $user->additional_phone) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="emailInput" class="text-dark">{{ __('admin.email') }}</label>
                                            <input id="emailInput" type="text" placeholder="example@example.com"
                                                name="email" placeholder="{{ __('admin.email') }}"
                                                class="form-control" value="{{ old('email', $user->email) }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="passwordInput"
                                                class="text-dark">{{ __('admin.password') }}</label>
                                            <input id="passwordInput" type="password" name="password"
                                                placeholder="{{ __('admin.password') }}" class="form-control">
                                            <small class="form-text text-muted">{{ __('admin.password_optional') }}</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="balanceInput" class="text-dark">{{ __('admin.balance') }}</label>
                                            <input id="balanceInput"
                                                type="number"
                                                name="balance"
                                                placeholder="{{ __('admin.balance') }}"
                                                step="any"
                                                min="0"
                                                value="{{ old('balance', $user->wallet->balance) }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                                @if($allowedCompanies && count($allowedCompanies) > 0)
                                <hr>
                                <div class="form-group">
                                    <h5 class="mb-3">{{ __('admin.shipping_companies') }}</h5>
                                    <div class="row">
                                        @foreach($allowedCompanies as $company)
                                        @php
                                        $idx = $loop->index;
                                        $methods = (array) ($company['shippingMethods'] ?? []);
                                        $cid = (string) ($company['id'] ?? '');
                                        $existing = isset($userShippingMap[$cid]) ? $userShippingMap[$cid] : null;
                                        $hasLocal = in_array('local', $methods, true);
                                        $hasInternational = in_array('international', $methods, true);
                                        @endphp
                                        <div class="col-md-4 mb-4">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        @if(!empty($company['logoUrl']))
                                                        <img src="{{ $company['logoUrl'] }}" alt="{{ $company['name'] ?? 'Company' }}"
                                                            class="me-3" style="height: 40px;">
                                                        @endif
                                                    </div>
                                                    <input type="hidden" name="shipping_prices[{{ $idx }}][id]" value="{{ $cid }}">
                                                    <input type="hidden" name="shipping_prices[{{ $idx }}][name]" value="{{ $company['name'] ?? ($company['serviceName'] ?? '') }}">
                                                    <input type="hidden" name="shipping_prices[{{ $idx }}][require_local]" value="{{ $hasLocal ? 1 : 0 }}">
                                                    <input type="hidden" name="shipping_prices[{{ $idx }}][require_international]" value="{{ $hasInternational ? 1 : 0 }}">
                                                    @if($hasLocal)
                                                    <div class="mb-3">
                                                        <label for="local_price_{{ $cid ?: $idx }}" class="form-label text-dark">
                                                            {{ __('admin.local_price') }} ({{ __('admin.sar') }}) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number"
                                                            step="0.01" min="0"
                                                            class="form-control"
                                                            id="local_price_{{ $cid ?: $idx }}"
                                                            name="shipping_prices[{{ $idx }}][localprice]"
                                                            value="{{ old('shipping_prices.'.$idx.'.localprice', optional($existing)->local_price) }}"
                                                            placeholder="{{ __('admin.enter_local_price') }}"
                                                            required>
                                                    </div>
                                                    @endif
                                                    @if($hasInternational)
                                                    <div class="mb-3">
                                                        <label for="international_price_{{ $cid ?: $idx }}" class="form-label text-dark">
                                                            {{ __('admin.international_price') }} ({{ __('admin.sar') }}) <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="number"
                                                            step="0.01" min="0"
                                                            class="form-control"
                                                            id="international_price_{{ $cid ?: $idx }}"
                                                            name="shipping_prices[{{ $idx }}][internationalprice]"
                                                            value="{{ old('shipping_prices.'.$idx.'.internationalprice', optional($existing)->international_price) }}"
                                                            placeholder="{{ __('admin.enter_international_price') }}"
                                                            required>
                                                    </div>
                                                    @endif
                                                    @if(!$hasLocal && !$hasInternational)
                                                    <div class="text-muted small">
                                                        {{ __('admin.no_shipping_support') }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" value="{{ __('admin.update') }}"
                                            class="mt-4 btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
