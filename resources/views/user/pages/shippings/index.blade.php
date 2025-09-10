@extends('user.layouts.app')
@section('title', __('admin.shippings'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.shippings') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div style="display:flex;flex-direction: row;justify-content: space-between;">
                        <div style="margin: 15px 15px 0 15px;">
                            <a href="{{ route('user.shippings.create') }}" class="btn btn-primary">
                                <i class="fa fa-add"></i> {{ __('admin.create_new_shipment') }}
                            </a>
                        </div>
                        <div style="margin: 15px 15px 0 15px;">
                            <a href="{{ route('user.shippings.export', request()->query()) }}" class="btn btn-outline-dark">
                                <i class="fa fa-file-excel"></i> {{ __('admin.export_excel') }}
                            </a>
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
                                            <form action="{{ route('user.shippings.index') }}" method="GET"
                                                class="p-3">
                                                <div class="row">

                                                    <div class="col-md-3 mb-3">
                                                        <label for="isCod">{{ __('admin.isCod') }}</label>
                                                        @php $isCod = request()->get('isCod'); @endphp
                                                        <select name="isCod" class="form-control" id="isCod">
                                                            <option value="">{{ __('admin.choose_isCod') }}</option>
                                                            <option value="true" @selected($isCod==='true' )>{{ __('admin.yes') }}</option>
                                                            <option value="false" @selected($isCod==='false' )>{{ __('admin.no') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="shippingCompanyId">{{ __('admin.shippingCompanyId') }}</label>
                                                        <select name="shippingCompanyId" class="form-control" id="shippingCompanyId">
                                                            <option value="" selected>{{ __('admin.choose_shipping_company') }}</option>
                                                            @foreach ($companies as $company)
                                                            <option value="{{ $company['id'] }}"
                                                                @selected(request()->get('shippingCompanyId') == $company['id'])>
                                                                {{ $company['name'] }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="method">{{ __('admin.choose_shipping_method') }}</label>
                                                        <select name="method" class="form-control" id="method">
                                                            <option value="" selected>{{ __('admin.choose_shipping_method') }}</option>
                                                            <option value="local" @selected(request()->get('method') == 'local')>{{ __('admin.local') }}</option>
                                                            <option value="international" @selected(request()->get('method') == 'international')>{{ __('admin.international') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="type">{{ __('admin.shipment_type') }}</label>
                                                        <select name="type" class="form-control" id="type">
                                                            <option value="" selected>{{ __('admin.choose_shipment_type') }}</option>
                                                            <option value="box" @selected(request()->get('type') == 'box')>{{ __('admin.boxes') }}</option>
                                                            <option value="document" @selected(request()->get('type') == 'documents')>{{ __('admin.documents') }}</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label for="status">{{ __('admin.status') }}</label>
                                                        <select name="status" class="form-control" id="status">
                                                            <option value="" selected>{{ __('admin.choose_status') }}</option>
                                                            <option value="delivered" @selected(request()->get('status') == 'delivered')>{{ __('admin.delivered') }}</option>
                                                            <option value="returned" @selected(request()->get('status') == 'returned')>{{ __('admin.returned') }}</option>
                                                            <option value="pending" @selected(request()->get('status') == 'pending')>{{ __('admin.pending') }}</option>
                                                            <option value="processing" @selected(request()->get('status') == 'processing')>{{ __('admin.processing') }}</option>
                                                            <option value="failed" @selected(request()->get('status') == 'failed')>{{ __('admin.failed') }}</option>
                                                            <option value="canceled" @selected(request()->get('status') == 'canceled')>{{ __('admin.canceled') }}</option>
                                                            <option value="cancelRequest" @selected(request()->get('status') == 'cancelRequest')>{{ __('admin.cancelrequest') }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for=trackingNumber>{{ __('admin.tracking_number') }}</label>
                                                        <input type="input" value="{{ request()->get('search') }}"
                                                            name="search" id="trackingNumber" class="form-control"
                                                            placeholder="{{ __('admin.tracking_number') }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="receiverName">{{ __('admin.receiver_name') }}</label>
                                                        <input type="text" value="{{ request()->get('receiverName') }}"
                                                            name="receiverName" id="receiverName" class="form-control"
                                                            placeholder="{{ __('admin.receiver_name') }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="receiverPhone">{{ __('admin.receiver_phone') }}</label>
                                                        <input type="text" value="{{ request()->get('receiverPhone') }}"
                                                            name="receiverPhone" id="receiverPhone" class="form-control"
                                                            placeholder="{{ __('admin.receiver_phone') }}">
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <label for="dateFrom">{{ __('admin.dateFrom') }}</label>
                                                        <input type="date" value="{{ request()->get('dateFrom') }}"
                                                            name="dateFrom" id="dateFrom" class="form-control"
                                                            placeholder="{{ __('admin.dateFrom') }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label for="dateTo">{{ __('admin.dateTo') }}</label>
                                                        <input type="date" value="{{ request()->get('dateTo') }}"
                                                            name="dateTo" id="dateTo" class="form-control"
                                                            placeholder="{{ __('admin.dateTo') }}">
                                                    </div>

                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-3 mb-3">
                                                        <button type="submit"
                                                            class="bg-success form-control btn-block">{{ __('admin.search') }}</button>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a role="button" class="btn btn-danger form-control btn-block"
                                                            href="{{ route('user.shippings.index') }}">{{ __('admin.cancel') }}</a>
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
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.shippings') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.company_image') }}</th>
                                    <th scope="col">{{ __('admin.tracking_number') }}</th>
                                    <th scope="col">{{ __('admin.sender') }}</th>
                                    <th scope="col">{{ __('admin.receiver') }}</th>
                                    <th scope="col">{{ __('admin.weight') }}</th>
                                    <th scope="col">{{ __('admin.shipment_type') }}</th>
                                    <th scope="col">{{ __('admin.date_added') }}</th>
                                    <th scope="col">{{ __('admin.shipment_Cod') }}</th>
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    <th scope="col">{{ __('admin.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($shipments as $shipment)
                                <tr>
                                    <td>
                                        <img src="{{ $shipment['shippingCompany']['logoUrl'] }}" style="width:70px;height:auto;">
                                    </td>
                                    <td>{{ $shipment['trackingNumber'] ?? __('admin.n/a') }}</td>
                                    <td>{{ $shipment['shipmentDetails']['senderName'] ?? __('admin.n/a') }}</td>
                                    <td>{{ $shipment['shipmentDetails']['receiverName'] ?? __('admin.n/a') }}</td>
                                    <td>{{ optional($shipment['shipmentDetails'])['weight'] .' '. __('admin.kg') ?? __('admin.n/a') }}</td>
                                    <td>
                                        @if($shipment['method'] === 'local')
                                        <span class="badge bg-box">{{ __('admin.local') }}</span>
                                        @elseif($shipment['method'] === 'international')
                                        <span class="badge bg-box">{{ __('admin.international') }}</span>
                                        @endif

                                        @if($shipment['type'] === 'box')
                                        <span class="badge bg-box text-white">{{ __('admin.boxes') }}</span>
                                        @elseif($shipment['type'] === 'document')
                                        <span class="badge bg-document text-white">{{ __('admin.documents') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($shipment['createdAt'])->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @php
                                        $isCod = (bool) data_get($shipment, 'isCod', false);
                                        $codPrice = data_get($shipment, 'shipmentCod.codPrice');
                                        @endphp

                                        @if ($isCod)
                                        @if ($codPrice !== null && $codPrice !== '')
                                        {{ number_format((float) $codPrice, 2) }} {{ __('admin.currency_symbol') }}
                                        @endif
                                        @else
                                        {{ __('admin.regular_shipment') }}
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                        switch ($shipment['status']) {
                                        case 'pending':
                                        $class = 'badge bg-warning text-white'; $label = __('admin.pending'); break;
                                        case 'processing':
                                        $class = 'badge bg-success text-white'; $label = __('admin.processing'); break;
                                        case 'failed':
                                        $class = 'badge bg-danger white'; $label = __('admin.failed'); break;
                                        case 'canceled':
                                        $class = 'badge bg-danger white'; $label = __('admin.canceled'); break;
                                        case 'delivered':
                                        $class = 'badge bg-info white'; $label = __('admin.delivered'); break;
                                        case 'returned':
                                        $class = 'badge bg-dark white'; $label = __('admin.returned'); break;
                                        case 'cancelRequest':
                                        $class = 'badge bg-warning white'; $label = __('admin.cancelrequest'); break;
                                        default:
                                        $class = 'badge bg-success white'; $label = __('admin.' . strtolower($shipment['status'])); break;
                                        }
                                        @endphp
                                        <span class="{{ $class }}">{{ $label }}</span>
                                    </td>
                                    <td>
                                        @if(!empty($shipment['labelUrl']))
                                        <a href="{{ $shipment['labelUrl'] }}" target="_blank" class="badge bg-primary text-white">
                                            {{ __('admin.shipment_file') }}
                                        </a>
                                        @else
                                        <span class="badge bg-primary text-white">{{ __('admin.n/a') }}</span>
                                        @endif


                                        <a href="{{ route('user.shippings.show', $shipment['id']) }}" class="badge bg-info text-white">
                                            {{ __('admin.show') }}
                                        </a>

                                        @if(!empty($shipment['trackingUrl']))
                                        <a href="{{ $shipment['trackingUrl'] }}" target="_blank" class="badge bg-dark text-white">
                                            {{ __('admin.track_shipment') }}
                                        </a>
                                        @else
                                        <span class="badge bg-dark text-white">{{ __('admin.n/a') }}</span>
                                        @endif
                                        @if($shipment['status'] == 'processing')
                                        <a href="{{ route('user.shippings.delete', $shipment['id']) }}" class="badge bg-danger text-white">
                                            {{ __('admin.cancel') }}
                                        </a>
                                        @endif
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="bg-white">
                                        <div class="text-center p-5">
                                            <i class="fa fa-truck fa-3x mb-3 text-muted"></i>
                                            <h5 class="mb-2">
                                                {{ __('admin.no_shipments_title') ?? 'No Shippments Right Now' }}
                                            </h5>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                    @if ($shipments->count())
                    {{ $shipments->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection