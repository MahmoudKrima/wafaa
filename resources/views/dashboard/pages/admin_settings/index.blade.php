@extends('dashboard.layouts.app')
@section('title', __('admin.admin_shipments_settings'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.admin_shipments_settings') }}</span></li>
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
                        <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                            <h4>{{ __('admin.admin_shipments_settings') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.admin-settings.update', $adminSetting->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="extra_weight_price" class="text-dark">{{ __('admin.extra_weight_price') }}</label>
                                            <input type="number" step="0.01" name="extra_weight_price" id="extra_weight_price"
                                                class="form-control" placeholder="{{ __('admin.extra_weight_price') }}"
                                                value="{{ old('extra_weight_price', $adminSetting->extra_weight_price) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cash_on_delivery_price" class="text-dark">{{ __('admin.cash_on_delivery_price') }}</label>
                                            <input type="number" step="0.01" name="cash_on_delivery_price" id="cash_on_delivery_price"
                                                class="form-control" placeholder="{{ __('admin.cash_on_delivery_price') }}"
                                                value="{{ old('cash_on_delivery_price', $adminSetting->cash_on_delivery_price) }}">
                                        </div>
                                    </div>
                                </div>
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
