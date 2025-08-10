@extends('dashboard.layouts.app')
@section('title', __('admin.users'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.users') }}</span></li>
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
                            @haspermission('users.create', 'admin')
                            <a href="{{ route('admin.users.create') }}"
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
                                            <form action="{{ route('admin.users.search') }}" method="GET"
                                                class="p-3">
                                                <div class="row mt-2">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="name">{{ __('admin.name') }}</label>
                                                        <input type="text" value="{{ request()->get('name') }}"
                                                            name="name" id="name" class="form-control"
                                                            placeholder="{{ __('admin.name') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="phone">{{ __('admin.phone') }}</label>
                                                        <input type="text" value="{{ request()->get('phone') }}"
                                                            name="phone" id="phone" class="form-control"
                                                            placeholder="{{ __('admin.phone') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="email">{{ __('admin.email') }}</label>
                                                        <input type="text" value="{{ request()->get('email') }}"
                                                            name="email" id="email" class="form-control"
                                                            placeholder="{{ __('admin.email') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="city">{{ __('admin.city') }}</label>
                                                        <select name="city" class="form-control" id="city">
                                                            <option value="">{{ __('admin.choose_city') }}</option>
                                                            @foreach ($cities as $city)
                                                            <option @selected($city->id == request()->get('city'))
                                                                value="{{ $city->id }}">
                                                                {{ $city->name}}
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
                                                            href="{{ route('admin.users.index') }}">
                                                            {{ __('admin.cancel') }}</a>
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
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.users') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.name') }}</th>
                                    <th scope="col">{{ __('admin.phone') }}</th>
                                    <th scope="col">{{ __('admin.additional_phone') }}</th>
                                    <th scope="col">{{ __('admin.email') }}</th>
                                    <th scope="col">{{ __('admin.city') }}</th>
                                    <th scope="col">{{ __('admin.address') }}</th>
                                    <th scope="col">{{ __('admin.added_by') }}</th>
                                    @if (auth('admin')->user()->hasAnyPermission(['users.update', 'users.delete']))
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->additional_phone ?? __('admin.n/a') }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->city?->name}}</td>
                                    <td>{{ Str::limit($user->address, 50) }}</td>
                                    <td>{{ $user->addedByAdmin?->name}}</td>
                                    @if (auth('admin')->user()->hasAnyPermission(['users.update', 'users.delete']))
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('users.update', 'admin')
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                style="padding:7px;" title="{{ __('admin.update') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
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
                                            @haspermission('users.delete', 'admin')
                                            <form action="{{ route('admin.users.delete', $user->id) }}"
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
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection