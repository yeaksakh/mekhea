@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            attendance_table = $('#attendance_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'index']) }}",
                    "data": function(d) {
                        // Filter by the specific user
                        d.employee_id = {{ $user->id }};
                        if ($('#date_range').val()) {
                            var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    }
                },
                columns: [
                    { data: 'date', name: 'clock_in_time' },
                    { data: 'user', name: 'user' },
                    { data: 'clock_in', name: 'clock_in', orderable: false, searchable: false },
                    { data: 'clock_out', name: 'clock_out', orderable: false, searchable: false },
                    { data: 'work_duration', name: 'work_duration', orderable: false, searchable: false },
                    { data: 'ip_address', name: 'ip_address' },
                    { data: 'shift_name', name: 'es.name' },
                    @can('essentials.crud_all_attendance')
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    @endcan
                ],
            });

            $('#date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range').val('');
                attendance_table.ajax.reload();
            });

            $(document).on('change', '#date_range', function() {
                attendance_table.ajax.reload();
            });

            $(document).on('submit', 'form#attendance_form', function(e) {
                e.preventDefault();
                if($(this).valid()) {
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var data = $(this).serialize();
                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $('div#attendance_modal').modal('hide');
                                $('div#edit_attendance_modal').modal('hide');
                                toastr.success(result.msg);
                                attendance_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });

            $(document).on('change', '#date_range', function() {
                get_attendance_summary();
            });

            get_attendance_summary();

            function get_attendance_summary() {
                $('#user_attendance_summary').addClass('hide');
                var user_id = {{ $user->id }};
                
                var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                $.ajax({
                    url: '{{ action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'getUserAttendanceSummary']) }}?user_id=' + user_id + '&start_date=' + start + '&end_date=' + end,
                    dataType: 'html',
                    success: function(response) {
                        $('#total_work_hours').html(response);
                        $('#user_attendance_summary').removeClass('hide');
                    },
                });
            }

            $(document).on('click', 'button.delete-attendance', function() {
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
                                    attendance_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });

            $('#edit_attendance_modal').on('hidden.bs.modal', function(e) {
                $('#edit_attendance_modal #clock_in_time').data("DateTimePicker").destroy();
                $('#edit_attendance_modal #clock_out_time').data("DateTimePicker").destroy();
            });

            $('#attendance_modal').on('shown.bs.modal', function(e) {
                $('#attendance_modal .select2').select2();
            });
            $('#edit_attendance_modal').on('shown.bs.modal', function(e) {
                $('#edit_attendance_modal .select2').select2();
                $('#edit_attendance_modal #clock_in_time, #edit_attendance_modal #clock_out_time').datetimepicker({
                    format: moment_date_format + ' ' + moment_time_format,
                    ignoreReadonly: true,
                });

                validate_clockin_clock_out = {
                    url: '/hrm/validate-clock-in-clock-out',
                    type: 'post',
                    data: {
                        user_ids: function() {
                            return $('#employees').val();
                        },
                        clock_in_time: function() {
                            return $('#clock_in_time').val();
                        },
                        clock_out_time: function() {
                            return $('#clock_out_time').val();
                        },
                        attendance_id: function() {
                            if($('form#attendance_form #attendance_id').length) {
                               return $('form#attendance_form #attendance_id').val();
                            } else {
                                return '';
                            }
                        },
                    },
                };

                $('form#attendance_form').validate({
                    rules: {
                        clock_in_time: {
                            remote: validate_clockin_clock_out,
                        },
                        clock_out_time: {
                            remote: validate_clockin_clock_out,
                        },
                    },
                    messages: {
                        clock_in_time: {
                            remote: "{{__('essentials::lang.clock_in_clock_out_validation_msg')}}",
                        },
                        clock_out_time: {
                            remote: "{{__('essentials::lang.clock_in_clock_out_validation_msg')}}",
                        },
                    },
                });
            });
        });
    </script>
@endsection