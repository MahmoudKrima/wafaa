@extends('dashboard.layouts.app')
@section('title', __('admin.admin_shipping'))

@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-center text-md-start">{{ __('admin.admin_shipping') }}</h4>
                        </div>
                    </div>
                </div>

                <div class="widget-content widget-content-area">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-center">
                                <div class="step-indicator d-flex flex-column flex-sm-row align-items-center">
                                    @for ($i = 1; $i <= 7; $i++)
                                        <div class="step {{ $i === 1 ? 'active' : '' }} mb-2 mb-sm-0">
                                        <div class="step-number {{ $i===1 ? 'bg-primary' : 'bg-secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mx-sm-0"
                                            style="width:35px;height:35px;font-size:14px;font-weight:bold;">{{ $i }}</div>
                                        <span class="d-block d-sm-inline ms-0 ms-sm-2 text-center text-sm-start mt-1 mt-sm-0 small">
                                            @switch($i)
                                            @case(1) {{ __('admin.select_user') }} @break
                                            @case(2) {{ __('admin.select_company') }} @break
                                            @case(3) {{ __('admin.select_method') }} @break
                                            @case(4) {{ __('admin.user_information') }} @break
                                            @case(5) {{ __('admin.receivers') }} @break
                                            @case(6) {{ __('admin.shipping_details') }} @break
                                            @case(7) {{ __('admin.payment_details') }} @break
                                            @endswitch
                                        </span>
                                </div>
                                @if($i<7)
                                    <div class="step-line d-none d-sm-block mx-3" style="width:40px;height:2px;background:#e9ecef;">
                            </div>
                            <div class="step-line d-block d-sm-none my-2" style="width:2px;height:20px;background:#e9ecef;"></div>
                            @endif
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
            <div class="step-content" id="step-1">
                <h5 class="text-center mb-4">{{ __('admin.select_user_for_shipping') }}</h5>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="user_select" class="text-dark">{{ __('admin.select_user') }}</label>
                            <select id="user_select" name="user_id" class="form-control" required>
                                <option value="">{{ __('admin.choose_user') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <button type="button" id="btn-next" class="btn btn-primary" disabled>
                            {{ __('admin.next') }} {{ app()->getLocale() === 'ar' ? '←' : '→' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    const translations = {
        select_user: '{{ __("admin.select_user") }}',
        choose_user: '{{ __("admin.choose_user") }}',
        next: '{{ __("admin.next") }}',
        previous: '{{ __("admin.previous") }}',
        user_not_found: '{{ __("admin.user_not_found") }}',
        error_loading_user: '{{ __("admin.error_loading_user") }}'
    };

    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user_select');
        const btnNext = document.getElementById('btn-next');

        userSelect.addEventListener('change', function() {
            if (this.value) {
                btnNext.disabled = false;
                btnNext.classList.remove('btn-secondary');
                btnNext.classList.add('btn-primary');
            } else {
                btnNext.disabled = true;
                btnNext.classList.remove('btn-primary');
                btnNext.classList.add('btn-secondary');
            }
        });

        btnNext.addEventListener('click', function() {
            if (userSelect.value) {
                // Redirect to create page with user_id
                window.location.href = `{{ route('admin.admin-shipping.create') }}?user_id=${userSelect.value}`;
            }
        });
    });
</script>
@endpush
