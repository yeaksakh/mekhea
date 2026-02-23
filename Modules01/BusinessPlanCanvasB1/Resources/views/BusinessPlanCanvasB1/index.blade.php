@extends('layouts.app')
@section('title', __('businessplancanvasb1::lang.BusinessPlanCanvasB1'))
@section('content')
    @includeIf('businessplancanvasb1::layouts.nav')
    <section class="content-header no-print">
        <h1>@lang('businessplancanvasb1::lang.businessplancanvasb1')</h1>
    </section>
    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('businessplancanvasb1_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('date_range', null, [
                'placeholder' => __('lang_v1.select_a_date_range'),
                'class' => 'form-control',
                'id' => 'businessplancanvasb1_date_range',
                'readonly',
            ]) !!}
        </div>  
    </div>
        @endcomponent
        @component('components.widget', ['class' => 'box-primary', 'title' => __('businessplancanvasb1::lang.all_BusinessPlanCanvasB1')])
            @slot('tool')
                <div class="box-tools">
                    <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{ route('BusinessPlanCanvasB1.create') }}"
                        data-container=".BusinessPlanCanvasB1_modal">
                        <i class="fa fa-plus"></i> @lang('messages.add')
                    </button>
                </div>
            @endslot
            <table class="table table-bordered table-striped" id="BusinessPlanCanvasB1_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>@lang('businessplancanvasb1::lang.title')</th>
                        <th>@lang('businessplancanvasb1::lang.create_by')</th>
                        
                        
                        
                    
                        <th>@lang('messages.action')</th>
                    </tr>
                </thead>
            </table>
        @endcomponent

    </section>
    <div class="modal fade BusinessPlanCanvasB1_modal" tabindex="-1" role="dialog" aria-labelledby="createBusinessPlanCanvasB1ModalLabel" aria-hidden="true"></div>
@stop

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            
                $('#businessplancanvasb1_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#businessplancanvasb1_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#businessplancanvasb1_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#businessplancanvasb1_date_range').val('');
                    table.ajax.reload();
                });
            
            var table = $('#BusinessPlanCanvasB1_table').DataTable({
                processing: true,
                serverSide: true,
                scrollX:true,
                autoWidth: false,
                ajax: {
                    url: "{{ action([\Modules\BusinessPlanCanvasB1\Http\Controllers\BusinessPlanCanvasB1Controller::class, 'index']) }}",
                    data: function(d) {
                        
                if($('#businessplancanvasb1_date_range').val()) {
                    var start = $('#businessplancanvasb1_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#businessplancanvasb1_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            
                    }
                },
                order: [[1, 'desc']],
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
                    { data: 'title', name: 'title' },
                    { data: 'create_by', name: 'create_by' },
                    
                    
                    
                    
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
            });

                    

            function initializeSelect2InModal() {
                $('.BusinessPlanCanvasB1_modal .select2').select2({
                    width: '100%',
                    dropdownParent: $(".BusinessPlanCanvasB1_modal")
                });
            }

            $(document).on('click', '.btn-modal', function(e) {
                e.preventDefault();
                var href = $(this).data('href'); // Correctly define the href variable
                var container = $(this).data('container');
                $.ajax({
                    url: href,
                    type: 'GET',
                    success: function(data) {
                        $(container).html(data);
                        $(container).modal('show');
                        initializeSelect2InModal(); // Initialize Select2 after loading content into modal
                    },
                    error: function(xhr) {
                        alert('An error occurred while loading the modal.');
                    }
                });
            });
                
            $(document).on('submit', '#add_BusinessPlanCanvasB1_form, #edit_BusinessPlanCanvasB1_form, #audit_BusinessPlanCanvasB1_form', function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: formData, // Use FormData object
                    processData: false, // Prevent jQuery from processing the data
                    contentType: false, // Prevent jQuery from setting the content type
                    success: function(result) {
                        if (result.success) {
                            $('.BusinessPlanCanvasB1_modal').modal('hide');
                            table.ajax.reload();
                            toastr.success(result.msg);
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to save BusinessPlanCanvasB1:', error);
                        toastr.error('Failed to save BusinessPlanCanvasB1');
                    }
                });
            });

            $(document).on('click', '.delete-BusinessPlanCanvasB1', function(e) {
                e.preventDefault();
                var url = $(this).data('href');
                if (confirm('Are you sure you want to delete this BusinessPlanCanvasB1?')) {
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        success: function(result) {
                            if (result.success) {
                                table.ajax.reload();
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to delete BusinessPlanCanvasB1:', error);
                            toastr.error('Failed to delete BusinessPlanCanvasB1');
                        }
                    });
                }
            });
        });
    </script>
@endsection