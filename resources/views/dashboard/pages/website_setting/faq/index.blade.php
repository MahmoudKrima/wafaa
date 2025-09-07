@extends('dashboard.layouts.app')
@section('title', __('admin.faqs'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mt-2">
                            <div class="col-12" style="margin: 15px 15px 0 15px;">
                                @haspermission('faqs.create', 'admin')
                                    <a href="{{ route('admin.faqs.create') }}"
                                        class="btn btn-primary">{{ __('admin.add') }}</a>
                                @endhaspermission
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.faqs') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.question') }}</th>
                                        <th scope="col">{{ __('admin.answer') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['faqs.update', 'faqs.delete']))
                                            <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($faqs as $faq)
                                        <tr>
                                            <td>
                                                {{ $faq->question }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#descriptionModal" data-description="{{ $faq->answer }}">
                                                    {{ __('admin.answer') }}
                                                </button>
                                            </td>
                                            <td>
                                                @if (auth('admin')->user()->hasAnyPermission(['faqs.update']))
                                                    <form method="POST"
                                                        action="{{ route('admin.faqs.updateStatus', $faq->id) }}">
                                                        @csrf
                                                        <button
                                                            class="{{ $faq->status->badge() }} btn-sm btn-alert">{{ $faq->status->lang() }}</button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="{{ $faq->status->badge() }}">{{ $faq->status->lang() }}</span>
                                                @endif
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['faqs.update', 'faqs.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @haspermission('faqs.update', 'admin')
                                                            <a href="{{ route('admin.faqs.edit', $faq->id) }}"
                                                                class="action-btn btn-edit bs-tooltip me-2 badge rounded-pill bg-warning"
                                                                style="padding:7px;" data-toggle="tooltip"
                                                                data-placement="top" aria-label="Edit"
                                                                title="{{ __('admin.edit') }}" data-bs-original-title="Edit">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                                    height="17" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="feather feather-edit-2">
                                                                    <path
                                                                        d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z">
                                                                    </path>
                                                                </svg>
                                                            </a>
                                                        @endhaspermission
                                                        @haspermission('faqs.delete', 'admin')
                                                            <form action="{{ route('admin.faqs.delete', $faq->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button
                                                                    style="border: none; background:transparent;padding:7px;margin:0 5px;"
                                                                    type="submit" title="{{ __('admin.delete') }}"
                                                                    class="action-btn btn-dlt bs-tooltip badge rounded-pill bg-danger"
                                                                    data-toggle="tooltip" data-placement="top"
                                                                    aria-label="Delete" data-bs-original-title="Delete">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                                        height="17" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="feather feather-trash-2">
                                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                                        <path
                                                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                        </path>
                                                                        <line x1="10" y1="11" x2="10"
                                                                            y2="17"></line>
                                                                        <line x1="14" y1="11" x2="14"
                                                                            y2="17"></line>
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
                        <div class="d-flex justify-content-center">
                            {{ $faqs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="descriptionModalLabel">{{ __('admin.description') }}</h5>
      </div>
      <div class="modal-body">
        <p id="description"></p>
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
        $(document).ready(function() {
            $('#descriptionModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var description = button.data('description');
                $('#description').text(description);
            });
        });
    </script>
@endpush
