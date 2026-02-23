@extends('layouts.app')

@section('title', 'Telegram Bot Images')

@section('content')
    @includeIf('purchaseautofill::layouts.nav')
    <section class="content-header no-print">
        <h1>Telegram Bot Images</h1>
    </section>

    <section class="content no-print">
        @component('components.filters', ['title' => 'Filters'])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ocr_status', 'OCR Status:') !!}
                    {!! Form::select('ocr_status', $ocrStatuses, null, ['class' => 'form-control', 'id' => 'ocr_status']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('from_date', 'From Date:') !!}
                    {!! Form::text('from_date', null, [
                        'class' => 'form-control',
                        'id' => 'from_date',
                        'placeholder' => 'From Date',
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('to_date', 'To Date:') !!}
                    {!! Form::text('to_date', null, ['class' => 'form-control', 'id' => 'to_date', 'placeholder' => 'To Date']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('actions', 'Actions:') !!}
                    <div class="btn-group" style="width: 100%;">
                        <button type="button" class="btn btn-info" id="refresh-btn" style="width: 50%;">
                            <i class="fa fa-refresh"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-success" id="sync-btn" style="width: 50%;">
                            <i class="fa fa-cloud-download"></i> Sync
                        </button>
                    </div>
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => 'All Telegram Images'])
            {{-- @slot('tool')
                <div class="box-tools">
                    <a type="button"
                        class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                        id="set-webhook-btn"
                        href="{{ action([\Modules\PurchaseAutoFill\Http\Controllers\BotImageController::class, 'prefillForm']) }}">
                        <i class="fa fa-plug"></i> Set Prefillfrom
                    </a>
                </div>
            @endslot --}}

            <table class="table table-bordered table-striped" id="bot_images_table">
                <thead>
                    <tr>
                        <th>@lang('purchaseautofill::lang.id')</th>
                        <th>@lang('purchaseautofill::lang.action')</th>
                        <th>@lang('purchaseautofill::lang.image')</th>
                        <th>@lang('purchaseautofill::lang.from')</th>
                        <th>@lang('purchaseautofill::lang.date')</th>
                        <th>@lang('purchaseautofill::lang.ocr_status')</th>
                        <th>@lang('purchaseautofill::lang.final_total')</th>
                        <th>@lang('purchaseautofill::lang.size')</th>
                        <th>@lang('purchaseautofill::lang.dimensions')</th>
                    </tr>
                </thead>
            </table>
        @endcomponent
    </section>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="imageModalLabel">@lang('messages.image_preview')</h4>
                </div>
                <div class="modal-body text-center">
                    <img id="modal-image" src="" alt="Image" style="max-width: 100%; max-height: 500px;">
                    <div id="image-details" class="mt-3"></div>
                    <div id="ocr-data" class="mt-3" style="display: none;">
                        <h4>@lang('messages.ocr_data')</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="ocr-data-table">
                                    <!-- OCR data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success" id="create-purchase-from-ocr">
                                <i class="fa fa-plus"></i> @lang('messages.create_purchase')
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script type="text/javascript">
        var baseUrl = "{{ url('/purchaseautofill') }}";
        var baseImgUrl = "{{ url('') }}";
        // URL to get JSON details from our database
        var getImageDetailsUrl = baseUrl + "/get-image-details/"; // <-- CHANGED
        // URL to get the actual image file for display/download
        var getImageFileUrl = baseUrl + "/bot-image/"; // <-- NEW
        var setWebhookUrl = baseUrl + "/set-webhook";
        // Make sure this route name matches your web.php
        var botImagesIndexUrl = "{{ route('bot-images.index') }}";
        var currentImageData = null;

        // Helper function to format file size (if not already defined globally)
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        $(document).ready(function() {
            $('#from_date, #to_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            var table = $('#bot_images_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                autoWidth: false,
                ajax: {
                    url: botImagesIndexUrl,
                    data: function(d) {
                        d.ocr_status = $('#ocr_status').val();
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'telegram_from',
                        name: 'telegram_from'
                    },
                    {
                        data: 'telegram_date',
                        name: 'telegram_date'
                    },
                    {
                        data: 'ocr_status',
                        name: 'ocr_status'
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'telegram_file_size',
                        name: 'telegram_file_size'
                    },
                    {
                        data: 'dimensions',
                        name: 'dimensions',
                        orderable: false
                    }
                ]
            });

            $('#ocr_status, #from_date, #to_date').on('change', function() {
                table.ajax.reload();
            });

            $('#refresh-btn').on('click', function() {
                table.ajax.reload(null, false);
                toastr.success('Table refreshed successfully');
            });

            // You can remove the sync-btn logic as it's no longer needed
            $('#sync-btn').parent().remove(); // Hide the sync button


            // View image in modal
            $(document).on('click', '.view-image', function(e) {
                e.preventDefault();
                var dbId = $(this).data('id');

                // Get image details from our database using the endpoint
                $.ajax({
                    url: getImageDetailsUrl + dbId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentImageData = response.data;

                            // FIX: The image_path already contains 'telegram_images/filename.jpg'
                            // So we just need to prepend the storage URL
                            var imageUrl = baseImgUrl + '/' + currentImageData['image_path'];

                            // Set the image src with the full URL
                            $('#modal-image').attr('src', imageUrl);

                            var detailsHtml =
                                '<div class="text-left" style="margin-top: 20px;">' +
                                '<p><strong>@lang('messages.from'):</strong> ' + response.data
                                .telegram_from + '</p>' +
                                '<p><strong>@lang('messages.date'):</strong> ' + response.data
                                .telegram_date + '</p>' +
                                '<p><strong>@lang('messages.size'):</strong> ' + formatFileSize(
                                    response.data.telegram_file_size) + '</p>' +
                                '<p><strong>@lang('messages.dimensions'):</strong> ' + response.data
                                .telegram_width + 'x' + response.data.telegram_height + '</p>' +
                                '<p><strong>@lang('messages.ocr_status'):</strong> ' + response.data
                                .ocr_status + '</p>';

                            if (response.data.ocr_error) {
                                detailsHtml += '<p><strong>@lang('messages.ocr_error'):</strong> ' +
                                    response.data.ocr_error + '</p>';
                            }
                            detailsHtml += '</div>';
                            $('#image-details').html(detailsHtml);

                            // Handle OCR data display
                            var ocrData = response.data.ocr_data ? JSON.parse(response.data
                                .ocr_data) : null;

                            if (response.data.ocr_status === 'completed' && ocrData) {
                                var ocrHtml = '';
                                var ocrFields = [{
                                        label: '@lang('messages.supplier_name')',
                                        field: 'supplier_name'
                                    },
                                    {
                                        label: '@lang('messages.reference_no')',
                                        field: 'ref_no'
                                    },
                                    {
                                        label: '@lang('messages.transaction_date')',
                                        field: 'transaction_date'
                                    },
                                    {
                                        label: '@lang('messages.final_total')',
                                        field: 'final_total'
                                    },
                                    {
                                        label: '@lang('messages.additional_notes')',
                                        field: 'additional_notes'
                                    }
                                ];
                                ocrFields.forEach(function(item) {
                                    if (ocrData[item.field]) {
                                        ocrHtml += '<tr><td><strong>' + item.label +
                                            '</strong></td><td>' + ocrData[item.field] +
                                            '</td></tr>';
                                    }
                                });
                                $('#ocr-data-table').html(ocrHtml);
                                $('#ocr-data').show();
                            } else {
                                $('#ocr-data').hide();
                            }

                            $('#imageModal').modal('show');
                        } else {
                            toastr.error(response.message || 'Failed to load image details');
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = 'Error loading image details.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastr.error(errorMsg);
                    }
                });
            });

            // Delete image
            $(document).on('click', '.delete-image', function(e) {
                e.preventDefault();
                var dbId = $(this).data('id');

                if (confirm(
                        'Are you sure you want to delete this image and its data? This cannot be undone.'
                    )) {
                    $.ajax({
                        url: baseUrl + '/bot-image/' + dbId,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.message);
                                table.ajax.reload();
                            } else {
                                toastr.error(result.message);
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = 'Error deleting image.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            toastr.error(errorMsg);
                        }
                    });
                }
            });


            $(document).on('click', '.accept-ocr', function(e) {
                e.preventDefault();
                window.location.href = $(this).attr('href');
            });

        });
    </script>
@endsection
