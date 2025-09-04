@extends('user.layouts.app')
@section('title', __('admin.wallet_logs'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.wallet_logs') }}</span></li>
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
                                            <form action="{{ route('user.wallet-logs.index') }}" method="GET"
                                                class="p-3">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="type">{{ __('admin.type') }}</label>
                                                        <select name="type" class="form-control" id="type">
                                                            <option value="">{{ __('admin.choose_type') }}
                                                            </option>
                                                            @foreach ($types as $type)
                                                            <option @selected($type->value == request()->get('type'))
                                                                value="{{ $type->value }}">
                                                                {{ $type->lang() }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="trans_type">{{ __('admin.trans_type') }}</label>
                                                        <select name="trans_type" class="form-control" id="trans_type">
                                                            <option value="">{{ __('admin.choose_trans_type') }}
                                                            </option>
                                                            @foreach ($trans_types as $trans_type)
                                                            <option @selected($trans_type->value == request()->get('trans_type'))
                                                                value="{{ $trans_type->value }}">
                                                                {{ $trans_type->lang() }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="created_at">{{ __('admin.date_from') }}</label>
                                                        <input type="date" name="date_from" class="form-control" id="date_from" value="{{ request()->get('date_from') }}">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="created_at">{{ __('admin.date_to') }}</label>
                                                        <input type="date" name="date_to" class="form-control" id="date_to" value="{{ request()->get('date_to') }}">
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-3 mb-3">
                                                        <button type="submit"
                                                            class="bg-success form-control btn-block">{{ __('admin.search') }}</button>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a role="button" class="btn btn-danger form-control btn-block"
                                                            href="{{ route('user.wallet-logs.index') }}">{{ __('admin.cancel') }}</a>
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
                                <h4 class="mt-4 mb-3">{{ trans('admin.wallet_logs') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.type') }}</th>
                                    <th scope="col">{{ __('admin.transaction_type') }}</th>
                                    <th scope="col">{{ __('admin.amount') }}</th>
                                    <th scope="col">{{ __('admin.admin') }}</th>
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th scope="col">{{ __('admin.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                <tr>
                                    <td>
                                        <span class="{{ $log->type->badge() }}">{{ $log->type->lang() }}</span>
                                    </td>
                                    <td>
                                        <span class="{{ $log->trans_type->badge() }}">{{ $log->trans_type->lang() }}</span>
                                    </td>
                                    <td>
                                        {{$log->amount}} {{ __('admin.currency_symbol') }}
                                    </td>
                                    <td>
                                        {{optional($log->admin)->name ?? __('admin.n/a')}}
                                    </td>
                                    <td>
                                        {{$log->description}}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y h:i')}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
