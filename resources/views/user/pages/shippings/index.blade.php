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
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            <a href="{{ route('user.shippings.create') }}"
                                class="btn btn-primary">{{ __('admin.create') }}</a>
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
                                                    <div class="col-md-4 mb-3">
                                                        <label for="code">{{ __('admin.code') }}</label>
                                                        <input type="text" value="{{ request()->get('code') }}"
                                                            name="code" id="code" class="form-control"
                                                            placeholder="{{ __('admin.code') }}">
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
                                    <th scope="col">{{ __('admin.code') }}</th>
                                    <th scope="col">{{ __('admin.bank') }}</th>
                                    <th scope="col">{{ __('admin.amount') }}</th>
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    <th scope="col">{{ __('admin.attachment') }}</th>
                                    <th scope="col">{{ __('admin.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($result['shippings'] as $transation)
                                <tr>
                                    <td>
                                        {{$transation->code}}
                                    </td>
                                    <td>
                                        {{optional($transation->bank)->name ?? __('admin.n/a')}}
                                    </td>
                                    <td>
                                        {{$transation->amount}}
                                    </td>
                                    <td>
                                        <span
                                            class="{{ $transation->status->badge() }}">{{ $transation->status->lang() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ displayImage($transation->attachment) }}"
                                            class="btn btn-primary btn-sm" target="_blank">
                                            {{ __('admin.attachment') }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $transation->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $result['shippings']->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection