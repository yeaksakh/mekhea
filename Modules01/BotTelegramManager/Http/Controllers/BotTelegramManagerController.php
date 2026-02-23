<?php

namespace Modules\BotTelegramManager\Http\Controllers;

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
use Modules\BotTelegramManager\Entities\BotTelegramManager;
use Modules\BotTelegramManager\Entities\BotTelegramManagerCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\BotTelegramManager\Entities\BotTelegramManagerSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class BotTelegramManagerController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_bottelegrammanager = BotTelegramManager::where('business_id', $business_id)->count();

        $total_bottelegrammanager_category =BotTelegramManagerCategory::where('business_id', $business_id)->count();

        $bottelegrammanager_category = DB::table('bottelegrammanager_main as bottelegrammanager')
            ->leftJoin('bottelegrammanager_category as bottelegrammanagercategory', 'bottelegrammanager.category_id', '=', 'bottelegrammanagercategory.id')
            ->select(
                DB::raw('COUNT(bottelegrammanager.id) as total'),
                'bottelegrammanagercategory.name as category'
            )
            ->where('bottelegrammanager.business_id', $business_id)
            ->groupBy('bottelegrammanagercategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('bottelegrammanager::BotTelegramManager.dashboard')
            ->with(compact('total_bottelegrammanager', 'total_bottelegrammanager_category', 'bottelegrammanager_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $BotTelegramManager = BotTelegramManager::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $BotTelegramManager->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $BotTelegramManager->where('category_id', ${'category_id'});

            }

            $BotTelegramManager->get();

            return DataTables::of($BotTelegramManager)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BotTelegramManager.show', $row->id) . '" data-container="#BotTelegramManager_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BotTelegramManager.edit', $row->id) . '" data-container="#BotTelegramManager_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-BotTelegramManager" data-href="' . route('BotTelegramManager.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = BotTelegramManagerCategory::find($row->category_id);
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
        $category = BotTelegramManagerCategory::forDropdown($business_id);
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

        return view('bottelegrammanager::BotTelegramManager.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('BotTelegramManager.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'bottelegrammanager::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'bottelegrammanager::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'bottelegrammanager::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'bottelegrammanager::lang.createdat'],
            

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('BotTelegramManager.qrcodeView', ['id' => $id]);
        $bottelegrammanager = BotTelegramManager::findOrFail($id);
        $createdby = User::findOrFail($bottelegrammanager->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('bottelegrammanager::BotTelegramManager.qr_view')->with(compact('bottelegrammanager','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $bottelegrammanager_categories = BotTelegramManagerCategory::forDropdown($business_id);
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

        return view('bottelegrammanager::BotTelegramManager.create', compact('bottelegrammanager_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('BotTelegramManager.qrcodeView', ['id' => $id]);

        $bottelegrammanager = BotTelegramManager::where('business_id', $business_id)->findOrFail($id);
        $bottelegrammanager = BotTelegramManager::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $bottelegrammanager->getAttributes();

        // Find the first field that ends with '1'
        $first_field = null;
        foreach ($attributes as $fieldName => $fieldValue) {
            if (str_ends_with($fieldName, '1')) {
                $first_field = $fieldValue;
                break; // Get the first one found
            }
        }

        // Or get all fields that end with '1'
        $fields_ending_with_1 = [];
        foreach ($attributes as $fieldName => $fieldValue) {
            if (str_ends_with($fieldName, '1')) {
                $fields_ending_with_1[$fieldName] = $fieldValue;
            }
        }
        
        
        $createdby = User::findOrFail($bottelegrammanager->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('bottelegrammanager::BotTelegramManager.show')->with(compact('bottelegrammanager','name','print_by', 'date_range','first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bottelegrammanager_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
                            
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $bottelegrammanager = new BotTelegramManager();
            $bottelegrammanager->business_id = $business_id;
            $bottelegrammanager->category_id = $request->bottelegrammanager_category_id;
            $bottelegrammanager->created_by = auth()->user()->id;
            $BotTelegramManagerSocial = BotTelegramManagerSocial::where('business_id', $business_id)->first();
    
            if ($BotTelegramManagerSocial && $BotTelegramManagerSocial->social_status == 1) {
                $BotToken = $BotTelegramManagerSocial->social_token;
                $ChatId = $BotTelegramManagerSocial->social_id;
                $message = __('bottelegrammanager::lang.bottelegrammanager_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
             
            
                            if ($request->hasFile('id_1')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'id_1', 'BotTelegramManager');
                                $bottelegrammanager->{'id_1'} = $documentPath;
                            }
                        
            $bottelegrammanager->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $bottelegrammanager = BotTelegramManager::find($id);
        $bottelegrammanager_categories = BotTelegramManagerCategory::forDropdown($business_id);
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
        return view('bottelegrammanager::BotTelegramManager.edit', compact('bottelegrammanager', 'bottelegrammanager_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bottelegrammanager_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
                            
        ]);

        try {
            $bottelegrammanager = BotTelegramManager::find($id);
            $bottelegrammanager->category_id = $request->bottelegrammanager_category_id;
            $bottelegrammanager->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
             
            
                            if ($request->hasFile('id_1')) {
                                $oldFile = public_path('uploads/tracking/' . basename($bottelegrammanager->{'id_1'}));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'id_1', 'BotTelegramManager');
                                $bottelegrammanager->{'id_1'} = $documentPath;
                            }
                        

            $bottelegrammanager->save();


            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            BotTelegramManager::destroy($id);
            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = BotTelegramManagerCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('BotTelegramManager-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('BotTelegramManager-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('bottelegrammanager::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('bottelegrammanager::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new BotTelegramManagerCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'BotTelegramManagerCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'bottelegrammanager')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.bottelegrammanager')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = BotTelegramManagerCategory::find($id);
        return view('bottelegrammanager::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = BotTelegramManagerCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'BotTelegramManagerCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            BotTelegramManagerCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('bottelegrammanager::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}