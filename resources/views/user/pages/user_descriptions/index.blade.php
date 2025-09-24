@extends('user.layouts.app')
@section('title', __('admin.shipping_descriptions'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.shipping_descriptions') }}</span></li>
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
                            <a href="{{ route('user.user-descriptions.create') }}"
                                class="btn btn-primary">{{ __('admin.add') }}</a>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.shipping_descriptions') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userDescriptions as $userDescription)
                                <tr>
                                    <td>{{ $userDescription->description }}</td>
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            <a href="{{ route('user.user-descriptions.edit', $userDescription->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                style="padding:7px;" title="{{ __('admin.update') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('user.user-descriptions.delete', $userDescription->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                    type="submit" title="{{ __('admin.delete') }}"
                                                    class="action-btn btn-dlt bs-tooltip badge rounded-pill bg-danger"
                                                    data-toggle="tooltip" data-placement="top" aria-label="Delete"
                                                    data-bs-original-title="Delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $userDescriptions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection