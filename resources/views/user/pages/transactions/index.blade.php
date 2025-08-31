@extends('user.layouts.app')
@section('title', __('admin.transactions'))

@push('css')
<style>
    /* Simple Bootstrap card styling */
    .card {
        transition: box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endpush

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.transactions') }}</span></li>
    </ol>
</nav>
@endpush

@section('content')
<div class="layout-px-spacing">
    @if($banks && count($banks) > 0)
    <div class="row layout-top-spacing">
        <div class="col-12">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4 class="page-title">{{ __('admin.bank_accounts') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area">
                    <div class="row g-4">
                        @foreach($banks as $bank)
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex align-items-center">
                                        @if($bank->image)
                                        <img src="{{ displayImage($bank->image) }}"
                                            alt="{{ $bank->name }}"
                                            class="me-3"
                                            style="width: 60px; height: 40px; object-fit: cover;">
                                        @else
                                        <div class="bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 40px;">
                                            <i class="fas fa-university text-white"></i>
                                        </div>
                                        @endif
                                        <h5 class="card-title mb-0 text-white  mx-3">{{ $bank->name }}</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <small class="text-muted d-block">{{ __('admin.account_owner') }}</small>
                                        <strong class="text-dark  mx-3">{{ $bank->account_owner }}</strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">{{ __('admin.account_number') }}</small>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark me-2 text-break mx-3">{{ $bank->account_number }}</strong>
                                            <button class="btn btn-sm btn-outline-secondary btn-copy"
                                                data-copy="{{ $bank->account_number }}"
                                                title="{{ __('admin.copy_to_clipboard') }}">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">{{ __('admin.iban_number') }}</small>
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark me-2 text-break mx-3">{{ $bank->iban_number }}</strong>
                                            <button class="btn-sm btn-outline-secondary btn-copy"
                                                data-copy="{{ $bank->iban_number }}"
                                                title="{{ __('admin.copy_to_clipboard') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="5" height="5"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="feather feather-edit-2">
                                                    <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row layout-top-spacing">
        <div class="col-12">
            <div class="statbox widget box box-shadow">
                <div class="widget-content widget-content-area text-center py-5">
                    <div class="empty-state">
                        <div class="empty-state__icon mb-4">
                            <i class="fas fa-university text-muted" style="font-size: 64px; opacity: 0.3;"></i>
                        </div>
                        <h4 class="text-muted mb-3">{{ __('admin.no_bank_accounts') }}</h4>
                        <p class="text-muted mb-0 fs-6">{{ __('admin.no_bank_accounts_description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row layout-top-spacing">
        <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row mt-2">
                        <div class="col-12" style="margin: 15px 15px 0 15px;">
                            <a href="{{ route('user.transactions.create') }}"
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
                                            <form action="{{ route('user.transactions.index') }}" method="GET"
                                                class="p-3">
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="code">{{ __('admin.code') }}</label>
                                                        <input type="text" value="{{ request()->get('code') }}"
                                                            name="code" id="code" class="form-control"
                                                            placeholder="{{ __('admin.code') }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="bank">{{ __('admin.bank') }}</label>
                                                        <select name="bank" class="form-control" id="bank">
                                                            <option value="">{{ __('admin.choose_bank') }}
                                                            </option>
                                                            @foreach ($result['banks'] as $bank)
                                                            <option @selected($bank->id == request()->get('bank'))
                                                                value="{{ $bank->id }}">
                                                                {{ $bank->name }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="status">{{ __('admin.status') }}</label>
                                                        <select name="status" class="form-control" id="status">
                                                            <option value="">{{ __('admin.choose_status') }}
                                                            </option>
                                                            @foreach ($result['status'] as $stat)
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
                                                            href="{{ route('user.transactions.index') }}">{{ __('admin.cancel') }}</a>
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
                                <h4 class="mt-4 mb-3">{{ trans('admin.transactions') }}</h4>
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
                                @foreach ($result['transactions'] as $transation)
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
                    {{ $result['transactions']->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Copy button functionality
        document.querySelectorAll('.btn-copy').forEach(button => {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                const icon = this.querySelector('i');

                // Create a temporary textarea element to hold the text
                const textarea = document.createElement('textarea');
                textarea.value = textToCopy;
                document.body.appendChild(textarea);

                // Select and copy the text
                textarea.select();
                document.execCommand('copy');

                // Remove the temporary textarea
                document.body.removeChild(textarea);

                // Visual feedback
                const originalClass = icon.className;
                icon.className = 'fas fa-check';
                this.classList.add('btn-success');
                this.classList.remove('btn-outline-secondary');

                // Reset after 2 seconds
                setTimeout(() => {
                    icon.className = originalClass;
                    this.classList.remove('btn-success');
                    this.classList.add('btn-outline-secondary');
                }, 2000);
            });
        });
    });
</script>
@endpush