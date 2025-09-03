@extends('user.layouts.app')
@section('title', __('admin.notifications'))

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.notifications') }}</span></li>
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
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                    <div class="table-responsive mb-2">
                                        <div class="col-12 mx-auto border">
                                            <form action="{{ route('user.notifications.index') }}" method="GET" class="p-3">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="type">{{ __('admin.type') }}</label>
                                                        <select name="type" class="form-control" id="type">
                                                            <option value="">{{ __('admin.choose_type') }}</option>
                                                            @foreach ($types as $type)
                                                            <option value="{{ $type->value }}" @selected(request('type')===$type->value)>
                                                                {{ $type->lang() }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-3 mb-3">
                                                        <button type="submit" class="bg-success form-control btn-block">
                                                            {{ __('admin.search') }}
                                                        </button>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <a role="button" class="btn btn-danger form-control btn-block"
                                                            href="{{ route('user.notifications.index') }}">
                                                            {{ __('admin.cancel') }}
                                                        </a>
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
                                <h4 class="mt-4 mb-3">{{ trans('admin.notifications') }}</h4>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-vcenter">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.id') }}</th>
                                    <th scope="col">{{ __('admin.type') }}</th>
                                    <th scope="col">{{ __('admin.data') }}</th>
                                    <th scope="col">{{ __('admin.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($notifications as $n)
                                @php
                                $enum = \App\Enum\NotificationTypeEnum::tryFrom($n->type);
                                $badge = $enum ? $enum->badge() : 'badge badge-secondary';
                                $label = $enum ? $enum->lang() : $n->type;
                                $data = (array) $n->data;
                                $msgEn = $data['en'] ?? (is_string($n->data) ? $n->data : '');
                                $msgAr = $data['ar'] ?? '';
                                @endphp
                                <tr>
                                    <td>
                                        {{ $n->id }}
                                    </td>
                                    <td>
                                        <span class="{{ $badge }}">{{ $label }}</span>
                                    </td>
                                    <td>
                                        @if(app()->getLocale() == 'en')
                                        <div>{{ $msgEn ?: __('admin.n/a') }}</div>
                                        @else
                                        <div>{{ $msgAr ?: __('admin.n/a') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($n->created_at)->timezone('Asia/Riyadh')->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="fa fa-bell fa-3x mb-3 text-muted"></i>
                                        <div class="mt-2">{{ __('admin.no_notifications') }}</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection