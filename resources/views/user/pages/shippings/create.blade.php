@extends('user.layouts.app')
@section('title', __('admin.create_shipping'))

@push('css')
@if (App::getLocale() === 'ar')
<link rel="stylesheet" href="{{ asset('user/shipping-styles.css') }}">
@else
<link rel="stylesheet" href="{{ asset('user/shipping-styles_en.css') }}">
@endif
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endpush

<style>
    .widget {
        padding: 0 40px !important
    }

    @media screen and (max-width:1025px) {
        .widget {
            padding: 0 0 !important
        }
    }

    .navbar .language-dropdown .custom-dropdown-icon a.dropdown-toggle:before,
    .navbar .navbar-item .nav-item.user-profile-dropdown .nav-link.user:before {
        top: 17px !important
    }

    @media screen and (max-width:600px) {
        .main-content {
            margin-top: 130px !important
        }
    }
</style>

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-12 layout-spacing">

            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center my_indicators">
                                <div class="step-indicator d-flex flex-column flex-sm-row align-items-center step_theme">
                                    <div class="prev_next_btn mb-2 mb-sm-0">
                                        <div class="col-12 d-flex flex-column flex-sm-row justify-content-between gap-2">
                                            <button type="button" class="btn btn-secondary" id="btn-prev" style="display:none;">
                                                {{ app()->getLocale() === 'ar' ? '→' : '←' }} {{ __('admin.previous') }}
                                            </button>
                                        </div>
                                    </div>
                                    @for ($i = 1; $i <= 6; $i++)
                                        <div class="step {{ $i === 1 ? 'active' : '' }} mb-2 mb-sm-0">
                                        <div class="step-number {{ $i===1 ? 'bg-primary' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0" style="width:35px;height:35px;font-size:14px;font-weight:bold;margin-bottom:10px;">{{ $i }}</div>
                                        <span class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-2 small step_text">
                                            @switch($i)
                                            @case(1) {{ __('admin.select_company') }} @break
                                            @case(2) {{ __('admin.sender_information') }} @break
                                            @case(3) {{ __('admin.receivers') }} @break
                                            @case(4) {{ __('admin.shipping_details') }} @break
                                            @case(5) {{ __('admin.payment_details') }} @break
                                            @case(6) {{ __('admin.summary') }} @break
                                            @endswitch
                                        </span>
                                </div>
                                @if($i<6)
                                    <div class="step-line d-none d-sm-block mx-3" style="width:40px;height:2px;background:#e9ecef;">
                            </div>
                            @endif
                            @endfor
                            <div class="prev_next_btn mb-2 mb-sm-0">
                                <div class="col-12 d-flex flex-column flex-sm-row justify-content-between gap-2">
                                    <button type="button" class="btn btn-primary" id="btn-next" disabled>
                                        {{ __('admin.next') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}
                                    </button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>




    <div class="step-content" id="step-1">
        <h5 class="text-center mb-4">{{ __('admin.choose_shipping_company') }}</h5>
        <div id="companies-container">
            <div class="text-center">
                <div class="mb-3">
                    <img src="{{ asset('front/assets/img/preload.png') }}" alt="Logo" class="img-fluid" style="max-height:60px;max-width:150px;">
                </div>
                <div class="spinner-border text-primary" role="status" style="width:2rem;height:2rem;">
                    <span class="visually-hidden"></span>
                </div>
                <p class="mt-2">{{ __('admin.loading_companies') }}</p>
            </div>
        </div>
        <div id="company-selected-summary" class="mt-4" style="display:none;"></div>
        <div id="company-pricing-display" class="mt-4" style="display:none;"></div>

        <div>
            <hr>
        </div>
        <div id="inline-step2-under-step1" class="mt-5" style="display:none;">
            <h5 class="text-center mb-4">{{ __('admin.choose_shipping_method') }}</h5>
            <p class="text-center text-muted mb-4">
                {{ __('admin.select_shipping_method_for') }} <strong id="selected-company-name"></strong>
            </p>

            <div id="method-options" class="row" style="display:flex;justify-content:center;"></div>
        </div>
    </div>

    <div class="step-content" id="step-2" style="display:none;" data-app-locale="{{ app()->getLocale() }}" data-senders-url="{{ route('user.senders.getSenders') }}">
        <h5 class="text-center mb-4">{{ __('admin.user_information') }}</h5>

        <div class="row mb-4">
            <div class="col-12 col-md-12">
                <label class="text-dark">{{ __('admin.sender_type') }}</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sender_type" id="sender_auth" value="auth" checked>
                    <label class="form-check-label" for="sender_auth">{{ __('admin.authenticated_user') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sender_type" id="sender_existing" value="existing">
                    <label class="form-check-label" for="sender_existing">{{ __('admin.existing_sender') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="sender_type" id="sender_new" value="new">
                    <label class="form-check-label" for="sender_new">{{ __('admin.new_sender') }}</label>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-2" id="existing_sender_select_wrap" style="display:none;">
                <label for="sender_select" class="text-dark">{{ __('admin.select_sender') }}</label>
                <select id="sender_select" class="form-control">
                    <option value="">{{ __('admin.choose_sender') }}</option>
                </select>
            </div>
        </div>

        
        <div id="language-note" class="alert alert-warning" style="display:none;"></div>

        <div id="sender_form_container">
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_name" class="text-dark">{{ __('admin.full_name') }}</label>
                    <input id="user_name" name="user_name" type="text" class="form-control" required
                        value="{{ old('user_name', auth()->user()->name ?? '') }}"
                        placeholder="{{ __('admin.full_name') }}"
                        data-auth-value="{{ auth()->user()->name ?? '' }}">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                    <input id="user_phone" name="user_phone" type="text" class="form-control" required
                        value="{{ old('user_phone', auth()->user()->phone ?? '') }}"
                        placeholder="{{ __('admin.phone_number') }}"
                        data-auth-value="{{ auth()->user()->phone ?? '' }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_additional_phone" class="text-dark">{{ __('admin.additional_phone') }}</label>
                    <input id="user_additional_phone" name="user_additional_phone" type="text" class="form-control"
                        value="{{ old('user_additional_phone', auth()->user()->additional_phone ?? '') }}"
                        placeholder="{{ __('admin.additional_phone') }}"
                        data-auth-value="{{ auth()->user()->additional_phone ?? '' }}">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_email" class="text-dark">{{ __('admin.email') }}</label>
                    <input id="user_email" name="user_email" type="email" class="form-control" required
                        value="{{ old('user_email', auth()->user()->email ?? '') }}"
                        placeholder="email@example.com"
                        data-auth-value="{{ auth()->user()->email ?? '' }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_country" class="text-dark">{{ __('admin.country') }}</label>
                    <select id="user_country" name="country_id" class="form-control" required
                        data-selected="{{ old('country_id') }}">
                        <option value="">{{ __('admin.choose_country') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_state" class="text-dark">{{ __('admin.state') }}</label>
                    <select id="user_state" name="state_id" class="form-control" required
                        data-selected="{{ old('state_id') }}">
                        <option value="">{{ __('admin.choose_state') }}</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_city" class="text-dark">{{ __('admin.city') }}</label>
                    <select id="user_city" name="city_id" class="form-control" required
                        data-selected="{{ old('city_id') }}">
                        <option value="">{{ __('admin.choose_city') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="user_postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                    <input id="user_postal_code" type="text" name="postal_code" class="form-control"
                        value="{{ old('postal_code') }}"
                        placeholder="{{ __('admin.postal_code') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="user_address" class="text-dark">{{ __('admin.full_address') }}</label>
                    <textarea id="user_address" name="address" class="form-control" rows="3" required
                        placeholder="{{ __('admin.full_address') }}"
                        data-auth-value="{{ auth()->user()->address ?? '' }}">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="step-content" id="step-3" style="display:none;">
        <h5 class="text-center mb-4">{{ __('admin.receiver_information') }}</h5>
        <div class="row mb-4 receiver_theme">
            <div class="col-12 col-md-6">
                <label class="text-dark">{{ __('admin.receiver_type') }}</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="receiver_type" id="existing_receiver" value="existing">
                    <label class="form-check-label" for="existing_receiver">{{ __('admin.existing_receiver') }}</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="receiver_type" id="new_receiver" value="new">
                    <label class="form-check-label" for="new_receiver">{{ __('admin.new_receiver') }}</label>
                </div>
            </div>
            <div class="col-12 col-md-6 mt-2">
                <button type="button" id="add-receiver-btn" class="btn btn-success">
                    <i class="fas fa-plus"></i> {{ __('admin.add_receiver') }}
                </button>
            </div>
        </div>

        <div id="existing_receiver_section">
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <label for="receiver_select" class="text-dark">{{ __('admin.select_receiver') }}</label>
                    <select id="receiver_select" name="receiver_id" class="form-control">
                        <option value="">{{ __('admin.choose_receiver') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="new_receiver_section" style="display:none;">
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="name" class="text-dark">{{ __('admin.full_name') }}</label>
                    <input id="name" type="text" name="name" class="form-control" placeholder="{{ __('admin.enter_full_name') }}" required>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                    <input id="phone" type="input" name="phone" class="form-control" placeholder="{{ __('admin.enter_phone_number') }}" pattern="05[0-9]{8}" title="Phone must start with 05 followed by 8 digits (e.g., 0512345678)" required>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="additional_phone" class="text-dark">{{ __('admin.additional_phone') }}</label>
                    <input id="additional_phone" type="input" name="additional_phone" class="form-control" placeholder="{{ __('admin.enter_additional_phone') }}" pattern="05[0-9]{8}">
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="email" class="text-dark">{{ __('admin.email') }}</label>
                    <input id="email" type="email" name="email" class="form-control" placeholder="{{ __('admin.enter_email') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="country" class="text-dark">{{ __('admin.country') }}</label>
                    <select id="country" name="country" class="form-control" required>
                        <option value="">{{ __('admin.select_country') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="state" class="text-dark">{{ __('admin.state') }}</label>
                    <select id="state" name="state" class="form-control" required>
                        <option value="">{{ __('admin.select_state') }}</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="city" class="text-dark">{{ __('admin.city') }}</label>
                    <select id="city" name="city" class="form-control" required>
                        <option value="">{{ __('admin.select_city') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <label for="postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                    <input id="postal_code" type="text" name="postal_code" class="form-control" placeholder="{{ __('admin.enter_postal_code') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="address" class="text-dark">{{ __('admin.full_address') }}</label>
                    <textarea id="address" name="address" class="form-control" rows="3" placeholder="{{ __('admin.enter_full_address') }}" required></textarea>
                </div>
            </div>
        </div>

        <div id="receivers-container" class="mt-4" style="display:none;"></div>
        <div id="receiver-success-msg" class="mt-3" style="display:none;"></div>
        <div id="receiver-error-msg" class="mt-3" style="display:none;"></div>
    </div>

    <div class="step-content" id="step-4" style="display:none;">
        <h5 class="text-center mb-4">{{ __('admin.shipping_details') }}</h5>

        <form action="{{ route('user.shippings.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
            @csrf
            <input type="hidden" name="sender_kind" id="sender_kind_hidden">
            <input type="hidden" name="shipping_company_id" id="shipping_company_id">
            <input type="hidden" name="shipping_method" id="shipping_method">
            <input type="hidden" name="selected_receivers" id="selected_receivers_hidden">
            <input type="hidden" name="sender_name" id="sender_name_hidden">
            <input type="hidden" name="sender_phone" id="sender_phone_hidden">
            <input type="hidden" name="sender_additional_phone" id="sender_additional_phone_hidden">
            <input type="hidden" name="sender_email" id="sender_email_hidden">
            <input type="hidden" name="sender_address" id="sender_address_hidden">
            <input type="hidden" name="sender_country_id" id="sender_country_id_hidden">
            <input type="hidden" name="sender_country_name" id="sender_country_name_hidden">
            <input type="hidden" name="sender_state_id" id="sender_state_id_hidden">
            <input type="hidden" name="sender_state_name" id="sender_state_name_hidden">
            <input type="hidden" name="sender_city_id" id="sender_city_id_hidden">
            <input type="hidden" name="sender_city_name" id="sender_city_name_hidden">
            <input type="hidden" name="sender_postal_code" id="sender_postal_code_hidden">
            <input type="hidden" name="payment_method" id="payment_method_hidden">
            <input type="hidden" name="shipping_price_per_receiver" id="shipping_price_per_receiver_hidden">
            <input type="hidden" name="extra_weight_per_receiver" id="extra_weight_per_receiver_hidden">
            <input type="hidden" name="cod_price_per_receiver" id="cod_price_per_receiver_hidden">
            <input type="hidden" name="total_per_receiver" id="total_per_receiver_hidden">
            <input type="hidden" name="total_amount" id="total_amount_hidden">
            <input type="hidden" name="receivers_count" id="receivers_count_hidden">
            <input type="hidden" name="currency" id="currency_hidden">
            <input type="hidden" name="max_weight" id="max_weight_hidden">
            <input type="hidden" name="entered_weight" id="entered_weight_hidden">
            <input type="hidden" name="extra_kg" id="extra_kg_hidden">

            <div class="row mb-4">
                <div class="col-12 col-md-4 mb-3 mb-md-0">
                    <label for="package_type" class="text-dark mb-2">{{ __('admin.package_type') }}</label>
                    <select id="package_type" name="package_type" class="form-control" required>
                        <option value="">{{ __('admin.select_package_type') }}</option>
                        <option value="box" {{ old('package_type')=='box' ? 'selected' : '' }}>{{ __('admin.boxes') }}</option>
                        <option value="document" {{ old('package_type')=='document' ? 'selected' : '' }}>{{ __('admin.documents') }}</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label for="package_number" class="text-dark mb-2">{{ __('admin.number') }}</label>
                    <input id="package_number" type="number" name="package_number" class="form-control" placeholder="1" min="1" value="{{ old('package_number', 1) }}" required>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label for="weight" class="text-dark">{{ __('admin.weight_kg') }}</label>
                    <input id="weight" type="number" name="weight" placeholder="{{ __('admin.weight_kg') }}" class="form-control" value="{{ old('weight') }}" step="0.1" min="0.1" required>
                </div>
            </div>

            <div class="row d-flex" id="dimensions_section">
                <div class="col-4 mb-3">
                    <label for="length" class="text-dark">{{ __('admin.length_cm') }}</label>
                    <input id="length" type="number" name="length" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('length',1) }}" required>
                </div>
                <div class="col-4 mb-3">
                    <label for="width" class="text-dark">{{ __('admin.width_cm') }}</label>
                    <input id="width" type="number" name="width" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('width',1) }}" required>
                </div>
                <div class="col-4 mb-3">
                    <label for="height" class="text-dark">{{ __('admin.height_cm') }}</label>
                    <input id="height" type="number" name="height" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('height',1) }}" required>
                </div>
            </div>


            <div class="row">
                <div class="col-12 mb-3">
                    <label for="package_description" class="text-dark">{{ __('admin.package_description') }}</label>

                    <div class="mb-2">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="description_type" id="description_new" value="new" checked>
                            <label class="form-check-label" for="description_new">{{ __('admin.new_description') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="description_type" id="description_existing" value="existing">
                            <label class="form-check-label" for="description_existing">{{ __('admin.select_existing_description') }}</label>
                        </div>
                    </div>

                    <div id="existing_descriptions_container" style="display: none;" class="mb-2">
                        <select id="existing_descriptions" class="form-control">
                            <option value="">{{ __('admin.select_description') }}</option>
                        </select>
                    </div>

                    <textarea id="package_description" name="package_description" class="form-control" rows="3"
                        placeholder="{{ __('admin.enter_package_description') }}" required>{{ old('package_description') }}</textarea>

                    <input type="hidden" id="description_id" name="description_id" value="">
                    <input type="hidden" id="is_new_description" name="is_new_description" value="1">
                </div>
            </div>


            <div class="row">
                <div class="col-12 mb-5 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="accept_terms" name="accept_terms" {{ old('accept_terms') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="accept_terms">
                            <a href="{{ route('front.terms') }}" target="_blank" class="text-primary">{{ __('admin.i_accept_terms') }}</a>
                        </label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3 custom-file-container" data-upload-id="myFirstImage">
                    <label>
                        {{ __('admin.shipment_image') }}
                        <a href="javascript:void(0)" class="custom-file-container__image-clear" title="{{ __('admin.clear_image') }}">
                            <span style="background:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span>
                        </a>
                    </label>
                    <label class="custom-file-container__custom-file">
                        <input type="file" id="shipmentImage" class="custom-file-container__custom-file__custom-file-input" name="shipment_image" accept="image/*" data-max-file-size="2M" data-max-files="1">
                        <div class="mt-2"><small class="text-muted">{{ __('admin.upload_shipment_image_help') }}</small></div>
                        <span class="custom-file-container__custom-file__custom-file-control"></span>
                    </label>
                    <div class="custom-file-container__image-preview"></div>
                </div>
            </div>
        </form>
    </div>

    <div class="step-content" id="step-5" style="display:none;">
        <h5 class="text-center mb-4">{{ __('admin.shippment_type') }}</h5>
        <div class="row">
            <div class="col-12">
                <div class="payment-options-container"></div>
            </div>
        </div>
    </div>

    <div id="step-6" class="step-content" style="display:none;">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-4">{{ __('admin.final_shipment_review') }}</h4>
                <div id="step6-errors" class="mb-3"></div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4" style="border-radius:15px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-user-tie" style="margin:0 5px;"></i>{{ __('admin.sender_information') }}</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>{{ __('admin.name') . ' : ' }}</strong> <span id="sender-name-preview">{{ __('admin.name') }}</span></p>
                                <p><strong>{{ __('admin.phone') . ' : ' }}</strong> <span id="sender-phone-preview">{{ __('admin.phone') }}</span></p>
                                <p><strong>{{ __('admin.city') . ' : ' }}</strong> <span id="sender-city-preview">{{ __('admin.city') }}</span></p>
                                <p><strong>{{ __('admin.address') . ' : ' }}</strong> <span id="sender-address-preview">{{ __('admin.address') }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4" style="border-radius:15px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-house-flood-water-circle-arrow-right" style="margin:0 5px;"></i>{{ __('admin.shipping_company_details') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <div>
                                            <img id="company-logo-preview" src="" alt="Company Logo" class="me-3" style="width:80px;height:60px;object-fit:contain;">
                                            <div>
                                                <h6 id="company-name-preview" style="margin:5px 15px;" class="mb-1">{{ __('admin.company_name') }}</h6>
                                                <small id="company-service-preview" style="margin:5px 15px;" class="text-muted">{{ __('admin.service_type') }}</small>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-info fs-6" id="shipping-method-preview">{{ __('admin.shipping_method') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4" style="border-radius:15px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-user-friends" style="margin:0 5px;"></i>{{ __('admin.receivers_info') }} (<span id="receivers-count-preview">0</span>)</h5>
                            </div>
                            <div class="card-body">
                                <div id="receivers-summary-container"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4" style="border-radius:15px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-truck-fast" style="margin:0 5px;"></i>{{ __('admin.package_details') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>{{ __('admin.package_type') . ': ' }}</strong> <span id="package-type-preview">{{ __('admin.package_type') }}</span></p>
                                        <p><strong>{{ __('admin.package_count') . ': ' }}</strong> <span id="package-count-preview">{{ __('admin.package_count') }}</span></p>
                                        <p><strong>{{ __('admin.weight_summary') . ': ' }}</strong> <span id="package-weight-preview">{{ __('admin.weight_kg') }}</span> {{ __('admin.kg') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>{{ __('admin.length_summary') . ': ' }}</strong> <span id="package-length-preview">{{ __('admin.length_cm') }}</span> {{ __('admin.cm') }}</p>
                                        <p><strong>{{ __('admin.width_summary') . ': ' }}</strong> <span id="package-width-preview">{{ __('admin.width_cm') }}</span> {{ __('admin.cm') }}</p>
                                        <p><strong>{{ __('admin.height_summary') . ': ' }}</strong> <span id="package-height-preview">{{ __('admin.height_cm') }}</span> {{ __('admin.cm') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><strong class="mb-1">{{ __('admin.package_description') }}</strong></p>
                                        <div id="package-description-preview" class="text-muted"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4" style="border-radius:15px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                                <h5 class="mb-0" style="color:#fff;"><i class="fa fa-info-circle" style="margin:0 5px;"></i>{{ __('admin.shipment_price_details') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.shipping_price_per_receiver') }}: </strong>
                                        <div class="text-muted" id="price-base-per-receiver"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-1 text-black">{{__('admin.extra_weight_per_receiver') }}: </strong>
                                        <div class="text-muted" id="price-extra-per-receiver"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="small mb-3 text-muted mt-0" id="extra-weight-note">{{__('admin.no_extra_weight') }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.shippment_type') }}: </strong>
                                        <div class="mb-0 text-muted" id="payment-method-preview">{{__('admin.cash_on_delivery') }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.cod_fee_per_one_receiver') }}: </strong>
                                        <div class="mb-0 text-muted" id="cod-fee-per-one-receiver-preview"></div>
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
                                        <strong class="mb-3 text-black">{{__('admin.shipping_fee') }}: </strong>
                                        <div class="mb-0 text-primary" id="shipping-fee-preview"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.extra_fees_weight') }}: </strong>
                                        <div class="mb-0 text-primary" id="extra-fees-preview"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.cod_fee') }}: </strong>
                                        <div class="mb-0 text-primary" id="cod-fees-preview"></div>
                                    </div>
                                </div>
                                <div class="row" id="wallet-balance-section" style="display:none;">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-3 text-black">{{__('admin.wallet_balance') }}: </strong>
                                        <div class="mb-0 text-primary" id="wallet-balance-display"></div>
                                    </div>
                                    <div class="col-md-12 alert alert-warning" id="wallet-balance-warning" style="display:none;">
                                        <small><i class="fas fa-exclamation-triangle me-1"></i> {{__('admin.insufficient_balance_warning') }}</small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12" style="display:flex;justify-content:space-between;">
                                        <strong class="mb-1 text-success">{{__('admin.total_amount_you_pay') }}: </strong>
                                        <div class="h6 mb-0 text-success" id="total-amount-preview"></div>
                                    </div>
                                    <div class="col-md-12">
                                        <span class="small mb-3 text-muted mt-0" id="receivers-count-display"></span> {{__('admin.receivers') }} ×
                                        <span class="small mb-3 text-muted mt-0" id="per-receiver-total"></span> {{__('admin.per_receiver') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg" id="btn-confirm-shipping" disabled>
                            <i class="fas fa-check me-2" style="margin:0 5px;"></i> {{ __('admin.confirm_shipment') }}
                        </button>
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
        add_receiver: '{{ __("admin.add_receiver") }}',
        add_from_existing: '{{ __("admin.add_from_existing") }}',
        choose_receiver: '{{ __("admin.choose_receiver") }}',
        no_receivers_found: '{{ __("admin.no_receivers_found") }}',
        error_loading_receivers: '{{ __("admin.error_loading_receivers") }}',
        error_loading_cities: '{{ __("admin.error_loading_cities") }}',
        boxes: '{{ __("admin.boxes") }}',
        documents: '{{ __("admin.documents") }}',
        package_type: '{{ __("admin.package_type") }}',
        select_package_type: '{{ __("admin.select_package_type") }}',
        number: '{{ __("admin.number") }}',
        weight_kg: '{{ __("admin.weight_kg") }}',
        length_cm: '{{ __("admin.length_cm") }}',
        width_cm: '{{ __("admin.width_cm") }}',
        height_cm: '{{ __("admin.height_cm") }}',
        package_description: '{{ __("admin.package_description") }}',
        enter_package_description: '{{ __("admin.enter_package_description") }}',
        i_accept_terms: '{{ __("admin.i_accept_terms") }}',
        terms_and_conditions: '{{ __("admin.terms_and_conditions") }}',
        shipment_image: '{{ __("admin.shipment_image") }}',
        upload_shipment_image_help: '{{ __("admin.upload_shipment_image_help") }}',
        close: '{{ __("admin.close") }}',
        shipping_terms_title: '{{ __("admin.shipping_terms_title") }}',
        shipping_terms_content: '{{ __("admin.shipping_terms_content") }}',
        package_terms_title: '{{ __("admin.package_terms_title") }}',
        package_terms_content: '{{ __("admin.package_terms_content") }}',
        liability_terms_title: '{{ __("admin.liability_terms_title") }}',
        liability_terms_content: '{{ __("admin.liability_terms_content") }}',
        payment_details: '{{ __("admin.payment_details") }}',
        cash_on_delivery: '{{ __("admin.cash_on_delivery") }}',
        cod_information: '{{ __("admin.cod_information") }}',
        cod_description: '{{ __("admin.cod_description") }}',
        cod_price: '{{ __("admin.cod_price") }}',
        total_with_cod: '{{ __("admin.total_with_cod") }}',
        shipment_summary: '{{ __("admin.shipment_summary") }}',
        summary: '{{ __("admin.summary") }}',
        receivers: '{{ __("admin.receivers") }}',
        shipping_company: '{{ __("admin.shipping_company") }}',
        user_information: '{{ __("admin.user_information") }}',
        cost_breakdown: '{{ __("admin.cost_breakdown") }}',
        pricing_information: '{{ __("admin.pricing_information") }}',
        local_shipping_price: '{{ __("admin.local_shipping_price") }}',
        international_shipping_price: '{{ __("admin.international_shipping_price") }}',
        extra_weight_price: '{{ __("admin.extra_weight_price") }}',
        cash_on_delivery_available: '{{ __("admin.cash_on_delivery_available") }}',
        additional_fee_per_receiver: '{{ __("admin.additional_fee_per_receiver") }}',
        max_weight: '{{ __("admin.max_weight") }}',
        kg: '{{ __("admin.kg") }}',
        receiver: '{{ __("admin.receiver") }}',
        bill_of_lading: '{{ __("admin.bill_of_lading") }}',
        individual_cost: '{{ __("admin.individual_cost") }}',
        cost_breakdown: '{{ __("admin.cost_breakdown") }}',
        shipping: '{{ __("admin.shipping") }}',
        cod: '{{ __("admin.cod") }}',
        package_details: '{{ __("admin.package_details") }}',
        weight: '{{ __("admin.weight") }}',
        dimensions: '{{ __("admin.dimensions") }}',
        description: '{{ __("admin.description") }}',
        cod_fee: '{{ __("admin.cod_fee") }}',
        cost_breakdown_title: '{{ __("admin.cost_breakdown_title") }}',
        base_shipping_cost: '{{ __("admin.base_shipping_cost") }}',
        extra_weight_fee: '{{ __("admin.extra_weight_fee") }}',
        shipment_fees: '{{ __("admin.shipment_fees") }}',
        fuel_surcharge: '{{ __("admin.fuel_surcharge") }}',
        cod_fee_per_receiver: '{{ __("admin.cod_fee_per_receiver") }}',
        total_cod_fees: '{{ __("admin.total_cod_fees") }}',
        total_cost: '{{ __("admin.total_cost") }}',
        terms_conditions: '{{ __("admin.terms_conditions") }}',
        accept_terms: '{{ __("admin.accept_terms") }}',
        add_receiver_error: '{{ __("admin.add_receiver_error") }}',
        select_company_method_error: '{{ __("admin.select_company_method_error") }}',
        package_details_error: '{{ __("admin.package_details_error") }}',
        no_companies_available: '{{ __("admin.no_companies_available") }}',
        error_loading_companies: '{{ __("admin.error_loading_companies") }}',
        loading_companies: '{{ __("admin.loading_companies") }}',
        no_companies_found: '{{ __("admin.no_companies_found") }}',
        select_country: '{{ __("admin.select_country") }}',
        no_countries_found: '{{ __("admin.no_countries_found") }}',
        error_loading_countries: '{{ __("admin.error_loading_countries") }}',
        select_state: '{{ __("admin.select_state") }}',
        no_states_found: '{{ __("admin.no_states_found") }}',
        error_loading_states: '{{ __("admin.error_loading_states") }}',
        company: '{{ __("admin.company") }}',
        service: '{{ __("admin.service") }}',
        method: '{{ __("admin.method") }}',
        city: '{{ __("admin.city") }}',
        phone: '{{ __("admin.phone") }}',
        address: '{{ __("admin.address") }}',
        postal_code: '{{ __("admin.postal_code") }}',
        email: '{{ __("admin.email") }}',
        name: '{{ __("admin.name") }}',
        per_receiver: '{{ __("admin.per_receiver") }}',
        select_city: '{{ __("admin.select_city") }}',
        no_cities_available: '{{ __("admin.no_cities_available") }}',
        error_loading_cities: '{{ __("admin.error_loading_cities") }}',
        add_receiver: '{{ __("admin.add_receiver") }}',
        receiver_added: '{{ __("admin.receiver_added") }}',
        payment_wallet: '{{ __("admin.wallet") }}',
        payment_wallet_desc: '{{ __("admin.wallet_description") }}',
        local_shipping: '{{ __("admin.local_shipping") }}',
        international_shipping: '{{ __("admin.international_shipping") }}',
        cash_on_delivery: '{{ __("admin.cash_on_delivery") }}',
        wallet: '{{ __("admin.wallet") }}',
        user_name: '{{ __("admin.user_name") }}',
        user_phone: '{{ __("admin.user_phone") }}',
        user_email: '{{ __("admin.user_email") }}',
        user_address: '{{ __("admin.user_address") }}',
        user_city: '{{ __("admin.user_city") }}',
        user_postal: '{{ __("admin.user_postal") }}',
        no_receivers_selected: '{{ __("admin.no_receivers_selected") }}',
        new: '{{ __("admin.new") }}',
        existing: '{{ __("admin.existing") }}',
        not_specified: '{{ __("admin.not_specified") }}',
        no_special_notes: '{{ __("admin.no_special_notes") }}',
        pdf_feature_coming_soon: '{{ __("admin.pdf_feature_coming_soon") }}',
        confirm_shipment_message: '{{ __("admin.confirm_shipment_message") }}',
        shipping_method: '{{ __("admin.shipping_method") }}',
        payment_method: '{{ __("admin.payment_method") }}',
        shipping_fees: '{{ __("admin.shipping_fees") }}',
        additional_fees: '{{ __("admin.additional_fees") }}',
        tax: '{{ __("admin.tax") }}',
        discount: '{{ __("admin.discount") }}',
        total_amount: '{{ __("admin.total_amount") }}',
        package_type: '{{ __("admin.package_type") }}',
        package_count: '{{ __("admin.package_count") }}',
        package_weight: '{{ __("admin.package_weight") }}',
        package_length: '{{ __("admin.package_length") }}',
        package_width: '{{ __("admin.package_width") }}',
        package_height: '{{ __("admin.package_height") }}',
        package_notes: '{{ __("admin.package_notes") }}',
        sender_name: '{{ __("admin.sender_name") }}',
        sender_phone: '{{ __("admin.sender_phone") }}',
        sender_email: '{{ __("admin.sender_email") }}',
        sender_address: '{{ __("admin.sender_address") }}',
        sender_city: '{{ __("admin.sender_city") }}',
        sender_postal: '{{ __("admin.sender_postal") }}',
        company_logo: '{{ __("admin.company_logo") }}',
        company_name: '{{ __("admin.company_name") }}',
        service_type: '{{ __("admin.service_type") }}',
        final_shipment_review: '{{ __("admin.final_shipment_review") }}',
        shipping_company_details: '{{ __("admin.shipping_company_details") }}',
        sender_information: '{{ __("admin.sender_information") }}',
        receivers: '{{ __("admin.receivers") }}',
        terms_and_conditions: '{{ __("admin.terms_and_conditions") }}',
        accept_terms_and_conditions: '{{ __("admin.accept_terms_and_conditions") }}',
        accept_shipping_terms: '{{ __("admin.accept_shipping_terms") }}',
        previous: '{{ __("admin.previous") }}',
        print_summary: '{{ __("admin.print_summary") }}',
        download_pdf: '{{ __("admin.download_pdf") }}',
        confirm_shipment: '{{ __("admin.confirm_shipment") }}',
        currency_symbol: '{{ __("admin.currency_symbol") }}',
        cm: '{{ __("admin.cm") }}',
        per_receiver_breakdown_title: '{{ __("admin.per_receiver_breakdown_title") }}',
        shipping_price_per_receiver: '{{ __("admin.shipping_price_per_receiver") }}',
        extra_weight_per_receiver: '{{ __("admin.extra_weight_per_receiver") }}',
        cod_price_per_receiver: '{{ __("admin.cod_price_per_receiver") }}',
        cod_fees: '{{ __("admin.cod_fees") }}',
        extra_weight_note: '{{ __("admin.extra_weight_note") }}',
        company_max_weight: '{{ __("admin.company_max_weight") }}',
        entered_weight: '{{ __("admin.entered_weight") }}',
        no_extra_weight: '{{ __("admin.no_extra_weight") }}',
        wallet_balance: '{{ __("admin.wallet_balance") }}',
        insufficient_balance_warning: '{{ __("admin.insufficient_balance_warning") }}',
        insufficient_balance: '{{ __("admin.insufficient_balance") }}',
        cod_amount: '{{ __("admin.cod_amount") }}',
        normal_shipment: '{{ __("admin.normal_shipment") }}',
        cash_on_delivery_shippment: '{{ __("admin.cash_on_delivery_shippment") }}',
        loading_countries: '{{ __("admin.loading_countries") }}',
        weight_summary: '{{ __("admin.weight_summary") }}',
        creating_shipment: '{{ __("admin.creating_shipment") }}',
        package_type_required: '{{ __("admin.package_type_required") }}',
        package_number_invalid: '{{ __("admin.package_number_invalid") }}',
        weight_invalid: '{{ __("admin.weight_invalid") }}',
        dimensions_missing: '{{ __("admin.dimensions_missing") }}',
        dimensions_required: '{{ __("admin.dimensions_required") }}',
        dimensions_invalid: '{{ __("admin.dimensions_invalid") }}',
        accept_terms_required: '{{ __("admin.accept_terms_required") }}',
        authenticated_user: '{{ __("admin.authenticated_user") }}',
        existing_sender: '{{ __("admin.existing_sender") }}',
        new_sender: '{{ __("admin.new_sender") }}',
        sender_type: '{{ __("admin.sender_type") }}',
        new_description: '{{ __("admin.new_description") }}',
        select_existing_description: '{{ __("admin.select_existing_description") }}',
        select_description: '{{ __("admin.select_description") }}',
        package_description_required: '{{ __("admin.package_description_required") }}',
    };
    const API_ENDPOINTS = {
        shippingCompanies: '{{ route("user.shippings.companies") }}',
        userDescriptions: '{{ route("user.user-descriptions.getUserDescriptions") }}'
    };
    window.API_ENDPOINTS = API_ENDPOINTS;
    window.APP_LOCALE = "{{ app()->getLocale() }}";
    window.inlineMethodsUnderStep1 = true;
    window.translations = translations;
</script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('user/step1.js') }}"></script>
<script src="{{ asset('user/step2.js') }}"></script>
<script src="{{ asset('user/step3.js') }}"></script>
<script src="{{ asset('user/step4.js') }}"></script>
<script src="{{ asset('user/step5.js') }}"></script>
<script src="{{ asset('user/step6.js') }}"></script>
<script src="{{ asset('user/step7.js') }}"></script>
<script src="{{ asset('user/utilities.js') }}"></script>
<script src="{{ asset('user/shipping-main.js') }}"></script>

<script>
    document.addEventListener('shippingCompanySelected', () => {
        const inline = document.getElementById('inline-step2-under-step1');
        if (inline) inline.style.display = 'block';
        if (typeof window.showMethodSelection === 'function') {
            window.showMethodSelection();
        }
        keepStep1Active();
    });

    document.addEventListener('shippingMethodSelected', () => {
        keepStep1Active();
        const btnNext = document.getElementById('btn-next');
        if (btnNext) {
            btnNext.disabled = false;
            btnNext.classList.remove('btn-secondary');
            btnNext.classList.add('btn-primary');
        }
    });

    function keepStep1Active() {
        const steps = document.querySelectorAll('.step-indicator .step');
        const nums = document.querySelectorAll('.step-indicator .step-number');
        steps.forEach((s, i) => s.classList.toggle('active', i === 0));
        nums.forEach((n, i) => {
            n.classList.remove('bg-primary', 'bg-secondary');
            n.classList.add(i === 0 ? 'bg-primary' : 'bg-secondary');
        });
        const btnPrev = document.getElementById('btn-prev');
        if (btnPrev) btnPrev.style.display = 'none';
        window.currentStep = 1;
    }

    (function() {
        const origShowStep = window.showStep;
        if (!origShowStep) return;

        window.showStep = function(step) {
            origShowStep(step);

            const btnNext = document.getElementById('btn-next');
            if (btnNext) btnNext.style.display = step === 6 ? 'none' : 'inline-block';

            if (step === 6) {
                if (typeof window.setupStep7 === 'function') window.setupStep7();
                document.dispatchEvent(new CustomEvent('stepChanged', {
                    detail: {
                        currentStep: 7
                    }
                }));
            }

            if (step === 2) {
                const btn = document.getElementById('btn-next');
                if (btn) {
                    btn.dataset.frozen = '';
                }

                if (typeof window.setupLocationFields === 'function') window.setupLocationFields();
                if (typeof window.handleCompanyRequirements === 'function') window.handleCompanyRequirements();

                const inputs = document.querySelectorAll("#step-2 input, #step-2 select, #step-2 textarea");
                inputs.forEach((inp) => {
                    if (!inp.dataset.boundStep2) {
                        const sync = () => {
                            const ok = window.validateStep3Form ? window.validateStep3Form() : true;
                            if (typeof window.hardEnableNext === 'function') {
                                window.hardEnableNext(ok);
                            } else if (btnNext) {
                                btnNext.disabled = !ok;
                                btnNext.classList.toggle('btn-secondary', !ok);
                                btnNext.classList.toggle('btn-primary', ok);
                            }
                        };
                        inp.addEventListener('input', sync);
                        inp.addEventListener('change', sync);
                        inp.dataset.boundStep2 = '1';
                    }
                });

                const ok = window.validateStep3Form ? window.validateStep3Form() : true;
                if (typeof window.hardEnableNext === 'function') {
                    window.hardEnableNext(ok);
                } else if (btnNext) {
                    btnNext.disabled = !ok;
                    btnNext.classList.toggle('btn-secondary', !ok);
                    btnNext.classList.toggle('btn-primary', ok);
                }
            }
        };
    })();

    (function() {
        const qs = (s, el = document) => el.querySelector(s);
        const qsa = (s, el = document) => Array.from(el.querySelectorAll(s));

        const stepEl = qs('#step-2');
        const formWrap = qs('#sender_form_container');
        const existingWrap = qs('#existing_sender_select_wrap');
        const selectEl = qs('#sender_select');
        const senderKindHidden = document.getElementById('sender_kind_hidden');

        function v(id, val) {
            const el = document.getElementById(id);
            if (!el) return;
            if (val === undefined) return el.value;
            el.value = val;
        }

        function setSel(id, val) {
            const el = document.getElementById(id);
            if (!el) return;
            el.value = val == null ? '' : String(val);
        }

        function curType() {
            const r = qs('input[name="sender_type"]:checked');
            return r ? r.value : 'auth';
        }

        function fillAuthDefaults() {
            qsa('#step-2 [data-auth-value]').forEach(el => {
                el.value = el.getAttribute('data-auth-value') || ''
            });
        }

        function clearForm() {
            ['user_name', 'user_phone', 'user_additional_phone', 'user_email', 'user_address', 'user_postal_code'].forEach(id => v(id, ''));
            setSel('user_country', '');
            setSel('user_state', '');
            setSel('user_city', '');
            ['user_country', 'user_state', 'user_city'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.dispatchEvent(new Event('change'));
            });
        }

        function applyPayload(p) {
            if (!p) return;
            v('user_name', p.name || '');
            v('user_phone', p.phone || '');
            v('user_additional_phone', p.additional_phone || '');
            v('user_email', p.email || '');
            v('user_postal_code', p.postal_code || '');
        }

        async function fetchSenders() {
            if (!stepEl || !selectEl) return;
            const url = stepEl.getAttribute('data-senders-url');
            if (!url) return;
            selectEl.innerHTML = `<option value="">{{ __('admin.choose_sender') }}</option>`;
            try {
                const res = await fetch(url, {
                    headers: {
                        Accept: 'application/json'
                    }
                });
                if (!res.ok) throw new Error();
                const data = await res.json();
                const list = Array.isArray(data) ? data : (data.data || []);
                list.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name ? `${s.name}${s.phone ? ' - ' + s.phone : ''}` : `ID #${s.id}`;
                    opt.dataset.payload = JSON.stringify(s);
                    selectEl.appendChild(opt);
                });
            } catch (e) {}
        }

        function requiredFilled() {
            const reqIds = ['user_name', 'user_phone', 'user_email', 'user_country', 'user_state', 'user_city', 'user_address'];
            for (const id of reqIds) {
                const el = document.getElementById(id);
                if (!el) return false;
                const val = ('value' in el) ? String(el.value).trim() : '';
                if (!val) return false;
            }
            return true;
        }


        function pushValidityToNext() {
            const ok = window.validateStep3Form ? window.validateStep3Form() : true;
            if (typeof window.hardEnableNext === 'function') {
                window.hardEnableNext(ok);
            } else {
                const btnNext = document.getElementById('btn-next');
                if (btnNext) {
                    btnNext.disabled = !ok;
                    btnNext.classList.toggle('btn-secondary', !ok);
                    btnNext.classList.toggle('btn-primary', ok);
                }
            }
        }

        function setSenderKindHidden() {
            if (senderKindHidden) senderKindHidden.value = curType();
        }

        function toggleUI() {
            const type = curType();
            setSenderKindHidden();

            if (type === 'auth') {
                if (existingWrap) existingWrap.style.display = 'none';
                if (formWrap) formWrap.style.display = '';
                fillAuthDefaults();
            } else if (type === 'existing') {
                clearForm();
                if (existingWrap) existingWrap.style.display = '';
                if (formWrap) formWrap.style.display = 'none';
                fetchSenders();
                if (selectEl) selectEl.value = '';
            } else {
                if (existingWrap) existingWrap.style.display = 'none';
                if (formWrap) formWrap.style.display = '';
                clearForm();
            }
            pushValidityToNext();
        }


        function bindRecalc() {
            const ids = [
                'user_name', 'user_phone', 'user_email', 'user_country', 'user_state', 'user_city', 'user_address',
                'user_additional_phone', 'user_postal_code'
            ];
            ids.forEach(id => {
                const el = document.getElementById(id);
                if (el && !el.dataset._bound_valid) {
                    el.addEventListener('input', pushValidityToNext);
                    el.addEventListener('change', pushValidityToNext);
                    el.dataset._bound_valid = '1';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            bindRecalc();
            toggleUI();

            qsa('input[name="sender_type"]').forEach(r => r.addEventListener('change', toggleUI));

            // Event binding is now handled in Select2 initialization
        });

        document.addEventListener('stepChanged', e => {
            if (e && e.detail && (e.detail.currentStep === 2 || e.detail.currentStep === '2')) {
                setSenderKindHidden();
                pushValidityToNext();
            }
        });

        // Initialize Select2 on sender select
        function initializeSelect2() {
            if (selectEl && !selectEl.dataset.select2Initialized) {
                $(selectEl).select2({
                    placeholder: '{{ __("admin.choose_sender") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_senders_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(selectEl).on('change', function() {
                    const opt = selectEl.options[selectEl.selectedIndex];
                    if (opt && opt.dataset.payload) {
                        try {
                            const payload = JSON.parse(opt.dataset.payload);
                            applyPayload(payload);
                            if (formWrap) formWrap.style.display = '';
                        } catch {}
                    } else {
                        if (formWrap) formWrap.style.display = 'none';
                    }
                    pushValidityToNext();
                });

                selectEl.dataset.select2Initialized = '1';
            }
        }

        // Initialize Select2 when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            initializeSelect2();
        });

        // Re-initialize Select2 when step 2 is shown
        document.addEventListener('stepChanged', e => {
            if (e && e.detail && (e.detail.currentStep === 2 || e.detail.currentStep === '2')) {
                setTimeout(() => {
                    initializeSelect2();
                }, 100);
            }
        });
    })();

    // Initialize Select2 for receiver select
    (function() {
        const receiverSelectEl = document.getElementById('receiver_select');
        const newSection = document.getElementById('new_receiver_section');
        const existingRadio = document.getElementById('existing_receiver');

        function initializeReceiverSelect2() {
            if (receiverSelectEl && !receiverSelectEl.dataset.select2Initialized) {
                $(receiverSelectEl).select2({
                    placeholder: '{{ __("admin.choose_receiver") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_receivers_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(receiverSelectEl).on('change', function() {
                    const id = receiverSelectEl.value;
                    if (id && existingRadio?.checked) {
                        if (newSection) newSection.style.display = "block";
                        if (typeof window.populateReceiverForm === 'function') {
                            window.populateReceiverForm(id);
                        }
                    }
                    if (typeof window.updateAddButtonState === 'function') {
                        window.updateAddButtonState();
                    }
                });

                receiverSelectEl.dataset.select2Initialized = '1';
            }
        }

        // Initialize Select2 when DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            initializeReceiverSelect2();
        });

        // Re-initialize Select2 when step 3 is shown
        document.addEventListener('stepChanged', e => {
            if (e && e.detail && (e.detail.currentStep === 3 || e.detail.currentStep === '3')) {
                setTimeout(() => {
                    initializeReceiverSelect2();
                }, 100);
            }
        });
    })();

    // Initialize Select2 for location selects (country, state, city) in receiver section
    (function() {
        const countrySelect = document.getElementById('country');
        const stateSelect = document.getElementById('state');
        const citySelect = document.getElementById('city');

        function initializeLocationSelect2() {
            // Initialize country select
            if (countrySelect && !countrySelect.dataset.select2Initialized) {
                
                $(countrySelect).select2({
                    placeholder: '{{ __("admin.select_country") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_countries_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(countrySelect).on('select2:select', function(e) {
                    // Set the underlying select value
                    countrySelect.value = e.params.data.id;
                    // Trigger native change event to maintain cascading logic
                    countrySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Handle clear events
                $(countrySelect).on('select2:clear', function() {
                    countrySelect.value = '';
                    countrySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                countrySelect.dataset.select2Initialized = '1';
            }

            // Initialize state select
            if (stateSelect && !stateSelect.dataset.select2Initialized) {
                
                $(stateSelect).select2({
                    placeholder: '{{ __("admin.select_state") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_states_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(stateSelect).on('select2:select', function(e) {
                    // Set the underlying select value
                    stateSelect.value = e.params.data.id;
                    // Trigger native change event to maintain cascading logic
                    stateSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Handle clear events
                $(stateSelect).on('select2:clear', function() {
                    stateSelect.value = '';
                    stateSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                stateSelect.dataset.select2Initialized = '1';
            }

            // Initialize city select
            if (citySelect && !citySelect.dataset.select2Initialized) {
                $(citySelect).select2({
                    placeholder: '{{ __("admin.select_city") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_cities_available") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Ensure the underlying select value is properly set
                $(citySelect).on('select2:select', function(e) {
                    // Get the selected value from Select2
                    const selectedValue = e.params.data.id;
                    // Set the underlying select value
                    citySelect.value = selectedValue;
                    // Trigger native change event
                    citySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Also handle clear events
                $(citySelect).on('select2:clear', function() {
                    citySelect.value = '';
                    citySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                citySelect.dataset.select2Initialized = '1';
            }
        }

        // Initialize Select2 when step 3 is shown (after original event listeners are bound)
        document.addEventListener('stepChanged', e => {
            if (e && e.detail && (e.detail.currentStep === 3 || e.detail.currentStep === '3')) {
                setTimeout(() => {
                    initializeLocationSelect2();
                }, 1000); // Wait longer to ensure original listeners are bound
            }
        });

        // Also initialize when the page loads if step 3 is already active
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                // Check if we're on step 3
                const step3 = document.getElementById('step-3');
                if (step3 && step3.style.display !== 'none') {
                    initializeLocationSelect2();
                }
            }, 1500);
        });

    })();

    // Initialize Select2 for sender location selects (user_country, user_state, user_city)
    (function() {
        const userCountrySelect = document.getElementById('user_country');
        const userStateSelect = document.getElementById('user_state');
        const userCitySelect = document.getElementById('user_city');

        function initializeSenderLocationSelect2() {
            // Initialize user country select
            if (userCountrySelect && !userCountrySelect.dataset.select2Initialized) {
                $(userCountrySelect).select2({
                    placeholder: '{{ __("admin.choose_country") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_countries_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(userCountrySelect).on('select2:select', function(e) {
                    // Set the underlying select value
                    userCountrySelect.value = e.params.data.id;
                    // Trigger native change event to maintain cascading logic
                    userCountrySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Handle clear events
                $(userCountrySelect).on('select2:clear', function() {
                    userCountrySelect.value = '';
                    userCountrySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                userCountrySelect.dataset.select2Initialized = '1';
            }

            // Initialize user state select
            if (userStateSelect && !userStateSelect.dataset.select2Initialized) {
                $(userStateSelect).select2({
                    placeholder: '{{ __("admin.choose_state") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_states_found") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Re-bind the change event after Select2 initialization
                $(userStateSelect).on('select2:select', function(e) {
                    // Set the underlying select value
                    userStateSelect.value = e.params.data.id;
                    // Trigger native change event to maintain cascading logic
                    userStateSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Handle clear events
                $(userStateSelect).on('select2:clear', function() {
                    userStateSelect.value = '';
                    userStateSelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                userStateSelect.dataset.select2Initialized = '1';
            }

            // Initialize user city select
            if (userCitySelect && !userCitySelect.dataset.select2Initialized) {
                $(userCitySelect).select2({
                    placeholder: '{{ __("admin.choose_city") }}',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 0,
                    closeOnSelect: true,
                    cache: true,
                    language: {
                        noResults: function() {
                            return '{{ __("admin.no_cities_available") }}';
                        },
                        searching: function() {
                            return '{{ __("admin.searching") }}';
                        }
                    }
                });

                // Ensure the underlying select value is properly set
                $(userCitySelect).on('select2:select', function(e) {
                    // Get the selected value from Select2
                    const selectedValue = e.params.data.id;
                    // Set the underlying select value
                    userCitySelect.value = selectedValue;
                    // Trigger native change event
                    userCitySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                // Also handle clear events
                $(userCitySelect).on('select2:clear', function() {
                    userCitySelect.value = '';
                    userCitySelect.dispatchEvent(new Event('change', { bubbles: true }));
                });

                userCitySelect.dataset.select2Initialized = '1';
            }
        }

        // Initialize Select2 when step 2 is shown (after original event listeners are bound)
        document.addEventListener('stepChanged', e => {
            if (e && e.detail && (e.detail.currentStep === 2 || e.detail.currentStep === '2')) {
                setTimeout(() => {
                    initializeSenderLocationSelect2();
                }, 1000); // Wait longer to ensure original listeners are bound
            }
        });

        // Also initialize when the page loads if step 2 is already active
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                // Check if we're on step 2
                const step2 = document.getElementById('step-2');
                if (step2 && step2.style.display !== 'none') {
                    initializeSenderLocationSelect2();
                }
            }, 1500);
        });

    })();

    (function() {
        let userDescriptions = [];
        let descriptionsLoaded = false;

        function loadUserDescriptions() {
            if (descriptionsLoaded) return;

            const url = window.API_ENDPOINTS?.userDescriptions || '/user/user-descriptions/getUserDescriptions';

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    userDescriptions = Array.isArray(data) ? data : (data.descriptions || []);
                    populateDescriptionsDropdown();
                    descriptionsLoaded = true;
                })
                .catch(error => {});
        }

        function populateDescriptionsDropdown() {
            const dropdown = document.getElementById('existing_descriptions');
            if (!dropdown) return;
            dropdown.innerHTML = '<option value="">{{ __("admin.select_description") }}</option>';
            userDescriptions.forEach(desc => {
                const option = document.createElement('option');
                option.value = desc.id;
                option.textContent = desc.description || desc.title || 'Untitled Description';
                option.dataset.description = desc.description || '';
                dropdown.appendChild(option);
            });
        }

        function setupDescriptionHandling() {
            const newRadio = document.getElementById('description_new');
            const existingRadio = document.getElementById('description_existing');
            const container = document.getElementById('existing_descriptions_container');
            const dropdown = document.getElementById('existing_descriptions');
            const textarea = document.getElementById('package_description');
            const descriptionIdInput = document.getElementById('description_id');
            const isNewInput = document.getElementById('is_new_description');

            if (!newRadio || !existingRadio || !container || !dropdown || !textarea || !descriptionIdInput || !isNewInput) return;

            function revalidate() {
                if (typeof window.syncNextBtnStep5 === 'function') window.syncNextBtnStep5();
            }

            function hardResetFields() {
                // wipe everything so nothing carries over between modes
                textarea.value = '';
                descriptionIdInput.value = '';
                dropdown.value = ''; // clear any previously chosen description
                revalidate();
            }

            function handleTypeChange() {
                hardResetFields();
                
                if (newRadio.checked) {
                    container.style.display = 'none';
                    textarea.disabled = false;
                    textarea.readOnly = false;
                    textarea.style.backgroundColor = '';
                    textarea.placeholder = (window.translations?.enter_package_description) || 'Enter package description';
                    isNewInput.value = '1';
                } else {
                    container.style.display = 'block';
                    textarea.disabled = true;
                    textarea.readOnly = true;
                    textarea.style.backgroundColor = '#f8f9fa';
                    textarea.placeholder = (window.translations?.select_description) || 'Select a description from the dropdown above';
                    isNewInput.value = '0';
                }

                revalidate();
            }

            function handleDropdownChange() {
                const opt = dropdown.options[dropdown.selectedIndex];
                if (opt && opt.value) {
                    textarea.disabled = true;
                    textarea.readOnly = true;
                    textarea.value = opt.dataset.description || '';
                    descriptionIdInput.value = opt.value;
                } else {
                    textarea.value = '';
                    descriptionIdInput.value = '';
                }
                revalidate();
            }

            // Initialize Select2 for description dropdown
            function initializeDescriptionSelect2() {
                if (dropdown && !dropdown.dataset.select2Initialized) {
                    $(dropdown).select2({
                        placeholder: '{{ __("admin.select_description") }}',
                        allowClear: true,
                        width: '100%',
                        language: {
                            noResults: function() {
                                return '{{ __("admin.no_descriptions_found") }}';
                            },
                            searching: function() {
                                return '{{ __("admin.searching") }}';
                            }
                        }
                    });

                    // Re-bind the change event after Select2 initialization
                    $(dropdown).on('change', function() {
                        handleDropdownChange();
                    });

                    dropdown.dataset.select2Initialized = '1';
                }
            }

            newRadio.addEventListener('change', handleTypeChange);
            existingRadio.addEventListener('change', handleTypeChange);
            // Remove the original change event listener since it's now handled by Select2
            // dropdown.addEventListener('change', handleDropdownChange);
            textarea.addEventListener('input', revalidate);
            handleTypeChange();

            // Initialize Select2 when descriptions are loaded
            const originalPopulateDescriptionsDropdown = populateDescriptionsDropdown;
            populateDescriptionsDropdown = function() {
                originalPopulateDescriptionsDropdown();
                // Initialize Select2 after populating the dropdown
                setTimeout(() => {
                    initializeDescriptionSelect2();
                }, 100);
            };
        }


        document.addEventListener('DOMContentLoaded', function() {
            setupDescriptionHandling();
            loadUserDescriptions();
        });

        document.addEventListener('stepChanged', function(e) {
            if (e && e.detail && e.detail.currentStep === 4) {
                loadUserDescriptions();
                // Initialize Select2 when step 4 is shown
                setTimeout(() => {
                    const dropdown = document.getElementById('existing_descriptions');
                    if (dropdown && !dropdown.dataset.select2Initialized) {
                        $(dropdown).select2({
                            placeholder: '{{ __("admin.select_description") }}',
                            allowClear: true,
                            width: '100%',
                            minimumInputLength: 0,
                            closeOnSelect: true,
                            cache: true,
                            language: {
                                noResults: function() {
                                    return '{{ __("admin.no_descriptions_found") }}';
                                },
                                searching: function() {
                                    return '{{ __("admin.searching") }}';
                                }
                            }
                        });

                        // Re-bind the change event after Select2 initialization
                        $(dropdown).on('change', function() {
                            const opt = dropdown.options[dropdown.selectedIndex];
                            if (opt && opt.value) {
                                const textarea = document.getElementById('package_description');
                                const descriptionIdInput = document.getElementById('description_id');
                                if (textarea) {
                                    textarea.disabled = true;
                                    textarea.readOnly = true;
                                    textarea.value = opt.dataset.description || '';
                                }
                                if (descriptionIdInput) {
                                    descriptionIdInput.value = opt.value;
                                }
                            } else {
                                const textarea = document.getElementById('package_description');
                                const descriptionIdInput = document.getElementById('description_id');
                                if (textarea) {
                                    textarea.value = '';
                                }
                                if (descriptionIdInput) {
                                    descriptionIdInput.value = '';
                                }
                            }
                            if (typeof window.syncNextBtnStep5 === 'function') {
                                window.syncNextBtnStep5();
                            }
                        });

                        dropdown.dataset.select2Initialized = '1';
                    }
                }, 200);
            }
        });
    })();

    var firstUpload = new FileUploadWithPreview('myFirstImage');
</script>


@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.showStep === 'function') window.showStep(6);
        const box = document.getElementById('step6-errors');
        if (box) {
            box.innerHTML = `<div class="alert alert-danger"><strong>{{ __('validation.there_were_errors') }}</strong><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>`;
            box.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
</script>
@endif

@if (session('force_step') === 7 || session('force_step') === 6)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.showStep === 'function') window.showStep(6);
    });
</script>
@endif
@endpush