@extends('dashboard.layouts.app')
@section('title', $bank->name)
@push('breadcrumb')
    <nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banks.index') }}">{{ __('admin.banks') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><span>{{ $bank->name }}</span></li>
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
                                                <form
                                                    action="{{ route('admin.banks-transactions.searchTransactions', $bank->id) }}"
                                                    method="GET" class="p-3">
                                                    <div class="row mt-2">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="plan">{{ __('admin.plan') }}</label>
                                                            <select name="plan" id="plan" class="form-control">
                                                                <option value="">{{ __('admin.choose_plan') }}
                                                                </option>
                                                                @foreach ($plans as $plan)
                                                                    <option @selected($plan->id == request()->input('plan'))
                                                                        value="{{ $plan->id }}">{{ $plan->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="provider">{{ __('admin.provider') }}</label>
                                                            <select name="provider" id="provider" class="form-control">
                                                                <option value="">{{ __('admin.choose_provider') }}
                                                                </option>
                                                                @foreach ($providers as $provider)
                                                                    <option @selected($provider->id == request()->input('provider'))
                                                                        value="{{ $provider->id }}">{{ $provider->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="name">{{ __('admin.name') }}</label>
                                                            <input type="text" name="name" id="name"
                                                                class="form-control" placeholder="{{ __('admin.name') }}"
                                                                value="{{ request()->input('name') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label
                                                                for="transaction_number">{{ __('admin.transaction_number') }}</label>
                                                            <input type="text" name="transaction_number"
                                                                id="transaction_number" class="form-control"
                                                                placeholder="{{ __('admin.transaction_number') }}"
                                                                value="{{ request()->input('transaction_number') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-6 mb-3">
                                                            <label
                                                                for="paid_amount_from">{{ __('admin.paid_amount_from') }}</label>
                                                            <input type="number" min="0" name="paid_amount_from"
                                                                id="paid_amount_from" class="form-control"
                                                                placeholder="{{ __('admin.paid_amount_from') }}"
                                                                value="{{ request()->input('paid_amount_from') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label
                                                                for="paid_amount_to">{{ __('admin.paid_amount_to') }}</label>
                                                            <input type="number" min="0" name="paid_amount_to"
                                                                id="paid_amount_to" class="form-control"
                                                                placeholder="{{ __('admin.paid_amount_to') }}"
                                                                value="{{ request()->input('paid_amount_to') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="date_from">{{ __('admin.date_from') }}</label>
                                                            <input type="date" name="date_from" id="date_from"
                                                                class="form-control"
                                                                placeholder="{{ __('admin.date_from') }}"
                                                                value="{{ request()->input('date_from') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="date_to">{{ __('admin.date_to') }}</label>
                                                            <input type="date" name="date_to" id="date_to"
                                                                class="form-control"
                                                                placeholder="{{ __('admin.date_to') }}"
                                                                value="{{ request()->input('date_to') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-12 mb-3">
                                                            <label for="status">{{ __('admin.status') }}</label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="">{{ __('admin.choose_status') }}
                                                                </option>
                                                                @foreach ($status as $stat)
                                                                    <option @selected($stat->value == request()->input('status'))
                                                                        value="{{ $stat->value }}">{{ $stat->lang() }}
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
                                                            <a role="button"
                                                                class="btn btn-danger form-control btn-block"
                                                                href="{{ route('admin.banks-transactions.showTransactions', $bank->id) }}">{{ __('admin.cancel') }}</a>
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
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ $bank->name }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.plan') }}</th>
                                        <th scope="col">{{ __('admin.provider') }}</th>
                                        <th scope="col">{{ __('admin.paid_amount') }}</th>
                                        <th scope="col">{{ __('admin.name') }}</th>
                                        <th scope="col">{{ __('admin.transaction_number') }}</th>
                                        <th scope="col">{{ __('admin.image') }}</th>
                                        <th scope="col">{{ __('admin.date') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['plan_transaction.update', 'plan_transaction.delete']))
                                            <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($planTransactions as $planTransaction)
                                        <tr>
                                            <td>{{ optional($planTransaction->plan)->name ?? __('admin.n/a') }}</td>
                                            <td>{{ optional($planTransaction->provider)->name ?? __('admin.n/a') }}</td>
                                            <td>{{ $planTransaction->paid_amount }}</td>
                                            <td>{{ $planTransaction->name }}</td>
                                            <td>{{ $planTransaction->transaction_number }}</td>
                                            <td>
                                                <a href="{{ displayImage($planTransaction->image) }}" target="_blank">
                                                    <img src="{{ displayImage($planTransaction->image) }}" width="70px"
                                                        height="70px" alt="">
                                                </a>
                                            </td>
                                            <td>{{ $planTransaction->date }}</td>
                                            <td>
                                                <span
                                                    class="{{ $planTransaction->status->badge() }}">{{ $planTransaction->status->lang() }}</span>
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['plan_transaction.update', 'plan_transaction.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @if ($planTransaction->status->value == 'pending')
                                                            @haspermission('plan_transaction.update', 'admin')
                                                                <a href="{{ route('admin.plan-transactions.edit', $planTransaction->id) }}"
                                                                    class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                                    style="padding:7px;" data-toggle="tooltip"
                                                                    title="{{ __('admin.edit_plan_transaction') }}"
                                                                    data-placement="top" aria-label="{{ __('admin.edit') }}"
                                                                    data-bs-original-title="{{ __('admin.edit') }}">
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
                                                        @endif
                                                        @haspermission('plan_transaction.delete', 'admin')
                                                            <form
                                                                action="{{ route('admin.plan-transactions.delete', $planTransaction->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button title="{{ __('admin.delete') }}"
                                                                    style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                                    type="submit"
                                                                    class="action-btn btn-alert bs-tooltip badge rounded-pill bg-danger"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    aria-label="{{ __('admin.delete') }}" data-bs-original-title="{{ __('admin.delete') }}">
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
                        {{ $planTransactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
