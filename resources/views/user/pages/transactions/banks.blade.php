@extends('user.layouts.app')
@section('title', __('admin.bank_accounts'))

@push('breadcrumb')
<nav class="breadcrumb-one" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('user.dashboard.index') }}">{{ __('admin.dashboard') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page"><span>{{ __('admin.bank_accounts') }}</span></li>
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
                            <a href="{{ route('user.transactions.create') }}" class="btn btn-primary">
                                <i class="fa fa-add"></i> {{__('admin.recharge_request')}}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area">
                    @if($banks && count($banks) > 0)
                        <div class="row g-4">
                            @foreach($banks as $bank)
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-2">
                                    <div class="card h-100" style="border-top-left-radius:10px;border-top-right-radius:10px;">
                                        <div class="card-header bg-white text-white" style="border-top-left-radius:10px;border-top-right-radius:10px;display:flex;justify-content: space-between;align-items: center;">
                                            <div class="d-flex align-items-center">
                                                @if($bank->image)
                                                    <img src="{{ displayImage($bank->image) }}" alt="{{ $bank->name }}" class="me-3" style="width:80px;height:60px;object-fit:contain;">
                                                @else
                                                    <div class="bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-3" style="width:80px;height:60px;">
                                                        <i class="fas fa-university text-dark"></i>
                                                    </div>
                                                @endif
                                                <h5 class="card-title mb-0 text-dark mx-3" style="font-size:15px;color:orange;">{{ $bank->name }}</h5>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <small class="text-muted d-block">{{ __('admin.account_owner') }}</small>
                                                <strong class="text-dark">{{ $bank->account_owner }}</strong>
                                            </div>

                                            <div class="mb-3">
                                                <small class="text-muted d-block">{{ __('admin.account_number') }}</small>
                                                <div class="d-flex align-items-center" style="display:flex !important;justify-content:space-between;">
                                                    <strong class="text-dark me-2 text-break">{{ $bank->account_number }}</strong>
                                                    <button class="btn btn-sm btn-outline-secondary btn-copy" data-copy="{{ $bank->account_number }}" title="{{ __('admin.copy_to_clipboard') }}">
                                                        <i class="fas fa-copy" style="font-size:15px;"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <small class="text-muted d-block">{{ __('admin.iban_number') }}</small>
                                                <div class="d-flex align-items-center" style="display:flex !important;justify-content:space-between;">
                                                    <strong class="text-dark me-2 text-break">{{ $bank->iban_number }}</strong>
                                                    <button class="btn btn-sm btn-outline-secondary btn-copy" data-copy="{{ $bank->iban_number }}" title="{{ __('admin.copy_to_clipboard') }}">
                                                        <i class="fas fa-copy" style="font-size:15px;"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="row layout-top-spacing">
                            <div class="col-12">
                                <div class="statbox widget box box-shadow">
                                    <div class="widget-content widget-content-area text-center py-5">
                                        <div class="empty-state">
                                            <div class="empty-state__icon mb-4">
                                                <i class="fas fa-university text-muted" style="font-size:64px;opacity:.3;"></i>
                                            </div>
                                            <h4 class="text-muted mb-3">{{ __('admin.no_bank_accounts') }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-copy').forEach(button => {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                const icon = this.querySelector('i');

                // copy (fallback-compatible)
                if (navigator.clipboard?.writeText) {
                    navigator.clipboard.writeText(textToCopy);
                } else {
                    const textarea = document.createElement('textarea');
                    textarea.value = textToCopy;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                }

                const originalClass = icon.className;
                icon.className = 'fas fa-check';
                this.classList.add('btn-success');
                this.classList.remove('btn-outline-secondary');
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
