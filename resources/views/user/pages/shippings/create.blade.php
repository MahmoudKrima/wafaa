@extends('user.layouts.app')
@section('title', __('admin.create_shipping'))

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.create_shipping') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <div class="step-indicator d-flex align-items-center">
                                    <div class="step active">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">1</div>
                                        <span class="ms-2">{{ __('admin.select_company') }}</span>
                                    </div>
                                    <div class="step-line mx-3" style="width: 60px; height: 3px; background: #e9ecef;"></div>
                                    <div class="step">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">2</div>
                                        <span class="ms-2">{{ __('admin.select_method') }}</span>
                                    </div>
                                    <div class="step-line mx-3" style="width: 60px; height: 3px; background: #e9ecef;"></div>
                                    <div class="step">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">3</div>
                                        <span class="ms-2">{{ __('admin.user_information') }}</span>
                                    </div>
                                    <div class="step-line mx-3" style="width: 60px; height: 3px; background: #e9ecef;"></div>
                                    <div class="step">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">4</div>
                                        <span class="ms-2">{{ __('admin.shipping_details') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Select Shipping Company -->
                    <div class="step-content" id="step-1">
                        <h5 class="text-center mb-4">{{ __('admin.choose_shipping_company') }}</h5>

                        <div id="companies-container">
                            <div class="text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('front/assets/img/preload.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px; max-width: 200px;">
                                </div>
                                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-2">{{ __('admin.loading_companies') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="step-content" id="step-2" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.choose_shipping_method') }}</h5>
                        <p class="text-center text-muted mb-4">{{ __('admin.select_shipping_method_for') }} <strong id="selected-company-name"></strong></p>

                        <div id="method-options" class="row">
                        </div>
                    </div>
                    <div class="step-content" id="step-3" style="display: none;"
                        data-user-city-id="{{ auth()->user()->city->city_id ?? '' }}"
                        data-app-locale="{{ app()->getLocale() }}">
                        <h5 class="text-center mb-4">{{ __('admin.user_information') }}</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_name" class="text-dark">{{ __('admin.full_name') }}</label>
                                <input id="user_name" type="text" name="user_name" class="form-control" value="{{ auth()->user()->name ?? '' }}" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                                <input id="user_phone" type="text" name="phone" class="form-control" value="{{ auth()->user()->phone ?? '' }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_additional_phone" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                <input id="user_additional_phone" type="text" name="additional_phone" class="form-control" value="{{ auth()->user()->additional_phone ?? '' }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_email" class="text-dark">{{ __('admin.email') }}</label>
                                <input id="user_email" type="text" name="email" class="form-control" value="{{ auth()->user()->email ?? '' }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_city" class="text-dark">{{ __('admin.city') }}</label>
                                <select id="user_city" name="city" class="form-control" disabled>
                                    <option value="">{{ __('admin.select_city') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_country" class="text-dark">{{ __('admin.country') }}</label>
                                <input id="user_country" type="text" name="country" class="form-control" value="" disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_address" class="text-dark">{{ __('admin.full_address') }}</label>
                                <textarea id="user_address" name="user_address" class="form-control" rows="3" disabled>{{ auth()->user()->address ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="user_postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                                <input id="user_postal_code" type="text" name="postal_code" class="form-control" value="{{ auth()->user()->postal_code ?? '' }}">
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            {{ __('admin.user_info_note') }}
                        </div>
                    </div>

                    <div class="step-content" id="step-4" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.receiver_information') }}</h5>

                        <!-- Receiver Type Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="text-dark">{{ __('admin.receiver_type') }}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="receiver_type" id="existing_receiver" value="existing" checked>
                                    <label class="form-check-label" for="existing_receiver">
                                        {{ __('admin.existing_receiver') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="receiver_type" id="new_receiver" value="new">
                                    <label class="form-check-label" for="new_receiver">
                                        {{ __('admin.new_receiver') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Receiver Selection -->
                        <div id="existing_receiver_section">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="receiver_select" class="text-dark">{{ __('admin.select_receiver') }}</label>
                                    <select id="receiver_select" name="receiver_id" class="form-control">
                                        <option value="">{{ __('admin.choose_receiver') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-sm btn-info" onclick="testReceiverPopulation()">
                                        {{ __('admin.test_receiver_population') }}
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div id="new_receiver_section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="text-dark">{{ __('admin.full_name') }}</label>
                                    <input id="name" type="text" name="name" class="form-control" placeholder="{{ __('admin.enter_full_name') }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                                    <input id="phone" type="text" name="phone" class="form-control" placeholder="{{ __('admin.enter_phone_number') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="additional_phone" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                    <input id="additional_phone" type="text" name="additional_phone" class="form-control" placeholder="{{ __('admin.enter_additional_phone') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="text-dark">{{ __('admin.email') }}</label>
                                    <input id="email" type="text" name="email" class="form-control" placeholder="{{ __('admin.enter_email') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="text-dark">{{ __('admin.city') }}</label>
                                    <select id="city" name="city" class="form-control">
                                        <option value="">{{ __('admin.select_city') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="text-dark">{{ __('admin.country') }}</label>
                                    <input id="country" type="text" name="country" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="address" class="text-dark">{{ __('admin.full_address') }}</label>
                                    <textarea id="address" name="address" class="form-control" rows="3" placeholder="{{ __('admin.enter_full_address') }}"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                                    <input id="postal_code" type="text" name="postal_code" class="form-control" placeholder="{{ __('admin.enter_postal_code') }}">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="step-content" id="step-5" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.shipping_details') }}</h5>

                        <form action="{{ route('user.shippings.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="shipping_company_id" id="shipping_company_id">
                            <input type="hidden" name="shipping_method" id="shipping_method">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="amountInput" class="text-dark">{{ __('admin.amount') }}</label>
                                    <input id="amountInput" type="text" name="amount" placeholder="{{ __('admin.amount') }}"
                                        class="form-control" value="{{ old('amount') }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="weight" class="text-dark">{{ __('admin.weight_kg') }}</label>
                                    <input id="weight" type="number" name="weight" placeholder="{{ __('admin.weight_kg') }}"
                                        class="form-control" value="{{ old('weight') }}" step="0.1" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12 col-md-12 mb-3 custom-file-container" data-upload-id="myFirstImage">
                                    <label>{{ __('admin.attachment') }}<a href="javascript:void(0)" class="custom-file-container__image-clear"
                                            title="{{ __('admin.clear_image') }}"><span style="background-color:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span></a></label>
                                    <label class="custom-file-container__custom-file">
                                        <input type="file" class="custom-file-container__custom-file__custom-file-input" name="attachment">
                                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                                    </label>
                                    <div class="custom-file-container__image-preview"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <input type="submit" value="{{ __('admin.create_shipping') }}" class="mt-4 btn btn-primary">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" id="btn-prev" style="display: none;">
                                ← {{ __('admin.previous') }}
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-next" disabled>
                                {{ __('admin.next') }} →
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const translations = {
        local: '{{ __("admin.local") }}',
        international: '{{ __("admin.international") }}',
        local_delivery: '{{ __("admin.local_delivery") }}',
        worldwide_shipping: '{{ __("admin.worldwide_shipping") }}',
        choose_receiver: '{{ __("admin.choose_receiver") }}',
        no_cities_available: '{{ __("admin.no_cities_available") }}',
        no_receivers_found: '{{ __("admin.no_receivers_found") }}',
        error_loading_receivers: '{{ __("admin.error_loading_receivers") }}',
        error_loading_cities: '{{ __("admin.error_loading_cities") }}',
        select_city: '{{ __("admin.select_city") }}',
        select_receiver: '{{ __("admin.select_receiver") }}',
        choose_receiver: '{{ __("admin.choose_receiver") }}',
        no_receivers_found: '{{ __("admin.no_receivers_found") }}',
        error_loading_receivers: '{{ __("admin.error_loading_receivers") }}',
        error_loading_cities: '{{ __("admin.error_loading_cities") }}',
        select_city: '{{ __("admin.select_city") }}',
    };
</script>
<script src="{{ asset('user/shipping.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof fetchShippingCompanies === 'function') {
            fetchShippingCompanies();
        } else {
            document.getElementById('companies-container').innerHTML =
                '<div class="alert alert-danger">JavaScript error: fetchShippingCompanies function not loaded</div>';
        }
    });
</script>
@endpush