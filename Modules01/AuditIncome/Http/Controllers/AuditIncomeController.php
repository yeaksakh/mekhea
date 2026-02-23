<?php

namespace Modules\AuditIncome\Http\Controllers;

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
use Modules\AuditIncome\Entities\AuditIncome;
use Modules\AuditIncome\Entities\AuditIncomeCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\AuditIncome\Entities\AuditIncomeSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class AuditIncomeController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_auditincome = AuditIncome::where('business_id', $business_id)->count();

        $total_auditincome_category =AuditIncomeCategory::where('business_id', $business_id)->count();

        $auditincome_category = DB::table('auditincome_main as auditincome')
            ->leftJoin('auditincome_category as auditincomecategory', 'auditincome.category_id', '=', 'auditincomecategory.id')
            ->select(
                DB::raw('COUNT(auditincome.id) as total'),
                'auditincomecategory.name as category'
            )
            ->where('auditincome.business_id', $business_id)
            ->groupBy('auditincomecategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('auditincome::AuditIncome.dashboard')
            ->with(compact('total_auditincome', 'total_auditincome_category', 'auditincome_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $AuditIncome = AuditIncome::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $AuditIncome->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $AuditIncome->where('category_id', ${'category_id'});

            }

            $AuditIncome->get();

            return DataTables::of($AuditIncome)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditIncome.show', $row->id) . '" data-container="#AuditIncome_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditIncome.edit', $row->id) . '" data-container="#AuditIncome_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-AuditIncome" data-href="' . route('AuditIncome.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = AuditIncomeCategory::find($row->category_id);
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
        $category = AuditIncomeCategory::forDropdown($business_id);
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

        return view('auditincome::AuditIncome.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('AuditIncome.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'auditincome::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'auditincome::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'auditincome::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'auditincome::lang.createdat'],
            [
                            'id' => 'IncomeSource_1content',
                            'label' => 'auditincome::lang.IncomeSource_1',
                        ],
[
                            'id' => 'Amount_2content',
                            'label' => 'auditincome::lang.Amount_2',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AuditIncome.qrcodeView', ['id' => $id]);
        $auditincome = AuditIncome::findOrFail($id);
        $createdby = User::findOrFail($auditincome->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('auditincome::AuditIncome.qr_view')->with(compact('auditincome','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $auditincome_categories = AuditIncomeCategory::forDropdown($business_id);
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

        return view('auditincome::AuditIncome.create', compact('auditincome_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'auditincome::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'auditincome::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'auditincome::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'auditincome::lang.createdat'],
            [
                            'id' => 'IncomeSource_1content',
                            'label' => 'auditincome::lang.IncomeSource_1',
                        ],
[
                            'id' => 'Amount_2content',
                            'label' => 'auditincome::lang.Amount_2',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AuditIncome.qrcodeView', ['id' => $id]);

        $auditincome = AuditIncome::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($auditincome->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('auditincome::AuditIncome.show')->with(compact('auditincome','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'auditincome_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'IncomeSource_1' => 'nullable',
                        

                            'Amount_2' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $auditincome = new AuditIncome();
            $auditincome->business_id = $business_id;
            $auditincome->category_id = $request->auditincome_category_id;
            $auditincome->created_by = auth()->user()->id;
            $AuditIncomeSocial = AuditIncomeSocial::where('business_id', $business_id)->first();
    
            if ($AuditIncomeSocial && $AuditIncomeSocial->social_status == 1) {
                $BotToken = $AuditIncomeSocial->social_token;
                $ChatId = $AuditIncomeSocial->social_id;
                $message = __('auditincome::lang.auditincome_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $auditincome->{'IncomeSource_1'} = $request->{'IncomeSource_1'};
                        

                            $auditincome->{'Amount_2'} = $request->{'Amount_2'};
                         
            
            $auditincome->save();

            return response()->json(['success' => true, 'msg' => __('auditincome::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $auditincome = AuditIncome::find($id);
        $auditincome_categories = AuditIncomeCategory::forDropdown($business_id);
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
        return view('auditincome::AuditIncome.edit', compact('auditincome', 'auditincome_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'auditincome_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'IncomeSource_1' => 'nullable',
                        

                            'Amount_2' => 'nullable',
                                        
        ]);

        try {
            $auditincome = AuditIncome::find($id);
            $auditincome->category_id = $request->auditincome_category_id;
            $auditincome->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $auditincome->{'IncomeSource_1'} = $request->{'IncomeSource_1'};
                        

                            $auditincome->{'Amount_2'} = $request->{'Amount_2'};
                         
            

            $auditincome->save();


            return response()->json(['success' => true, 'msg' => __('auditincome::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            AuditIncome::destroy($id);
            return response()->json(['success' => true, 'msg' => __('auditincome::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = AuditIncomeCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AuditIncome-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('AuditIncome-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('auditincome::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('auditincome::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new AuditIncomeCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AuditIncomeCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('auditincome::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'auditincome')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.auditincome')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = AuditIncomeCategory::find($id);
        return view('auditincome::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = AuditIncomeCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AuditIncomeCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('auditincome::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            AuditIncomeCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('auditincome::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}