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
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.edit_user_shipping_price') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.user-shipping-prices.update', [$user->id, $userShippingPrice->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- Hidden fields for company data -->
                                <input type="hidden" name="company_id" id="company_id" value="{{ $userShippingPrice->company_id }}">
                                <input type="hidden" name="company_name_ar" id="company_name_ar" value="{{ $userShippingPrice->company_name_ar }}">
                                <input type="hidden" name="company_name_en" id="company_name_en" value="{{ $userShippingPrice->company_name_en }}">
                                
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="company_select" class="text-dark">{{ __('admin.shipping_company') }}</label>
                                            <select name="company_select" id="company_select" class="form-control" required>
                                                <option value="">{{ __('admin.select_company') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="text-dark">{{ __('admin.country') }}</label>
                                            <div class="form-control-plaintext" id="country_display"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3" id="local_price_container" style="display: none;">
                                            <label for="local_price" class="text-dark">{{ __('admin.local_price') }}</label>
                                            <input type="number" step="0.01" name="local_price" id="local_price"
                                                class="form-control" placeholder="{{ __('admin.local_price') }}"
                                                value="{{ old('local_price', $userShippingPrice->local_price) }}">
                                        </div>
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
    const countryDisplay = document.getElementById('country_display');
    const localPriceContainer = document.getElementById('local_price_container');
    const internationalPriceContainer = document.getElementById('international_price_container');
    const companyIdField = document.getElementById('company_id');
    const companyNameArField = document.getElementById('company_name_ar');
    const companyNameEnField = document.getElementById('company_name_en');
    
    const currentLocale = '{{ app()->getLocale() }}';
    const currentCompanyId = '{{ $userShippingPrice->company_id }}';
    
    // Load shipping companies
    async function loadShippingCompanies() {
        try {
            const response = await fetch(
                "https://ghaya-express-staging-af597af07557.herokuapp.com/api/shipping-companies?page=0&pageSize=50&orderColumn=createdAt&orderDirection=desc",
                {
                    headers: {
                        accept: "*/*",
                        "x-api-key": "xwqn5mb5mpgf5u3vpro09i8pmw9fhkuu",
                    },
                }
            );
            
            if (!response.ok) {
                throw new Error('Failed to fetch companies');
            }
            
            const data = await response.json();
            
            // Clear existing options
            companySelect.innerHTML = '<option value="">{{ __("admin.select_company") }}</option>';
            
            // Add company options
            data.data.forEach(company => {
                const option = document.createElement('option');
                option.value = company.id;
                option.textContent = currentLocale === 'ar' ? company.name_ar : company.name_en;
                option.dataset.company = JSON.stringify(company);
                
                // Select current company
                if (company.id === currentCompanyId) {
                    option.selected = true;
                }
                
                companySelect.appendChild(option);
            });
            
            // Handle initial company selection
            if (currentCompanyId) {
                const selectedOption = companySelect.querySelector(`option[value="${currentCompanyId}"]`);
                if (selectedOption) {
                    const company = JSON.parse(selectedOption.dataset.company);
                    handleCompanySelection(company);
                }
            }
            
        } catch (error) {
            console.error('Error loading companies:', error);
        }
    }
    
    // Handle company selection
    function handleCompanySelection(company) {
        // Set hidden fields
        companyIdField.value = company.id;
        companyNameArField.value = company.name_ar || '';
        companyNameEnField.value = company.name_en || '';
        
        // Display country
        if (company.country) {
            const countryName = currentLocale === 'ar' ? company.country.name_ar : company.country.name_en;
            countryDisplay.textContent = countryName || company.country.name_en || 'N/A';
        } else {
            countryDisplay.textContent = 'N/A';
        }
        
        // Show/hide price inputs based on company capabilities
        const hasLocal = company.local_price !== null && company.local_price !== undefined;
        const hasInternational = company.international_price !== null && company.international_price !== undefined;
        
        localPriceContainer.style.display = hasLocal ? 'block' : 'none';
        internationalPriceContainer.style.display = hasInternational ? 'block' : 'none';
        
        // Clear price values when switching companies (only if they don't support that type)
        if (localPriceContainer.style.display === 'none') {
            document.getElementById('local_price').value = '';
        }
        if (internationalPriceContainer.style.display === 'none') {
            document.getElementById('international_price').value = '';
        }
    }
    
    // Event listener for company selection
    companySelect.addEventListener('change', function() {
        if (this.value) {
            const company = JSON.parse(this.selectedOptions[0].dataset.company);
            handleCompanySelection(company);
        } else {
            // Reset form when no company is selected
            companyIdField.value = '';
            companyNameArField.value = '';
            companyNameEnField.value = '';
            countryDisplay.textContent = '';
            localPriceContainer.style.display = 'none';
            internationalPriceContainer.style.display = 'none';
        }
    });
    
    // Load companies on page load
    loadShippingCompanies();
});
</script>
@endpush
