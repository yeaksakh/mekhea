<?php

namespace Modules\AuditExpense\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Contact;
use App\Product;
use App\Audit; 
use App\Category; 
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\AuditExpense\Entities\AuditExpense;
use Modules\AuditExpense\Entities\AuditExpenseCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\AuditExpense\Entities\AuditExpenseSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class AuditExpenseController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    )
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_auditexpense = AuditExpense::where('business_id', $business_id)->count();

        $total_auditexpense_category =AuditExpenseCategory::where('business_id', $business_id)->count();

        $auditexpense_category = DB::table('auditexpense_main as auditexpense')
            ->leftJoin('auditexpense_category as auditexpensecategory', 'auditexpense.category_id', '=', 'auditexpensecategory.id')
            ->select(
                DB::raw('COUNT(auditexpense.id) as total'),
                'auditexpensecategory.name as category'
            )
            ->where('auditexpense.business_id', $business_id)
            ->groupBy('auditexpensecategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('auditexpense::AuditExpense.dashboard')
            ->with(compact('total_auditexpense', 'total_auditexpense_category', 'auditexpense_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $AuditExpense = AuditExpense::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $AuditExpense->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $AuditExpense->where('category_id', ${'category_id'});

            }

            $AuditExpense->get();

            return DataTables::of($AuditExpense)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditExpense.show', $row->id) . '" data-container="#AuditExpense_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditExpense.edit', $row->id) . '" data-container="#AuditExpense_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-AuditExpense" data-href="' . route('AuditExpense.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = AuditExpenseCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = AuditExpenseCategory::forDropdown($business_id);
        $customer = Contact::where('business_id', $business_id)
        ->where('type', 'customer')
        ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
        ->where('type', 'supplier')
        ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
        ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('auditexpense::AuditExpense.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('AuditExpense.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'auditexpense::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'auditexpense::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'auditexpense::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'auditexpense::lang.createdat'],
            [
                            'id' => 'ExpenseSource_1content',
                            'label' => 'auditexpense::lang.ExpenseSource_1',
                        ],
[
                            'id' => 'Amount_2content',
                            'label' => 'auditexpense::lang.Amount_2',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AuditExpense.qrcodeView', ['id' => $id]);
        $auditexpense = AuditExpense::findOrFail($id);
        $createdby = User::findOrFail($auditexpense->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('auditexpense::AuditExpense.qr_view')->with(compact('auditexpense','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $auditexpense_categories = AuditExpenseCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
        ->where('type', 'customer')
        ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
        ->where('type', 'supplier')
        ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
        ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('auditexpense::AuditExpense.create', compact('auditexpense_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'auditexpense::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'auditexpense::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'auditexpense::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'auditexpense::lang.createdat'],
            [
                            'id' => 'ExpenseSource_1content',
                            'label' => 'auditexpense::lang.ExpenseSource_1',
                        ],
[
                            'id' => 'Amount_2content',
                            'label' => 'auditexpense::lang.Amount_2',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AuditExpense.qrcodeView', ['id' => $id]);

        $auditexpense = AuditExpense::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($auditexpense->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('auditexpense::AuditExpense.show')->with(compact('auditexpense','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'auditexpense_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'ExpenseSource_1' => 'nullable',
                        

                            'Amount_2' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $auditexpense = new AuditExpense();
            $auditexpense->business_id = $business_id;
            $auditexpense->category_id = $request->auditexpense_category_id;
            $auditexpense->created_by = auth()->user()->id;
            $AuditExpenseSocial = AuditExpenseSocial::where('business_id', $business_id)->first();
    
            if ($AuditExpenseSocial && $AuditExpenseSocial->social_status == 1) {
                $BotToken = $AuditExpenseSocial->social_token;
                $ChatId = $AuditExpenseSocial->social_id;
                $message = __('auditexpense::lang.auditexpense_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $auditexpense->{'ExpenseSource_1'} = $request->{'ExpenseSource_1'};
                        

                            $auditexpense->{'Amount_2'} = $request->{'Amount_2'};
                         
            
            $auditexpense->save();

            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $auditexpense = AuditExpense::find($id);
        $auditexpense_categories = AuditExpenseCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)
        ->where('type', 'customer')
        ->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)
        ->where('type', 'supplier')
        ->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)
        ->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_department')
            ->pluck('name', 'id');

        $designations = Category::where('business_id', $business_id)
            ->where('category_type', 'hrm_designation')
            ->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);
        return view('auditexpense::AuditExpense.edit', compact('auditexpense', 'auditexpense_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'auditexpense_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'ExpenseSource_1' => 'nullable',
                        

                            'Amount_2' => 'nullable',
                                        
        ]);

        try {
            $auditexpense = AuditExpense::find($id);
            $auditexpense->category_id = $request->auditexpense_category_id;
            $auditexpense->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $auditexpense->{'ExpenseSource_1'} = $request->{'ExpenseSource_1'};
                        

                            $auditexpense->{'Amount_2'} = $request->{'Amount_2'};
                         
            

            $auditexpense->save();


            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            AuditExpense::destroy($id);
            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = AuditExpenseCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditExpense-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('AuditExpense-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('auditexpense::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('auditexpense::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new AuditExpenseCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AuditExpenseCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditexpense')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditexpense')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = AuditExpenseCategory::find($id);
        return view('auditexpense::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = AuditExpenseCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AuditExpenseCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            AuditExpenseCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('auditexpense::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}