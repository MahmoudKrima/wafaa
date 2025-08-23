@extends('dashboard.layouts.app')
@section('title', __('admin.edit'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="basic" class="col-lg-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mb-4">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <h4 class="">{{ __('admin.edit') }}</h4>
                                <x-back-button route="{{ route('admin.services.index') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="row">
                            <div class="col-lg-12 col-12 mx-auto">
                                <form action="{{ route('admin.services.update', $service->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="title_ar" class="text-dark">{{ __('admin.title_ar') }}</label>
                                                <input type="text" name="title_ar" id="title_ar" class="form-control"
                                                    value="{{ old('title_ar', $service->getTranslation('title', 'ar')) }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="title_en" class="text-dark">{{ __('admin.title_en') }}</label>
                                                <input type="text" name="title_en" id="title_en" class="form-control"
                                                    value="{{ old('title_en', $service->getTranslation('title', 'en')) }}">
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="description_ar"
                                                    class="text-dark">{{ __('admin.description_ar') }}</label>
                                                <textarea name="description_ar" id="description_ar" class="form-control"
                                                    rows="3">{{ old('description_ar', $service->getTranslation('description', 'ar')) }}</textarea>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="description_en"
                                                    class="text-dark">{{ __('admin.description_en') }}</label>
                                                <textarea name="description_en" id="description_en" class="form-control"
                                                    rows="3">{{ old('description_en', $service->getTranslation('description', 'en')) }}</textarea>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <label for="statusInput" class="text-dark">{{ __('admin.status') }}</label>
                                                <select name="status" class="form-control" id="statusInput">
                                                    @foreach ($status as $stat)
                                                        <option @selected($stat->value == old('status', $service->status->value))
                                                            value="{{ $stat->value }}">
                                                            {{ $stat->lang() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
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
                                                <img width="200px" height="200px" src="{{ displayImage($service->image) }}"
                                                    alt="service">
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
    <script src="{{ asset('assets_' . assetLang()) }}/assets/js/scrollspyNav.js"></script>
    <script src="{{ asset('assets_' . assetLang()) }}/plugins/file-upload/file-upload-with-preview.min.js"></script>
    <script>
        var firstUpload = new FileUploadWithPreview('myFirstImage');
    </script>
@endpush