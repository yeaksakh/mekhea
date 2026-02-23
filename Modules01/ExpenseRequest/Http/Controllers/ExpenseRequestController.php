<?php

namespace Modules\ExpenseRequest\Http\Controllers;

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
use Modules\ExpenseRequest\Entities\ExpenseRequest;
use Modules\ExpenseRequest\Entities\ExpenseRequestCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\ExpenseRequest\Entities\ExpenseRequestSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class ExpenseRequestController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_expenserequest = ExpenseRequest::where('business_id', $business_id)->count();

        $total_expenserequest_category =ExpenseRequestCategory::where('business_id', $business_id)->count();

        $expenserequest_category = DB::table('expenserequest_main as expenserequest')
            ->leftJoin('expenserequest_category as expenserequestcategory', 'expenserequest.category_id', '=', 'expenserequestcategory.id')
            ->select(
                DB::raw('COUNT(expenserequest.id) as total'),
                'expenserequestcategory.name as category'
            )
            ->where('expenserequest.business_id', $business_id)
            ->groupBy('expenserequestcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('expenserequest::ExpenseRequest.dashboard')
            ->with(compact('total_expenserequest', 'total_expenserequest_category', 'expenserequest_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $ExpenseRequest = ExpenseRequest::where('business_id', $business_id)->with(['whorequestexpense3'])->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $ExpenseRequest->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
                                if (!empty(request()->{'who_request_expense_3'})) {
                                    ${'who_request_expense_3'} = request()->{'who_request_expense_3'};
                                    $ExpenseRequest->where('who_request_expense_3', ${'who_request_expense_3'});
                                }
                            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $ExpenseRequest->where('category_id', ${'category_id'});

            }

            $ExpenseRequest->get();

            return DataTables::of($ExpenseRequest)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ExpenseRequest.show', $row->id) . '" data-container="#ExpenseRequest_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ExpenseRequest.edit', $row->id) . '" data-container="#ExpenseRequest_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-ExpenseRequest" data-href="' . route('ExpenseRequest.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = ExpenseRequestCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                    ->addColumn('who_request_expense_3', function ($row) {
                                        return $row->{'whorequestexpense3'}->first_name . ' ' . $row->{'whorequestexpense3'}->last_name;
                                    })
                                
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = ExpenseRequestCategory::forDropdown($business_id);
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

        return view('expenserequest::ExpenseRequest.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('ExpenseRequest.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'expenserequest::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'expenserequest::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'expenserequest::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'expenserequest::lang.createdat'],
            [
                            'id' => 'amount_1content',
                            'label' => 'expenserequest::lang.amount_1',
                        ],
[
                            'id' => 'expense_for_2content',
                            'label' => 'expenserequest::lang.expense_for_2',
                        ],
[
                            'id' => 'who_request_expense_3content',
                            'label' => 'expenserequest::lang.who_request_expense_3',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('ExpenseRequest.qrcodeView', ['id' => $id]);
        $expenserequest = ExpenseRequest::findOrFail($id);
        $createdby = User::findOrFail($expenserequest->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('expenserequest::ExpenseRequest.qr_view')->with(compact('expenserequest','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $expenserequest_categories = ExpenseRequestCategory::forDropdown($business_id);
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

        return view('expenserequest::ExpenseRequest.create', compact('expenserequest_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'expenserequest::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'expenserequest::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'expenserequest::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'expenserequest::lang.createdat'],
            [
                            'id' => 'amount_1content',
                            'label' => 'expenserequest::lang.amount_1',
                        ],
[
                            'id' => 'expense_for_2content',
                            'label' => 'expenserequest::lang.expense_for_2',
                        ],
[
                            'id' => 'who_request_expense_3content',
                            'label' => 'expenserequest::lang.who_request_expense_3',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('ExpenseRequest.qrcodeView', ['id' => $id]);

        $expenserequest = ExpenseRequest::where('business_id', $business_id)->with(['whorequestexpense3'])->findOrFail($id);
        $createdby = User::findOrFail($expenserequest->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('expenserequest::ExpenseRequest.show')->with(compact('expenserequest','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expenserequest_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'amount_1' => 'nullable',
                        

                            'expense_for_2' => 'nullable',
                        

                            'who_request_expense_3' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $expenserequest = new ExpenseRequest();
            $expenserequest->business_id = $business_id;
            $expenserequest->category_id = $request->expenserequest_category_id;
            $expenserequest->created_by = auth()->user()->id;
            $ExpenseRequestSocial = ExpenseRequestSocial::where('business_id', $business_id)->first();
    
            if ($ExpenseRequestSocial && $ExpenseRequestSocial->social_status == 1) {
                $BotToken = $ExpenseRequestSocial->social_token;
                $ChatId = $ExpenseRequestSocial->social_id;
                $message = __('expenserequest::lang.expenserequest_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $expenserequest->{'amount_1'} = $request->{'amount_1'};
                        

                            $expenserequest->{'expense_for_2'} = $request->{'expense_for_2'};
                        

                            $expenserequest->{'who_request_expense_3'} = $request->{'who_request_expense_3'};
                         
            
            $expenserequest->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $expenserequest = ExpenseRequest::find($id);
        $expenserequest_categories = ExpenseRequestCategory::forDropdown($business_id);
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
        return view('expenserequest::ExpenseRequest.edit', compact('expenserequest', 'expenserequest_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expenserequest_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'amount_1' => 'nullable',
                        

                            'expense_for_2' => 'nullable',
                        

                            'who_request_expense_3' => 'nullable',
                                        
        ]);

        try {
            $expenserequest = ExpenseRequest::find($id);
            $expenserequest->category_id = $request->expenserequest_category_id;
            $expenserequest->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $expenserequest->{'amount_1'} = $request->{'amount_1'};
                        

                            $expenserequest->{'expense_for_2'} = $request->{'expense_for_2'};
                        

                            $expenserequest->{'who_request_expense_3'} = $request->{'who_request_expense_3'};
                         
            

            $expenserequest->save();


            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            ExpenseRequest::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = ExpenseRequestCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ExpenseRequest-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('ExpenseRequest-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('expenserequest::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('expenserequest::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new ExpenseRequestCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ExpenseRequestCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenserequest')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.expenserequest')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = ExpenseRequestCategory::find($id);
        return view('expenserequest::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = ExpenseRequestCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ExpenseRequestCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            ExpenseRequestCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenserequest::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}