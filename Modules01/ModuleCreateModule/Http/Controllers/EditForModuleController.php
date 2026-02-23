<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class EditForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createEdit($moduleName, $moduleType, $title, $type)
    {
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/edit.blade.php");
        $moduleNameLower = strtolower($moduleName);

        $assignToField = '';
        $supplierIdField = '';
        $customerIdField = '';
        $departmentIdField = '';
        $designationIdField = '';
        $productIdField = '';
        $dateField = '';
        $iconField = '';

        if (!is_null($moduleType) && is_array($moduleType)) {
            if (in_array('user', $moduleType)) {
                $assignToField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="assign_to">@lang(\'' . $moduleNameLower . '::lang.assign_to\'):</label>
                            <select class="form-control select2" id="assign_to" name="assign_to" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($users as $id => $user)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->assign_to == $id ? "selected" : "" }}>{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }

            if (in_array('supplier', $moduleType)) {
                $supplierIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="supplier_id">@lang(\'' . $moduleNameLower . '::lang.supplier\'):</label>
                            <select class="form-control select2" id="supplier_id" name="supplier_id" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($supplier as $id => $supplierName)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->supplier_id == $id ? "selected" : "" }}>{{ $supplierName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }
            if (in_array('designations', $moduleType)) {
                $designationIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="designation_id">@lang(\'' . $moduleNameLower . '::lang.designation\'):</label>
                            <select class="form-control select2" id="designation_id" name="designation_id" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($designations as $id => $designation)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->designation_id == $id ? "selected" : "" }}>{{ $designation }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }
            if (in_array('departments', $moduleType)) {
                $departmentIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="department_id">@lang(\'' . $moduleNameLower . '::lang.department\'):</label>
                            <select class="form-control select2" id="department_id" name="department_id" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($departments as $id => $department)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->department_id == $id ? "selected" : "" }}>{{ $department }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }

            if (in_array('customer', $moduleType)) {
                $customerIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="customer_id">@lang(\'' . $moduleNameLower . '::lang.customer\'):</label>
                            <select class="form-control select2" id="customer_id" name="customer_id" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($customer as $id => $customerName)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->customer_id == $id ? "selected" : "" }}>{{ $customerName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }

            if (in_array('product', $moduleType)) {
                $productIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="product_id">@lang(\'' . $moduleNameLower . '::lang.product\'):</label>
                            <select class="form-control select2" id="product_id" name="product_id" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($product as $id => $productName)
                                    <option value="{{ $id }}" {{ $' . $moduleNameLower . '->product_id == $id ? "selected" : "" }}>{{ $productName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                ';
            }

            if (in_array('module_date', $moduleType)) {
                $dateField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="module_date">@lang(\'' . $moduleNameLower . '::lang.date\'):</label>
                            <input type="date" class="form-control" id="module_date" name="module_date" value="{{ $' . $moduleNameLower . '->module_date }}">
                        </div>
                    </div>
                ';
            }
        }

        $fields = [];

        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];

                switch ($fieldType) {
                    case 'string':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <input type="text" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}">
                                </div>
                            </div>
                        HTML;
                        break;

                    case 'float':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <input type="number" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}" step="any">
                                </div>
                            </div>
                        HTML;
                        break;

                    case 'date':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <input type="date" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}">
                                </div>
                            </div>
                        HTML;
                        break;

                    case 'text':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <!-- <input type="text" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}"> -->
                                    <textarea class="form-control {$moduleName}_description" rows="7" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}">{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</textarea>

                                    <!-- <textarea class="form-control summernote" rows="7" name="{$fieldTitle}" value="{{ \${$moduleNameLower}->{'$fieldTitle'} }}">{!! \${$moduleNameLower}->{'$fieldTitle'} !!}</textarea> -->
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'lead':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$leads->get() as \$id => \$lead)
                                            <option value="{{ \$lead->id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$lead->id ? "selected" : "" }}>{{ \$lead->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;

                    case 'boolean':
                        $fields[] = <<<HTML
                            <div class="col-md-12" style="width: auto;"> 
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="{$fieldTitle}" name="{$fieldTitle}" style="background: none; width: auto; height: auto;" 
                                        @if(\${$moduleNameLower}->{$fieldTitle} == 1) checked @endif readonly> 
                                        <label class="form-check-label" for="{$fieldTitle}" style="line-height: 20px;"> 
                                            @lang('{$moduleNameLower}::lang.{$fieldTitle}')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'qrcode':
                        $fields[] = <<<HTML
                            <div class="col-md-12" style="width: auto;"> 
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="{$fieldTitle}" name="{$fieldTitle}" style="background: none; width: auto; height: auto;" 
                                        @if(\${$moduleNameLower}->{$fieldTitle} == 1) checked @endif readonly> 
                                        <label class="form-check-label" for="{$fieldTitle}" style="line-height: 20px;"> 
                                            @lang('{$moduleNameLower}::lang.{$fieldTitle}')
                                        </label>
                                    </div>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'file':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}') . ':') !!}
                                    {!! Form::file('{$fieldTitle}', [
                                        'id' => '{$fieldTitle}',
                                        'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))),
                                    ]) !!}
                                    @if(\${$moduleNameLower}->{'$fieldTitle'})
                                        @php
                                            \$imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                            \$fileExtension = strtolower(pathinfo(\${$moduleNameLower}->{'$fieldTitle'}, PATHINFO_EXTENSION));
                                            \$filePath = 'uploads/{$moduleName}/' . basename(\${$moduleNameLower}->{'$fieldTitle'});
                                        @endphp

                                        <div class="mt-3">
                                            @if(in_array(\$fileExtension, \$imageExtensions))
                                                <img src="{{ asset(\$filePath) }}" 
                                                    alt="Document Image" 
                                                    class="mt-2"
                                                    style="max-width: 100px;">
                                            @elseif(\$fileExtension === 'pdf')
                                                <a href="{{ asset(\$filePath) }}" 
                                                target="_blank">
                                                    <iframe 
                                                        src="{{ asset(\$filePath) }}#toolbar=0&navpanes=0&scrollbar=0"
                                                        width="50%"
                                                        height="250px"
                                                        frameborder="0"
                                                        class="pdf-viewer">
                                                    </iframe>
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    <p class="help-block">
                                        @lang('purchase.max_file_size', ['size' => config('constants.document_size_limit') / 1000000])
                                        @includeIf('components.document_help_text')
                                    </p>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'users':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$users as \$id => \$userName)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$userName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'departments':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$departments as \$id => \$department)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'designations':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$designations as \$id => \$designation)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$designation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'supplier':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$supplier as \$id => \$supplierName)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$supplierName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'customer':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$customer as \$id => \$customerName)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$customerName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'product':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$product as \$id => \$productName)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$productName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'business_location':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control select2" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        @foreach (\$business_locations as \$id => \$location)
                                            <option value="{{ \$id }}" {{ \${$moduleNameLower}->{$fieldTitle} == \$id ? "selected" : "" }}>{{ \$location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'status_true_false':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="0" {{ \${$moduleNameLower}->{$fieldTitle} == 0 ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.no') </option>
                                        <option value="1" {{ \${$moduleNameLower}->{$fieldTitle} == 1 ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.yes')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'status_authorize':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        <option value="pending" {{ \${$moduleNameLower}->{$fieldTitle} == "pending" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.pending')</option>
                                        <option value="accept" {{ \${$moduleNameLower}->{$fieldTitle} == "accept" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.accept')</option>
                                        <option value="cancel" {{ \${$moduleNameLower}->{$fieldTitle} == "cancel" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.cancel')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'status_priority':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        <option value="low" {{ \${$moduleNameLower}->{$fieldTitle} == "low" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.low')</option>
                                        <option value="medium" {{ \${$moduleNameLower}->{$fieldTitle} == "medium" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.medium')</option>
                                        <option value="high" {{ \${$moduleNameLower}->{$fieldTitle} == "high" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.high')</option>
                                        <option value="urgent" {{ \${$moduleNameLower}->{$fieldTitle} == "urgent" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.urgent')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'status_payment':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        <option value="pending" {{ \${$moduleNameLower}->{$fieldTitle} == "pending" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.pending')</option>
                                        <option value="partial" {{ \${$moduleNameLower}->{$fieldTitle} == "partial" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.partial')</option>
                                        <option value="paid" {{ \${$moduleNameLower}->{$fieldTitle} == "paid" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.paid')</option>
                                        <option value="due" {{ \${$moduleNameLower}->{$fieldTitle} == "due" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.due')</option>
                                        <option value="cancel" {{ \${$moduleNameLower}->{$fieldTitle} == "cancel" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.cancel')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'status_delivery':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <select class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" style="width: 100%;">
                                        <option value="">@lang('messages.select')</option>
                                        <option value="order" {{ \${$moduleNameLower}->{$fieldTitle} == "order" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.order')</option>
                                        <option value="packed" {{ \${$moduleNameLower}->{$fieldTitle} == "packed" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.packed')</option>
                                        <option value="shipped" {{ \${$moduleNameLower}->{$fieldTitle} == "shipped" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.shipped')</option>
                                        <option value="delivered" {{ \${$moduleNameLower}->{$fieldTitle} == "delivered" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.delivered')</option>
                                        <option value="returned" {{ \${$moduleNameLower}->{$fieldTitle} == "returned" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.returned')</option>
                                        <option value="cancel" {{ \${$moduleNameLower}->{$fieldTitle} == "cancel" ? "selected" : "" }}>@lang('{$moduleNameLower}::lang.cancel')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                }
            }
        }
        $fieldsString = implode("\n", $fields);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('{$moduleNameLower}::lang.edit_{$moduleNameLower}')</h4>
                    </div>
                    <div class="modal-body">
                        <form id="edit_{$moduleName}_form" method="POST" action="{{ route('{$moduleName}.update', \${$moduleNameLower}->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="{$moduleNameLower}_category_id">@lang('{$moduleNameLower}::lang.category'):</label>
                                        <select class="form-control" id="{$moduleNameLower}_category_id" name="{$moduleNameLower}_category_id" style="width: 100%;">
                                            <option value="">@lang('messages.select')</option>
                                            @foreach (\${$moduleNameLower}_categories as \$id => \$category)
                                                <option value="{{ \$id }}" {{ \${$moduleNameLower}->category_id == \$id ? 'selected' : '' }}>
                                                    {{ \$category }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {$assignToField}
                                {$supplierIdField}
                                {$customerIdField}
                                {$productIdField}
                                {$departmentIdField}
                                {$designationIdField}
                                {$dateField}
                                {$iconField}
                                {$fieldsString}
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            EOT;

            $this->files->put($viewPath, $content);
        }
    }
}
