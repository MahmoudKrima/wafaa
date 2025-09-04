@extends('user.layouts.app')
@section('title', __('admin.contacts_details'))
@section('content')
<div class="layout-px-spacing">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">

            <div class="card shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white text-center" style="border-top-left-radius:15px;border-top-right-radius:15px;">
                    <h4 class="mb-0">
                        <i class="fas fa-address-book me-2"></i> {{ __('admin.contacts_details') }}
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 text-primary">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <div class="mx-3">
                            <h6 class="mb-1">{{ __('admin.email') }}</h6>
                            <a href="mailto:{{ $contacts->email }}" class="text-decoration-none">
                                {{ $contacts->email ?? '—' }}
                            </a>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 text-success">
                            <i class="fas fa-phone fa-2x"></i>
                        </div>
                        <div class="mx-3">
                            <h6 class="mb-1">{{ __('admin.phone') }}</h6>
                            <a href="tel:{{ $contacts->phone }}" class="text-decoration-none">
                                {{ $contacts->phone ?? '—' }}
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex align-items-center">
                        <div class="me-3 text-success">
                            <i class="fab fa-whatsapp fa-2x"></i>
                        </div>
                        <div class="mx-3">
                            <h6 class="mb-1">{{ __('admin.whatsapp') }}</h6>
                            @if($contacts->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contacts->whatsapp) }}" target="_blank" class="text-decoration-none">
                                {{ $contacts->whatsapp }}
                            </a>
                            @else
                            <span>—</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection