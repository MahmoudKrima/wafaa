@extends('user.layouts.app')
@section('title', __('admin.profile'))
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.edit_profile') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name_arInput" class="text-dark">{{ __('admin.name_ar') }} <span class="text-danger">*</span></label>
                                            <input id="name_arInput" type="text" name="name_ar"
                                                placeholder="{{ __('admin.name_ar') }}" class="form-control"
                                                value="{{ old('name_ar', Auth::guard('web')->user()->getTranslation('name', 'ar')) }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="name_enInput" class="text-dark">{{ __('admin.name_en') }} <span class="text-danger">*</span></label>
                                            <input id="name_enInput" type="text" name="name_en"
                                                placeholder="{{ __('admin.name_en') }}" class="form-control"
                                                value="{{ old('name_en', Auth::guard('web')->user()->getTranslation('name', 'en')) }}" required>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="phoneInput" class="text-dark">{{ __('admin.phone') }} <span class="text-danger">*</span></label>
                                            <input id="phoneInput" type="text" name="phone"
                                                value="{{ old('phone', Auth::guard('web')->user()->phone) }}"
                                                placeholder="{{ __('admin.phone') }}" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="additionalPhoneInput" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                            <input id="additionalPhoneInput" type="text" name="additional_phone"
                                                value="{{ old('additional_phone', Auth::guard('web')->user()->additional_phone) }}"
                                                placeholder="{{ __('admin.additional_phone') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="emailInput" class="text-dark">{{ __('admin.email') }} <span class="text-danger">*</span></label>
                                            <input id="emailInput" type="email" name="email"
                                                placeholder="{{ __('admin.email') }}" class="form-control"
                                                value="{{ old('email', Auth::guard('web')->user()->email) }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="passwordInput" class="text-dark">{{ __('admin.password') }}</label>
                                            <input id="passwordInput" type="password" name="password"
                                                placeholder="{{ __('admin.password') }}" class="form-control">
                                            <small class="form-text text-muted">{{ __('admin.password_optional') }}</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="passwordConfirmationInput" class="text-dark">{{ __('admin.password_confirmation') }}</label>
                                            <input id="passwordConfirmationInput" type="password" name="password_confirmation"
                                                placeholder="{{ __('admin.password_confirmation') }}" class="form-control ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="mt-4 btn btn-primary">
                                            {{ __('admin.update') }}
                                        </button>
                                        <a href="{{ route('user.dashboard.index') }}" class="mt-4 btn btn-secondary ml-2">
                                            {{ __('admin.back') }}
                                        </a>
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