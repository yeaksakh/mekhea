@extends('layouts.app')

@section('title', 'Telegram Bot Images')

@section('content')
    @includeIf('expenseautofill::layouts.nav')
    <section class="content-header no-print">
        <h1>Telegram Bot Images</h1>
    </section>

    <section class="content no-print">
        @component('components.filters', ['title' => 'Filters'])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('status', 'Status:') !!} {{-- Changed from ocr_status to status --}}
                    {!! Form::select('status', $statuses, null, ['class' => 'form-control', 'id' => 'status']) !!} {{-- Changed from ocr_status to status --}}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, [
                        'placeholder' => __('lang_v1.select_a_date_range'),
                        'class' => 'form-control',
                        'readonly',
                    ]) !!}
                </div>
            </div>
        @endcomponent

        @component('components.widget', ['class' => 'box-primary', 'title' => 'All Telegram Images'])
            {{-- @slot('tool')
                <div class="box-tools">
                    <a type="button"
                        class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                        id="set-webhook-btn"
                        href="{{ action([\Modules\ExpenseAutoFill\Http\Controllers\BotImageController::class, 'prefillForm']) }}">
                        <i class="fa fa-plug"></i> Set Prefillfrom
                    </a>
                </div>
            @endslot --}}

            <table class="table table-bordered table-striped" id="bot_images_table">
                <thead>
                    <tr>
                        <th>@lang('expenseautofill::lang.id')</th>
                        <th>@lang('expenseautofill::lang.action')</th>
                        <th>@lang('expenseautofill::lang.image')</th>
                        <th>@lang('expenseautofill::lang.from')</th>
                        <th>@lang('expenseautofill::lang.date')</th>
                        <th>@lang('expenseautofill::lang.status')</th> {{-- Changed from ocr_status to status --}}
                        <th>@lang('expenseautofill::lang.total_amount')</th> {{-- Changed from final_total to total_amount --}}
                        <th>@lang('expenseautofill::lang.size')</th>
                        <th>@lang('expenseautofill::lang.dimensions')</th>
                        <th>@lang('expenseautofill::lang.supplier')</th> {{-- New column --}}
                        <th>@lang('expenseautofill::lang.ref_no')</th> {{-- New column --}}
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
                        <h4>@lang('messages.extracted_data')</h4> {{-- Changed from OCR data to extracted data --}}
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody id="ocr-data-table">
                                    <!-- Extracted data will be populated here -->
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
        var baseUrl = "{{ url('/expenseautofill') }}";
        var baseImgUrl = "{{ url('') }}";
        // URL to get JSON details from our database
        var getImageDetailsUrl = baseUrl + "/get-image-details/";
        // URL to get the actual image file for display/download
        var getImageFileUrl = baseUrl + "/bot-image/";
        var setWebhookUrl = baseUrl + "/set-webhook";
        // Make sure this route name matches your web.php
        var botImagesIndexUrl = "{{ route('ExpenseAutoFill.index') }}";
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
                        d.status = $('#status').val(); // Changed from ocr_status to status
                        if ($('#date_range').val()) {
                            var start = $('#date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            var end = $('#date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
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
                        data: 'status', // Changed from ocr_status to status
                        name: 'status'
                    },
                    {
                        data: 'total_amount', // Changed from final_total to total_amount
                        name: 'total_amount'
                    },
                    {
                        data: 'telegram_file_size',
                        name: 'telegram_file_size'
                    },
                    {
                        data: 'dimensions',
                        name: 'dimensions',
                        orderable: false
                    },
                    {
                        data: 'supplier_info', // New column
                        name: 'supplier_info',
                        orderable: false
                    },
                    {
                        data: 'ref_info', // New column
                        name: 'ref_info',
                        orderable: false
                    }
                ]
            });

            $('#status, #from_date, #to_date').on('change', function() { // Changed from ocr_status to status
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

                            // FIX: The file_path already contains 'telegram_images/filename.jpg'
                            // So we just need to prepend the storage URL
                            var imageUrl = baseImgUrl + '/' + currentImageData[
                                'file_path']; // Changed from image_path to file_path

                            // Set the image src with the full URL
                            $('#modal-image').attr('src', imageUrl);

                            var detailsHtml =
                                '<div class="text-left" style="margin-top: 20px;">' +
                                '<p><strong>@lang('messages.from'):</strong> ' + response.data
                                .telegram_user_first_name + ' ' + (response.data
                                    .telegram_user_last_name || '') + '</p>' +
                                // Changed from telegram_from to individual name fields
                                '<p><strong>@lang('messages.date'):</strong> ' + response.data
                                .telegram_date + '</p>' +
                                '<p><strong>@lang('messages.size'):</strong> ' + formatFileSize(
                                    response.data.telegram_file_size) + '</p>' +
                                '<p><strong>@lang('messages.dimensions'):</strong> ' + response.data
                                .telegram_width + 'x' + response.data.telegram_height + '</p>' +
                                '<p><strong>@lang('messages.status'):</strong> ' + response
                                .data // Changed from ocr_status to status
                                .status + '</p>';

                            if (response.data.supplier) { // New field
                                detailsHtml += '<p><strong>@lang('messages.supplier'):</strong> ' +
                                    response.data.supplier + '</p>';
                            }

                            if (response.data.ref_no) { // New field
                                detailsHtml += '<p><strong>@lang('messages.reference_no'):</strong> ' +
                                    response.data.ref_no + '</p>';
                            }

                            if (response.data.transaction_date) { // New field
                                detailsHtml += '<p><strong>@lang('messages.transaction_date'):</strong> ' +
                                    response.data.transaction_date + '</p>';
                            }

                            if (response.data
                                .total_amount) { // Changed from final_total to total_amount
                                detailsHtml += '<p><strong>@lang('messages.total_amount'):</strong> ' +
                                    response.data.total_amount + '</p>';
                            }

                            if (response.data.notes) { // New field
                                detailsHtml += '<p><strong>@lang('messages.notes'):</strong> ' +
                                    response.data.notes + '</p>';
                            }

                            detailsHtml += '</div>';
                            $('#image-details').html(detailsHtml);

                            // Handle extracted data display
                            if (response.data.status ===
                                'processed'
                            ) { // Changed from ocr_status to status and completed to processed
                                var ocrHtml = '';
                                var ocrFields = [{
                                        label: '@lang('messages.supplier')',
                                        field: 'supplier'
                                    }, // Changed from supplier_name to supplier
                                    {
                                        label: '@lang('messages.reference_no')',
                                        field: 'ref_no'
                                    },
                                    {
                                        label: '@lang('messages.transaction_date')',
                                        field: 'transaction_date'
                                    },
                                    {
                                        label: '@lang('messages.total_amount')',
                                        field: 'total_amount'
                                    }, // Changed from final_total to total_amount
                                    {
                                        label: '@lang('messages.location')',
                                        field: 'location'
                                    }, // New field
                                    {
                                        label: '@lang('messages.category')',
                                        field: 'category'
                                    }, // New field
                                    {
                                        label: '@lang('messages.notes')',
                                        field: 'notes'
                                    } // Changed from additional_notes to notes
                                ];

                                ocrFields.forEach(function(item) {
                                    if (response.data[item.field]) {
                                        ocrHtml += '<tr><td><strong>' + item.label +
                                            '</strong></td><td>' + response.data[item
                                                .field] +
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
                e.stopPropagation(); // if needed
                window.open($(this).attr('href'), '_blank');
            });


            $(document).ready(function() {
                //Date range as a button
                $('#date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#date_range').val(start.format(moment_date_format) + ' ~ ' +
                            end.format(
                                moment_date_format));
                        table.ajax.reload();
                    }
                );
                $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#date_range').val('');
                    table.ajax.reload();
                });


            });
        });
    </script>
@endsection
