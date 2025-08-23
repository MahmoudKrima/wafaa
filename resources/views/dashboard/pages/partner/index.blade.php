@extends('dashboard.layouts.app')
@section('title', __('admin.partners'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mt-2">
                            <div class="col-12" style="margin: 15px 15px 0 15px;">
                                @haspermission('partners.create', 'admin')
                                    <a href="{{ route('admin.partners.create') }}"
                                        class="btn btn-primary">{{ __('admin.add') }}</a>
                                @endhaspermission
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.partners') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.image') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['partners.update', 'partners.delete']))
                                            <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($partners as $partner)
                                        <tr>
                                            <td>
                                                <a href="{{ displayImage($partner->image) }}" target="_blank">
                                                    <img width="40px" height="40px" class="rounded-circle"
                                                        src="{{ displayImage($partner->image) }}" alt="">
                                                </a>
                                            </td>
                                            <td>
                                                @if (auth('admin')->user()->hasAnyPermission(['partners.update']))
                                                    <form method="POST"
                                                        action="{{ route('admin.partners.updateStatus', $partner->id) }}">
                                                        @csrf
                                                        <button
                                                            class="{{ $partner->status->badge() }} btn-sm btn-alert">{{ $partner->status->lang() }}</button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="{{ $partner->status->badge() }}">{{ $partner->status->lang() }}</span>
                                                @endif
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['partners.update', 'partners.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @haspermission('partners.update', 'admin')
                                                            <a href="{{ route('admin.partners.edit', $partner->id) }}"
                                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                                style="padding:7px;" data-toggle="tooltip"
                                                                data-placement="top" aria-label="Edit"
                                                                title="{{ __('admin.edit') }}" data-bs-original-title="Edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                                    height="17" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-edit-2">
                                                                    <path
                                                                        d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                                    </path>
                                                                </svg>
                                                            </a>
                                                        @endhaspermission
                                                        @haspermission('partners.delete', 'admin')
                                                            <form action="{{ route('admin.partners.delete', $partner->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button
                                                                    style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                                    type="submit" title="{{ __('admin.delete') }}"
                                                                    class="action-btn btn-dlt bs-tooltip badge rounded-pill bg-danger"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    aria-label="Delete" data-bs-original-title="Delete">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                                        height="17" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="feather feather-trash-2">
                                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                                        <path
                                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                        </path>
                                                                        <line x1="10" y1="11" x2="10"
                                                                            y2="17"></line>
                                                                        <line x1="14" y1="11" x2="14"
                                                                            y2="17"></line>
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
                        <div class="d-flex justify-content-center">
                            {{ $partners->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
