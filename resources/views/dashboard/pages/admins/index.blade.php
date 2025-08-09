@extends('dashboard.layouts.app')
@section('title', __('admin.admins'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mt-2">
                            <div class="col-12" style="margin: 15px 15px 0 15px;">
                                @haspermission('admins.create', 'admin')
                                    <a href="{{ route('admin.admins.create') }}"
                                        class="btn btn-primary">{{ __('admin.add') }}</a>
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
                                                <form action="{{ route('admin.admins.search') }}" method="GET"
                                                    class="p-3">
                                                    <div class="row mt-2">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="name">{{ __('admin.name') }}</label>
                                                            <input type="text" value="{{ request()->get('name') }}"
                                                                name="name" id="name" class="form-control"
                                                                placeholder="{{ __('admin.name') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="email">{{ __('admin.email') }}</label>
                                                            <input type="text" value="{{ request()->get('email') }}"
                                                                name="email" id="email" class="form-control"
                                                                placeholder="{{ __('admin.email') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="phone">{{ __('admin.phone') }}</label>
                                                            <input type="text" value="{{ request()->get('phone') }}"
                                                                name="phone" id="phone" class="form-control"
                                                                placeholder="{{ __('admin.phone') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="role">{{ __('admin.roles') }}</label>
                                                            <select name="role" class="form-control" id="role">
                                                                <option value="">{{ __('admin.choose_role') }}
                                                                </option>
                                                                @foreach ($roles as $role)
                                                                    <option @selected($role->id == request()->get('role'))
                                                                        value="{{ $role->id }}">
                                                                        {{ $role->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label for="status">{{ __('admin.status') }}</label>
                                                            <select name="status" class="form-control" id="status">
                                                                <option value="">{{ __('admin.choose_status') }}
                                                                </option>
                                                                @foreach ($status as $stat)
                                                                    <option @selected($stat->value == request()->get('status'))
                                                                        value="{{ $stat->value }}">
                                                                        {{ $stat->lang() }}</option>
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
                                                                href="{{ route('admin.admins.index') }}">{{ __('admin.cancel') }}</a>
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
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.admins') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.image') }}</th>
                                        <th scope="col">{{ __('admin.name') }}</th>
                                        <th scope="col">{{ __('admin.email') }}</th>
                                        <th scope="col">{{ __('admin.role') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['admins.update', 'admins.delete']))
                                            <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($admins as $admin)
                                        <tr>
                                            <td>
                                                <a href="{{ displayImage($admin->image) }}" target="_blank">
                                                    <img width="40px" height="40px" class="rounded-circle"
                                                        src="{{ displayImage($admin->image) }}" alt="">
                                                </a>
                                            </td>
                                            <td>
                                                <p class="mb-0">{{ $admin->name }}</p>
                                            </td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ optional($admin->roles->first())->name ?? __('admin.n/a') }}</td>
                                            <td>
                                                @if (auth('admin')->user()->hasAnyPermission(['admins.update']))
                                                    <form method="POST"
                                                        action="{{ route('admin.admins.updateStatus', $admin->id) }}">
                                                        @csrf
                                                        <button
                                                            class="{{ $admin->status->badge() }} btn-sm btn-alert">{{ $admin->status->lang() }}</button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="{{ $admin->status->badge() }}">{{ $admin->status->lang() }}</span>
                                                @endif
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['admins.update', 'admins.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @haspermission('admins.update', 'admin')
                                                            <a href="{{ route('admin.admins.edit', $admin->id) }}"
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
                                                        @haspermission('admins.delete', 'admin')
                                                            <form action="{{ route('admin.admins.delete', $admin->id) }}"
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
                            {{ $admins->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
