<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class CreateForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createCreate($moduleName, $moduleType, $title, $type)
    {
        // Define the path for the view file
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/create.blade.php");
        $moduleNameLower = strtolower($moduleName);

        // Initialize field variables
        $assignToField = '';
        $supplierIdField = '';
        $customerIdField = '';
        $departmentIdField = '';
        $designationIdField = '';
        $productIdField = '';
        $dateField = '';
        $iconField = '';


        // Check if $moduleType is not null and is an array
        if (!is_null($moduleType) && is_array($moduleType)) {
            if (in_array('user', $moduleType)) {
                $assignToField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="assign_to">@lang(\'' . $moduleNameLower . '::lang.assign_to\'):</label>
                            <select class="form-control select2" id="assign_to" name="assign_to" style="width: 100%;">
                                <option value="">@lang(\'messages.select\')</option>
                                @foreach ($users as $id => $user)
                                    <option value="{{ $id }}">{{ $user }}</option>
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
                                    <option value="{{ $id }}">{{ $supplierName }}</option>
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
                                    <option value="{{ $id }}">{{ $customerName }}</option>
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
                            {{ Form::select(
                                \'designations\', 
                                $designations, 
                                null, 
                                [
                                    \'class\' => \'form-control\',
                                    \'id\' => \'designation_id\', 
                                    \'placeholder\' => @lang(\'' . $moduleNameLower . '::lang.designation\')
                                ]
                            ) }}
                        </div>
                    </div>
                ';
            }
            if (in_array('departments', $moduleType)) {
                $departmentIdField = '
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="department_id">@lang(\'' . $moduleNameLower . '::lang.department\'):</label>
                            {{ Form::select(
                                \'departments\', 
                                $departments, 
                                null, 
                                [
                                    \'class\' => \'form-control\',
                                    \'id\' => \'department_id\',
                                    \'placeholder\' => @lang(\'' . $moduleNameLower . '::lang.department\')
                                ]
                            ) }}
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
                                    <option value="{{ $id }}">{{ $productName }}</option>
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
                            <input type="date" class="form-control" id="module_date" name="module_date" value="{{ old(\'module_date\') }}">
                        </div>
                    </div>
                ';
            }
        }

        $fields = [];

        // Process title and type
        if ((!is_null($title) && is_array($title)) && (!is_null($type) && is_array($type))) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index]; // Default to string if type is not defined

                switch ($fieldType) {
                    case 'string':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <input type="text" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}">
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'float':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <input type="number" class="form-control" id="{$fieldTitle}" name="{$fieldTitle}" step="any">
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'boolean':
                        $fields[] = <<<HTML
                            <div class="col-md-12" style="width: auto;"> <!-- Adjust width for consistency with the layout -->
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" style="width: 20px; height: 20px;" type="checkbox" type="checkbox" value="1" id="{$fieldTitle}" name="{$fieldTitle}"> <!-- Width added here -->
                                        <label class="form-check-label" for="{$fieldTitle}" style="line-height: 20px;"> <!-- Align the label properly -->
                                            @lang('{$moduleNameLower}::lang.{$fieldTitle}')
                                        </label>
                                    </div>
                                </div>
                            </div>

                        HTML;
                        break;
                    case 'qrcode':
                        $fields[] = <<<HTML
                            <div class="col-md-12" style="width: auto;"> <!-- Adjust width for consistency with the layout -->
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" style="width: 20px; height: 20px;" type="checkbox" type="checkbox" value="1" id="{$fieldTitle}" name="{$fieldTitle}"> <!-- Width added here -->
                                        <label class="form-check-label" for="{$fieldTitle}" style="line-height: 20px;"> <!-- Align the label properly -->
                                            @lang('{$moduleNameLower}::lang.{$fieldTitle}')
                                        </label>
                                    </div>
                                </div>
                            </div>

                        HTML;
                        break;
                    case 'date':
                        $fields[] = <<<HTML
                            
                            <div class="col-sm-12 individual">
                                <div class="form-group">
                                    {!! Form::label('{$fieldTitle}', __('{$moduleNameLower}::lang.{$fieldTitle}') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                        {!! Form::date('{$fieldTitle}', Carbon::now()->format('Y-m-d'), ['class' => 'form-control {$fieldTitle}-date-picker','placeholder' => Carbon::now()->format('Y-m-d')]); !!}
                                    </div>
                                </div>
                            </div>

                        HTML;
                        break;
                    case 'text':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    <textarea class="form-control {$moduleName}_description" name="{$fieldTitle}" rows="3"></textarea>
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
                                            <option value="{{ \$id }}">{{ \$userName }}</option>
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
                                            <option value="{{ \$id }}">{{ \$supplierName }}</option>
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
                                            <option value="{{ \$id }}">{{ \$customerName }}</option>
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
                                    {{ Form::select('{$fieldTitle}', \$departments, 
                                        null, 
                                        ['class' => 'form-control select2','id' => '{$fieldTitle}', 'placeholder' => __('messages.select')]) 
                                    }}
                                </div>
                            </div>
                        HTML;
                        break;
                    case 'designations':
                        $fields[] = <<<HTML
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="{$fieldTitle}">@lang('{$moduleNameLower}::lang.{$fieldTitle}'):</label>
                                    {{ Form::select('{$fieldTitle}', \$designations, 
                                        null, 
                                        ['class' => 'form-control select2','id' => '{$fieldTitle}', 'placeholder' => __('messages.select')]) 
                                    }}
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
                                            <option value="{{ \$id }}">{{ \$productName }}</option>
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
                                            <option value="{{ \$id }}">{{ \$location }}</option>
                                        @endforeach
                                    </select>
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
                                            <option value="{{ \$lead->id }}">{{ \$lead->name }}</option>
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
                                        <option value="">@lang('messages.select')</option>
                                        <option value="0">@lang('{$moduleNameLower}::lang.no')</option>
                                        <option value="1">@lang('{$moduleNameLower}::lang.yes')</option>
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
                                        <option value="pending">@lang('{$moduleNameLower}::lang.pending')</option>
                                        <option value="accept">@lang('{$moduleNameLower}::lang.accept')</option>
                                        <option value="cancel">@lang('{$moduleNameLower}::lang.cancel')</option>
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
                                        <option value="low">@lang('{$moduleNameLower}::lang.low')</option>
                                        <option value="medium">@lang('{$moduleNameLower}::lang.medium')</option>
                                        <option value="high">@lang('{$moduleNameLower}::lang.high')</option>
                                        <option value="urgent">@lang('{$moduleNameLower}::lang.urgent')</option>
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
                                        <option value="pending">@lang('{$moduleNameLower}::lang.pending')</option>
                                        <option value="partial">@lang('{$moduleNameLower}::lang.partial')</option>
                                        <option value="paid">@lang('{$moduleNameLower}::lang.paid')</option>
                                        <option value="due">@lang('{$moduleNameLower}::lang.due')</option>
                                        <option value="cancel">@lang('{$moduleNameLower}::lang.cancel')</option>
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
                                        <option value="order">@lang('{$moduleNameLower}::lang.order')</option>
                                        <option value="packed">@lang('{$moduleNameLower}::lang.packed')</option>
                                        <option value="shipped">@lang('{$moduleNameLower}::lang.shipped')</option>
                                        <option value="delivered">@lang('{$moduleNameLower}::lang.delivered')</option>
                                        <option value="returned">@lang('{$moduleNameLower}::lang.returned')</option>
                                        <option value="cancel">@lang('{$moduleNameLower}::lang.cancel')</option>
                                    </select>
                                </div>
                            </div>
                        HTML;
                        break;
                }
            }
        }

        $fieldsString = implode("\n", $fields);

        // Ensure the directory exists
        if (!$this->files->exists(dirname($viewPath))) {
            $this->files->makeDirectory(dirname($viewPath), 0755, true);
        }

        // Create the view file if it doesn't exist
        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    {!! Form::open(['url' => action([\\Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'store']), 'method' => 'post', 'id' => 'add_{$moduleName}_form' ]) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('{$moduleNameLower}::lang.add_{$moduleName}')</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="{$moduleNameLower}_category_id">@lang('{$moduleNameLower}::lang.category'):</label>
                                        <select class="form-control select2" id="{$moduleNameLower}_category_id" name="{$moduleNameLower}_category_id" style="width: 100%;">
                                            <option value="">@lang('messages.select')</option>
                                            @foreach (\${$moduleNameLower}_categories as \$id => \$category)
                                                <option value="{{ \$id }}">{{ \$category }}</option>
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
                            <hr>
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                                <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
