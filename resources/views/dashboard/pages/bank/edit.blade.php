@extends('dashboard.layouts.app')
@section('title', __('admin.edit_bank'))

@push('breadcrumb')
    <nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banks.index') }}">{{ __('admin.banks') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.edit_bank') }}</span></li>
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
                                <h4>{{ __('admin.edit_bank') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="row">
                            <div class="col-lg-12 col-12 mx-auto">
                                <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name_ar" class="text-dark">{{ __('admin.name_ar') }}</label>
                                                <input type="text" name="name_ar" id="name_ar" class="form-control"
                                                    placeholder="{{ __('admin.name_ar') }}"
                                                    value="{{ old('name_ar', $bank->getTranslation('name', 'ar')) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="name_en" class="text-dark">{{ __('admin.name_en') }}</label>
                                                <input type="text" name="name_en" id="name_en" class="form-control"
                                                    placeholder="{{ __('admin.name_en') }}"
                                                    value="{{ old('name_en', $bank->getTranslation('name', 'en')) }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="account_owner_ar"
                                                    class="text-dark">{{ __('admin.account_owner_ar') }}</label>
                                                <input type="text" name="account_owner_ar" id="account_owner_ar"
                                                    class="form-control" placeholder="{{ __('admin.account_owner_ar') }}"
                                                    value="{{ old('account_owner_ar', $bank->getTranslation('account_owner', 'ar')) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="account_owner_en"
                                                    class="text-dark">{{ __('admin.account_owner_en') }}</label>
                                                <input type="text" name="account_owner_en" id="account_owner_en"
                                                    class="form-control" placeholder="{{ __('admin.account_owner_en') }}"
                                                    value="{{ old('account_owner_en', $bank->getTranslation('account_owner', 'en')) }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="account_number"
                                                    class="text-dark">{{ __('admin.account_number') }}</label>
                                                <input type="text" name="account_number" id="account_number"
                                                    class="form-control" placeholder="{{ __('admin.account_number') }}"
                                                    value="{{ old('account_number', $bank->account_number) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="iban_number"
                                                    class="text-dark">{{ __('admin.iban_number') }}</label>
                                                <input type="text" name="iban_number" id="iban_number"
                                                    class="form-control" placeholder="{{ __('admin.iban_number') }}"
                                                    value="{{ old('iban_number', $bank->iban_number) }}">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="status" class="text-dark">{{ __('admin.status') }}</label>
                                                <select name="status" id="status" class="form-control">
                                                    <option disabled selected>{{ __('admin.choose_status') }}</option>
                                                    @foreach ($status as $stat)
                                                        <option @selected($stat->value == old('status', $bank->status->value)) value="{{ $stat->value }}">
                                                            {{ $stat->lang() }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-6 mb-3 custom-file-container" data-upload-id="myFirstImage">
                                                <label>{{ __('admin.image') }}<a href="javascript:void(0)"
                                                        class="custom-file-container__image-clear"
                                                        title="{{ __('admin.clear_image') }}"><span
                                                            style="background-color:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span></a></label>
                                                <label class="custom-file-container__custom-file">
                                                    <input type="file"
                                                        class="custom-file-container__custom-file__custom-file-input"
                                                        name="image">
                                                    <span
                                                        class="custom-file-container__custom-file__custom-file-control"></span>
                                                </label>
                                                <div class="custom-file-container__image-preview"></div>
                                            </div>

                                            <div class="col-md-6 mb-3 d-flex align-items-center">
                                                <img width="200px" height="200px"
                                                    src="{{ displayImage($bank->image) }}" alt="{{ $bank->name }}">
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
        var firstUpload = new FileUploadWithPreview('myFirstImage');
    </script>
@endpush
