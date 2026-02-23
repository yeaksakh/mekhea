<?php

namespace Modules\CustomerCardB1\Http\Controllers;

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
use Modules\CustomerCardB1\Entities\CustomerCardB1;
use Modules\CustomerCardB1\Entities\CustomerCardB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\CustomerCardB1\Entities\CustomerCardB1Social;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class CustomerCardB1Controller extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_customercardb1 = CustomerCardB1::where('business_id', $business_id)->count();

        $total_customercardb1_category =CustomerCardB1Category::where('business_id', $business_id)->count();

        $customercardb1_category = DB::table('customercardb1_main as customercardb1')
            ->leftJoin('customercardb1_category as customercardb1category', 'customercardb1.category_id', '=', 'customercardb1category.id')
            ->select(
                DB::raw('COUNT(customercardb1.id) as total'),
                'customercardb1category.name as category'
            )
            ->where('customercardb1.business_id', $business_id)
            ->groupBy('customercardb1category.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('customercardb1::CustomerCardB1.dashboard')
            ->with(compact('total_customercardb1', 'total_customercardb1_category', 'customercardb1_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $CustomerCardB1 = CustomerCardB1::where('business_id', $business_id)->with(['customer1'])->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $CustomerCardB1->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
                                if (!empty(request()->{'customer_1'})) {
                                    ${'customer_1'} = request()->{'customer_1'};
                                    $CustomerCardB1->where('customer_1', ${'customer_1'});
                                }
                            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $CustomerCardB1->where('category_id', ${'category_id'});

            }

            $CustomerCardB1->get();

            return DataTables::of($CustomerCardB1)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('CustomerCardB1.show', $row->id) . '" data-container="#CustomerCardB1_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('CustomerCardB1.edit', $row->id) . '" data-container="#CustomerCardB1_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-CustomerCardB1" data-href="' . route('CustomerCardB1.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = CustomerCardB1Category::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                    ->addColumn('customer_1', function ($row) {
                                        return $row->{'customer1'}->name;
                                    })
                                
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = CustomerCardB1Category::forDropdown($business_id);
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

        return view('customercardb1::CustomerCardB1.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('CustomerCardB1.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'customercardb1::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'customercardb1::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'customercardb1::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'customercardb1::lang.createdat'],
            [
                            'id' => 'customer_1content',
                            'label' => 'customercardb1::lang.customer_1',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('CustomerCardB1.qrcodeView', ['id' => $id]);
        $customercardb1 = CustomerCardB1::findOrFail($id);
        $createdby = User::findOrFail($customercardb1->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('customercardb1::CustomerCardB1.qr_view')->with(compact('customercardb1','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $customercardb1_categories = CustomerCardB1Category::forDropdown($business_id);
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

        return view('customercardb1::CustomerCardB1.create', compact('customercardb1_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'customercardb1::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'customercardb1::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'customercardb1::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'customercardb1::lang.createdat'],
            [
                            'id' => 'customer_1content',
                            'label' => 'customercardb1::lang.customer_1',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('CustomerCardB1.qrcodeView', ['id' => $id]);

        $customercardb1 = CustomerCardB1::where('business_id', $business_id)->with(['customer1'])->findOrFail($id);
        $createdby = User::findOrFail($customercardb1->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('customercardb1::CustomerCardB1.show')->with(compact('customercardb1','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customercardb1_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'customer_1' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $customercardb1 = new CustomerCardB1();
            $customercardb1->business_id = $business_id;
            $customercardb1->category_id = $request->customercardb1_category_id;
            $customercardb1->created_by = auth()->user()->id;
            $CustomerCardB1Social = CustomerCardB1Social::where('business_id', $business_id)->first();
    
            if ($CustomerCardB1Social && $CustomerCardB1Social->social_status == 1) {
                $BotToken = $CustomerCardB1Social->social_token;
                $ChatId = $CustomerCardB1Social->social_id;
                $message = __('customercardb1::lang.customercardb1_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $customercardb1->{'customer_1'} = $request->{'customer_1'};
                         
            
            $customercardb1->save();

            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $customercardb1 = CustomerCardB1::find($id);
        $customercardb1_categories = CustomerCardB1Category::forDropdown($business_id);
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
        return view('customercardb1::CustomerCardB1.edit', compact('customercardb1', 'customercardb1_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customercardb1_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'customer_1' => 'nullable',
                                        
        ]);

        try {
            $customercardb1 = CustomerCardB1::find($id);
            $customercardb1->category_id = $request->customercardb1_category_id;
            $customercardb1->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $customercardb1->{'customer_1'} = $request->{'customer_1'};
                         
            

            $customercardb1->save();


            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            CustomerCardB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = CustomerCardB1Category::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('CustomerCardB1-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('CustomerCardB1-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('customercardb1::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('customercardb1::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new CustomerCardB1Category();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'CustomerCardB1Category');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'customercardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.customercardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = CustomerCardB1Category::find($id);
        return view('customercardb1::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = CustomerCardB1Category::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'CustomerCardB1Category');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            CustomerCardB1Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('customercardb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}