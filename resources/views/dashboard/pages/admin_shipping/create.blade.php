@extends('dashboard.layouts.app')
@section('title', __('admin.create_admin_shipping'))

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-center text-md-start">{{ __('admin.create_admin_shipping') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <div class="step-indicator d-flex flex-column flex-sm-row align-items-center">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <div class="step {{ $i === 1 ? 'active' : '' }} mb-2 mb-sm-0">
                                        <div class="step-number {{ $i===1 ? 'bg-primary' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0"
                                            style="width:35px;height:35px;font-size:14px;font-weight:bold;">{{ $i }}</div>
                                        <span class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small">
                                            @switch($i)
                                            @case(1) {{ __('admin.select_user') }} @break
                                            @case(2) {{ __('admin.select_company') }} @break
                                            @case(3) {{ __('admin.select_method') }} @break
                                            @case(4) {{ __('admin.user_information') }} @break
                                            @case(5) {{ __('admin.receivers') }} @break
                                            @case(6) {{ __('admin.shipping_details') }} @break
                                            @case(7) {{ __('admin.payment_details') }} @break
                                            @endswitch
                                        </span>
                                </div>
                                @if($i<7)
                                    <div class="step-line d-none d-sm-block mx-3" style="width:40px;height:2px;background:#e9ecef;">
                            </div>
                            <div class="step-line d-block d-sm-none my-2" style="width:2px;height:20px;background:#e9ecef;"></div>
                            @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-content" id="step-1">
                <h5 class="text-center mb-4">{{ __('admin.select_user_for_shipping') }}</h5>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="user_select" class="text-dark">{{ __('admin.select_user') }}</label>
                            <select id="user_select" name="user_id" class="form-control" required>
                                <option value="">{{ __('admin.choose_user') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button type="button" id="btn-next" class="btn btn-primary" disabled>
                            {{ __('admin.next') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="step-content" id="step-2" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.select_shipping_company') }}</h5>
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
            </div>

            <div class="step-content" id="step-3" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.choose_shipping_method') }}</h5>
                <p class="text-center text-muted mb-4">
                    {{ __('admin.select_shipping_method_for') }} <strong id="selected-company-name"></strong>
                </p>
                <div id="method-options" class="row"></div>
            </div>

            <div class="step-content" id="step-4" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.user_information') }}</h5>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_name" class="text-dark">{{ __('admin.full_name') }}</label>
                        <input id="user_name" type="text" class="form-control" value="" disabled>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_phone" class="text-dark">{{ __('admin.phone_number') }}</label>
                        <input id="user_phone" type="text" class="form-control" value="" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_additional_phone" class="text-dark">{{ __('admin.additional_phone') }}</label>
                        <input id="user_additional_phone" type="text" class="form-control" value="" disabled>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_email" class="text-dark">{{ __('admin.email') }}</label>
                        <input id="user_email" type="text" class="form-control" value="" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_state" class="text-dark">{{ __('admin.state') }}</label>
                        <input id="user_state" type="text" class="form-control" value="" disabled>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_city" class="text-dark">{{ __('admin.city') }}</label>
                        <input id="user_city" type="text" class="form-control" value="" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_country" class="text-dark">{{ __('admin.country') }}</label>
                        <input id="user_country" type="text" class="form-control" value="" disabled>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="user_address" class="text-dark">{{ __('admin.full_address') }}</label>
                        <textarea id="user_address" class="form-control" rows="3" disabled></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="user_postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                        <input id="user_postal_code" type="text" name="postal_code" class="form-control" value="">
                    </div>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ __('admin.user_info_note') }}
                </div>
            </div>

            <div class="step-content" id="step-5" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.receiver_information') }}</h5>
                <div class="row mb-4">
                    <div class="col-12">
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
                </div>

                <div id="existing_receiver_section">
                    <div class="row mb-3">
                        <div class="col-12">
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
                            <label for="address" class="text-dark">{{ __('admin.full_address') }}</label>
                            <textarea id="address" name="address" class="form-control" rows="3" placeholder="{{ __('admin.enter_full_address') }}" required></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="postal_code" class="text-dark">{{ __('admin.postal_code') }}</label>
                            <input id="postal_code" type="text" name="postal_code" class="form-control" placeholder="{{ __('admin.enter_postal_code') }}">
                        </div>
                    </div>
                </div>

                <div class="row mt-3" id="receiver-action-buttons">
                    <div class="col-12 text-center">
                        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                            <button type="button" id="add-receiver-btn" class="btn btn-success">
                                <i class="fas fa-plus"></i> {{ __('admin.add_receiver') }}
                            </button>
                        </div>
                    </div>
                </div>

                <div id="receivers-container" class="mt-4" style="display:none;"></div>
                <div id="receiver-success-msg" class="mt-3" style="display:none;"></div>
                <div id="receiver-error-msg" class="mt-3" style="display:none;"></div>
            </div>

            <div class="step-content" id="step-6" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.shipping_details') }}</h5>
                <form action="{{ route('admin.admin-shipping.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id_hidden">
                    <input type="hidden" name="shipping_company_id" id="shipping_company_id">
                    <input type="hidden" name="shipping_method" id="shipping_method">
                    <input type="hidden" name="selected_receivers" id="selected_receivers_hidden">
                    <input type="hidden" name="sender_name" id="sender_name_hidden">
                    <input type="hidden" name="sender_phone" id="sender_phone_hidden">
                    <input type="hidden" name="sender_email" id="sender_email_hidden">
                    <input type="hidden" name="sender_address" id="sender_address_hidden">
                    <input type="hidden" name="sender_city" id="sender_city_hidden">
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
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <label for="package_type" class="text-dark mb-2">{{ __('admin.package_type') }}</label>
                            <select id="package_type" name="package_type" class="form-control" required>
                                <option value="">{{ __('admin.select_package_type') }}</option>
                                <option value="boxes" {{ old('package_type')=='boxes' ? 'selected' : '' }}>{{ __('admin.boxes') }}</option>
                                <option value="documents" {{ old('package_type')=='documents' ? 'selected' : '' }}>{{ __('admin.documents') }}</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="package_number" class="text-dark mb-2">{{ __('admin.number') }}</label>
                            <input id="package_number" type="number" name="package_number" class="form-control" placeholder="1" min="1" value="{{ old('package_number', 1) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-4 mb-3">
                            <label for="length" class="text-dark">{{ __('admin.length_cm') }}</label>
                            <input id="length" type="number" name="length" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('length') }}" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="width" class="text-dark">{{ __('admin.width_cm') }}</label>
                            <input id="width" type="number" name="width" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('width') }}" required>
                        </div>
                        <div class="col-12 col-md-4 mb-3">
                            <label for="height" class="text-dark">{{ __('admin.height_cm') }}</label>
                            <input id="height" type="number" name="height" class="form-control" placeholder="0" min="0" step="0.1" value="{{ old('height') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="weight" class="text-dark">{{ __('admin.weight_kg') }}</label>
                            <input id="weight" type="number" name="weight" placeholder="{{ __('admin.weight_kg') }}" class="form-control" value="{{ old('weight') }}" step="0.1" min="0.1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="package_description" class="text-dark">{{ __('admin.package_description') }}</label>
                            <textarea id="package_description" name="package_description" class="form-control" rows="3" placeholder="{{ __('admin.enter_package_description') }}">{{ old('package_description') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="accept_terms" name="accept_terms" {{ old('accept_terms') ? 'checked' : '' }} required>
                                <label class="form-check-label" for="accept_terms">
                                    {{ __('admin.i_accept_terms') }}
                                    <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">{{ __('admin.terms_and_conditions') }}</a>
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

            <div class="step-content" id="step-7" style="display:none;">
                <h5 class="text-center mb-4">{{ __('admin.payment_details') }}</h5>
                <div class="row">
                    <div class="col-12">
                        <div class="payment-options-container"></div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 d-flex flex-column flex-sm-row justify-content-between gap-2">
                    <button type="button" class="btn btn-secondary" id="btn-prev" style="display:none;">
                        {{ app()->getLocale() === 'ar' ? '→' : '←' }} {{ __('admin.previous') }}
                    </button>
                    <button type="button" class="btn btn-primary" id="btn-next" disabled>
                        {{ __('admin.next') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const translations = {
        select_user: '{{ __("admin.select_user") }}',
        choose_user: '{{ __("admin.choose_user") }}',
        next: '{{ __("admin.next") }}',
        previous: '{{ __("admin.previous") }}',
        user_not_found: '{{ __("admin.user_not_found") }}',
        error_loading_user: '{{ __("admin.error_loading_user") }}'
    };

    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_select');
        const btnNext = document.getElementById('btn-next');

        userSelect.addEventListener('change', function() {
            if (this.value) {
                btnNext.disabled = false;
                btnNext.classList.remove('btn-secondary');
                btnNext.classList.add('btn-primary');
            } else {
                btnNext.disabled = true;
                btnNext.classList.remove('btn-primary');
                btnNext.classList.add('btn-secondary');
            }
        });

        btnNext.addEventListener('click', function() {
            if (userSelect.value) {
                // Redirect to create page with user_id
                window.location.href = `{{ route('admin.admin-shipping.create') }}?user_id=${userSelect.value}`;
            }
        });
    });
</script>
@endpush
