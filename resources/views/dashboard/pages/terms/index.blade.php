@extends('dashboard.layouts.app')
@section('title', __('admin.terms'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.terms') }}</span></li>
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
                        <div class="col-xl-6 col-md-6 col-sm-6 col-6">
                            <h4>{{ __('admin.terms') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('admin.terms.update', $term->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="term_description_ar" class="text-dark">{{ __('admin.term_description_ar') }}</label>
                                            <textarea type="text" step="0.01" name="term_description_ar" id="term_description_ar"
                                                class="form-control" placeholder="{{ __('admin.term_description_ar') }}"
                                                value="{{ old('term_description_ar', $term->getTranslation('term_description', 'ar')) }}">{{ $term->getTranslation('term_description', 'ar') }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="term_description_en" class="text-dark">{{ __('admin.term_description_en') }}</label>
                                            <textarea type="text" step="0.01" name="term_description_en" id="term_description_en"
                                                class="form-control" placeholder="{{ __('admin.term_description_en') }}"
                                                value="{{ old('term_description_ar', $term->getTranslation('term_description', 'en')) }}">{{ $term->getTranslation('term_description', 'en') }}</textarea>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="policy_description_ar" class="text-dark">{{ __('admin.policy_description_ar') }}</label>
                                            <textarea type="text" step="0.01" name="policy_description_ar" id="policy_description_ar"
                                                class="form-control" placeholder="{{ __('admin.policy_description_ar') }}"
                                                value="{{ old('policy_description_ar', $term->getTranslation('policy_description', 'ar')) }}">{{ $term->getTranslation('policy_description', 'ar') }}</textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="policy_description_en" class="text-dark">{{ __('admin.policy_description_en') }}</label>
                                            <textarea type="text" step="0.01" name="policy_description_en" id="policy_description_en"
                                                class="form-control" placeholder="{{ __('admin.policy_description_en') }}"
                                                value="{{ old('policy_description_en', $term->getTranslation('policy_description', 'en')) }}">{{ $term->getTranslation('policy_description', 'en') }}</textarea>
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