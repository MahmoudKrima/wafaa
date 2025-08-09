@extends('dashboard.layouts.app')
@section('title', __('admin.create_role'))
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/css/styleRoles.css') }}">
@endpush
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="basic" class="col-lg-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mb-4">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <h4 class="">{{ __('admin.create_role') }}</h4>
                                <x-back-button route="{{ route('admin.roles.index') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="row">
                            <div class="col-lg-12 col-12 mx-auto">
                                <form id="general-info" method="post" action="{{ route('admin.roles.store') }}">
                                    @csrf
                                    <div class="info">
                                        <div class="row">
                                            <div class="col-lg-11 mx-auto">
                                                <div class="row">
                                                    <div class="col-12 mt-md-0 mt-4">
                                                        <div class="form">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label for="nameArInput"
                                                                        class="text-dark">{{ __('admin.role') }}</label>
                                                                    <input id="nameArInput" type="text" name="name"
                                                                        placeholder="{{ __('admin.role') }}"
                                                                        class="form-control" value="{{ old('name') }}">
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <table class="permissions-table table table-responsive">
                                                                        <tbody>
                                                                            @php
                                                                                $currentGroup = '';
                                                                            @endphp
                                                                            <tr>
                                                                                <th>
                                                                                    <input type="checkbox"
                                                                                        id="select-all-checkbox">
                                                                                    <label
                                                                                        for="select-all-checkbox">{{ __('admin.select_all') }}</label>
                                                                                </th>
                                                                            </tr>
                                                                            @foreach ($permissions as $key => $p)
                                                                                @php
                                                                                    $array = explode(
                                                                                        '.',
                                                                                        $permissions[$key]['name'],
                                                                                    );
                                                                                    $groupName = __(
                                                                                        'admin.' . $array[0],
                                                                                    );
                                                                                @endphp
                                                                                @if ($groupName !== $currentGroup)
                                                                                    @if ($currentGroup != '')
                                                                                        </tr>
                                                                                    @endif
                                                                                    <tr>
                                                                                        <td class="font-weight-bold">
                                                                                            {{ $groupName }}
                                                                                        </td>
                                                                                        @php
                                                                                            $currentGroup = $groupName;
                                                                                        @endphp
                                                                                @endif
                                                                                <td class="py-2">
                                                                                    <input type="checkbox"
                                                                                        id="permission_{{ $permissions[$key]['id'] }}"
                                                                                        value="{{ $permissions[$key]['id'] }}"
                                                                                        name="permission_id[]"
                                                                                        {{ old('permission_id') && in_array($permissions[$key]['id'], old('permission_id')) ? 'checked' : '' }}>
                                                                                    <label
                                                                                        for="permission_{{ $permissions[$key]['id'] }}">{{ __('admin.' . $array[1]) }}</label>
                                                                                </td>
                                                                            @endforeach
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <input type="submit" value="{{ __('admin.create') }}"
                                                                        class="mt-4 btn btn-primary">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <script src="{{ asset('admin/js/selectAll.js') }}"></script>
@endpush
