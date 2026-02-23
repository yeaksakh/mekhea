<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class IndexForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createIndex($moduleName, $moduleType, $title, $type)
    {
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/index.blade.php");
        $moduleNameLower = strtolower($moduleName);

        $assign_table = '';
        $supplier_table = '';
        $customer_table = '';
        $designation_table = '';
        $department_table = '';
        $product_table = '';
        $data_table = '';

        $assign_to = '';
        $supplier_id = '';
        $customer_id = '';
        $designation_id = '';
        $department_id = '';
        $product_id = '';
        $date = '';

        if (!is_null($moduleType) && is_array($moduleType)) {
            // Check if 'user' type is selected
            if (in_array('user', $moduleType)) {
                $assign_table = "
                    <th>@lang('{$moduleNameLower}::lang.assign_to')</th>
                ";
                $assign_to = "{ data: 'assign_to', name: 'assign_to' },";
            }

            // Check if 'supplier' type is selected
            if (in_array('supplier', $moduleType)) {
                $supplier_table = "
                    <th>@lang('{$moduleNameLower}::lang.supplier_id')</th>
                ";
                $supplier_id = "{ data: 'supplier_id', name: 'supplier_id' },";
            }
            if (in_array('department', $moduleType)) {
                $department_table = "
                    <th>@lang('{$moduleNameLower}::lang.department_id')</th>
                ";
                $department_id = "{ data: 'department_id', name: 'department_id' },";
            }
            if (in_array('designation', $moduleType)) {
                $department_table = "
                    <th>@lang('{$moduleNameLower}::lang.designation_id')</th>
                ";
                $designation_id = "{ data: 'designation_id', name: 'designation_id' },";
            }

            // Check if 'customer' type is selected
            if (in_array('customer', $moduleType)) {
                $customer_table = "
                    <th>@lang('{$moduleNameLower}::lang.customer_id')</th>
                ";
                $customer_id = "{ data: 'customer_id', name: 'customer_id' },";
            }

            // Check if 'product' type is selected
            if (in_array('product', $moduleType)) {
                $product_table = "
                    <th>@lang('{$moduleNameLower}::lang.product_id')</th>
                ";
                $product_id = "{ data: 'product_id', name: 'product_id' },";
            }
        }

        $datatable = [];
        $jsdata = [];
        if ((!is_null($title) && is_array($title))) {
            foreach ($title as $index) {
                if (!is_null($index)) {
                    $datatable[] = "
                        <th>@lang('{$moduleNameLower}::lang.{$index}')</th>
                    ";
                    $jsdata[] = "
                        { data: '{$index}', name: '{$index}', className: 'table-ellipsis' },
                    ";
                }
            }
        }

        $datatable = implode("\n", $datatable);
        $jsdata = implode("\n", $jsdata);

        $filters = [];
        $script_filter = [];
        $script_date = [];
        $hasDateFilter = false;

        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                switch ($fieldType) {
                    case 'date':
                        $filters[] = <<<HTML
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}') . ':') !!}
                                    {!! Form::text('date_range', null, [
                                        'placeholder' => __('lang_v1.select_a_date_range'),
                                        'class' => 'form-control',
                                        'id' => '{$fieldTitle}',
                                        'readonly',
                                    ]) !!}
                                </div>
                            </div>    
                        HTML;
                        $script_filter[] = "
                            if($('#{$fieldTitle}').val()) {
                                var start = $('#{$fieldTitle}').data('daterangepicker').startDate.format('YYYY-MM-DD');
                                var end = $('#{$fieldTitle}').data('daterangepicker').endDate.format('YYYY-MM-DD');
                                d.start_date = start;
                                d.end_date = end;
                            }
                        ";
                        $script_date[] = "
                            $('#{$fieldTitle}').daterangepicker(
                                dateRangeSettings,
                                function(start, end) {
                                    $('#{$fieldTitle}').val(start.format(moment_date_format) + ' ~ ' + end.format(
                                        moment_date_format));
                                        table.ajax.reload();
                                }
                            );
                            $('#{$fieldTitle}').on('cancel.daterangepicker', function(ev, picker) {
                                $('#{$fieldTitle}').val('');
                                table.ajax.reload();
                            });
                        ";
                        $hasDateFilter = true;
                        break;
                    case 'users':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$users, null, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                                d.{$fieldTitle} = $('#{$fieldTitle}').val();
                            ";
                        break;
                    case 'departments':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('employeecontractb1::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$departments, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.select_option')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                                d.{$fieldTitle} = $('#{$fieldTitle}').val();
                            ";
                        break;
                    case 'designations':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('employeecontractb1::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$designations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.select_option')]); !!}
                                </div>
                            </div>

                        HTML;
                        $script_filter[] = "
                                d.{$fieldTitle} = $('#{$fieldTitle}').val();
                            ";
                        break;
                    case 'supplier':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$supplier, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                                d.{$fieldTitle} = $('#{$fieldTitle}').val();
                            ";
                        break;
                    case 'customer':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$customer, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                    case 'product':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$product, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                    case 'business_location':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                    case 'lead':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select('{$fieldTitle}', \$leads, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                    case 'audit':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select(
                                        '{$fieldTitle}', 
                                        ['Done' => __('Done'), 'Pending' => __('Pending'), 'Problem' => __('Problem')],
                                        null, 
                                        ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]
                                    ) !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                    case 'audit':
                        $filters[] = <<<HTML
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}').':') !!}
                                    {!! Form::select(
                                        '{$fieldTitle}', 
                                        ['Done' => __('Done'), 'Pending' => __('Pending'), 'Problem' => __('Problem')],
                                        null, 
                                        ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.please_select')]
                                    ) !!}
                                </div>
                            </div>
                        HTML;
                        $script_filter[] = "
                            d.{$fieldTitle} = $('#{$fieldTitle}').val();
                        ";
                        break;
                }
            }
        }

        if (!$hasDateFilter) {
            $filters[] = <<<HTML
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('{$moduleNameLower}_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', null, [
                            'placeholder' => __('lang_v1.select_a_date_range'),
                            'class' => 'form-control',
                            'id' => '{$moduleNameLower}_date_range',
                            'readonly',
                        ]) !!}
                    </div>  
                </div>
            HTML;
            $script_filter[] = "
                if($('#{$moduleNameLower}_date_range').val()) {
                    var start = $('#{$moduleNameLower}_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#{$moduleNameLower}_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
            ";
            $script_date[] = "
                $('#{$moduleNameLower}_date_range').daterangepicker(
                    dateRangeSettings,
                    function(start, end) {
                        $('#{$moduleNameLower}_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                            moment_date_format));
                            table.ajax.reload();
                    }
                );
                $('#{$moduleNameLower}_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#{$moduleNameLower}_date_range').val('');
                    table.ajax.reload();
                });
            ";
        }
        $filters[] = <<<HTML
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('category_id', __('{$moduleNameLower}::lang.category') . ':') !!}
                        {!! Form::select('category_id', ['' => 'All Categories'] + \$category, null, ['class' => 'form-control select2', 'id' => 'category_id', 'style' => 'width:100%;']) !!}
                    </div>
                </div>
            HTML;

        $reload_table = [];
        if (!is_null($title) && is_array($title)) {
            foreach ($title as $index => $fieldTitle) {
                if ($fieldType !== 'date') {
                    $reload_table[] = "
                        $(document).on('change', '#{$fieldTitle}', function() {
                            table.ajax.reload();
                        });
                    ";
                }
            }
        }

        $filters = implode("\n", $filters);
        $script_filter = implode("\n", $script_filter);
        $script_date = implode("\n", $script_date);
        $reload_table = implode("\n", $reload_table);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            @extends('layouts.app')
            @section('title', __('{$moduleNameLower}::lang.{$moduleName}'))
            @section('content')
                @includeIf('{$moduleNameLower}::layouts.nav')
                <section class="content-header no-print">
                    <h1>@lang('{$moduleNameLower}::lang.{$moduleNameLower}')</h1>
                </section>
                <section class="content no-print">
                    @component('components.filters', ['title' => __('report.filters')])
                        {$filters}
                    @endcomponent
                    @component('components.widget', ['class' => 'box-primary', 'title' => __('{$moduleNameLower}::lang.all_{$moduleName}')])
                        @slot('tool')
                            <div class="box-tools">
                                <button type="button" class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full pull-right btn-modal" data-href="{{action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'create'])}} "
                                    data-container="#{$moduleName}_modal">
                                    <i class="fa fa-plus"></i> @lang('messages.add')
                                </button>
                            </div>
                        @endslot
                        <table class="table table-bordered table-striped" id="{$moduleName}_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('messages.action')</th>
                                    <th>@lang('{$moduleNameLower}::lang.category')</th>
                                    <th>@lang('{$moduleNameLower}::lang.create_by')</th>
                                    {$assign_table}
                                    {$supplier_table}
                                    {$department_table}
                                    {$designation_table}
                                    {$customer_table}
                                    {$product_table}
                                    {$datatable}
                                </tr>
                            </thead>
                        </table>
                    @endcomponent

                </section>
                <div class="modal fade " id="{$moduleName}_modal" tabindex="-1" role="dialog" aria-labelledby="create{$moduleName}ModalLabel" ></div>
            @stop

            @section('javascript')
                <script type="text/javascript">
                    $(document).ready(function() {
                        {$script_date}
                        var table = $('#{$moduleName}_table').DataTable({
                            processing: true,
                            serverSide: true,
                            scrollX:true,
                            autoWidth: false,
                            ajax: {
                                url: "{{ action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'index']) }}",
                                data: function(d) {
                                    d.category_id = $('#category_id').val();
                                    {$script_filter}
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
                                { data: 'action', name: 'action', orderable: false, searchable: false },
                                { data: 'category', name: 'category', className: 'table-ellipsis'},
                                { data: 'create_by', name: 'create_by', className: 'table-ellipsis'},
                                {$assign_to}
                                {$supplier_id}
                                {$department_id}
                                {$designation_id}
                                {$customer_id}
                                {$product_id}
                                {$date}
                                {$jsdata}
                                
                            ],
                        });
                        $('#category_id').on('change', function() {
                        table.ajax.reload(null, false); // Reload table without resetting the paging
                    });

                        {$reload_table}
                        $('#{$moduleName}_modal').on('shown.bs.modal', function(e) {
                            $('#{$moduleName}_modal .select2').select2();
                            $('form#add_{$moduleName}_form #start_date, form#add_{$moduleName}_form #end_date').datepicker({
                                autoclose: true,
                            });

                            tinymce.init({
                                selector: '#{$moduleName}_modal textarea.{$moduleName}_description',
                            });
                        });

                        $('#{$moduleName}_modal').on('hidden.bs.modal', function() {
                                tinymce.remove('#{$moduleName}_modal textarea.{$moduleName}_description');
                        });
                            
                        $(document).on('submit', 'form#add_{$moduleName}_form, #edit_{$moduleName}_form, #audit_{$moduleName}_form', function(e) {
                            e.preventDefault();
                            tinymce.triggerSave();
                            var formData = new FormData(this);
                            $.ajax({
                                method: $(this).attr('method'),
                                url: $(this).attr('action'),
                                dataType: 'json',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(result) {
                                    if (result.success) {
                                        $('div#{$moduleName}_modal').modal('hide');
                                        table.ajax.reload();
                                        toastr.success(result.msg);
                                    } else {
                                        toastr.error(result.msg);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('Failed to save {$moduleName}:', error);
                                    toastr.error('Failed to save {$moduleName}');
                                }
                            });
                        });

                        $(document).on('click', '.delete-{$moduleName}', function(e) {
                            e.preventDefault();
                            var url = $(this).data('href');
                            if (confirm('Are you sure you want to delete this {$moduleName}?')) {
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
                                        console.error('Failed to delete {$moduleName}:', error);
                                        toastr.error('Failed to delete {$moduleName}');
                                    }
                                });
                            }
                        });
                    });
                </script>
            @endsection
            EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
