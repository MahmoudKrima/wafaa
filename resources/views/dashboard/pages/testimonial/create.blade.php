@extends('dashboard.layouts.app')
@section('title', __('admin.create'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="basic" class="col-lg-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mb-4">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <h4 class="">{{ __('admin.create') }}</h4>
                                <x-back-button route="{{ route('admin.testimonials.index') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="row">
                            <div class="col-lg-12 col-12 mx-auto">
                                <form action="{{ route('admin.testimonials.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label for="name_ar"
                                                    class="text-dark">{{ __('admin.name_ar') }}</label>
                                                <input type="text" name="name_ar" id="name_ar" class="form-control"
                                                    value="{{ old('name_ar') }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="name_en"
                                                    class="text-dark">{{ __('admin.name_en') }}</label>
                                                <input type="text" name="name_en" id="name_en" class="form-control"
                                                    value="{{ old('name_en') }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="job_title_ar"
                                                    class="text-dark">{{ __('admin.job_title_ar') }}</label>
                                                <input type="text" name="job_title_ar" id="job_title_ar" class="form-control"
                                                    value="{{ old('job_title_ar') }}">
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="job_title_en"
                                                    class="text-dark">{{ __('admin.job_title_en') }}</label>
                                                <input type="text" name="job_title_en" id="job_title_en" class="form-control"
                                                    value="{{ old('job_title_en') }}">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="review_ar"
                                                    class="text-dark">{{ __('admin.review_ar') }}</label>
                                                <textarea name="review_ar" id="review_ar" class="form-control"
                                                    rows="3">{{ old('review_ar') }}</textarea>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="review_en"
                                                    class="text-dark">{{ __('admin.review_en') }}</label>
                                                <textarea name="review_en" id="review_en" class="form-control"
                                                    rows="3">{{ old('review_en') }}</textarea>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label for="rate"
                                                    class="text-dark">{{ __('admin.rate') }}</label>
                                                <input type="number" min="1" max="5" name="rate" id="rate" class="form-control"
                                                    value="{{ old('rate') }}">
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