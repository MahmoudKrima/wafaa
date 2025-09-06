@extends('dashboard.layouts.app')
@section('title', __('admin.recievers'))
@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.recievers') }}</span></li>
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
                                            <form action="{{ route('admin.recievers.search', $user->id) }}" method="GET" class="p-3">
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

                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-3 mb-3">
                                                        <button type="submit"
                                                            class="bg-success form-control btn-block">{{ __('admin.search') }}</button>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a role="button" class="btn btn-danger form-control btn-block"
                                                            href="{{ route('admin.recievers.index', $user->id) }}">
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
                                <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.recievers') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.user') }}</th>
                                    <th scope="col">{{ __('admin.name') }}</th>
                                    <th scope="col">{{ __('admin.phone') }}</th>
                                    <th scope="col">{{ __('admin.additional_phone') }}</th>
                                    <th scope="col">{{ __('admin.email') }}</th>
                                    <th scope="col">{{ __('admin.postal_code') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recievers as $reciever)
                                <tr>
                                    <td>{{ $reciever->user?->name }}</td>
                                    <td>{{ $reciever->name }}</td>
                                    <td>{{ $reciever->phone }}</td>
                                    <td>{{ $reciever->additional_phone ?? __('admin.n/a') }}</td>
                                    <td>{{ $reciever->email }}</td>
                                    <td>{{ $reciever->postal_code ?? __('admin.n/a') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $recievers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection