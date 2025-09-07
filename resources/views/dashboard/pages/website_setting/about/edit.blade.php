@extends('dashboard.layouts.app')
@section('title', __('admin.edit_about'))

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.about.index') }}">{{ __('admin.about') }}</a></li>
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

                            <form action="{{ route('admin.about.update', $about->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="title" class="text-dark">{{ __('admin.title') }}</label>
                                            <input
                                                type="text"
                                                name="title"
                                                id="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                placeholder="{{ __('admin.title') }}"
                                                value="{{ old('title', $about->title) }}">
                                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="subtitle" class="text-dark">{{ __('admin.subtitle') }}</label>
                                            <input
                                                type="text"
                                                name="subtitle"
                                                id="subtitle"
                                                class="form-control @error('subtitle') is-invalid @enderror"
                                                placeholder="{{ __('admin.subtitle') }}"
                                                value="{{ old('subtitle', $about->subtitle) }}">
                                            @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3 custom-file-container" data-upload-id="myFirstImage">
                                            <label>
                                                {{ __('admin.image') }}
                                                <a href="javascript:void(0)" class="custom-file-container__image-clear" title="{{ __('admin.clear_image') }}">
                                                    <span style="background-color:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span>
                                                </a>
                                            </label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file" class="custom-file-container__custom-file__custom-file-input @error('image') is-invalid @enderror" name="image">
                                                <span class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                            @error('image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6 mb-3 d-flex align-items-center">
                                            @if($about->image)
                                            <img width="200" height="200" src="{{ displayImage($about->image) }}" alt="{{ $about->title }}">
                                            @else
                                            <div class="text-muted">لا توجد صورة</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 col-12 mb-4">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h5 class="mb-3">{{ __('admin.edit_about_items') }}</h5>

                                                @foreach($aboutItems as $item)
                                                <div class="form-group border rounded p-3 mb-3">
                                                    <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">

                                                    <div class="mb-3">
                                                        <label for="title_{{ $item->id }}" class="text-dark">{{ __('admin.title') }}</label>
                                                        <input
                                                            type="text"
                                                            id="title_{{ $item->id }}"
                                                            name="items[{{ $item->id }}][title]"
                                                            class="form-control @error('items.'.$item->id.'.title') is-invalid @enderror"
                                                            placeholder="{{ __('admin.title') }}"
                                                            value="{{ old('items.'.$item->id.'.title', $item->title) }}">
                                                        @error('items.'.$item->id.'.title')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-0">
                                                        <label for="description_{{ $item->id }}" class="text-dark">{{ __('admin.description') }}</label>
                                                        <textarea
                                                            id="description_{{ $item->id }}"
                                                            name="items[{{ $item->id }}][description]"
                                                            class="form-control @error('items.'.$item->id.'.description') is-invalid @enderror"
                                                            placeholder="{{ __('admin.description') }}"
                                                            rows="3">{{ old('items.'.$item->id.'.description', $item->description) }}</textarea>
                                                        @error('items.'.$item->id.'.description')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="mt-4 btn btn-primary">
                                            {{ __('admin.update') }}
                                        </button>
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