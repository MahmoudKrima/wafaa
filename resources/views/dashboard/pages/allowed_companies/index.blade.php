@extends('dashboard.layouts.app')
@section('title', __('admin.allowed_companies'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="{{ __('admin.breadcrumb') }}">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.allowed_companies') }}</span></li>
    </ol>
</nav>
@endpush
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="widget-content widget-content-area">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.allowed_companies') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.image') }}</th>
                                        <th scope="col">{{ __('admin.company_name') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allowedCompanies as $allowedCompany)
                                    <tr>
                                        <td>
                                            <a href="{{ displayImage($allowedCompany->image) }}" target="_blank">
                                                <img src="{{ displayImage($allowedCompany->image) }}" style="width:60px;height:auto;">
                                            </a>
                                        </td>
                                        <td>
                                            <p class="mb-0">{{ $allowedCompany->company_name }}</p>
                                        </td>
                                        <td>
                                            @if (auth('admin')->user()->hasAnyPermission(['allowed_companies.update']))
                                            <form method="POST" action="{{ route('admin.allowed-companies.updateStatus', $allowedCompany->id) }}">
                                                @csrf
                                                <button
                                                    class="{{ $allowedCompany->status->badge() }} btn-sm btn-alert">{{ $allowedCompany->status->lang() }}</button>
                                            </form>
                                            @else
                                            <span class="{{ $allowedCompany->status->badge() }}">{{ $allowedCompany->status->lang() }}</span>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $allowedCompanies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
