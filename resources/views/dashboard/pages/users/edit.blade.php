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
                                            <label for="nameArInput" class="text-dark">{{ __('admin.name_ar') }}</label>
                                            <input id="nameArInput" type="text" name="name_ar"
                                                placeholder="{{ __('admin.name_ar') }}" class="form-control"
                                                value="{{ old('name_ar', $user->getTranslation('name', 'ar')) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="nameEnInput" class="text-dark">{{ __('admin.name_en') }}</label>
                                            <input id="nameEnInput" type="text" name="name_en"
                                                placeholder="{{ __('admin.name_en') }}" class="form-control"
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
                                            <label for="stateInput" class="text-dark">{{ __('admin.state') }}</label>
                                            <select id="stateInput" name="state_id" class="form-control">
                                                <option value="">{{ __('admin.choose_state') }}</option>
                                                @foreach ($states as $state)
                                                <option value="{{ $state['id'] }}"
                                                    data-state-name-ar="{{ $state['name']['ar'] }}"
                                                    data-state-name-en="{{ $state['name']['en'] }}"
                                                    data-country-id="{{ $state['countryId'] }}"
                                                    data-country-name-ar="{{ $state['country']['name']['ar'] }}"
                                                    data-country-name-en="{{ $state['country']['name']['en'] }}"
                                                    {{ old('state_id', $user->state_id) == $state['id'] ? 'selected' : '' }}>
                                                    {{ app()->getLocale() == 'ar' ? $state['name']['ar'] : $state['name']['en'] }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="cityInput" class="text-dark">{{ __('admin.city') }}</label>
                                            <select id="cityInput" name="city_id" class="form-control">
                                                <option value="">{{ __('admin.choose_city') }}</option>
                                                @if($user->state_id)
                                                @foreach ($cities as $city)
                                                <option value="{{ $city['id'] }}"
                                                    data-city-name-ar="{{ $city['name']['ar'] }}"
                                                    data-city-name-en="{{ $city['name']['en'] }}"
                                                    {{ old('city_id', $user->city_id) == $city['id'] ? 'selected' : '' }}>
                                                    {{ app()->getLocale() == 'ar' ? $city['name']['ar'] : $city['name']['en'] }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="addressInput" class="text-dark">{{ __('admin.address') }}</label>
                                            <textarea id="addressInput" name="address" rows="3"
                                                placeholder="{{ __('admin.enter_address') }}"
                                                class="form-control">{{ old('address', $user->address) }}</textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" name="state_name_ar" id="stateNameAr" value="{{ old('state_name_ar', $user->getTranslation('state_name', 'ar') ?? '') }}">
                                    <input type="hidden" name="state_name_en" id="stateNameEn" value="{{ old('state_name_en', $user->getTranslation('state_name', 'en') ?? '') }}">
                                    <input type="hidden" name="country_id" id="countryId" value="{{ old('country_id', $user->country_id ?? '') }}">
                                    <input type="hidden" name="country_name_ar" id="countryNameAr" value="{{ old('country_name_ar', $user->getTranslation('country_name', 'ar') ?? '') }}">
                                    <input type="hidden" name="country_name_en" id="countryNameEn" value="{{ old('country_name_en', $user->getTranslation('country_name', 'en') ?? '') }}">
                                    <input type="hidden" name="city_name_ar" id="cityNameAr" value="{{ old('city_name_ar', $user->getTranslation('city_name', 'ar') ?? '') }}">
                                    <input type="hidden" name="city_name_en" id="cityNameEn" value="{{ old('city_name_en', $user->getTranslation('city_name', 'en') ?? '') }}">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="passwordInput"
                                                class="text-dark">{{ __('admin.password') }}</label>
                                            <input id="passwordInput" type="password" name="password"
                                                placeholder="{{ __('admin.password') }}" class="form-control">
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
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.getElementById('stateInput');
        const citySelect = document.getElementById('cityInput');
        const currentLocale = '{{ app()->getLocale() }}';
        const currentCityId = '{{ $user->city_id }}';
        const stateNameAr = document.getElementById('stateNameAr');
        const stateNameEn = document.getElementById('stateNameEn');
        const countryId = document.getElementById('countryId');
        const countryNameAr = document.getElementById('countryNameAr');
        const countryNameEn = document.getElementById('countryNameEn');
        const cityNameAr = document.getElementById('cityNameAr');
        const cityNameEn = document.getElementById('cityNameEn');

        const translations = {
            chooseCity: '{{ __("admin.choose_city") }}',
            loadingCities: '{{ __("admin.loading_cities") }}',
            noCitiesFound: '{{ __("admin.no_cities_found") }}',
            errorLoadingCities: '{{ __("admin.error_loading_cities") }}'
        };

        if (stateSelect.value) {
            const selectedStateOption = stateSelect.options[stateSelect.selectedIndex];
            if (selectedStateOption.dataset.stateNameAr) {
                stateNameAr.value = selectedStateOption.dataset.stateNameAr;
                stateNameEn.value = selectedStateOption.dataset.stateNameEn;
                countryId.value = selectedStateOption.dataset.countryId;
                countryNameAr.value = selectedStateOption.dataset.countryNameAr;
                countryNameEn.value = selectedStateOption.dataset.countryNameEn;
            }
        }

        if (citySelect.value) {
            const selectedCityOption = citySelect.options[citySelect.selectedIndex];
            if (selectedCityOption.dataset.cityNameAr) {
                cityNameAr.value = selectedCityOption.dataset.cityNameAr;
                cityNameEn.value = selectedCityOption.dataset.cityNameEn;
            }
        }

        stateSelect.addEventListener('change', function() {
            const stateId = this.value;
            citySelect.innerHTML = `<option value="">${translations.chooseCity}</option>`;
            citySelect.disabled = true;

            cityNameAr.value = '';
            cityNameEn.value = '';

            if (stateId) {
                const selectedStateOption = this.options[this.selectedIndex];
                stateNameAr.value = selectedStateOption.dataset.stateNameAr || '';
                stateNameEn.value = selectedStateOption.dataset.stateNameEn || '';
                countryId.value = selectedStateOption.dataset.countryId || '';
                countryNameAr.value = selectedStateOption.dataset.countryNameAr || '';
                countryNameEn.value = selectedStateOption.dataset.countryNameEn || '';

                citySelect.innerHTML = `<option value="">${translations.loadingCities}</option>`;

                fetch(`{{ route('admin.users.citiesByState') }}?state_id=${stateId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(cities => {
                        citySelect.innerHTML = `<option value="">${translations.chooseCity}</option>`;
                        citySelect.disabled = false;

                        if (cities.length === 0) {
                            citySelect.innerHTML = `<option value="">${translations.noCitiesFound}</option>`;
                            citySelect.disabled = true;
                            return;
                        }

                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = currentLocale === 'ar' ? city.name.ar : city.name.en;
                            option.dataset.cityNameAr = city.name.ar || '';
                            option.dataset.cityNameEn = city.name.en || '';
                            if (city.id === currentCityId) {
                                option.selected = true;
                                cityNameAr.value = city.name.ar || '';
                                cityNameEn.value = city.name.en || '';
                            }
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        citySelect.innerHTML = `<option value="">${translations.errorLoadingCities}</option>`;
                        citySelect.disabled = true;
                    });
            } else {
                stateNameAr.value = '';
                stateNameEn.value = '';
                countryId.value = '';
                countryNameAr.value = '';
                countryNameEn.value = '';
                cityNameAr.value = '';
                cityNameEn.value = '';
            }
        });

        citySelect.addEventListener('change', function() {
            const cityId = this.value;

            if (cityId) {
                const selectedCityOption = this.options[this.selectedIndex];
                cityNameAr.value = selectedCityOption.dataset.cityNameAr || '';
                cityNameEn.value = selectedCityOption.dataset.cityNameEn || '';
            } else {
                cityNameAr.value = '';
                cityNameEn.value = '';
            }
        });
    });
</script>
@endpush