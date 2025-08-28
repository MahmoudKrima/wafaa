@extends('user.layouts.app')
@section('title', __('admin.create_shipping'))
@push('css')
<link rel="stylesheet" href="{{ asset('user/shipping-styles.css') }}">
@endpush
@section('content')

<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-12 layout-spacing">
            <div class="statbox widget box box-shadow">

                <div class="widget-content widget-content-area ">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center my_indicators">


                                <div class="step-indicator d-flex flex-column flex-sm-row align-items-center step_theme">


                                    <!-- Navigation Buttons -->
                                    <div class="prev_next_btn mb-2 mb-sm-0">
                                        <div class="col-12 d-flex flex-column flex-sm-row justify-content-between gap-2">
                                            <button type="button" class="btn btn-secondary" id="btn-prev" style="display: none;">
                                                {{ app()->getLocale() === 'ar' ? '→' : '←' }} {{ __('admin.previous') }}
                                            </button>
                                        </div>
                                    </div>



                                    <div class="step active mb-2 mb-sm-0">
                                        <div class="step-number bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            1
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.select_company') }}</span>
                                    </div>

                                    <!-- Step Line 1 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 2 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            2
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.select_method') }}</span>
                                    </div>

                                    <!-- Step Line 2 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 3 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            3
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.user_information') }}</span>
                                    </div>

                                    <!-- Step Line 3 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 4 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            4
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.receivers') }}</span>
                                    </div>

                                    <!-- Step Line 4 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 5 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            5
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.shipping_details') }}</span>
                                    </div>

                                    <!-- Step Line 5 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 6 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            6
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.payment_details') }}</span>
                                    </div>

                                    <!-- Step Line 6 -->
                                    <div class="step-line d-none d-sm-block mx-3"
                                         style="width: 40px; height: 2px; background: #e9ecef;"></div>
                                    <div class="step-line d-block d-sm-none my-2"
                                         style="width: 2px; height: 20px; background: #e9ecef;"></div>

                                    <!-- Step 7 -->
                                    <div class="step mb-2 mb-sm-0">
                                        <div class="step-number bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0">
                                            7
                                        </div>
                                        <span
                                            class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small step_text">{{ __('admin.summary') }}</span>
                                    </div>


                                    <!-- Navigation Buttons -->
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

                    <!-- Step 1: Select Shipping Company -->
                    <div class="step-content" id="step-1">
                        <h5 class="text-center mb-4">{{ __('admin.choose_shipping_company') }}</h5>

                        <div id="companies-container">
                            <div class="text-center">
                                <div class="mb-3">
                                    <img src="{{ asset('front/assets/img/preload.png') }}" alt="Logo" class="img-fluid"
                                         style="max-height: 60px; max-width: 150px;">
                                </div>
                                <div class="spinner-border text-primary" role="status"
                                     style="width: 2rem; height: 2rem;">
                                    <span class="visually-hidden"></span>
                                </div>
                                <p class="mt-2">{{ __('admin.loading_companies') }}</p>
                            </div>
                        </div>

                        <!-- Company Selected Summary -->
                        <div id="company-selected-summary" class="mt-4" style="display: none;"></div>

                        <!-- Company Pricing Display -->
                        <div id="company-pricing-display" class="mt-4" style="display: none;"></div>
                    </div>

                    <!-- Step 2: Select Shipping Method -->
                    <div class="step-content" id="step-2" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.choose_shipping_method') }}</h5>
                        <p class="text-center text-muted mb-4">{{ __('admin.select_shipping_method_for') }} <strong
                                id="selected-company-name"></strong></p>

                        <div id="method-options" class="row">
                        </div>
                    </div>

                    <!-- Step 3: User Information -->
                    <div class="step-content" id="step-3" style="display: none;"
                         data-app-locale="{{ app()->getLocale() }}">

                        <h5 class="text-center mb-4">{{ __('admin.user_information') }}</h5>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_name" class="text-dark">{{ __('admin.full_name') }}</label>
                                <input id="user_name" type="text" name="user_name" class="form-control"
                                       value="{{ auth()->user()->name ?? '' }}" disabled>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                                <input id="user_phone" type="text" name="phone" class="form-control"
                                       value="{{ auth()->user()->phone ?? '' }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_additional_phone"
                                       class="text-dark">{{ __('admin.additional_phone') }}</label>
                                <input id="user_additional_phone" type="text" name="additional_phone"
                                       class="form-control" value="{{ auth()->user()->additional_phone ?? '' }}" disabled>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_email" class="text-dark">{{ __('admin.email') }}</label>
                                <input id="user_email" type="text" name="email" class="form-control"
                                       value="{{ auth()->user()->email ?? '' }}" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_state" class="text-dark">{{ __('admin.state') }}</label>
                                <input id="user_state" type="text" name="state" class="form-control"
                                       value="{{ auth()->user()->state_name ?? '' }}" disabled>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_city" class="text-dark">{{ __('admin.city') }}</label>
                                <input id="user_city" type="text" name="city" class="form-control"
                                       value="{{ auth()->user()->city_name ?? '' }}" disabled>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_country" class="text-dark">{{ __('admin.country') }}</label>
                                <input id="user_country" type="text" name="country" class="form-control"
                                       value="{{ auth()->user()->country_name ?? '' }}" disabled>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="user_postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                                <input id="user_postal_code" type="text" name="postal_code" class="form-control"
                                       value="{{ auth()->user()->postal_code ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="user_address" class="text-dark">{{ __('admin.full_address') }}</label>
                                <textarea id="user_address" name="user_address" class="form-control" rows="3"
                                          disabled>{{ auth()->user()->address ?? '' }}</textarea>
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
                                    <input class="form-check-input" type="radio" name="receiver_type"
                                           id="existing_receiver" value="existing">
                                    <label class="form-check-label" for="existing_receiver">
                                        {{ __('admin.existing_receiver') }}
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="receiver_type" id="new_receiver"
                                           value="new">
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
                                    <label for="receiver_select"
                                           class="text-dark">{{ __('admin.select_receiver') }}</label>
                                    <select id="receiver_select" name="receiver_id" class="form-control">
                                        <option value="">{{ __('admin.choose_receiver') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- New Receiver Section -->
                        <div id="new_receiver_section" style="display: none;">
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="name" class="text-dark">{{ __('admin.full_name') }}</label>
                                    <input id="name" type="text" name="name" class="form-control"
                                           placeholder="{{ __('admin.enter_full_name') }}" required>
                                </div>

                                <div class="col-12 col-md-6 mb-3">
                                    <label for="phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                                    <input id="phone" type="input" name="phone" class="form-control"
                                           placeholder="{{ __('admin.enter_phone_number') }}" pattern="05[0-9]{8}"
                                           title="Phone must start with 05 followed by 8 digits (e.g., 0512345678)"
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="additional_phone"
                                           class="text-dark">{{ __('admin.additional_phone') }}</label>
                                    <input id="additional_phone" type="input" name="additional_phone" class="form-control"
                                           placeholder="{{ __('admin.enter_additional_phone') }}" pattern="05[0-9]{8}"
                                           title="Phone must start with 05 followed by 8 digits (e.g., 0512345678)">
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label for="email" class="text-dark">{{ __('admin.email') }}</label>
                                    <input id="email" type="email" name="email" class="form-control"
                                           placeholder="{{ __('admin.enter_email') }}"
                                           title="Please enter a valid email address">
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
                                    <input id="postal_code" type="text" name="postal_code" class="form-control"
                                           placeholder="{{ __('admin.enter_postal_code') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="address" class="text-dark">{{ __('admin.full_address') }}</label>
                                    <textarea id="address" name="address" class="form-control" rows="3"
                                              placeholder="{{ __('admin.enter_full_address') }}" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons for Adding Receivers -->
                        <div class="row mt-3" id="receiver-action-buttons">
                            <div class="col-12 text-center">
                                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" id="add-receiver-btn" class="btn btn-success">
                                        <i class="fas fa-plus"></i> {{ __('admin.add_receiver') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="receivers-container" class="mt-4" style="display: none;"></div>
                        <div id="receiver-success-msg" class="mt-3" style="display: none;"></div>
                        <div id="receiver-error-msg" class="mt-3" style="display: none;"></div>
                    </div>

                    <div class="step-content" id="step-5" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.shipping_details') }}</h5>

                        <form action="{{ route('user.shippings.store') }}" method="POST" enctype="multipart/form-data"
                              onsubmit="return validateForm()">
                            @csrf
                            <input type="hidden" name="shipping_company_id" id="shipping_company_id">
                            <input type="hidden" name="shipping_method" id="shipping_method">
                            <input type="hidden" name="selected_receivers" id="selected_receivers_hidden">

                            <!-- Package Type Selection -->
                            <div class="row mb-4">
                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    <label for="package_type"
                                           class="text-dark mb-2">{{ __('admin.package_type') }}</label>
                                    <select id="package_type" name="package_type" class="form-control" required>
                                        <option value="">{{ __('admin.select_package_type') }}</option>
                                        <option value="boxes">{{ __('admin.boxes') }}</option>
                                        <option value="documents">{{ __('admin.documents') }}</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="package_number" class="text-dark mb-2">{{ __('admin.number') }}</label>
                                    <input id="package_number" type="number" name="package_number" class="form-control"
                                           placeholder="1" min="1" value="1" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label for="weight" class="text-dark">{{ __('admin.weight_kg') }}</label>
                                    <input id="weight" type="number" name="weight"
                                           placeholder="{{ __('admin.weight_kg') }}" class="form-control"
                                           value="{{ old('weight') }}" step="0.1" min="0.1" required>
                                </div>

                            </div>

                            <!-- Package Details -->
                            <div class="row">
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="length" class="text-dark">{{ __('admin.length_cm') }}</label>
                                    <input id="length" type="number" name="length" class="form-control" placeholder="0"
                                           min="0" step="0.1" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="width" class="text-dark">{{ __('admin.width_cm') }}</label>
                                    <input id="width" type="number" name="width" class="form-control" placeholder="0"
                                           min="0" step="0.1" required>
                                </div>
                                <div class="col-12 col-md-4 mb-3">
                                    <label for="height" class="text-dark">{{ __('admin.height_cm') }}</label>
                                    <input id="height" type="number" name="height" class="form-control" placeholder="0"
                                           min="0" step="0.1" required>
                                </div>
                            </div>


                            <!-- Package Description -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="package_description"
                                           class="text-dark">{{ __('admin.package_description') }}</label>
                                    <textarea id="package_description" name="package_description" class="form-control"
                                              rows="3" placeholder="{{ __('admin.enter_package_description') }}"></textarea>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="accept_terms"
                                               name="accept_terms" required>
                                        <label class="form-check-label" for="accept_terms">
                                            {{ __('admin.i_accept_terms') }}
                                            <a href="#" class="text-primary" data-bs-toggle="modal"
                                               data-bs-target="#termsModal">
                                                {{ __('admin.terms_and_conditions') }}
                                            </a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipment Image -->
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="shipmentImage" class="form-label">{{ __('admin.shipment_image') }}</label>
                                    <input type="file"
                                           class="form-control"
                                           id="shipmentImage"
                                           name="shipment_image"
                                           accept="image/*"
                                           data-max-file-size="2M"
                                           data-max-files="1">
                                    <div class="mt-2">
                                        <small class="text-muted">{{ __('admin.upload_shipment_image_help') }}</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Step 6: Payment Details -->
                    <div class="step-content" id="step-6" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.payment_details') }}</h5>

                        <div class="row">
                            <div class="col-12 col-md-6 mx-auto">
                                <div class="payment-options-container">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="cash_on_delivery"
                                               name="cash_on_delivery">
                                        <label class="form-check-label" for="cash_on_delivery">
                                            <i class="fas fa-money-bill-wave text-success"></i>
                                            {{ __('admin.cash_on_delivery') }}
                                        </label>
                                    </div>

                                    <div id="cod_details" class="cod-details" style="display: none;">
                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle"></i> {{ __('admin.cod_information') }}
                                            </h6>
                                            <p class="mb-2">{{ __('admin.cod_description') }}</p>
                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-2 mb-md-0">
                                                    <strong>{{ __('admin.cod_price') }}:</strong>
                                                    <span id="cod_price_display" class="text-primary"></span>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <strong>{{ __('admin.total_with_cod') }}:</strong>
                                                    <span id="total_with_cod_display" class="text-success"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 7: Final Summary -->
                    <div class="step-content" id="step-7" style="display: none;">
                        <h5 class="text-center mb-4">{{ __('admin.shipment_summary') }}</h5>

                        <div id="final-shipment-summary">
                            <!-- Summary content will be populated by JavaScript -->
                        </div>

                        <!-- Submit Button -->
                        <form action="{{ route('user.shippings.store') }}" method="POST" enctype="multipart/form-data"
                              onsubmit="return validateForm()">
                            @csrf
                            <input type="hidden" name="shipping_company_id" id="shipping_company_id">
                            <input type="hidden" name="shipping_method" id="shipping_method">
                            <input type="hidden" name="selected_receivers" id="selected_receivers_hidden">

                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <input type="submit" value="{{ __('admin.create_shipping') }}"
                                           class="btn btn-success btn-lg">
                                </div>
                            </div>
                        </form>
                    </div>


                </div>


            </div>
        </div>
    </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">{{ __('admin.terms_and_conditions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="terms-content">
                    <h6>{{ __('admin.shipping_terms_title') }}</h6>
                    <p>{{ __('admin.shipping_terms_content') }}</p>

                    <h6>{{ __('admin.package_terms_title') }}</h6>
                    <p>{{ __('admin.package_terms_content') }}</p>

                    <h6>{{ __('admin.liability_terms_title') }}</h6>
                    <p>{{ __('admin.liability_terms_content') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>

</style>
@endpush

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
    };

    const API_ENDPOINTS = {
        shippingCompanies: '{{ route("user.shippings.companies") }}'
    };

    window.translations = translations;
    window.API_ENDPOINTS = API_ENDPOINTS;
</script>
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
    window.APP_LOCALE = "{{ app()->getLocale() }}";
</script>

@endpush
