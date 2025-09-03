@extends('user.layouts.app')
@section('title', __('admin.create_recharge_request'))
@section('content')
<div class="layout-px-spacing">
    <div class="row layout-top-spacing">
        <div id="basic" class="col-lg-12 layout-spacing">
            <div class="statbox widget box box-shadow">
                <div class="widget-header">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                            <h4>{{ __('admin.create_recharge_request') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="widget-content widget-content-area">
                    <div class="row">
                        <div class="col-lg-12 col-12 mx-auto">
                            <form action="{{ route('user.transactions.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="amountInput" class="text-dark">{{ __('admin.transfer_amount') }}</label>
                                            <input id="amountInput" type="text" name="amount"
                                                placeholder="{{ __('admin.amount') }}" class="form-control"
                                                value="{{ old('amount') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="bankInput" class="text-dark">{{ __('admin.choose_transfer_bank') }}</label>
                                            <select name="banks_id" id="bank" class="form-control">
                                                <option value="" disabled selected>{{ __('admin.choose_bank') }}
                                                </option>
                                                @foreach ($banks as $bank)
                                                <option value="{{ $bank->id }}" @selected($bank->id == old('banks_id'))>
                                                    {{ $bank->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 col-md-6 mb-3 custom-file-container"
                                            data-upload-id="myFirstImage">
                                            <label>{{ __('admin.receipt_attachment') }}<a href="javascript:void(0)"
                                                    class="custom-file-container__image-clear"
                                                    title="{{ __('admin.clear_image') }}"><span
                                                        style="background-color:#ababab;padding:5px;border-radius:50%;margin:0 10px;">X</span></a></label>
                                            <label class="custom-file-container__custom-file">
                                                <input type="file"
                                                    class="custom-file-container__custom-file__custom-file-input"
                                                    name="attachment">
                                                <span
                                                    class="custom-file-container__custom-file__custom-file-control"></span>
                                            </label>
                                            <div class="custom-file-container__image-preview"></div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="submit" value="{{ __('admin.send_request') }}" class="mt-4 btn btn-primary">
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
<script>
    var firstUpload = new FileUploadWithPreview('myFirstImage');
</script>
@endpush
