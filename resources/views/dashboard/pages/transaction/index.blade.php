@extends('dashboard.layouts.app')
@section('title', __('admin.recharge_requests'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.recharge_requests') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
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
                                            <form action="{{ route('admin.transactions.search') }}" method="GET" class="p-3">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="code">{{ __('admin.code') }}</label>
                                                        <input type="text" value="{{ request()->get('code') }}"
                                                            name="code" id="code" class="form-control"
                                                            placeholder="{{ __('admin.code') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="user">{{ __('admin.user') }}</label>
                                                        <select name="user_id" class="form-control" id="user">
                                                            <option value="">{{ __('admin.choose_user') }}
                                                            </option>
                                                            @foreach ($users as $user)
                                                            <option @selected($user->id == request()->get('user_id'))
                                                                value="{{ $user->id }}">
                                                                {{ $user->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="bank">{{ __('admin.bank') }}</label>
                                                        <select name="bank" class="form-control" id="bank">
                                                            <option value="">{{ __('admin.choose_bank') }}
                                                            </option>
                                                            @foreach ($banks as $bank)
                                                            <option @selected($bank->id == request()->get('bank'))
                                                                value="{{ $bank->id }}">
                                                                {{ $bank->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
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
                                                            href="{{ route('admin.transactions.index') }}">{{ __('admin.cancel') }}</a>
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
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.recharge_requests') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.code') }}</th>
                                    <th scope="col">{{ __('admin.user') }}</th>
                                    <th scope="col">{{ __('admin.bank') }}</th>
                                    <th scope="col">{{ __('admin.amount') }}</th>
                                    <th scope="col">{{ __('admin.attachment') }}</th>
                                    <th scope="col">{{ __('admin.edited_by') }}</th>
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    <th scope="col">{{ __('admin.added_at') }}</th>
                                    <th scope="col">{{ __('admin.updated_at') }}</th>
                                    @if (auth('admin')->user()->hasAnyPermission(['transactions.update', 'transactions.delete', 'plan_transaction.view']))
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                <tr>
                                    <td>
                                        <p class="mb-0">{{ $transaction->code }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->user->name }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->bank->name }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->amount }}</p>
                                    </td>
                                    <td>
                                        <a href="{{ displayImage($transaction->attachment) }}"
                                            class="btn btn-primary btn-sm " target="_blank">
                                            {{ __('admin.receipt_attachment') }}
                                        </a>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->acceptedBy?->name ?? __('admin.n/a') }}</p>
                                    </td>
                                    <td>
                                        @if (auth('admin')->user()->hasAnyPermission(['transactions.update']))
                                        @if($transaction->status->value === 'pending')
                                        <button type="button"
                                            class="{{ $transaction->status->badge() }}"
                                            data-toggle="modal"
                                            data-target="#statusModal{{ $transaction->id }}">
                                            {{ $transaction->status->lang() }}
                                        </button>
                                        @else
                                        <button type="button"
                                            class="{{ $transaction->status->badge() }}"
                                            disabled
                                            style="cursor: not-allowed; pointer-events: none;"
                                            title="{{ __('admin.status_already_updated') }}">
                                            {{ $transaction->status->lang() }}
                                        </button>
                                        @endif
                                        @else
                                        <span class="{{ $transaction->status->badge() }}">{{ $transaction->status->lang() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</p>
                                    </td>
                                    @if (auth('admin')->user()->hasAnyPermission(['transactions.update', 'transactions.delete', 'plan_transaction.view']))
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('transactions.delete', 'admin')
                                            <form action="{{ route('admin.transactions.delete', $transaction->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="action-btn btn-dlt bs-tooltip mb-2 badge rounded-circle bg-danger p-2"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ __('admin.delete') }}" aria-label="Delete Service"
                                                    data-bs-original-title="Delete Service"
                                                    style="border: none; background:transparent; margin-right: 10px;">
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
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($transactions as $transaction)
@if (auth('admin')->user()->hasAnyPermission(['transactions.update']) && $transaction->status->value === 'pending')
<div class="modal fade" id="statusModal{{ $transaction->id }}" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel{{ $transaction->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-transparent text-white text-center">
                <h5 class="modal-title" id="statusModalLabel{{ $transaction->id }}" style="font-size:16px;">
                    {{ __('admin.update_transaction_status') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="alert alert-info">
                        {{ __('admin.select_new_status_for_transaction') }}: <strong>{{ $transaction->code }}</strong>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.transactions.updateStatus', $transaction->id) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                {{ __('admin.accept') }}
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.transactions.updateStatus', $transaction->id) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-danger btn-lg btn-block">
                                {{ __('admin.reject') }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-3 pt-2">
                    <div class="row">
                        <div class="col-md-4">
                            <strong class="text-muted">{{ __('admin.user') }}:</strong>
                            <p class="mb-0"><strong>{{ $transaction->user->name }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">{{ __('admin.bank') }}:</strong>
                            <p class="mb-0"><strong>{{ $transaction->bank->name }}</strong></p>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">{{ __('admin.amount') }}:</strong>
                            <p class="mb-3"><strong>{{ $transaction->amount }} {{ __('admin.currency_symbol') }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection
