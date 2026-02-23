<?php

namespace Modules\AiStudio\Http\Controllers;

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
use Modules\AiStudio\Entities\AiStudio;
use Modules\AiStudio\Entities\AiStudioCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\AiStudio\Entities\AiStudioSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class AiStudioController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_aistudio = AiStudio::where('business_id', $business_id)->count();

        $total_aistudio_category =AiStudioCategory::where('business_id', $business_id)->count();

        $aistudio_category = DB::table('aistudio_main as aistudio')
            ->leftJoin('aistudio_category as aistudiocategory', 'aistudio.category_id', '=', 'aistudiocategory.id')
            ->select(
                DB::raw('COUNT(aistudio.id) as total'),
                'aistudiocategory.name as category'
            )
            ->where('aistudio.business_id', $business_id)
            ->groupBy('aistudiocategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('aistudio::AiStudio.dashboard')
            ->with(compact('total_aistudio', 'total_aistudio_category', 'aistudio_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $AiStudio = AiStudio::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $AiStudio->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $AiStudio->where('category_id', ${'category_id'});

            }

            $AiStudio->get();

            return DataTables::of($AiStudio)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AiStudio.show', $row->id) . '" data-container="#AiStudio_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AiStudio.edit', $row->id) . '" data-container="#AiStudio_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-AiStudio" data-href="' . route('AiStudio.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = AiStudioCategory::find($row->category_id);
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
        $category = AiStudioCategory::forDropdown($business_id);
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

        return view('aistudio::AiStudio.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('AiStudio.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'aistudio::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'aistudio::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'aistudio::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'aistudio::lang.createdat'],
            [
                            'id' => 'message_1content',
                            'label' => 'aistudio::lang.message_1',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AiStudio.qrcodeView', ['id' => $id]);
        $aistudio = AiStudio::findOrFail($id);
        $createdby = User::findOrFail($aistudio->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('aistudio::AiStudio.qr_view')->with(compact('aistudio','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $aistudio_categories = AiStudioCategory::forDropdown($business_id);
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

        return view('aistudio::AiStudio.create', compact('aistudio_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'aistudio::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'aistudio::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'aistudio::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'aistudio::lang.createdat'],
            [
                            'id' => 'message_1content',
                            'label' => 'aistudio::lang.message_1',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('AiStudio.qrcodeView', ['id' => $id]);

        $aistudio = AiStudio::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($aistudio->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('aistudio::AiStudio.show')->with(compact('aistudio','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'aistudio_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'message_1' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $aistudio = new AiStudio();
            $aistudio->business_id = $business_id;
            $aistudio->category_id = $request->aistudio_category_id;
            $aistudio->created_by = auth()->user()->id;
            $AiStudioSocial = AiStudioSocial::where('business_id', $business_id)->first();
    
            if ($AiStudioSocial && $AiStudioSocial->social_status == 1) {
                $BotToken = $AiStudioSocial->social_token;
                $ChatId = $AiStudioSocial->social_id;
                $message = __('aistudio::lang.aistudio_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $aistudio->{'message_1'} = $request->{'message_1'};
                         
            
            $aistudio->save();

            return response()->json(['success' => true, 'msg' => __('aistudio::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $aistudio = AiStudio::find($id);
        $aistudio_categories = AiStudioCategory::forDropdown($business_id);
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
        return view('aistudio::AiStudio.edit', compact('aistudio', 'aistudio_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'aistudio_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'message_1' => 'nullable',
                                        
        ]);

        try {
            $aistudio = AiStudio::find($id);
            $aistudio->category_id = $request->aistudio_category_id;
            $aistudio->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $aistudio->{'message_1'} = $request->{'message_1'};
                         
            

            $aistudio->save();


            return response()->json(['success' => true, 'msg' => __('aistudio::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            AiStudio::destroy($id);
            return response()->json(['success' => true, 'msg' => __('aistudio::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = AiStudioCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('AiStudio-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('AiStudio-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('aistudio::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('aistudio::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new AiStudioCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AiStudioCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('aistudio::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'aistudio')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.aistudio')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = AiStudioCategory::find($id);
        return view('aistudio::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = AiStudioCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'AiStudioCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('aistudio::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            AiStudioCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('aistudio::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}