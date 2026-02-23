<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class ControllerNewModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function modifyController($moduleName, $moduleType, $title, $type)
    {
        $controllerPath = base_path("Modules/{$moduleName}/Http/Controllers/{$moduleName}Controller.php");
        $moduleNameLower = strtolower($moduleName);

        $assignToField = '';
        $supplierIdField = '';
        $customerIdField = '';
        $leadIdField = '';
        $departmentIdField = '';
        $designationIdField = '';
        $productIdField = '';

        $assignToStoreEdit = '';
        $supplierIdStoreEdit = '';
        $customerIdStoreEdit = '';
        $leadIdStoreEdit = '';
        $productIdStoreEdit = '';
        $dateStoreEdit = '';
        $departmentIdStoreEdit = '';
        $designationIdStoreEdit = '';

        $assignToTryStoreEdit = '';
        $supplierIdTryStoreEdit = '';
        $customerIdTryStoreEdit = '';
        $leadIdTryStoreEdit = '';
        $productIdTryStoreEdit = '';
        $dateTryStoreEdit = '';
        $departmentIdTryStoreEdit = '';
        $designationIdTryStoreEdit = '';

        if (!is_null($moduleType) && is_array($moduleType)) {
            // Check if 'user' type is selected
            if (in_array('user', $moduleType)) {
                $assignToField = "
                    ->addColumn('assign_to', function (\$row) {
                        \$user = User::find(\$row->assign_to);
                        return \$user ? \$user->name : '';
                    })
                ";
                $assignToStoreEdit = "
                    'assign_to' => 'nullable|integer',
                ";
                $assignToTryStoreEdit = "
                    \${$moduleNameLower}->assign_to = \$request->assign_to;
                ";
            }

            // Check if 'supplier' type is selected
            if (in_array('supplier', $moduleType)) {
                $supplierIdField = "
                    ->addColumn('supplier_id', function (\$row) {
                        \$supplier = Contact::find(\$row->supplier_id);
                        return \$supplier ? \$supplier->mobile : '';
                    })
                ";
                $supplierIdStoreEdit = "
                    'supplier_id' => 'nullable|integer',
                ";
                $supplierIdTryStoreEdit = "
                    \${$moduleNameLower}->supplier_id = \$request->supplier_id;
                ";
            }
            if (in_array('departments', $moduleType)) {
                $departmentIdField = "
                    ->addColumn('department_id', function (\$row) {
                        \$department = Category::find(\$row->department_id);
                        return \$department ? \$department->name : '';
                    })
                ";
                $departmentIdStoreEdit = "
                    'department_id' => 'nullable|integer',
                ";
                $departmentIdTryStoreEdit = "
                    \${$moduleNameLower}->department_id = \$request->department_id;
                ";
            }

            if (in_array('designations', $moduleType)) {
                $designationIdField = "
                    ->addColumn('designation_id', function (\$row) {
                        \$designation = Category::find(\$row->designation_id);
                        return \$designation ? \$designation->name : '';
                    })
                ";
                $departmentIdStoreEdit = "
                    'designation_id' => 'nullable|integer',
                ";
                $designationIdTryStoreEdit = "
                    \${$moduleNameLower}->designation_id = \$request->designation_id;
                ";
            }

            // Check if 'customer' type is selected
            if (in_array('customer', $moduleType)) {
                $customerIdField = "
                    ->addColumn('customer_id', function (\$row) {
                        \$customer = Contact::find(\$row->customer_id);
                        return \$customer ? \$customer->mobile : '';
                    })
                ";
                $customerIdStoreEdit = "
                    'customer_id' => 'nullable|integer',
                ";
                $customerIdTryStoreEdit = "
                    \${$moduleNameLower}->customer_id = \$request->customer_id;
                ";
            }
            if (in_array('lead', $moduleType)) {
                $leadIdField = "
                    ->addColumn('{$fieldTitle}', function (\$row) {
                        \$lead = Contact::find(\$row->fieldTitle);
                        \$name = \$lead->first_name . ' ' . \$lead->last_name;
                        return \$name ? \$name : '';
                    })
                ";
                $leadIdStoreEdit = "
                    'lead_id' => 'nullable|integer',
                ";
                $leadIdTryStoreEdit = "
                    \${$moduleNameLower}->lead_id = \$request->lead_id;
                ";
            }

            // Check if 'product' type is selected
            if (in_array('product', $moduleType)) {
                $productIdField = "
                    ->addColumn('product_id', function (\$row) {
                       \$product = Product::find(\$row->product_id);
                        return \$product ? \$product->name : '';
                    })
                ";
                $productIdStoreEdit = "
                    'product_id' => 'nullable|integer',
                ";
                $productIdTryStoreEdit = "
                    \${$moduleNameLower}->product_id = \$request->product_id;
                ";
            }

            if (in_array('module_date', $moduleType)) {
                $dateStoreEdit = "
                    'module_date' => 'nullable|date_format:Y-m-d',
                ";
                $dateTryStoreEdit = "
                    \${$moduleNameLower}->module_date = \$request->module_date;
                ";
            }
        }

        $fieldsStoreEdit = [];
        $fieldsDynamic = [];
        $fieldsTryStoreEdit = [];
        $fileUploadStatements = [];
        $fileUpdateStatements = [];
        $filters = [];
        $call_audit = [];
        $return_data = [];
        $function_audit = [];
        $update_audit_status = [];
        $getDataRelation = [];
        $withRelation = [];

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            // Code inside the foreach loop executes only if $title and $type are valid arrays
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                if (!is_null($fieldType)) {
                    if ($fieldType !== 'file') {
                        $fieldsStoreEdit[] = "
                            '$fieldTitle' => 'nullable',
                        ";
                        $label = "{$moduleNameLower}::lang." . $fieldTitle;
                        $fieldsDynamic[] =
                            "[
                            'id' => '{$fieldTitle}content',
                            'label' => '{$label}',
                        ],";

                        $fieldsTryStoreEdit[] = "
                            \${$moduleNameLower}->{'$fieldTitle'} = \$request->{'$fieldTitle'};
                        ";
                        if ($fieldType == 'status_true_false') {
                            $getDataRelation[] = "
                                ->addColumn('{$fieldTitle}', function (\$row) {
                                    return \$row->{'$fieldTitle'} == 1 ? __('{$moduleNameLower}::lang.yes') : __('{$moduleNameLower}::lang.no');
                                })
                            ";
                        }
                        if ($fieldType == 'lead') {
                            $getDataRelation[] = "
                                ->addColumn('{$fieldTitle}', function (\$row) {
                                    \$lead = Contact::find(\$row->{$fieldTitle});
                                    return \$lead ? \$lead->name : '';
                                })
                            ";
                        }
                        if ($fieldType == 'text') {
                            $getDataRelation[] = "
                                ->addColumn('{$fieldTitle}', function (\$row) {
                                    return strip_tags(\$row->{$fieldTitle});
                                })
                            ";
                        }
                        if ($fieldType == 'users' || $fieldType == 'supplier' || $fieldType == 'customer' || $fieldType == 'product' || $fieldType == 'designations' || $fieldType == 'departments'|| $fieldType == 'business_location') {
                            $filters[] = "
                                if (!empty(request()->{'$fieldTitle'})) {
                                    \${'$fieldTitle'} = request()->{'$fieldTitle'};
                                    \${$moduleName}->where('{$fieldTitle}', \${'$fieldTitle'});
                                }
                            ";
                            $cleanFieldTitle = str_replace('_', '', $fieldTitle);
                            if ($fieldType == 'users') {
                                $getDataRelation[] = "
                                    ->addColumn('{$fieldTitle}', function (\$row) {
                                        return \$row->{'$cleanFieldTitle'}->first_name . ' ' . \$row->{'$cleanFieldTitle'}->last_name;
                                    })
                                ";
                            }
                            if ($fieldType == 'supplier') {
                                $getDataRelation[] = "
                                    ->addColumn('{$fieldTitle}', function (\$row) {
                                        return \$row->{'$cleanFieldTitle'}->supplier_business_name;
                                    })
                                ";
                            }
                            if ($fieldType == 'customer' || $fieldType == 'product' || $fieldType == 'business_location' || $fieldType == 'designations' || $fieldType == 'departments') {
                                $getDataRelation[] = "
                                    ->addColumn('{$fieldTitle}', function (\$row) {
                                        return \$row->{'$cleanFieldTitle'}->name;
                                    })
                                ";
                            }

                            if (in_array($fieldType, ['users', 'supplier', 'customer', 'product', 'business_location', 'lead'])) {
                                $withRelation[] = $cleanFieldTitle; // Add relationship to array
                            }
                        }
                        if ($fieldType == 'audit') {
                            $call_audit[] = "
                                ->editColumn('{$fieldTitle}', function (\$row) {
                                    // Define a mapping of statuses to bootstrap background classes
                                    \$action_status = [
                                        'Pending' => 'bg-red',
                                        'Done' => 'bg-blue',
                                        'Problem' => 'bg-yellow',
                                        'Default' => 'bg-grey', // Define a default color if needed
                                    ];
                        
                                    // Determine the current label and corresponding color
                                    \$label = !empty(\$row->{$fieldTitle}) ? \$row->{$fieldTitle} : 'Pending'; // Escape output
                                    \$status_color = \$action_status[\$label] ?? \$action_status['Default'];
                                                
                                    // Generate the action URL
                                    \$url = route('{$moduleName}.edit', ['id' => \$row->id]) . '?type=audit';

                                    return '<a href=\"#\" class=\"btn-modal\" data-href=\"' . \$url . '\" data-container=\".{$moduleName}_modal\"><span class=\"label ' . \$status_color . '\">' . \$label . '</span></a>';
                                })
                            ";
                            $return_data[] = "'{$fieldTitle}'";
                            $function_audit[] = "
                                if (\$type === 'audit') {
                                    \${$moduleNameLower} = {$moduleName}::where('business_id', \$business_id)->findOrFail(\$id);

                                    // \$audit = Audit::with('user')
                                    //             ->where('business_id', \$business_id)
                                    //             ->where('mini_app', \$id) 
                                    //             ->get();

                                    return view('{$moduleNameLower}::{$moduleName}.audit')->with(compact('{$moduleNameLower}', 'audit'));
                                }
                            ";
                            $update_audit_status[] = "
                                public function updateAuditStatus(Request \$request, \$id)
                                {
                                    try {
                                        \$business_id = \$request->session()->get('user.business_id');
                                        \$user_id = auth()->user()->id;

                                        // Retrieve the transaction by business_id and id
                                        \${$moduleNameLower}_get = {$moduleName}::where('business_id', \$business_id)->where('id', \$id)->first();
                                        \${$moduleNameLower} = {$moduleName}::where('business_id', \$business_id)->findOrFail(\$id);

                                        
                                        // Update transaction
                                        \${$moduleNameLower}->{$fieldTitle} = \$request->input('audit_status');
                                        \${$moduleNameLower}->save();

                                        // Create a new audit record
                                        Audit::create([
                                            'business_id' => \$business_id,
                                            'user_id' => \$user_id,
                                            'mini_app' =>  \${$moduleNameLower}->id,
                                            'old_status' => \${$moduleNameLower}_get->{$fieldTitle} ?? 'Pending',
                                            'new_status' => \$request->input('audit_status'),
                                            'note' => \$request->input('audit_note')
                                        ]);

                                        \$output = [
                                            'success' => 1,
                                            'msg' => trans('lang_v1.updated_success'),
                                        ];
                                    } catch (\Exception \$e) {
                                        \Log::emergency('File:' . \$e->getFile() . 'Line:' . \$e->getLine() . 'Message:' . \$e->getMessage());

                                        \$output = [
                                            'success' => 0,
                                            'msg' => trans('messages.something_went_wrong'),
                                        ];
                                    }

                                    return response()->json(\$output);
                                }
                            ";
                            $filters[] = "
                                if (!empty(request()->{'$fieldTitle'})) {
                                    if (request()->has('{$fieldTitle}') && request()->{$fieldTitle} === 'Pending') {
                                        \${$moduleName}->where(function (\$query) {
                                            \$query->where('{$fieldTitle}', 'Pending')
                                                  ->orWhereNull('{$fieldTitle}');
                                        });
                                    }else{
                                        \${'$fieldTitle'} = request()->{'$fieldTitle'};
                                        \${$moduleName}->where('{$fieldTitle}', \${$fieldTitle});
                                    }
                                                                        
                                }
                            ";
                        }
                    } else {
                        $fileUploadStatements[] = "
                            if (\$request->hasFile('{$fieldTitle}')) {
                                \$documentPath = \$this->transactionUtil->uploadFile(\$request, '{$fieldTitle}', '{$moduleName}');
                                \${$moduleNameLower}->{'$fieldTitle'} = \$documentPath;
                            }
                        ";
                        $fileUpdateStatements[] = "
                            if (\$request->hasFile('{$fieldTitle}')) {
                                \$oldFile = public_path('uploads/tracking/' . basename(\${$moduleNameLower}->{'$fieldTitle'}));
                                if (file_exists(\$oldFile)) {
                                    unlink(\$oldFile);
                                }
                                \$documentPath = \$this->transactionUtil->uploadFile(\$request, '{$fieldTitle}', '{$moduleName}');
                                \${$moduleNameLower}->{'$fieldTitle'} = \$documentPath;
                            }
                        ";
                    }
                }
            }
        }

        $fieldsStoreEditString = implode("\n", $fieldsStoreEdit);
        $fieldsDynamicString = implode("\n", $fieldsDynamic);
        $fieldsTryStoreEditString = implode("\n", $fieldsTryStoreEdit);
        $fileUploadStatementsString = implode("\n", $fileUploadStatements);
        $fileUpdateStatementsString = implode("\n", $fileUpdateStatements);
        $filters = implode("\n", $filters);
        $return_data = implode("\n", $return_data);
        $call_audit = implode("\n", $call_audit);
        $function_audit = implode("\n", $function_audit);
        $update_audit_status = implode("\n", $update_audit_status);
        $getDataRelation = implode("\n", $getDataRelation);
        $withRelationsString = implode("', '", $withRelation);
        $withRelationsString = "'{$withRelationsString}'";

        if (!empty($withRelation)) {
            // Join the relations array into a single string with quotes and commas
            $withRelationsString = "'" . implode("', '", $withRelation) . "'";
            // Build the relation string
            $Relation = "->with([$withRelationsString])";
        }


        $content = <<<EOT
        <?php

        namespace Modules\\{$moduleName}\\Http\\Controllers;

        use Illuminate\\Http\\Request;
        use Illuminate\\Routing\\Controller;
        use Illuminate\\Http\\Response;
        use Illuminate\\Support\\Facades\\DB;
        use Illuminate\Support\Facades\Auth;
        use Illuminate\\Support\\Facades\\Http;
        use App\\User;
        use App\\Contact;
        use App\\Product;
        use App\\Audit; 
        use App\\Category; 
        use App\\BusinessLocation;
        use App\\Utils\\ModuleUtil;
        use App\\Utils\\TransactionUtil;
        use Modules\\{$moduleName}\\Entities\\{$moduleName};
        use Modules\\{$moduleName}\\Entities\\{$moduleName}Category;
        use Modules\\ModuleCreateModule\\Entities\\ModuleCreator;
        use Modules\\Crm\\Utils\\CrmUtil;
        use Modules\\{$moduleName}\\Entities\\{$moduleName}Social;
        use SimpleSoftwareIO\\QrCode\\Facades\\QrCode;
        use Yajra\\DataTables\\Facades\\DataTables;



        class {$moduleName}Controller extends Controller
        {
            protected \$moduleUtil;
            protected \$transactionUtil;
            protected \$crmUtil;

            public function __construct(
                ModuleUtil \$moduleUtil,
                TransactionUtil \$transactionUtil,
                CrmUtil \$crmUtil
            )
            {
                \$this->moduleUtil = \$moduleUtil;
                \$this->transactionUtil = \$transactionUtil;
                \$this->crmUtil = \$crmUtil;
            }

            public function dashboard()
            {
                \$business_id = request()->session()->get('user.business_id');
                
                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) || ! auth()->user()->can('superadmin') || ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                \$total_{$moduleNameLower} = {$moduleName}::where('business_id', \$business_id)->count();

                \$total_{$moduleNameLower}_category ={$moduleName}Category::where('business_id', \$business_id)->count();

                \${$moduleNameLower}_category = DB::table('{$moduleNameLower}_main as {$moduleNameLower}')
                    ->leftJoin('{$moduleNameLower}_category as {$moduleNameLower}category', '{$moduleNameLower}.category_id', '=', '{$moduleNameLower}category.id')
                    ->select(
                        DB::raw('COUNT({$moduleNameLower}.id) as total'),
                        '{$moduleNameLower}category.name as category'
                    )
                    ->where('{$moduleNameLower}.business_id', \$business_id)
                    ->groupBy('{$moduleNameLower}category.id')
                    ->get();

                \$user_id = auth()->user()->id;

                return view('{$moduleNameLower}::{$moduleName}.dashboard')
                    ->with(compact('total_{$moduleNameLower}', 'total_{$moduleNameLower}_category', '{$moduleNameLower}_category', 'module'));
            }

            public function index(Request \$request)
            {
                \$business_id = request()->session()->get('user.business_id');

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                if (\$request->ajax()) {
                    \$business_id = request()->session()->get('user.business_id');
                    \${$moduleName} = {$moduleName}::where('business_id', \$business_id){$Relation}->orderBy('id','desc');
                    
                    if (!empty(request()->start_date) && !empty(request()->end_date)) {
                        \$start = request()->start_date;
                        \$end = request()->end_date;
                        \${$moduleName}->whereDate('created_at', '>=', \$start)
                            ->whereDate('created_at', '<=', \$end);
                    }

                    {$filters}
                    if (!empty(request()->{'category_id'})) {
                        \${'category_id'} = request()->{'category_id'};
                        \${$moduleName}->where('category_id', \${'category_id'});

                    }

                    \${$moduleName}->get();

                    return DataTables::of(\${$moduleName})
                        ->addColumn('action', function (\$row) {
                            \$html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('{$moduleName}.show', \$row->id) . '" data-container="#{$moduleName}_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                            \$html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('{$moduleName}.edit', \$row->id) . '" data-container="#{$moduleName}_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                            \$html .= ' <button class="btn btn-xs btn-danger delete-{$moduleName}" data-href="' . route('{$moduleName}.destroy', \$row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                            return \$html;
                        })
                       ->addColumn('category', function (\$row) {
                            \$category = {$moduleName}Category::find(\$row->category_id);
                            return \$category ? \$category->name : '';
                        })
                        ->addColumn('create_by', function (\$row) {
                            \$user = User::find(\$row->created_by);
                            \$name = \$user->first_name . ' ' . \$user->last_name;
                            return \$name ? \$name : '';
                        })
                        {$assignToField}
                        {$supplierIdField}
                        {$customerIdField}
                        {$leadIdField}
                        {$departmentIdField}
                        {$designationIdField}
                        {$productIdField}
                        {$call_audit}
                        {$getDataRelation}
                        ->rawColumns(['action', {$return_data}])
                        ->make(true);
                }
                
                \$users = User::forDropdown(\$business_id, false, true, true);
                \$category = {$moduleName}Category::forDropdown(\$business_id);
                \$customer = Contact::where('business_id', \$business_id)
                ->where('type', 'customer')
                ->pluck('name', 'id');
                \$supplier = Contact::where('business_id', \$business_id)
                ->where('type', 'supplier')
                ->pluck('supplier_business_name', 'id');
                \$product = Product::where('business_id', \$business_id)
                ->pluck('name', 'id');
                \$business_locations = BusinessLocation::forDropdown(\$business_id, false);
                \$departments = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_department')
                    ->pluck('name', 'id');

                \$designations = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_designation')
                    ->pluck('name', 'id');
                \$leads = \$this->crmUtil->getLeadsListQuery(\$business_id);

                return view('{$moduleNameLower}::{$moduleName}.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
            }

            public function showQrcodeUrl(\$id)
            {

                \$url = route('{$moduleName}.qrcodeView', ['id' => \$id]);
                \$qrcode = QrCode::size(50)->generate(\$url);
                return \$qrcode;
            }

            public function qrcodeView(\$id){
                \$checkboxes = [
                    
                    ['id' => 'categorycontent',
                    'label' => '{$moduleNameLower}::lang.category'],
                    ['id' => 'qrcontent',
                    'label' => '{$moduleNameLower}::lang.qrcode'],
                    ['id' => 'createdbycontent',
                    'label' => '{$moduleNameLower}::lang.createdby'],
                    ['id' => 'createdatcontent',
                    'label' => '{$moduleNameLower}::lang.createdat'],
                    {$fieldsDynamicString}

                ];

                \$qrcode = \$this->showQrcodeUrl(\$id);
                \$link =  route('{$moduleName}.qrcodeView', ['id' => \$id]);
                \${$moduleNameLower} = {$moduleName}::findOrFail(\$id);
                \$createdby = User::findOrFail(\${$moduleNameLower}->created_by);
                \$name = \$createdby->first_name . ' ' . \$createdby->last_name;
                return view('{$moduleNameLower}::{$moduleName}.qr_view')->with(compact('{$moduleNameLower}','qrcode','link','checkboxes','name'));
            }

            public function create(Request \$request)
            {
                \$business_id = request()->session()->get('user.business_id');
                \${$moduleNameLower}_categories = {$moduleName}Category::forDropdown(\$business_id);
                \$users = User::forDropdown(\$business_id, false);
                \$customer = Contact::where('business_id', \$business_id)
                ->where('type', 'customer')
                ->pluck('name', 'id');
                \$supplier = Contact::where('business_id', \$business_id)
                ->where('type', 'supplier')
                ->pluck('supplier_business_name', 'id');
                \$product = Product::where('business_id', \$business_id)
                ->pluck('name', 'id');
                \$business_locations = BusinessLocation::forDropdown(\$business_id, false);
                \$departments = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_department')
                    ->pluck('name', 'id');

                \$designations = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_designation')
                    ->pluck('name', 'id');
                \$leads = \$this->crmUtil->getLeadsListQuery(\$business_id);

                return view('{$moduleNameLower}::{$moduleName}.create', compact('{$moduleNameLower}_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
            }

             public function show(\$id, Request \$request)
            {
                
                \$business_id = request()->session()->get('user.business_id');

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }
                // \$qrcode = \$this->showQrcodeUrl(\$id);
                // \$link =  route('{$moduleName}.qrcodeView', ['id' => \$id]);

                \${$moduleNameLower} = {$moduleName}::where('business_id', \$business_id){$Relation}->findOrFail(\$id);
                \${$moduleNameLower} = {$moduleName}::where('business_id', \$business_id){$Relation}->findOrFail(\$id);

                // Get all attributes from the model
                \$attributes = \${$moduleNameLower}->getAttributes();

                // Find the first field that ends with '1'
                \$first_field = null;
                foreach (\$attributes as \$fieldName => \$fieldValue) {
                    if (str_ends_with(\$fieldName, '1')) {
                        \$first_field = \$fieldValue;
                        break; // Get the first one found
                    }
                }

                // Or get all fields that end with '1'
                \$fields_ending_with_1 = [];
                foreach (\$attributes as \$fieldName => \$fieldValue) {
                    if (str_ends_with(\$fieldName, '1')) {
                        \$fields_ending_with_1[\$fieldName] = \$fieldValue;
                    }
                }
                
                
                \$createdby = User::findOrFail(\${$moduleNameLower}->created_by);
                \$name = \$createdby->first_name . ' ' . \$createdby->last_name;
                \$print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
                \$date_range = \$request->query('date_range');

                return view('{$moduleNameLower}::{$moduleName}.show')->with(compact('{$moduleNameLower}','name','print_by', 'date_range','first_field'));
            }

            public function store(Request \$request)
            {
                \$request->validate([
                    '{$moduleNameLower}_category_id' => 'nullable|integer',
                    {$assignToStoreEdit}
                    {$supplierIdStoreEdit}
                    {$customerIdStoreEdit}
                    {$leadIdStoreEdit}
                    {$productIdStoreEdit}
                    {$designationIdStoreEdit}
                    {$departmentIdStoreEdit}
                    {$dateStoreEdit}
                    {$fieldsStoreEditString}                
                ]);

                \$business_id = request()->session()->get('user.business_id');
                // \$document = \$this->transactionUtil->uploadFile(\$request, 'document', 'tracking');

                try {
                    \${$moduleNameLower} = new {$moduleName}();
                    \${$moduleNameLower}->business_id = \$business_id;
                    \${$moduleNameLower}->category_id = \$request->{$moduleNameLower}_category_id;
                    \${$moduleNameLower}->created_by = auth()->user()->id;
                    \${$moduleName}Social = {$moduleName}Social::where('business_id', \$business_id)->first();
            
                    if (\${$moduleName}Social && \${$moduleName}Social->social_status == 1) {
                        \$BotToken = \${$moduleName}Social->social_token;
                        \$ChatId = \${$moduleName}Social->social_id;
                        \$message = __('{$moduleNameLower}::lang.{$moduleNameLower}_created');

                        \$Url = "https://api.telegram.org/bot\$BotToken/sendMessage";
                        Http::post(\$Url, [
                            'chat_id' => \$ChatId,
                            'text' => \$message,
                        ]);
                    }
                    {$assignToTryStoreEdit}
                    {$supplierIdTryStoreEdit}
                    {$customerIdTryStoreEdit}
                    {$productIdTryStoreEdit}  
                    {$leadIdTryStoreEdit}
                    {$designationIdTryStoreEdit}
                    {$departmentIdTryStoreEdit}
                    {$dateTryStoreEdit} 
                    {$fieldsTryStoreEditString} 
                    {$fileUploadStatementsString}
                    \${$moduleNameLower}->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.saved_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }
            
            public function edit(Request \$request, \$id)
            {
                \$business_id = request()->session()->get('user.business_id');
                \$type = \$request->query('type'); 
                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                {$function_audit}

                \${$moduleNameLower} = {$moduleName}::find(\$id);
                \${$moduleNameLower}_categories = {$moduleName}Category::forDropdown(\$business_id);
                \$users = User::forDropdown(\$business_id, false);
                \$customer = Contact::where('business_id', \$business_id)
                ->where('type', 'customer')
                ->pluck('name', 'id');
                \$supplier = Contact::where('business_id', \$business_id)
                ->where('type', 'supplier')
                ->pluck('supplier_business_name', 'id');
                \$product = Product::where('business_id', \$business_id)
                ->pluck('name', 'id');
                \$business_locations = BusinessLocation::forDropdown(\$business_id, false);
                \$departments = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_department')
                    ->pluck('name', 'id');

                \$designations = Category::where('business_id', \$business_id)
                    ->where('category_type', 'hrm_designation')
                    ->pluck('name', 'id');
                \$leads = \$this->crmUtil->getLeadsListQuery(\$business_id);
                return view('{$moduleNameLower}::{$moduleName}.edit', compact('{$moduleNameLower}', '{$moduleNameLower}_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
            }

            public function update(Request \$request, \$id)
            {
                \$request->validate([
                    '{$moduleNameLower}_category_id' => 'nullable|integer',
                    {$assignToStoreEdit}
                    {$supplierIdStoreEdit}
                    {$customerIdStoreEdit}
                    {$productIdStoreEdit}  
                    {$designationIdStoreEdit}
                    {$departmentIdStoreEdit}
                    {$dateStoreEdit}  
                    {$fieldsStoreEditString}                
                ]);

                try {
                    \${$moduleNameLower} = {$moduleName}::find(\$id);
                    \${$moduleNameLower}->category_id = \$request->{$moduleNameLower}_category_id;
                    \${$moduleNameLower}->created_by = auth()->user()->id;
                    {$assignToTryStoreEdit}
                    {$supplierIdTryStoreEdit}
                    {$customerIdTryStoreEdit}
                    {$productIdTryStoreEdit}
                    {$leadIdTryStoreEdit}
                    {$designationIdTryStoreEdit}
                    {$departmentIdTryStoreEdit}
                    {$dateTryStoreEdit}  
                    {$fieldsTryStoreEditString} 
                    {$fileUpdateStatementsString}

                    \${$moduleNameLower}->save();


                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.updated_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            {$update_audit_status}

            public function destroy(\$id)
            {
                try {
                    {$moduleName}::destroy(\$id);
                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.deleted_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function getCategories(Request \$request)
            {
                \$business_id = request()->session()->get('user.business_id');

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                if (request()->ajax()) {
                    \$categories = {$moduleName}Category::where('business_id', \$business_id)->orderBy('id', 'desc')->get();

                    return DataTables::of(\$categories)
                        ->addColumn('action', function (\$row) {
                            \$html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('{$moduleName}-categories.edit', \$row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                            \$html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('{$moduleName}-categories.destroy', \$row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                            return \$html;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
                }

                return view('{$moduleNameLower}::Category.index')->with(compact('module'));
            }

            public function createCategory()
            {
                \$business_id = request()->session()->get('user.business_id');

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }

                return view('{$moduleNameLower}::Category.create');
            }

            public function storeCategory(Request \$request)
            {
                \$business_id = request()->session()->get('user.business_id');

                try {
                    \$category = new {$moduleName}Category();
                    \$category->name = \$request->name;
                    if (\$request->hasFile('image')) {
                        \$documentPath = \$this->transactionUtil->uploadFile(\$request, 'image', '{$moduleName}Category');
                        \$category->{'image'} = \$documentPath;
                    }
                    \$category->description = \$request->description;
                    \$category->business_id = \$business_id;
                    \$category->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.saved_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }

            public function editCategory(\$id)
            {
                \$business_id = request()->session()->get('user.business_id');

                \$module = ModuleCreator::where('module_name', '$moduleNameLower')->first();

                \$is_admin = \$this->moduleUtil->is_admin(auth()->user(), \$business_id);
                
                if ((! auth()->user()->can('module.{$moduleNameLower}')) && ! auth()->user()->can('superadmin') && ! \$is_admin) {
                    abort(403, 'Unauthorized action.');
                }
                    
                \$category = {$moduleName}Category::find(\$id);
                return view('{$moduleNameLower}::Category.edit', compact('category'));
            }

            public function updateCategory(Request \$request, \$id)
            {
                \$business_id = request()->session()->get('user.business_id');

                try {
                    \$category = {$moduleName}Category::find(\$id);
                    \$category->name = \$request->name;
                    \$category->business_id = \$business_id;
                    if (\$request->hasFile('image')) {
                        \$oldFile = public_path('uploads/tracking/' . basename(\$category->{'image'}));
                        if (file_exists(\$oldFile)) {
                            unlink(\$oldFile);
                        }
                        \$documentPath = \$this->transactionUtil->uploadFile(\$request, 'image', '{$moduleName}Category');
                        \$category->{'image'} = \$documentPath;
                    }
                    \$category->description = \$request->description;
                    \$category->save();

                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.updated_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }
            
            public function destroyCategory(\$id)
            {
                try {
                    {$moduleName}Category::destroy(\$id);
                    return response()->json(['success' => true, 'msg' => __('{$moduleNameLower}::lang.deleted_successfully')]);
                } catch (\Exception \$e) {
                    return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
                }
            }
        }
        EOT;

        $this->files->put($controllerPath, $content);
    }
}