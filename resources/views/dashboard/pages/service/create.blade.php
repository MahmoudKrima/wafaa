@extends('dashboard.layouts.app')
@section('title', __('admin.add_new_service'))
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h4 class="">{{ __('admin.add_new_service') }}</h4>
                            <x-back-button route="{{ route('admin.services.index') }}" />
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.services.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="title"
                                                class="text-dark">{{ __('admin.title') }}</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                value="{{ old('title') }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="statusInput" class="text-dark">{{ __('admin.status') }}</label>
                                            <select name="status" id="statusInput" class="form-control">
                                                @foreach ($status as $stat)
                                                    <option @selected($stat->value == old('status'))
                                                            value="{{ $stat->value }}">
                                                        {{ $stat->lang() }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="description"
                                                class="text-dark">{{ __('admin.description') }}</label>
                                            <textarea name="description" id="description" class="form-control"
                                                rows="3">{{ old('description') }}</textarea>
                                        </div>


                                        <div class="col-lg-12 col-md-12 mb-3 custom-file-container"
                                            data-upload-id="myFirstImage">
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
                                    </div>
                                </div>
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
    var firstUpload = new FileUploadWithPreview('myFirstImage');
</script>
@endpush
