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
                                class="btn btn-primary">{{ __('admin.add_user') }}</a>
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
                                            <form action="{{ route('admin.users.search') }}" method="GET" class="p-3">
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
                                                            name="phone" id="phone" class="form-control" style="direction:ltr;"
                                                            placeholder="{{ __('admin.phone') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="email">{{ __('admin.email') }}</label>
                                                        <input type="text" value="{{ request()->get('email') }}"
                                                            name="email" id="email" class="form-control"
                                                            placeholder="{{ __('admin.email') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
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
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    <th scope="col">{{ __('admin.added_by') }}</th>
                                    <th scope="col">{{ __('admin.balance') }}</th>
                                    @if (auth('admin')->user()->hasAnyPermission(['users.update', 'users.delete']))
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td style="direction:ltr;">{{ $user->phone }}</td>
                                    <td>{{ $user->additional_phone ?? __('admin.n/a') }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if (auth('admin')->user()->hasAnyPermission(['users.update']))
                                        <form method="POST"
                                            action="{{ route('admin.users.updateStatus', $user->id) }}">
                                            @csrf
                                            <button
                                                class="{{ $user->status->badge() }} btn-sm btn-alert">{{ $user->status->lang() }}</button>
                                        </form>
                                        @else
                                        <span
                                            class="{{ $user->status->badge() }}">{{ $user->status->lang() }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->addedByAdmin?->name}}</td>
                                    <td>{{ optional($user->wallet)->balance .' '. __('admin.currency_symbol')  ?? __('admin.n/a')}}</td>
                                    @if (auth('admin')->user()->hasAnyPermission(['users.update', 'users.delete', 'user_shipping_prices.view', 'wallet_logs.view', 'senders.view', 'recievers.view', 'shippings.view']))
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('shippings.view', 'admin')
                                            <a href="{{ route('admin.users.shippings', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-primary"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.shippings') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-truck"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('senders.view', 'admin')
                                            <a href="{{ route('admin.senders.index', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-info"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.senders') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-user"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('recievers.view', 'admin')
                                            <a href="{{ route('admin.recievers.index', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-info"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.recievers') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-users"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('user_shipping_prices.view', 'admin')
                                            <a href="{{ route('admin.user-shipping-prices.index', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-dark"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.user_shipping_prices') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-money-check-alt"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('wallet_logs.view', 'admin')
                                            <a href="{{ route('admin.wallet-logs.index', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-success"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.wallet_logs') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-money-bill-trend-up"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('users.update', 'admin')
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                style="padding:7px;margin:0 5px;" title="{{ __('admin.update') }}"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            @endhaspermission
                                            @haspermission('users.delete', 'admin')
                                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                    type="submit" title="{{ __('admin.delete') }}"
                                                    class="action-btn btn-dlt bs-tooltip badge rounded-pill bg-danger"
                                                    data-toggle="tooltip" data-placement="top" aria-label="Delete"
                                                    data-bs-original-title="Delete">
                                                    <i class="fa fa-trash"></i>
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
