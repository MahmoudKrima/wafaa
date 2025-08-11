@extends('dashboard.layouts.app')
@section('title', __('admin.update'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('admin.users') }}</a>
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
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nameInput" class="text-dark">{{ __('admin.name') }}</label>
                                            <input id="nameInput" type="text" name="name_ar"
                                                placeholder="{{ __('admin.name') }}" class="form-control"
                                                value="{{ old('name_ar', $user->getTranslation('name', 'ar')) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nameInput" class="text-dark">{{ __('admin.name') }}</label>
                                            <input id="nameInput" type="text" name="name_en"
                                                placeholder="{{ __('admin.name') }}" class="form-control"
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
                                        <div class="col-md-6 mb-3">
                                            <label for="cityInput" class="text-dark">{{ __('admin.city') }}</label>
                                            <select id="cityInput" name="city_id" class="form-control">
                                                <option value="">{{ __('admin.choose_city') }}</option>
                                                @foreach ($cities as $city)
                                                <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="passwordInput"
                                                class="text-dark">{{ __('admin.password') }}</label>
                                            <input id="passwordInput" type="password" name="password"
                                                placeholder="{{ __('admin.password') }}" class="form-control">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="addressInput" class="text-dark">{{ __('admin.address') }}</label>
                                            <textarea id="addressInput" name="address" rows="3"
                                                placeholder="{{ __('admin.enter_address') }}"
                                                class="form-control">{{ old('address', $user->address) }}</textarea>
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