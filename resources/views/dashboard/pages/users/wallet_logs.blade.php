@extends('dashboard.layouts.app')
@section('title', __('admin.wallet_logs'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
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
                <div class="widget-header">
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            <a href="{{ route('admin.users.index') }}"
                                class="btn btn-primary">{{ __('admin.back') }}</a>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">

                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.wallet_logs') }}</h4>
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
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th scope="col">{{ __('admin.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($walletLogs as $walletLog)
                                <tr>
                                    <td>{{ $walletLog->type->lang() }}</td>
                                    <td>
                                        @if($walletLog->loggable_type === 'App\Models\Transaction')
                                        <span class="badge badge-info">{{ __('admin.transaction') }}</span>
                                        <br><small class="text-muted">#{{ $walletLog->loggable->code }}</small>
                                        @elseif($walletLog->loggable_type === 'App\Models\Shipment')
                                        <span class="badge badge-warning">{{ __('admin.shipment') }}</span>
                                        <br><small class="text-muted">#{{ $walletLog->loggable->code ?? $walletLog->loggable->id }}</small>
                                        @else
                                        <span class="badge badge-secondary">{{ __('admin.other') }}</span>
                                        <br><small class="text-muted">#{{ $walletLog->loggable->id ?? 'N/A' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $walletLog->amount }}</td>
                                    <td>{{ $walletLog->description }}</td>
                                    <td>{{ $walletLog->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $walletLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection