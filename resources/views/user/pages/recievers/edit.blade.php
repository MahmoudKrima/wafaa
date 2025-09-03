@extends('user.layouts.app')
@section('title', __('admin.update'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('user.recievers.index') }}">{{ __('admin.recievers') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.update') }}</span></li>
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
                            <h4>{{ __('admin.update') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('user.recievers.update', $reciever->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nameArInput" class="text-dark">{{ __('admin.name') }}</label>
                                            <input id="nameArInput" type="text" name="name"
                                                placeholder="{{ __('admin.name') }}" class="form-control"
                                                value="{{ old('name', $reciever->name) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phoneInput" class="text-dark">{{ __('admin.phone') }}</label>
                                            <input id="phoneInput" type="number" placeholder="05XXXXXXXX"
                                                name="phone" placeholder="{{ __('admin.phone') }}"
                                                class="form-control" value="{{ old('phone', $reciever->phone) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="additionalPhoneInput" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                            <input id="additionalPhoneInput" type="text" placeholder="05XXXXXXXX"
                                                name="additional_phone" placeholder="{{ __('admin.additional_phone') }}"
                                                class="form-control" value="{{ old('additional_phone', $reciever->additional_phone) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="emailInput" class="text-dark">{{ __('admin.email') }}</label>
                                            <input id="emailInput" type="text" placeholder="example@example.com"
                                                name="email" placeholder="{{ __('admin.email') }}"
                                                class="form-control" value="{{ old('email', $reciever->email) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="postal_codeInput" class="text-dark">{{ __('admin.postal_code') }}</label>
                                            <input id="postal_codeInput" type="text" placeholder="123456"
                                                name="postal_code" placeholder="{{ __('admin.postal_code') }}"
                                                class="form-control" value="{{ old('postal_code', $reciever->postal_code) }}">
                                        </div>
                                    </div>
                                </div>
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