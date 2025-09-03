@extends('dashboard.layouts.app')
@section('title', __('admin.edit'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('admin.users') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.edit_user_shipping_price') }}</span></li>
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
                            <h4>{{ __('admin.edit_user_shipping_price') }}</h4>
                        </div>
                        <div class="col-xl-6 col-md-6 col-sm-6 col-6 text-right mt-2">
                            <a href="{{ route('admin.user-shipping-prices.index', $user->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('admin.back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.user-shipping-prices.update', [$user->id, $userShippingPrice->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="company_id" id="company_id" value="{{ $userShippingPrice->company_id }}">
                                <input type="hidden" name="company_name_ar" id="company_name_ar" value="{{ $userShippingPrice->getTranslation('company_name', 'ar') }}">
                                <input type="hidden" name="company_name_en" id="company_name_en" value="{{ $userShippingPrice->getTranslation('company_name', 'en') }}">

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="company_select" class="text-dark">{{ __('admin.shipping_company') }}</label>
                                            <select name="company_select" id="company_select" class="form-control" required>
                                                <option value="">{{ __('admin.select_company') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3" id="local_price_container" style="display: none;">
                                            <label for="local_price" class="text-dark">{{ __('admin.local_price') }}</label>
                                            <input type="number" step="0.01" name="local_price" id="local_price"
                                                class="form-control" placeholder="{{ __('admin.local_price') }}"
                                                value="{{ old('local_price', $userShippingPrice->local_price) }}">
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 mb-3" id="international_price_container" style="display: none;">
                                            <label for="international_price" class="text-dark">{{ __('admin.international_price') }}</label>
                                            <input type="number" step="0.01" name="international_price" id="international_price"
                                                class="form-control" placeholder="{{ __('admin.international_price') }}"
                                                value="{{ old('international_price', $userShippingPrice->international_price) }}">
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
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const companySelect = document.getElementById('company_select');
        const localPriceContainer = document.getElementById('local_price_container');
        const internationalPriceContainer = document.getElementById('international_price_container');
        const companyIdField = document.getElementById('company_id');
        const companyNameArField = document.getElementById('company_name_ar');
        const companyNameEnField = document.getElementById('company_name_en');

        const currentLocale = '{{ app()->getLocale() }}';
        const currentCompanyId = '{{ $userShippingPrice->company_id }}';

        async function loadShippingCompanies() {
            try {
                const response = await fetch(
                    "https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies?page=0&pageSize=50&orderColumn=createdAt&orderDirection=desc", {
                        headers: {
                            accept: "*/*",
                            "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseData = await response.json();

                let companies = [];
                if (responseData.data && Array.isArray(responseData.data)) {
                    companies = responseData.data;
                } else if (responseData.results && Array.isArray(responseData.results)) {
                    companies = responseData.results;
                } else if (Array.isArray(responseData)) {
                    companies = responseData;
                } else if (responseData.companies && Array.isArray(responseData.companies)) {
                    companies = responseData.companies;
                } else if (responseData.items && Array.isArray(responseData.items)) {
                    companies = responseData.items;
                } else {
                    throw new Error('Invalid API response structure');
                }

                if (companies.length === 0) {
                    companySelect.innerHTML = '<option value="">{{ __("admin.no_companies_found") }}</option>';
                    return;
                }

                companySelect.innerHTML = '<option value="">{{ __("admin.select_company") }}</option>';

                companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;

                    let companyName = 'Unknown Company';
                    if (company.name && typeof company.name === 'object') {
                        companyName = currentLocale === 'ar' ? (company.name.ar || company.name.name_ar || '') : (company.name.en || company.name.name_en || '');
                    } else if (company.name_ar && company.name_en) {
                        companyName = currentLocale === 'ar' ? company.name_ar : company.name_en;
                    } else if (company.name) {
                        companyName = company.name;
                    }

                    option.textContent = companyName || 'Unknown Company';
                    option.dataset.company = JSON.stringify(company);

                    if (company.id.toString() === currentCompanyId.toString()) {
                        option.selected = true;
                    }

                    companySelect.appendChild(option);
                });

                if (currentCompanyId) {
                    const selectedOption = companySelect.querySelector(`option[value="${currentCompanyId}"]`);
                    if (selectedOption) {
                        const company = JSON.parse(selectedOption.dataset.company);
                        handleCompanySelection(company);
                    }
                }

            } catch (error) {
                companySelect.innerHTML = '<option value="">{{ __("admin.error_loading_companies") }}</option>';
            }
        }

        function handleCompanySelection(company) {
            companyIdField.value = company.id;

            let companyNameAr = '';
            let companyNameEn = '';

            if (company.name && typeof company.name === 'object') {
                companyNameAr = company.name.ar || company.name.name_ar || '';
                companyNameEn = company.name.en || company.name.name_en || '';
            } else if (company.name_ar && company.name_en) {
                companyNameAr = company.name_ar;
                companyNameEn = company.name_en;
            } else if (company.name) {
                companyNameAr = company.name;
                companyNameEn = company.name;
            }

            companyNameArField.value = companyNameAr;
            companyNameEnField.value = companyNameEn;

            let hasLocal = false;
            let hasInternational = false;

            if (company.shippingMethods && Array.isArray(company.shippingMethods)) {
                hasLocal = company.shippingMethods.includes('local');
                hasInternational = company.shippingMethods.includes('international');
            } else {
                hasLocal = (company.local_price !== null && company.local_price !== undefined) ||
                    (company.localPrice !== null && company.localPrice !== undefined) ||
                    (company.local !== null && company.local !== undefined);
                hasInternational = (company.international_price !== null && company.international_price !== undefined) ||
                    (company.internationalPrice !== null && company.internationalPrice !== undefined) ||
                    (company.international !== null && company.international !== undefined);
            }

            localPriceContainer.style.display = hasLocal ? 'block' : 'none';
            internationalPriceContainer.style.display = hasInternational ? 'block' : 'none';

            if (localPriceContainer.style.display === 'none') {
                document.getElementById('local_price').value = '';
            }
            if (internationalPriceContainer.style.display === 'none') {
                document.getElementById('international_price').value = '';
            }

            companySelect.disabled = true;
        }

        companySelect.addEventListener('change', function() {
            if (this.value) {
                const company = JSON.parse(this.selectedOptions[0].dataset.company);
                handleCompanySelection(company);
            } else {
                companyIdField.value = '';
                companyNameArField.value = '';
                companyNameEnField.value = '';
                localPriceContainer.style.display = 'none';
                internationalPriceContainer.style.display = 'none';
            }
        });

        loadShippingCompanies();
    });
</script>
@endpush
