<script src="{{ asset('modules/project/js/project.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#ledger_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#ledger_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            }
        );
        $('#ledger_date_range, #ledger_location').change(function() {
            get_contact_ledger();
        });
        get_contact_ledger();

        rp_log_table = $('#rp_log_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            aaSorting: [
                [0, 'desc']
            ],
            ajax: '/sells?customer_id={{ $contact->id }}&rewards_only=true',
            columns: [{
                    data: 'transaction_date',
                    name: 'transactions.transaction_date'
                },
                {
                    data: 'invoice_no',
                    name: 'transactions.invoice_no'
                },
                {
                    data: 'rp_earned',
                    name: 'transactions.rp_earned'
                },
                {
                    data: 'rp_redeemed',
                    name: 'transactions.rp_redeemed'
                },
            ]
        });

        supplier_stock_report_table = $('#supplier_stock_report_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            'ajax': {
                url: "{{ action([\App\Http\Controllers\ContactController::class, 'getSupplierStockReport'], [$contact->id]) }}",
                data: function(d) {
                    d.location_id = $('#sr_location_id').val();
                }
            },
            columns: [{
                    data: 'product_name',
                    name: 'p.name'
                },
                {
                    data: 'sub_sku',
                    name: 'v.sub_sku'
                },
                {
                    data: 'purchase_quantity',
                    name: 'purchase_quantity',
                    searchable: false
                },
                {
                    data: 'total_quantity_sold',
                    name: 'total_quantity_sold',
                    searchable: false
                },
                {
                    data: 'total_quantity_transfered',
                    name: 'total_quantity_transfered',
                    searchable: false
                },
                {
                    data: 'total_quantity_returned',
                    name: 'total_quantity_returned',
                    searchable: false
                },
                {
                    data: 'current_stock',
                    name: 'current_stock',
                    searchable: false
                },
                {
                    data: 'stock_price',
                    name: 'stock_price',
                    searchable: false
                }
            ],
            fnDrawCallback: function(oSettings) {
                __currency_convert_recursively($('#supplier_stock_report_table'));
            },
        });

        $('#sr_location_id').change(function() {
            supplier_stock_report_table.ajax.reload();
        });

        $('#contact_id').change(function() {
            if ($(this).val()) {
                window.location = "{{ url('/contacts') }}/" + $(this).val();
            }
        });

        $('a[href="#sales_tab"]').on('shown.bs.tab', function(e) {
            sell_table.ajax.reload();
        });

        //Date picker
        $('#discount_date').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });

        $(document).on('submit', 'form#add_discount_form, form#edit_discount_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();

            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        $('div#add_discount_modal').modal('hide');
                        $('div#edit_ledger_discount_modal').modal('hide');
                        toastr.success(result.msg);
                        form[0].reset();
                        form.find('button[type="submit"]').removeAttr('disabled');
                        get_contact_ledger();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        $(document).on('click', 'button.delete_ledger_discount', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();

                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                get_contact_ledger();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        $('#assigned_users_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\ContactController::class, 'listAssign'], [$contact->id]) }}", 
                data: function(d) {
                    d.project_id = '{{ $project_id }}';
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    orderable: false
                },
                { data: 'name', name: 'name' },
                { data: 'department', name: 'department' },
                { data: 'designation', name: 'designation' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: 3, render: $.fn.dataTable.render.text() }
            ]
        });
    });

    $(document).on('shown.bs.modal', '#edit_ledger_discount_modal', function(e) {
        $('#edit_ledger_discount_modal').find('#edit_discount_date').datetimepicker({
            format: moment_date_format + ' ' + moment_time_format,
            ignoreReadonly: true,
        });
    })

    $("input.transaction_types, input#show_payments").on('ifChanged', function(e) {
        get_contact_ledger();
    });

    $(document).on('change', 'input[name="ledger_format"]', function() {
        get_contact_ledger();
    })

    $(document).one('shown.bs.tab', 'a[href="#payments_tab"]', function() {
        get_contact_payments();
    })

    $(document).on('click', '#contact_payments_pagination a', function(e) {
        e.preventDefault();
        get_contact_payments($(this).attr('href'));
    })

    function get_contact_payments(url = null) {
        if (!url) {
            url =
                "{{ action([\App\Http\Controllers\ContactController::class, 'getContactPayments'], [$contact->id]) }}";
        }
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(result) {
                $('#contact_payments_div').fadeOut(400, function() {
                    $('#contact_payments_div')
                        .html(result).fadeIn(400);
                });
            },
        });
    }

    function get_contact_ledger() {

        var start_date = '';
        var end_date = '';
        var transaction_types = $('input.transaction_types:checked').map(function(i, e) {
            return e.value
        }).toArray();
        var show_payments = $('input#show_payments').is(':checked');
        var location_id = $('#ledger_location').val();

        if ($('#ledger_date_range').val()) {
            start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
        }

        var format = $('input[name="ledger_format"]:checked').val();
        var data = {
            start_date: start_date,
            transaction_types: transaction_types,
            show_payments: show_payments,
            end_date: end_date,
            format: format,
            location_id: location_id
        }
        $.ajax({
            url: '/contacts/ledger?contact_id={{ $contact->id }}',
            data: data,
            dataType: 'html',
            success: function(result) {
                $('#contact_ledger_div')
                    .html(result);
                __currency_convert_recursively($('#contact_ledger_div'));

                $('#ledger_table').DataTable({
                    searching: false,
                    ordering: false,
                    paging: false,
                    fixedHeader: false,
                    dom: 't'
                });
            },
        });
    }

    $(document).on('click', '#send_ledger', function() {
        var start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var format = $('input[name="ledger_format"]:checked').val();

        var location_id = $('#ledger_location').val();

        var url =
            "{{ action([\App\Http\Controllers\NotificationController::class, 'getTemplate'], [$contact->id, 'send_ledger']) }}" +
            '?start_date=' + start_date + '&end_date=' + end_date + '&format=' + format + '&location_id=' +
            location_id;

        $.ajax({
            url: url,
            dataType: 'html',
            success: function(result) {
                $('.view_modal')
                    .html(result)
                    .modal('show');
            },
        });
    })

    $(document).on('click', '#print_ledger_pdf', function() {
        var start_date = $('#ledger_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end_date = $('#ledger_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

        var format = $('input[name="ledger_format"]:checked').val();

        var location_id = $('#ledger_location').val();

        var url = $(this).data('href') + '&start_date=' + start_date + '&end_date=' + end_date + '&format=' +
            format + '&location_id=' + location_id;
        window.open(url);
    });
    $(document).ready(function() {
        var projects_table = $('#projects_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\Modules\Project\Http\Controllers\ProjectController::class, 'index']) }}?contact_id={{ $contact->id }}",
                data: function(d) {
                    d.status = $('#status_filter').val();
                    d.end_date = $('#end_date_filter').val();
                    d.category_id = $('#category_filter').val();
                    d.lead_id = $('#lead_filter').val();
                }
            },
            columns: [
                { data: 'name', name: 'name' },
                { data: 'customer', name: 'customer' },
                { data: 'lead', name: 'lead' }, // Make sure this is properly defined
                { data: 'status', name: 'status' },
                { data: 'end_date', name: 'end_date' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[4, 'asc']], // Order by end_date ascending
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search projects"
            }
        });
        $('.filter-control').on('change', function() {
            projects_table.ajax.reload();
        });

        $('#status_filter, #end_date_filter, #category_filter, #lead_filter').addClass('filter-control');
        $(document).on('click', '.delete_a_project', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            swal({
                title: "{{ __('messages.confirm_delete') }}",
                text: "{{ __('messages.confirm_delete_msg') }}",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: 'DELETE',
                        url: url,
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                projects_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
            });
        });
    });
</script>
@include('contact.partials.map_js')
@include('sale_pos.partials.sale_table_javascript')
@include('sale_pos.partials.sale_quotation_javascript')
@include('sale_pos.partials.sale_consignment_javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@if (in_array($contact->type, ['both', 'supplier']))
    <script src="{{ asset('js/purchase.js?v=' . $asset_v) }}"></script>
@endif
@include('documents_and_notes.document_and_note_js')
@if (!empty($contact_view_tabs))
    @foreach ($contact_view_tabs as $key => $tabs)
        @foreach ($tabs as $index => $value)
            @if (!empty($value['module_js_path']))
                @include($value['module_js_path'])
            @endif
        @endforeach
    @endforeach
@endif

<script type="text/javascript">
    $(document).ready(function() {
        $('#purchase_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function(start, end) {
                $('#purchase_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end
                .format(moment_date_format));
                purchase_table.ajax.reload();
            }
        );
        $('#purchase_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#purchase_list_filter_date_range').val('');
            purchase_table.ajax.reload();
        });
    });
</script>
@include('sale_pos.partials.subscriptions_table_javascript', ['contact_id' => $contact->id])
<script src="{{ asset('modules/productcatalogue/plugins/easy.qrcode.min.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#sell_list_filter_date_range').daterangepicker(
            dateRangeSettings,
            function (start, end) {
                $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                sell_return_table.ajax.reload();
            }
        );
        $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#sell_list_filter_date_range').val('');
            sell_return_table.ajax.reload();
        });

        sell_return_table = $('#sell_return_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader:false,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": "/sell-return",
                "data": function ( d ) {
                    if($('#sell_list_filter_date_range').val()) {
                        var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }

                    if($('#sell_list_filter_location_id').length) {
                        d.location_id = $('#sell_list_filter_location_id').val();
                    }
                    d.customer_id = $('#customer_id').val();

                    if($('#created_by').length) {
                        d.created_by = $('#created_by').val();
                    }
                }
            },
            columnDefs: [ {
                "targets": [7, 8],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'parent_sale', name: 'T1.invoice_no'},
                { data: 'name', name: 'contacts.name'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'payment_status', name: 'payment_status'},
                { data: 'final_total', name: 'final_total'},
                { data: 'payment_due', name: 'payment_due'},
                { data: 'action', name: 'action'}
            ],
            "fnDrawCallback": function (oSettings) {
                var total_sell = sum_table_col($('#sell_return_table'), 'final_total');
                $('#footer_sell_return_total').text(total_sell);
                
                $('#footer_payment_status_count_sr').html(__sum_status_html($('#sell_return_table'), 'payment-status-label'));

                var total_due = sum_table_col($('#sell_return_table'), 'payment_due');
                $('#footer_total_due_sr').text(total_due);

                __currency_convert_recursively($('#sell_return_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(2)').attr('class', 'clickable_td');
            }
        });
        $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by, #customer_id',  function() {
            sell_return_table.ajax.reload();
        });
    })

    $(document).ready(function() {
        var employee_id = {{ $contact->id }}; 
        var employeeLink = "{{ url('/business/' . $business_id . '/customer') }}/" +  $('#customer_id').val(); 
        var qrOptions = {
            text: employeeLink,
            margin: 4,
            width: 256,
            height: 256,
            quietZone: 20,
            colorDark: "#000000",
            colorLight: "#ffffffff"
        };

        // Generate QR code immediately on page load
        new QRCode(document.getElementById("employee_qrcode"), qrOptions);
        $('#employee_link').html('<a target="_blank" href="' + employeeLink + '">Customer Link</a>');
        $('#employee_download_image').removeClass('hide');
        $('#employee_qrcode').find('canvas').attr('id', 'employee_qr_canvas');
        $('#employee_download_image').click(function(e) {
            e.preventDefault();
            var link = document.createElement('a');
            link.download = 'employee_qrcode.png';
            link.href = document.getElementById('employee_qr_canvas').toDataURL();
            link.click();
        });
    });
</script>
<script type="text/javascript">
		$(function () {
			$('#follow_up_date_range').daterangepicker(
		        dateRangeSettings,
		        function (start, end) {
		            $('#follow_up_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
		            follow_up_datatable.ajax.reload();
		        }
		    );
		    $('#follow_up_date_range').on('cancel.daterangepicker', function(ev, picker) {
		        $('#follow_up_date_range').val('');
		        follow_up_datatable.ajax.reload();
		    });
		    $('#followup_category_id_filter').change(function(){
		    	follow_up_datatable.ajax.reload();
		    })

		    follow_up_datatable = $("#follow_up_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "80vh",
				scrollX: true,
				scrollCollapse: true,
		        ajax: {
		            url: "/crm/follow-ups",
		            data:function(d) {
		            	d.contact_id = $("#sell_list_filter_customer_id").val();
		            	d.assgined_to = $("#assgined_to_filter").val();
		            	d.status = $("#status_filter").val();
		            	d.schedule_type = $("#schedule_type_filter").val();
		            	d.follow_up_by = $("#follow_up_by_filter").val();
		            	d.followup_category_id = $("#followup_category_id_filter").val();

		            	if ($('#follow_up_date_range').val()) {
		            		d.start_date_time = $('#follow_up_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
		            		d.end_date_time = $('#follow_up_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
		            	}
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 7, 9],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[2, 'desc']],
		        columns: [
		        	{ data: 'action', name: 'action' },
		        	{
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
		        	{ data: 'contact', name: 'contacts.name' },
		        	{ data: 'start_datetime', name: 'start_datetime' },
		            { data: 'end_datetime', name: 'end_datetime' },
		            { data: 'status', name: 'crm_schedules.status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'followup_category', name: 'C.name' },
		            { data: 'users', name: 'users' },
		            { data: 'description', name: 'description'},
		            { data: 'additional_info', name: 'additional_info' },
		            { data: 'title', name: 'title' },
		            { data: 'added_by', name: 'added_by' },
		            { data: 'added_on', name: 'crm_schedules.created_at' },
		            { data: 'phone_number', name: 'contact.phone_number' },
			    { data: 'address', name: 'contact.address' }
		        ],
		        "fnDrawCallback": function( oSettings ) {
		        	__show_date_diff_for_human($("#follow_up_table"));

		        	$('a.view_schedule_log').click(function(){
		        		getScheduleLog($(this).data('schedule_id'), true);
		        	})
			    },
		        "footerCallback": function ( row, data, start, end, display ) {
		        	$('.footer_follow_up_status_count').html(__count_status(data, 'status'));
		            $('.footer_follow_up_type_count').html(__count_status(data, 'schedule_type'));
		        }
			});

			recursive_follow_up_table = $("#recursive_follow_up_table").DataTable({
				processing: true,
		        serverSide: true,
		        scrollY: "80vh",
				scrollX: true,
				scrollCollapse: true,
		        ajax: {
		            url: "/crm/follow-ups",
		            data:function(d) {
		            	d.assgined_to = $("#assgined_to_filter").val();
		            	d.is_recursive = 1;
		            }
		        },
		        columnDefs: [
		            {
		                targets: [0, 6],
		                orderable: false,
		                searchable: false,
		            },
		        ],
		        aaSorting: [[2, 'desc']],
		        columns: [
		        	{ data: 'action', name: 'action' },
		        	{
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
		            { data: 'status', name: 'crm_schedules.status' },
		            { data: 'schedule_type', name: 'schedule_type' },
		            { data: 'followup_category', name: 'C.name' },
		            { data: 'follow_up_by', name: 'crm_schedules.follow_up_by' },
		            { data: 'recursion_days', name: 'crm_schedules.recursion_days' },
		            { data: 'users', name: 'users' },
		            { data: 'description', name: 'description'},
		            { data: 'additional_info', name: 'additional_info' },
		            { data: 'title', name: 'title' },
		            { data: 'added_by', name: 'added_by' },
		            { data: 'added_on', name: 'crm_schedules.created_at' },
		        ]
			});

			$(document).on('change', '#sell_list_filter_customer_id, #assgined_to_filter, #status_filter, #schedule_type_filter, #follow_up_by_filter', function() {
			    follow_up_datatable.ajax.reload();
			});
			
			// Set default date from get parameter
	        @if(!empty($default_start_date) && !empty($default_end_date))
	            $('#follow_up_date_range').val({{$default_start_date . ' - ' . $default_end_date}});
	            $('#follow_up_date_range').data('daterangepicker').setStartDate('{{$default_start_date}}');
	            $('#follow_up_date_range').data('daterangepicker').setEndDate('{{$default_end_date}}');
	            follow_up_datatable.ajax.reload();
	        @endif
		        
		});
	</script>