@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        {{-- <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('expense.expenses')</h1> --}}
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('auditexpense::lang.auditexpense')</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @component('components.filters', ['title' => __('report.filters')])
                    @if (auth()->user()->can('all_expense.access'))
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                                {!! Form::select('location_id', $business_locations, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                ]) !!}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                {!! Form::label('expense_for', __('expense.expense_for') . ':') !!}
                                {!! Form::select('expense_for', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('expense_contact_filter', __('contact.contact') . ':') !!}
                                {!! Form::select('expense_contact_filter', $contacts, null, [
                                    'class' => 'form-control select2',
                                    'style' => 'width:100%',
                                    'placeholder' => __('lang_v1.all'),
                                ]) !!}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_category_id', __('expense.expense_category') . ':') !!}
                            {!! Form::select('expense_category_id', $categories, null, [
                                'placeholder' => __('report.all'),
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'expense_category_id',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_sub_category_id_filter', __('product.sub_category') . ':') !!}
                            {!! Form::select('expense_sub_category_id_filter', $sub_categories, null, [
                                'placeholder' => __('report.all'),
                                'class' => 'form-control select2',
                                'style' => 'width:100%',
                                'id' => 'expense_sub_category_id_filter',
                            ]) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range', null, [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'id' => 'expense_date_range',
                                'readonly',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
                            {!! Form::select(
                                'expense_payment_status',
                                ['paid' => __('lang_v1.paid'), 'due' => __('lang_v1.due'), 'partial' => __('lang_v1.partial')],
                                null,
                                ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')],
                            ) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('audit_status', __('lang_v1.audit_status') . ':') !!}
                            {!! Form::select('audit_status', $audit_statuses, null, [
                                'class' => 'form-control select2',
                                'id' => 'audit_status',
                                'style' => 'width:100%',
                                'placeholder' => __('lang_v1.all'),
                            ]) !!}
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>

        {{-- @component('components.widget', ['class' => 'box-solid'])
            <!-- Use a flex container with nowrap and horizontal scrolling -->
            <div class="d-flex flex-nowrap overflow-auto" style="gap: 60px;">
                <!-- List Purchases -->
                @if (auth()->user()->can('purchase.view') || auth()->user()->can('view_own_purchase'))
                    <button
                        onclick="window.location.href='{{ action([\App\Http\Controllers\PurchaseController::class, 'index']) }}'"
                        class="btn btn-primary rounded-2">
                        <i class="fas fa-list fa-fw"></i> {{ __('purchase.list_purchase') }}
                    </button>
                @endif

                <!-- Suppliers -->
                @if (auth()->user()->can('supplier.view') || auth()->user()->can('supplier.view_own'))
                    <button
                        onclick="window.location.href='{{ action([\App\Http\Controllers\ContactController::class, 'index'], ['type' => 'supplier']) }}'"
                        class="btn btn-success rounded-2">
                        <i class="fas fa-hiking fa-fw"></i> {{ __('report.supplier') }}
                    </button>
                @endif
                <!-- Expense Categories -->
                @if (auth()->user()->can('expense.add') || auth()->user()->can('expense.edit'))
                    <button
                        onclick="window.location.href='{{ action([\App\Http\Controllers\ExpenseCategoryController::class, 'index']) }}'"
                        class="btn btn-warning rounded-2">
                        <span>
                            <i class="fa fa-folder-open"></i>
                        </span>
                        {{ __('expense.expense_categories') }}
                    </button>
                @endif
            </div>
        @endcomponent --}}

        {{-- Image Path: public/modules/auditexpense/icons/auditexpense.jpg --}}
        <img src="{{ asset('modules/auditexpense/images/audit_expense.jpg') }}" alt="Audit Expense Icon" style="width:100%; height: 150px; margin-bottom: 10px;">

        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('expense.all_expenses')])
                    @can('expense.add')
                        @slot('tool')
                            <div class="box-tools">
                                <a class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right"
                                    href="{{ action([\Modules\AuditExpense\Http\Controllers\ExpenseController::class, 'create']) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 5l0 14" />
                                        <path d="M5 12l14 0" />
                                    </svg> @lang('messages.add')
                                </a>
                            </div>
                        @endslot
                    @endcan
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="expense_table">
                            <thead>
                                <tr>
                                    <th>@lang('messages.action')</th>
                                    <th>#</th>
                                    <th>@lang('messages.date')</th>
                                    <th>@lang('purchase.ref_no')</th>
                                    <th>@lang('lang_v1.recur_details')</th>
                                    <th>@lang('expense.expense_category')</th>
                                    <th>@lang('product.sub_category')</th>
                                    <th>@lang('business.location')</th>
                                    <th>@lang('sale.audit_status')</th>
                                    <th>@lang('sale.payment_status')</th>
                                    <th>@lang('product.tax')</th>
                                    <th>@lang('sale.total_amount')</th>
                                    <th>@lang('purchase.payment_due')</th>
                                    <th>@lang('expense.expense_for')</th>
                                    <th>@lang('lang_v1.suppliers')</th>
                                    <th>@lang('expense.expense_note')</th>
                                    <th>@lang('lang_v1.added_by')</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr class="bg-gray font-17 text-center footer-total">
                                    <td colspan="9"><strong>@lang('sale.total'):</strong></td>
                                    <td class="footer_payment_status_count"></td>
                                    <td></td>
                                    <td class="footer_expense_total"></td>
                                    <td class="footer_total_due"></td>
                                    <td colspan="4"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endcomponent
            </div>
        </div>

    </section>
    <!-- /.content -->
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade view_expent_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
@stop
@section('javascript')
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    <script>
        $(document).ready(function() {
            // Date filter for expense table
            if ($('#expense_date_range').length == 1) {
                $('#expense_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#expense_date_range').val(
                            start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                        );
                        expense_table.ajax.reload();
                        if (typeof expense_request_table !== 'undefined') {
                            expense_request_table.ajax.reload();
                        }
                    }
                );

                $('#expense_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#product_sr_date_filter').val('');
                    expense_table.ajax.reload();
                    if (typeof expense_request_table !== 'undefined') {
                        expense_request_table.ajax.reload();
                    }
                });
            }

            // Expense table
            let expense_table;
            if ($.fn.DataTable.isDataTable('#expense_table')) {
                $('#expense_table').DataTable().destroy();
            }
            expense_table = $('#expense_table').DataTable({
                processing: true,
                serverSide: true,
                fixedHeader: false,
                aaSorting: [
                    [1, 'desc']
                ],
                ajax: {
                    url: '/auditexpense/AuditExpense-expenses',
                    data: function(d) {
                        d.expense_for = $('select#expense_for').val();
                        d.contact_id = $('select#expense_contact_filter').val();
                        d.location_id = $('select#location_id').val();
                        d.expense_category_id = $('select#expense_category_id').val();
                        d.audit_status = $('select#audit_status').val();
                        d.expense_sub_category_id = $('select#expense_sub_category_id_filter').val();
                        d.payment_status = $('select#expense_payment_status').val();
                        d.start_date = $('input#expense_date_range')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('input#expense_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    },
                },
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: null,
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'transaction_date',
                        name: 'transaction_date'
                    },
                    {
                        data: 'ref_no',
                        name: 'ref_no'
                    },
                    {
                        data: 'recur_details',
                        name: 'recur_details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'category',
                        name: 'ec.name'
                    },
                    {
                        data: 'sub_category',
                        name: 'esc.name'
                    },
                    {
                        data: 'location_name',
                        name: 'bl.name'
                    },
                    {
                        data: 'audit_status',
                        name: 'audit_status'
                    },
                    {
                        data: 'payment_status',
                        name: 'payment_status',
                        orderable: false
                    },
                    {
                        data: 'tax',
                        name: 'tr.name'
                    },
                    {
                        data: 'final_total',
                        name: 'final_total'
                    },
                    {
                        data: 'payment_due',
                        name: 'payment_due'
                    },
                    {
                        data: 'expense_for',
                        name: 'expense_for'
                    },
                    {
                        data: 'contact_name',
                        name: 'c.name'
                    },
                    {
                        data: 'additional_notes',
                        name: 'additional_notes'
                    },
                    {
                        data: 'added_by',
                        name: 'usr.first_name'
                    }
                ],
                fnDrawCallback: function(row, data, start, end, display) {
                    var expense_total = sum_table_col($('#expense_table'), 'final-total');
                    var total_due = sum_table_col($('#expense_table'), 'payment_due');

                    $('.footer_expense_total').html(__currency_trans_from_en(expense_total));
                    $('.footer_total_due').html(__currency_trans_from_en(total_due));

                    $('.footer_payment_status_count').html(
                        __sum_status_html($('#expense_table'), 'payment-status')
                    );
                },
                createdRow: function(row, data, dataIndex) {
                    $(row)
                        .find('td:eq(4)')
                        .attr('class', 'clickable_td');
                },
            });
            $(document).on('change', '#location_id, #expense_for, #expense_contact_filter, #expense_category_id, #expense_sub_category_id_filter, #expense_payment_status, #audit_status', function() {
                expense_table.ajax.reload();
            });

            $(document).on('submit', 'form#edit_audit_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var data = form.serialize();

                $.ajax({
                    method: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    beforeSend: function(xhr) {
                        __disable_submit_button(form.find('button[type="submit"]'));
                    },
                    success: function(result) {
                        if (result.success == true) {
                            $('div.view_modal').modal('hide');
                            toastr.success(result.msg);
                            expense_table.ajax.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                });
            });
        });

        $(document).on('click', 'a.view_expent_show_modal', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var href = $(this).attr('href');
            var container = $('.view_expent_modal');

            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $(container)
                        .html(result)
                        .modal('show');
                    __currency_convert_recursively(container);
                },
            });
        });
    </script>
@endsection
