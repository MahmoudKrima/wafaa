@extends('dashboard.layouts.app')
@section('title', __('admin.about'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.about') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            @haspermission('about.update', 'admin')
                            <a href="{{ route('admin.about.edit') }}"
                                class="btn btn-primary">{{ __('admin.edit') }}</a>
                            @endhaspermission
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.about_section') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.image') }}</th>
                                    <th scope="col">{{ __('admin.title') }}</th>
                                    <th scope="col">{{ __('admin.subtitle') }}</th>
                                    <th scope="col">{{ __('admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <a href="{{ displayImage($about->image) }}" target="_blank">
                                            <img width="40px" height="40px" class="rounded-circle"
                                                src="{{ displayImage($about->image) }}" alt="">
                                        </a>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:200px;">{{ $about->title }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:200px;">{{ $about->subtitle }}</p>
                                    </td>
                                    <td>
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('about.update', 'admin')
                                            <a href="{{ route('admin.about.edit') }}"
                                                class="action-btn bs-tooltip me-2 mb-2 badge rounded-circle bg-warning p-2"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit about"
                                                data-bs-original-title="Edit about" title="{{ __('admin.edit_about') }}"
                                                style="margin-right: 10px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @endhaspermission
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- About Items Section -->
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            @haspermission('about-items.update', 'admin')
                            <a href="{{ route('admin.about.edit') }}"
                                class="btn btn-primary">{{ __('admin.edit_items') }}</a>
                            @endhaspermission
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.about_items') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ __('admin.title') }}</th>
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th scope="col">{{ __('admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($aboutItems as $index => $item)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:200px;">{{ $item->title }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:300px;">{{ $item->description }}</p>
                                    </td>
                                    <td>
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('about-items.update', 'admin')
                                            <a href="{{ route('admin.about.edit') }}"
                                                class="action-btn bs-tooltip me-2 mb-2 badge rounded-circle bg-warning p-2"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit about item"
                                                data-bs-original-title="Edit about item" title="{{ __('admin.edit_about_items') }}"
                                                style="margin-right: 10px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @endhaspermission
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection