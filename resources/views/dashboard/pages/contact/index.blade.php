@extends('dashboard.layouts.app')
@section('title', __('admin.contacts'))
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
                                        <button class="btn" type="button" data-toggle="collapse" data-target="#collapseOne"
                                            aria-expanded="false" aria-controls="collapseOne">
                                            {{ __('admin.Filter Options') }}
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne"
                                    data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="table-responsive mb-2">
                                            <div class="col-12 mx-auto border">
                                                <form action="{{ route('admin.contacts.index') }}" method="GET" class="p-3">
                                                    <div class="row mt-2">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="name">{{ __('admin.name') }}</label>
                                                            <input placeholder="{{ __('admin.name') }}" type="text"
                                                                name="name" class="form-control"
                                                                value="{{ request()->get('name') }}" id="name">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="email">{{ __('admin.email') }}</label>
                                                            <input placeholder="{{ __('admin.email') }}" type="text"
                                                                name="email" class="form-control"
                                                                value="{{ request()->get('email') }}" id="email">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="status">{{ __('admin.status') }}</label>
                                                            <select name="status" id="status" class="form-control">
                                                                <option value="">{{ __('admin.choose_status') }}</option>
                                                                @foreach ($status as $stat)
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
                                                                class="bg-success form-control btn btn-success btn-block">
                                                                {{ __('admin.search') }}
                                                            </button>
                                                        </div>
                                                        <div class="col-md-3 mb-3">
                                                            <a role="button" class="btn btn-danger form-control btn-block"
                                                                href="{{ route('admin.contacts.index') }}">
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
                                    <h4 class="py-3">{{ __('admin.contacts') }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.first_name') }}</th>
                                        <th scope="col">{{ __('admin.last_name') }}</th>
                                        <th scope="col">{{ __('admin.email') }}</th>
                                        <th scope="col">{{ __('admin.phone') }}</th>
                                        <th scope="col">{{ __('admin.message') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['contacts.delete']))
                                            <th class="text-center" scope="col">{{ __('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($contacts as $contact)
                                        <tr>
                                            <td>{{ $contact->first_name }}</td>
                                            <td>{{ $contact->last_name }}</td>
                                            <td>{{ $contact->email }}</td>
                                            <td>{{ $contact->phone }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                                    data-target="#messageModal" data-message="{{ $contact->message }}">
                                                    {{ __('admin.message') }}
                                                </button>
                                            </td>
                                            <td>
                                                @if (auth('admin')->user()->hasAnyPermission(['contacts.reply']))
                                                    @if ($contact->status->value == 'pending')
                                                        <button data-contact-id="{{ $contact->id }}" type="button"
                                                            class="{{ $contact->status->badge() }} btn-sm" data-toggle="modal"
                                                            data-target="#replyModal">
                                                            {{ $contact->status->lang() }}
                                                        </button>
                                                    @else
                                                        <span class="{{ $contact->status->badge() }}">
                                                            {{ $contact->status->lang() }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="{{ $contact->status->badge() }}">
                                                        {{ $contact->status->lang() }}
                                                    </span>
                                                @endif
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['contacts.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @haspermission('contacts.delete', 'admin')
                                                        <form action="{{ route('admin.contacts.delete', $contact->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button
                                                                style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                                type="submit" title="{{ __('admin.delete') }}"
                                                                class="action-btn btn-dlt bs-tooltip badge rounded-pill bg-danger"
                                                                data-toggle="tooltip" data-placement="top"
                                                                aria-label="{{ __('admin.delete') }}"
                                                                data-bs-original-title="Delete">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-trash-2">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        @endhaspermission
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $contacts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="replyModalLabel">{{ __('admin.reply') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('admin.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.contacts.reply') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="contact" id="contact_id" value="">
                        <div class="form-group">
                            <label for="reply_message">{{ __('admin.message') }}</label>
                            <textarea name="message" id="reply_message" class="form-control"
                                rows="5">{{ old('message') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('admin.close') }}</button>
                        <button type="submit" class="btn btn-info">{{ __('admin.send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">{{ __('admin.message') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('admin.close') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="messageModalBody" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('admin.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function ($) {
            'use strict';

            $('#replyModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var contactId = button.data('contact-id');
                $(this).find('#contact_id').val(contactId);
            });

            $('#replyModal').on('hidden.bs.modal', function () {
                var $form = $(this).find('form')[0];
                if ($form) $form.reset();
                $(this).find('#contact_id').val('');
                $(this).find('#reply_message').val('');
            });

            $('#messageModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var message = button.data('message') || '';
                $(this).find('#messageModalBody').text(message);
            });
        })(jQuery);
    </script>
@endpush