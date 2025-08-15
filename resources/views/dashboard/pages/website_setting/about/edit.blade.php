@extends('dashboard.layouts.app')
@section('title', __('admin.edit_about'))

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">{{ __('admin.about') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.edit_about') }}</span></li>
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
                            <h4>{{ __('admin.edit_about') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.about.update', $about->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="text-dark">{{ __('admin.title') }}</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="{{ __('admin.title') }}"
                                                value="{{ old('title', $about->title) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="subtitle" class="text-dark">{{ __('admin.subtitle') }}</label>
                                            <input type="text" name="subtitle" id="subtitle" class="form-control"
                                                placeholder="{{ __('admin.subtitle') }}"
                                                value="{{ old('subtitle', $about->subtitle) }}">
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
                                            @if($about->image)
                                            <img width="200px" height="200px"
                                                src="{{ displayImage($about->image) }}" alt="{{ $about->title }}">
                                            @else
                                            <div class="text-muted">لا توجد صورة</div>
                                            @endif
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

    <!-- About Items Section -->
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.edit_about_items') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        @foreach($aboutItems as $index => $item)
                        <div class="col-lg-6 col-12 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ __('admin.item') }} {{ $index + 1 }}</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.about.update-item', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group">
                                            <div class="mb-3">
                                                <label for="title_{{ $item->id }}" class="text-dark">{{ __('admin.title') }}</label>
                                                <input type="text" name="title" id="title_{{ $item->id }}" class="form-control"
                                                    placeholder="{{ __('admin.title') }}"
                                                    value="{{ old('title', $item->title) }}">
                                            </div>

                                            <div class="mb-3">
                                                <label for="description_{{ $item->id }}" class="text-dark">{{ __('admin.description') }}</label>
                                                <textarea name="description" id="description_{{ $item->id }}" class="form-control"
                                                    placeholder="{{ __('admin.description') }}" rows="3">{{ old('description', $item->description) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-end">
                                            <input type="submit" value="{{ __('admin.update') }}"
                                                class="btn btn-info btn-sm">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
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