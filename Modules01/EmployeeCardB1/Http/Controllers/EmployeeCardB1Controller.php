<?php

namespace Modules\EmployeeCardB1\Http\Controllers;

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
use Modules\EmployeeCardB1\Entities\EmployeeCardB1;
use Modules\EmployeeCardB1\Entities\EmployeeCardB1Category;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\EmployeeCardB1\Entities\EmployeeCardB1Social;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class EmployeeCardB1Controller extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_employeecardb1 = EmployeeCardB1::where('business_id', $business_id)->count();

        $total_employeecardb1_category =EmployeeCardB1Category::where('business_id', $business_id)->count();

        $employeecardb1_category = DB::table('employeecardb1_main as employeecardb1')
            ->leftJoin('employeecardb1_category as employeecardb1category', 'employeecardb1.category_id', '=', 'employeecardb1category.id')
            ->select(
                DB::raw('COUNT(employeecardb1.id) as total'),
                'employeecardb1category.name as category'
            )
            ->where('employeecardb1.business_id', $business_id)
            ->groupBy('employeecardb1category.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('employeecardb1::EmployeeCardB1.dashboard')
            ->with(compact('total_employeecardb1', 'total_employeecardb1_category', 'employeecardb1_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $EmployeeCardB1 = EmployeeCardB1::where('business_id', $business_id)->with(['employee1'])->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $EmployeeCardB1->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
                                if (!empty(request()->{'employee_1'})) {
                                    ${'employee_1'} = request()->{'employee_1'};
                                    $EmployeeCardB1->where('employee_1', ${'employee_1'});
                                }
                            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $EmployeeCardB1->where('category_id', ${'category_id'});

            }

            $EmployeeCardB1->get();

            return DataTables::of($EmployeeCardB1)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('EmployeeCardB1.show', $row->id) . '" data-container="#EmployeeCardB1_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('EmployeeCardB1.edit', $row->id) . '" data-container="#EmployeeCardB1_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-EmployeeCardB1" data-href="' . route('EmployeeCardB1.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = EmployeeCardB1Category::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                    ->addColumn('employee_1', function ($row) {
                                        return $row->{'employee1'}->first_name . ' ' . $row->{'employee1'}->last_name;
                                    })
                                
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = EmployeeCardB1Category::forDropdown($business_id);
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

        return view('employeecardb1::EmployeeCardB1.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('EmployeeCardB1.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'employeecardb1::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'employeecardb1::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'employeecardb1::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'employeecardb1::lang.createdat'],
            [
                            'id' => 'employee_1content',
                            'label' => 'employeecardb1::lang.employee_1',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('EmployeeCardB1.qrcodeView', ['id' => $id]);
        $employeecardb1 = EmployeeCardB1::findOrFail($id);
        $createdby = User::findOrFail($employeecardb1->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('employeecardb1::EmployeeCardB1.qr_view')->with(compact('employeecardb1','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $employeecardb1_categories = EmployeeCardB1Category::forDropdown($business_id);
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

        return view('employeecardb1::EmployeeCardB1.create', compact('employeecardb1_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'employeecardb1::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'employeecardb1::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'employeecardb1::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'employeecardb1::lang.createdat'],
            [
                            'id' => 'employee_1content',
                            'label' => 'employeecardb1::lang.employee_1',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('EmployeeCardB1.qrcodeView', ['id' => $id]);

        $employeecardb1 = EmployeeCardB1::where('business_id', $business_id)->with(['employee1'])->findOrFail($id);
        $createdby = User::findOrFail($employeecardb1->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('employeecardb1::EmployeeCardB1.show')->with(compact('employeecardb1','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employeecardb1_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'employee_1' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $employeecardb1 = new EmployeeCardB1();
            $employeecardb1->business_id = $business_id;
            $employeecardb1->category_id = $request->employeecardb1_category_id;
            $employeecardb1->created_by = auth()->user()->id;
            $EmployeeCardB1Social = EmployeeCardB1Social::where('business_id', $business_id)->first();
    
            if ($EmployeeCardB1Social && $EmployeeCardB1Social->social_status == 1) {
                $BotToken = $EmployeeCardB1Social->social_token;
                $ChatId = $EmployeeCardB1Social->social_id;
                $message = __('employeecardb1::lang.employeecardb1_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $employeecardb1->{'employee_1'} = $request->{'employee_1'};
                         
            
            $employeecardb1->save();

            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $employeecardb1 = EmployeeCardB1::find($id);
        $employeecardb1_categories = EmployeeCardB1Category::forDropdown($business_id);
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
        return view('employeecardb1::EmployeeCardB1.edit', compact('employeecardb1', 'employeecardb1_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employeecardb1_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'employee_1' => 'nullable',
                                        
        ]);

        try {
            $employeecardb1 = EmployeeCardB1::find($id);
            $employeecardb1->category_id = $request->employeecardb1_category_id;
            $employeecardb1->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $employeecardb1->{'employee_1'} = $request->{'employee_1'};
                         
            

            $employeecardb1->save();


            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            EmployeeCardB1::destroy($id);
            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = EmployeeCardB1Category::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('EmployeeCardB1-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('EmployeeCardB1-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('employeecardb1::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('employeecardb1::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new EmployeeCardB1Category();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'EmployeeCardB1Category');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'employeecardb1')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.employeecardb1')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = EmployeeCardB1Category::find($id);
        return view('employeecardb1::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = EmployeeCardB1Category::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'EmployeeCardB1Category');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            EmployeeCardB1Category::destroy($id);
            return response()->json(['success' => true, 'msg' => __('employeecardb1::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}