@extends('dashboard.layouts.app')
@section('title', __('admin.edit_slider'))

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('admin.sliders.index') }}">{{ __('admin.sliders') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.edit_slider') }}</span></li>
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
                            <h4>{{ __('admin.edit_slider') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="text-dark">{{ __('admin.title') }}</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="{{ __('admin.title') }}"
                                                value="{{ old('title', $slider->getTranslation('title', 'ar')) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="subtitle" class="text-dark">{{ __('admin.subtitle') }}</label>
                                            <input type="text" name="subtitle" id="subtitle" class="form-control"
                                                placeholder="{{ __('admin.subtitle') }}"
                                                value="{{ old('subtitle', $slider->getTranslation('subtitle', 'ar')) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="button_url"
                                                class="text-dark">{{ __('admin.button_url') }}</label>
                                            <input type="text" name="button_url" id="button_url"
                                                class="form-control" placeholder="{{ __('admin.button_url') }}"
                                                value="{{ old('button_url', $slider->getTranslation('button_url', 'ar')) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="button_text"
                                                class="text-dark">{{ __('admin.button_text') }}</label>
                                            <input type="text" name="button_text" id="button_text"
                                                class="form-control" placeholder="{{ __('admin.button_text') }}"
                                                value="{{ old('button_text', $slider->getTranslation('button_text', 'ar')) }}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="text-dark">{{ __('admin.status') }}</label>
                                            <select name="status" id="status" class="form-control">
                                                <option disabled selected>{{ __('admin.choose_status') }}</option>
                                                @foreach ($status as $stat)
                                                <option @selected($stat->value == old('status', $slider->status->value)) value="{{ $stat->value }}">
                                                    {{ $stat->lang() }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="description"
                                                class="text-dark">{{ __('admin.description') }}</label>
                                            <input type="text" name="description" id="description"
                                                class="form-control" placeholder="{{ __('admin.description') }}"
                                                value="{{ old('description', $slider->getTranslation('description', 'ar')) }}">
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
                                                src="{{ displayImage($slider->image) }}" alt="{{ $slider->title }}">
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