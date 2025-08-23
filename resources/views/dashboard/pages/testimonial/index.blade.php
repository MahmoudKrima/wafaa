@extends('dashboard.layouts.app')
@section('title', __('admin.testimonials'))
@section('content')
    <div class="layout-px-spacing">
        <div class="row layout-top-spacing">
            <div id="tableCustomBasic" class="col-lg-12 col-12 layout-spacing">
                <div class="statbox widget box box-shadow">
                    <div class="widget-header">
                        <div class="row mt-2">
                            <div class="col-12" style="margin: 15px 15px 0 15px;">
                                @haspermission('testimonials.create', 'admin')
                                    <a href="{{ route('admin.testimonials.create') }}"
                                        class="btn btn-primary">{{ __('admin.add') }}</a>
                                @endhaspermission
                            </div>
                        </div>
                    </div>
                    <div class="widget-content widget-content-area">
                        <div class="widget-header">
                            <div class="row">
                                <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                    <h4 style="padding: 30px 0px 15px 0px;">{{ trans('admin.testimonials') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-vcenter">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('admin.name') }}</th>
                                        <th scope="col">{{ __('admin.job_title') }}</th>
                                        <th scope="col">{{ __('admin.review') }}</th>
                                        <th scope="col">{{ __('admin.rate') }}</th>
                                        <th scope="col">{{ __('admin.status') }}</th>
                                        @if (auth('admin')->user()->hasAnyPermission(['testimonials.update', 'testimonials.delete']))
                                            <th class="text-center" scope="col">{{ trans('admin.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($testimonials as $testimonial)
                                        <tr>
                                            <td>
                                                {{ $testimonial->name }}
                                            </td>
                                            <td>
                                                {{ $testimonial->job_title }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                                    data-target="#reviewModal" data-review="{{ $testimonial->review }}">
                                                    {{ __('admin.review') }}
                                                </button>
                                            </td>
                                            <td>
                                                {{ $testimonial->rate }}
                                            </td>
                                            <td>
                                                @if (auth('admin')->user()->hasAnyPermission(['testimonials.update']))
                                                    <form method="POST"
                                                        action="{{ route('admin.testimonials.updateStatus', $testimonial->id) }}">
                                                        @csrf
                                                        <button
                                                            class="{{ $testimonial->status->badge() }} btn-sm btn-alert">{{ $testimonial->status->lang() }}</button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="{{ $testimonial->status->badge() }}">{{ $testimonial->status->lang() }}</span>
                                                @endif
                                            </td>
                                            @if (auth('admin')->user()->hasAnyPermission(['testimonials.update', 'testimonials.delete']))
                                                <td class="text-center">
                                                    <div class="action-btns d-flex justify-content-center">
                                                        @haspermission('testimonials.update', 'admin')
                                                            <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}"
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
                                                        @haspermission('testimonials.delete', 'admin')
                                                            <form action="{{ route('admin.testimonials.delete', $testimonial->id) }}"
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
                            {{ $testimonials->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="reviewModalLabel">{{ __('admin.review') }}</h5>
      </div>
      <div class="modal-body">
        <p id="review"></p>
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
            $('#reviewModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var review = button.data('review');
                $('#review').text(review);
            });
        });
    </script>
@endpush
