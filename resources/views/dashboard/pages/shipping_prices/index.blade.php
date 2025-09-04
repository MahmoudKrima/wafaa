@extends('dashboard.layouts.app')
@section('title', __('admin.user_shipping_prices'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.user_shipping_prices') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <!-- <div class="widget-header">
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            @haspermission('user_shipping_prices.create', 'admin')
                            <a href="{{ route('admin.user-shipping-prices.create', $user->id) }}"
                                class="btn btn-primary">{{ __('admin.create') }}</a>
                            @endhaspermission
                        </div>
                    </div>
                </div> -->
                <div class="widget-content widget-content-area">
                    <div class="widget-header">
                        <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.user_shipping_prices') }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.company_name') }}</th>
                                    <th scope="col">{{ __('admin.local_price') }}</th>
                                    <th scope="col">{{ __('admin.international_price') }}</th>
                                    @if (auth('admin')->user()->hasAnyPermission(['user_shipping_prices.update', 'user_shipping_prices.delete']))
                                    <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($userShippingPrices as $userShippingPrice)
                                <tr>
                                    <td>
                                        <p class="mb-0">{{ $userShippingPrice->company_name }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $userShippingPrice->local_price ? $userShippingPrice->local_price .' '. __('admin.currency_symbol') : __('admin.n/a') }}</p>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $userShippingPrice->international_price ? $userShippingPrice->international_price .' '. __('admin.currency_symbol') : __('admin.n/a') }}</p>
                                    </td>

                                    @if (auth('admin')->user()->hasAnyPermission(['user_shipping_prices.update', 'user_shipping_prices.delete']))
                                    <td class="text-center">
                                        <div class="action-btns d-flex justify-content-center">
                                            @haspermission('user_shipping_prices.update', 'admin')
                                            <a href="{{ route('admin.user-shipping-prices.edit', [$user->id, $userShippingPrice->id]) }}"
                                                class="action-btn bs-tooltip me-2 mb-2 badge rounded-circle bg-warning p-2"
                                                data-toggle="tooltip" data-placement="top" aria-label="Edit"
                                                data-bs-original-title="Edit bank" title="{{ __('admin.edit') }}"
                                                style="margin-right: 10px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                    </path>
                                                </svg>
                                            </a>
                                            @endhaspermission

                                            @haspermission('user_shipping_prices.delete', 'admin')
                                            <form action="{{ route('admin.user-shipping-prices.delete', $userShippingPrice->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="action-btn btn-alert bs-tooltip mb-2 badge rounded-circle bg-danger p-2"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="{{ __('admin.delete') }}" aria-label="Delete"
                                                    data-bs-original-title="Delete"
                                                    style="border: none; background:transparent; margin-right: 10px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
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
                                            @endhaspermission
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $userShippingPrices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
