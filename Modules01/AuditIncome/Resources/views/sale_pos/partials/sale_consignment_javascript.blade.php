<script type="text/javascript">
$(document).ready(function () {
    // Ensure CSRF token is included in AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Date range picker initialization
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings, // Ensure this is defined elsewhere
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_consignment.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function (ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_consignment.ajax.reload();
    });

    // Initialize DataTable
    var sell_consignment = $('#sell_consignment').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: false,
        aaSorting: [[1, 'desc']], // Sort by transaction_date
        ajax: {
            url: '/sells/draft-dt?is_consignment=1',
            data: function (d) {
                console.log('AJAX Data:', d); // Debugging: Log AJAX parameters
                if ($('#sell_list_filter_date_range').val()) {
                    var start = $('#sell_list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#sell_list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                if ($('#sell_list_filter_location_id').length) {
                    d.location_id = $('#sell_list_filter_location_id').val();
                }
                d.customer_id = $('#sell_list_filter_customer_id').val();
                d.audit_status = $('#audit_status').val();
                if ($('#created_by').length) {
                    d.created_by = $('#created_by').val();
                }
            }
        },
        columnDefs: [{
            targets: 8, // Action column
            orderable: false,
            searchable: false
        }],
        columns: [
            {
                data: null,
                name: 'id',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'audit_status', name: 'audit_status' },
            { data: 'mobile', name: 'contacts.mobile' },
            { data: 'business_location', name: 'bl.name' },
            { data: 'total_items', name: 'total_items', searchable: false },
            { data: 'added_by', name: 'added_by' },
            { data: 'action', name: 'action' }
        ],
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#sell_consignment')); // Ensure this function is defined
        }
    });

    // Filter change handlers
    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #created_by, #audit_status', function () {
        console.log('Filter changed:', this.id, this.value); // Debugging: Log filter changes
        sell_consignment.ajax.reload();
    });

    // Convert to Proforma action
    $(document).on('click', 'a.convert-to-proforma', function (e) {
        e.preventDefault();
        swal({
            title: LANG.sure, // Ensure LANG.sure is defined
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then(confirm => {
            if (confirm) {
                var url = $(this).attr('href');
                $.ajax({
                    method: 'GET',
                    url: url,
                    dataType: 'json',
                    success: function (result) {
                        if (result.success == true) {
                            toastr.success(result.msg);
                            sell_consignment.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function (xhr, status, error) {
                        toastr.error('Error converting to proforma: ' + error);
                    }
                });
            }
        });
    });
});
</script>