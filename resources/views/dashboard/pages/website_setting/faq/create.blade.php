@extends('dashboard.layouts.app')
@section('title', __('admin.create'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="basic" class="col-lg-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mb-4">
                            <div class="col-12 d-flex justify-content-between align-items-center">
                                <h4 class="">{{ __('admin.create') }}</h4>
                                <x-back-button route="{{ route('admin.faqs.index') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="row">
                            <div class="col-lg-12 col-12 mx-auto">
                                <form action="{{ route('admin.faqs.store') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label for="question" class="text-dark">{{ __('admin.question') }}</label>
                                                <input type="text" name="question" id="question" class="form-control"
                                                    value="{{ old('question') }}">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="answer" class="text-dark">{{ __('admin.answer') }}</label>
                                                <textarea name="answer" id="answer" class="form-control"
                                                    rows="3">{{ old('answer') }}</textarea>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label for="statusInput" class="text-dark">{{ __('admin.status') }}</label>
                                                <select name="status" id="statusInput" class="form-control">
                                                    @foreach ($status as $stat)
                                                        <option @selected($stat->value == old('status'))
                                                            value="{{ $stat->value }}">
                                                            {{ $stat->lang() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <input type="submit" value="{{ __('admin.create') }}"
                                                class="mt-4 btn btn-primary">
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