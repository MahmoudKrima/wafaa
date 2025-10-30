@extends('user.layouts.app')
@section('title', __('admin.create'))
@push('css')
<style>
    .shipping-company-row {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6 !important;
    }

    .shipping-company-row:hover {
        background-color: #e9ecef;
    }

    .remove-shipping-company {
        margin-top: 25px;
    }
</style>
@endpush
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('user.senders.index') }}">{{ __('admin.senders') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.create') }}</span></li>
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
                            <h4>{{ __('admin.create') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('user.senders.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="nameArInput" class="text-dark">{{ __('admin.name') }}</label>
                                            <input id="nameArInput" type="text" name="name"
                                                placeholder="{{ __('admin.name') }}" class="form-control" 
                                                value="{{ old('name') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phoneInput" class="text-dark">{{ __('admin.phone') }}</label>
                                            <input id="phoneInput" type="text" placeholder="05XXXXXXXX" style="direction:ltr;"
                                                name="phone" placeholder="{{ __('admin.phone') }}"
                                                class="form-control" value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="additional_phoneInput" class="text-dark">{{ __('admin.additional_phone') }}</label>
                                            <input id="additional_phoneInput" type="text" placeholder="05XXXXXXXX" style="direction:ltr;"
                                                name="additional_phone" placeholder="{{ __('admin.additional_phone') }}"
                                                class="form-control" value="{{ old('additional_phone') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="addressInput" class="text-dark">{{ __('admin.address') }}</label>
                                            <input type="text" id="addressInput" name="address"
                                                placeholder="{{ __('admin.address') }}" class="form-control" value="{{ old('address') }}">
                                        </div>
                                    </div>
                                    
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <input type="submit" value="{{ __('admin.create') }}"
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
    $(document).ready(function() {
        let shippingCompanyCounter = 0;
        const shippingCompanies = @json($shippingCompanies['results'] ?? []);
        const locale = @json(app()->getLocale());
        const oldShippingCompanies = @json(old('shipping_companies', []));

        if (oldShippingCompanies && oldShippingCompanies.length > 0) {
            oldShippingCompanies.forEach(function(shippingCompany, index) {
                addShippingCompanyRow();
                const lastRow = $('.shipping-company-row').last();
                const companySelect = lastRow.find('.shipping-company-select');
                const citySelect = lastRow.find('.city-select');

                if (shippingCompany.company_id) {
                    companySelect.val(shippingCompany.company_id);

                    if (shippingCompany.city_id) {
                        citySelect.data('pending-city-value', shippingCompany.city_id);
                    }

                    // Add delay to ensure proper loading
                    setTimeout(function() {
                        companySelect.trigger('change');
                    }, index * 200);
                }
            });
        } else {
            addShippingCompanyRow();
        }

        $('#addShippingCompany').on('click', function(e) {
            e.preventDefault();
            addShippingCompanyRow();
        });

        function addShippingCompanyRow() {
            const rowId = 'shipping_company_' + shippingCompanyCounter;
            const selectedCompanyIds = [];
            $('.shipping-company-select').each(function() {
                const selectedValue = $(this).val();
                if (selectedValue) {
                    selectedCompanyIds.push(selectedValue);
                }
            });

            const availableCompanies = shippingCompanies.filter(company =>
                !selectedCompanyIds.includes(company.id)
            );

            const optionsHtml = availableCompanies.map(company => {
                const label = (company.name && typeof company.name === 'object') ?
                    (company.name[locale] || company.name.en || '') :
                    (company.name || '');
                return '<option value="' + company.id + '">' + label + '</option>';
            }).join('');

            const shippingCompanyLabel = @json(__('admin.shipping_company'));
            const selectShippingCompanyLabel = @json(__('admin.select_shipping_company'));
            const cityLabel = @json(__('admin.city'));
            const selectCityLabel = @json(__('admin.select_city'));

            const rowHtml =
                '<div class="shipping-company-row mb-3 p-3 border rounded" data-row-id="' + rowId + '">' +
                '<div class="row">' +
                '<div class="col-md-5">' +
                '<label class="text-dark">' + shippingCompanyLabel + '</label>' +
                '<select name="shipping_companies[' + shippingCompanyCounter + '][company_id]" class="form-control shipping-company-select" data-row="' + rowId + '">' +
                '<option value="">' + selectShippingCompanyLabel + '</option>' +
                optionsHtml +
                '</select>' +
                '</div>' +
                '<div class="col-md-5">' +
                '<label class="text-dark">' + cityLabel + '</label>' +
                '<select name="shipping_companies[' + shippingCompanyCounter + '][city_id]" class="form-control city-select" data-row="' + rowId + '" disabled>' +
                '<option value="">' + selectCityLabel + '</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-md-2 d-flex align-items-end">' +
                '<button type="button" class="btn btn-sm btn-danger remove-shipping-company" data-row="' + rowId + '">' +
                '<i class="fa fa-trash"></i>' +
                '</button>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#shippingCompaniesContainer').append(rowHtml);
            shippingCompanyCounter++;
            updateRemoveButtons();
        }

        $(document).on('click', '.remove-shipping-company', function() {
            $(this).closest('.shipping-company-row').remove();
            updateRemoveButtons();
            updateAllDropdowns();
        });

        $(document).on('change', '.shipping-company-select', function() {
            const rowId = $(this).data('row');
            const companyId = $(this).val();
            const citySelect = $('.city-select[data-row="' + rowId + '"]');
            const loadingText = @json(__('admin.loading'));
            const selectCityText = @json(__('admin.select_city'));
            const errorText = @json(__('admin.error_loading_cities'));

            if (companyId) {
                updateOtherDropdowns(rowId, companyId);
                citySelect.prop('disabled', true).html('<option value="">' + loadingText + '...</option>');
                const baseUrl = @json(route('user.senders.getCitiesByCompanyAndCountry', ['shippingCompanyId' => '__COMPANY_ID__']));
                const url = baseUrl.replace('__COMPANY_ID__', encodeURIComponent(companyId));

                const currentCityValue = citySelect.data('pending-city-value');

                $.ajax({
                    url: url,
                    method: 'GET',
                    cache: true,
                    success: function(response) {
                        let options = '<option value="">' + selectCityText + '</option>';
                        const results = Array.isArray(response?.results) ? response.results : [];
                        results.forEach(function(city) {
                            const label = (city.name && typeof city.name === 'object') ?
                                (city.name[locale] || city.name.en || '') :
                                (city.name || '');
                            options += '<option value="' + city.id + '">' + label + '</option>';
                        });
                        citySelect.html(options).prop('disabled', false);

                        // Restore the pending city value with a small delay to ensure DOM is updated
                        if (currentCityValue) {
                            setTimeout(function() {
                                // Try to set the value
                                citySelect.val(currentCityValue);

                                // Check if the value was actually set
                                if (citySelect.val() !== currentCityValue) {
                                    // Try to find by exact match first
                                    const exactMatch = citySelect.find('option[value="' + currentCityValue + '"]');
                                    if (exactMatch.length > 0) {
                                        citySelect.val(currentCityValue);
                                    } else {
                                        // Try to find by partial match or similar ID
                                        const allOptions = citySelect.find('option');
                                        let found = false;
                                        allOptions.each(function() {
                                            const optionValue = $(this).val();
                                            if (optionValue && optionValue.toString().includes(currentCityValue.toString().substring(0, 8))) {
                                                citySelect.val(optionValue);
                                                found = true;
                                                return false; // break the loop
                                            }
                                        });

                                        if (!found) {
                                            console.log('No matching city found for ID:', currentCityValue);
                                        }
                                    }
                                }

                                citySelect.removeData('pending-city-value');
                            }, 100);
                        }
                    },
                    error: function(xhr, status, error) {
                        citySelect.html('<option value="">' + errorText + '</option>').prop('disabled', false);
                    }
                });
            } else {
                citySelect.html('<option value="">' + selectCityText + '</option>').prop('disabled', true);
                updateOtherDropdowns(rowId, null);
            }
        });

        function updateAllDropdowns() {
            const selectedCompanyIds = [];
            const selectShippingCompanyText = @json(__('admin.select_shipping_company'));
            const selectCityText = @json(__('admin.select_city'));

            // Collect all currently selected company IDs
            $('.shipping-company-select').each(function() {
                const selectedValue = $(this).val();
                if (selectedValue) {
                    selectedCompanyIds.push(selectedValue);
                }
            });

            $('.shipping-company-select').each(function() {
                const currentValue = $(this).val();
                const $select = $(this);
                const rowId = $select.data('row');
                const wasSelected = currentValue;

                $select.empty();
                $select.append('<option value="">' + selectShippingCompanyText + '</option>');

                shippingCompanies.forEach(company => {
                    if (!selectedCompanyIds.includes(company.id) || company.id === wasSelected) {
                        const label = (company.name && typeof company.name === 'object') ?
                            (company.name[locale] || company.name.en || '') :
                            (company.name || '');
                        $select.append('<option value="' + company.id + '">' + label + '</option>');
                    }
                });

                if (wasSelected) {
                    $select.val(wasSelected);
                }
            });
        }

        function updateOtherDropdowns(currentRowId, selectedCompanyId) {
            if (!selectedCompanyId) return;

            const selectedCompanyIds = [];
            const selectShippingCompanyText = @json(__('admin.select_shipping_company'));
            const selectCityText = @json(__('admin.select_city'));

            $('.shipping-company-select').each(function() {
                const selectedValue = $(this).val();
                if (selectedValue) {
                    selectedCompanyIds.push(selectedValue);
                }
            });

            $('.shipping-company-select').each(function() {
                const rowId = $(this).data('row');
                if (rowId !== currentRowId) {
                    const currentValue = $(this).val();
                    const $select = $(this);
                    if (currentValue === selectedCompanyId) {
                        $select.val('');
                        const citySelect = $('.city-select[data-row="' + rowId + '"]');
                        citySelect.html('<option value="">' + selectCityText + '</option>').prop('disabled', true);
                    }

                    $select.empty();
                    $select.append('<option value="">' + selectShippingCompanyText + '</option>');

                    shippingCompanies.forEach(company => {
                        if (!selectedCompanyIds.includes(company.id) || company.id === currentValue) {
                            const label = (company.name && typeof company.name === 'object') ?
                                (company.name[locale] || company.name.en || '') :
                                (company.name || '');
                            $select.append('<option value="' + company.id + '">' + label + '</option>');
                        }
                    });

                    if (currentValue && currentValue !== selectedCompanyId) {
                        $select.val(currentValue);
                    }
                }
            });
        }

        function updateRemoveButtons() {
            const rows = $('.shipping-company-row');
            if (rows.length <= 1) {
                $('.remove-shipping-company').hide();
            } else {
                $('.remove-shipping-company').show();
            }
        }
    });
</script>
@endpush