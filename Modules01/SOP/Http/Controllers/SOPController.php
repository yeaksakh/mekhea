<?php

namespace Modules\SOP\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Contact;
use App\Product;
use App\Business;
use App\Audit; 
use App\Category; 
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\SOP\Entities\SOP;
use Modules\SOP\Entities\SOPCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\SOP\Entities\SOPSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class SOPController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.sop')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_sop = SOP::where('business_id', $business_id)->count();

        $total_sop_category =SOPCategory::where('business_id', $business_id)->count();

        $sop_category = DB::table('sop_main as sop')
            ->leftJoin('sop_category as sopcategory', 'sop.category_id', '=', 'sopcategory.id')
            ->select(
                DB::raw('COUNT(sop.id) as total'),
                'sopcategory.name as category'
            )
            ->where('sop.business_id', $business_id)
            ->groupBy('sopcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('sop::SOP.dashboard')
            ->with(compact('total_sop', 'total_sop_category', 'sop_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
    

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $SOP = SOP::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $SOP->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $SOP->where('category_id', ${'category_id'});

            }

            $SOP->get();

            return DataTables::of($SOP)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SOP.show', $row->id) . '" data-container="#SOP_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SOP.edit', $row->id) . '" data-container="#SOP_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-SOP" data-href="' . route('SOP.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = SOPCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                ->addColumn('description_5', function ($row) {
                                    return strip_tags($row->description_5);
                                })
                            
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = SOPCategory::forDropdown($business_id);
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

        return view('sop::SOP.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('SOP.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'sop::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'sop::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'sop::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'sop::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'sop::lang.title_1',
                        ],
[
                            'id' => 'description_5content',
                            'label' => 'sop::lang.description_5',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('SOP.qrcodeView', ['id' => $id]);
        $sop = SOP::findOrFail($id);
        $createdby = User::findOrFail($sop->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('sop::SOP.qr_view')->with(compact('sop','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $sop_categories = SOPCategory::forDropdown($business_id);
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

        return view('sop::SOP.create', compact('sop_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'sop::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'sop::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'sop::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'sop::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'sop::lang.title_1',
                        ],
[
                            'id' => 'description_5content',
                            'label' => 'sop::lang.description_5',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $business = Business::where('id', $business_id)->first();

        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        

        $sop = SOP::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($sop->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        $date_range = $request->query('date_range');

        return view('sop::SOP.show')->with(compact('sop','checkboxes','name', 'business', 'print_by', 'date_range'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sop_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'description_5' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $sop = new SOP();
            $sop->business_id = $business_id;
            $sop->category_id = $request->sop_category_id;
            $sop->created_by = auth()->user()->id;
            $SOPSocial = SOPSocial::where('business_id', $business_id)->first();
    
            if ($SOPSocial && $SOPSocial->social_status == 1) {
                $BotToken = $SOPSocial->social_token;
                $ChatId = $SOPSocial->social_id;
                $message = __('sop::lang.sop_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $sop->{'title_1'} = $request->{'title_1'};
                        

                            $sop->{'description_5'} = $request->{'description_5'};
                         
            
            $sop->save();

            return response()->json(['success' => true, 'msg' => __('sop::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
    

        

        $sop = SOP::find($id);
        $sop_categories = SOPCategory::forDropdown($business_id);
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
        return view('sop::SOP.edit', compact('sop', 'sop_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'sop_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'description_5' => 'nullable',
                                        
        ]);

        try {
            $sop = SOP::find($id);
            $sop->category_id = $request->sop_category_id;
            $sop->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $sop->{'title_1'} = $request->{'title_1'};
                        

                            $sop->{'description_5'} = $request->{'description_5'};
                         
            

            $sop->save();


            return response()->json(['success' => true, 'msg' => __('sop::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            SOP::destroy($id);
            return response()->json(['success' => true, 'msg' => __('sop::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
    

        if (request()->ajax()) {
            $categories = SOPCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SOP-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('SOP-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('sop::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
    

        return view('sop::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new SOPCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SOPCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('sop::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'sop')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
    
            
        $category = SOPCategory::find($id);
        return view('sop::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = SOPCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SOPCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('sop::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            SOPCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('sop::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}