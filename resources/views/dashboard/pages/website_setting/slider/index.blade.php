@extends('dashboard.layouts.app')
@section('title', __('admin.sliders'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.sliders') }}</span></li>
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
                            @haspermission('sliders.create', 'admin')
                            <a href="{{ route('admin.sliders.create') }}"
                                class="btn btn-primary">{{ __('admin.create') }}</a>
                            @endhaspermission
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div id="accordion">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn" data-toggle="collapse" data-target="#collapseOne"
                                        aria-expanded="true" aria-controls="collapseOne">
                                        {{ trans('admin.Filter Options') }}
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                data-parent="#accordion">
                                <div class="card-body">
                                    <div class="table-responsive mb-2">
                                        <div class="col-12 mx-auto border">
                                            <form action="{{ route('admin.sliders.search') }}" method="GET" class="p-3">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="title">{{ __('admin.title') }}</label>
                                                        <input type="text" value="{{ request()->get('title') }}"
                                                            title="title" id="title" class="form-control"
                                                            placeholder="{{ __('admin.title') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="status">{{ __('admin.status') }}</label>
                                                        <select name="status" class="form-control" id="status">
                                                            <option value="">{{ __('admin.choose_status') }}
                                                            </option>
                                                            @foreach ($status as $stat)
                                                            <option @selected($stat->value == request()->get('status'))
                                                                value="{{ $stat->value }}">
                                                                {{ $stat->lang() }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-3 mb-3">
                                                        <button type="submit"
                                                            class="bg-success form-control btn-block">{{ __('admin.search') }}</button>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a role="button" class="btn btn-danger form-control btn-block"
                                                            href="{{ route('admin.sliders.index') }}">{{ __('admin.cancel') }}</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.sliders') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.image') }}</th>
                                    <th scope="col">{{ __('admin.title') }}</th>
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th scope="col">{{ __('admin.subtitle') }}</th>
                                    <th scope="col">{{ __('admin.button_text') }}</th>
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    @if (auth('admin')->user()->hasAnyPermission(['sliders.update', 'sliders.delete']))
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sliders as $slider)
                                <tr>
                                    <td>
                                        <a href="{{ displayImage($slider->image) }}" target="_blank">
                                            <img width="40px" height="40px" class="rounded-circle"
                                                src="{{ displayImage($slider->image) }}" alt="">
                                        </a>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:200px;">{{ $slider->title }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0" style="max-width:200px;">{{ $slider->description }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $slider->subtitle }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $slider->button_text }}</p>
                                    </td>
                                    <td>
                                        @if (auth('admin')->user()->hasAnyPermission(['sliders.update']))
                                        <form method="POST" action="{{ route('admin.sliders.updateStatus', $slider->id) }}">
                                            @csrf
                                            <button
                                                class="{{ $slider->status->badge() }} btn-sm btn-alert">{{ $slider->status->lang() }}</button>
                                        </form>
                                        @else
                                        <span class="{{ $slider->status->badge() }}">{{ $slider->status->lang() }}</span>
                                        @endif
                                    </td>
                                    @if (auth('admin')->user()->hasAnyPermission(['sliders.update', 'sliders.delete']))
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('sliders.update', 'admin')
                                            <a href="{{ route('admin.sliders.edit', $slider->id) }}"
                                                class="action-btn bs-tooltip me-2 mb-2 badge rounded-circle bg-warning p-2"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit slider"
                                                data-bs-original-title="Edit slider" title="{{ __('admin.edit_slider') }}"
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

                                            @haspermission('sliders.delete', 'admin')
                                            <form action="{{ route('admin.sliders.delete', $slider->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="action-btn btn-alert bs-tooltip mb-2 badge rounded-circle bg-danger p-2"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ __('admin.delete') }}" aria-label="Delete Slider"
                                                    data-bs-original-title="Delete Slider"
                                                    style="border: none; background:transparent; margin-right: 10px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="feather feather-trash-2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path
                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                        </path>
                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endhaspermission
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $sliders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection