@extends('dashboard.layouts.app')
@section('title', __('admin.settings'))
@push('css')
<link href="{{ asset('assets_' . assetLang()) }}/assets/css/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets_' . assetLang()) }}/plugins/file-upload/file-upload-with-preview.min.css" rel="stylesheet"
    type="text/css" />
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.settings') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.settings.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    @php
                                    $text = [
                                    'app_name_ar',
                                    'app_name_en',
                                    'address_ar',
                                    'address_en',
                                    'email',
                                    'phone',
                                    'whatsapp',
                                    'facebook',
                                    'twitter',
                                    'tiktok',
                                    'snapchat',
                                    'instagram',
                                    'play_store',
                                    'app_store',
                                    ];
                                    $text_area = ['footer_bio'];
                                    $imgs = ['logo', 'fav_icon'];
                                    @endphp

                                    <div class="row">
                                        @foreach ($settings as $setting)
                                        @if (in_array($setting->key, $text))
                                        <div class="col-md-6 mb-3">
                                            <label for="{{ $setting->key }}Input"
                                                class="text-dark">{{ __('admin.' . $setting->key) }}</label>
                                            <input id="{{ $setting->key }}Input" type="text" name="{{ $setting->key }}"
                                                placeholder="{{ __('admin.' . $setting->key) }}" class="form-control"
                                                value="{{ old($setting->key, $setting->value) }}">
                                        </div>
                                        @elseif(in_array($setting->key, $imgs))
                                        <div class="col-md-6 mb-3 custom-file-container"
                                            data-upload-id="{{ $setting->key }}">
                                            <label>{{ __('admin.' . $setting->key) }}<a href="javascript:void(0)"
                                                    class="custom-file-container__image-clear"
                                                    title="{{ __('admin.clear_image') }}"><span
                                                        style="background-color:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span></a></label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file"
                                                    class="custom-file-container__custom-file__custom-file-input"
                                                    name="{{ $setting->key }}">
                                                <span
                                                    class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                        <div class="col-md-6 mb-3 d-flex align-items-center">
                                            <img width="200px" height="200px" src="{{ displayImage($setting->value) }}"
                                                alt="{{ __('admin.' . $setting->key) }}">
                                        </div>
                                        @else
                                        <div class="col-md-6 mb-3">
                                            <label for="{{ $setting->key }}Input"
                                                class="text-dark">{{ __('admin.' . $setting->key) }}</label>
                                            <textarea id="{{ $setting->key }}Input" name="{{ $setting->key }}"
                                                placeholder="{{ __('admin.' . $setting->key) }}"
                                                class="form-control">{{ old($setting->key, $setting->value) }}</textarea>
                                        </div>
                                        @endif
                                        @endforeach
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
    var firstUpload = new FileUploadWithPreview('logo');
    var secondUpload = new FileUploadWithPreview('fav_icon');
</script>
@endpush