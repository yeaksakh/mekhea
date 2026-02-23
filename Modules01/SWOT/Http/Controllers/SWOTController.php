<?php

namespace Modules\SWOT\Http\Controllers;

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
use Modules\SWOT\Entities\SWOT;
use Modules\SWOT\Entities\SWOTCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\SWOT\Entities\SWOTSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class SWOTController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_swot = SWOT::where('business_id', $business_id)->count();

        $total_swot_category =SWOTCategory::where('business_id', $business_id)->count();

        $swot_category = DB::table('swot_main as swot')
            ->leftJoin('swot_category as swotcategory', 'swot.category_id', '=', 'swotcategory.id')
            ->select(
                DB::raw('COUNT(swot.id) as total'),
                'swotcategory.name as category'
            )
            ->where('swot.business_id', $business_id)
            ->groupBy('swotcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('swot::SWOT.dashboard')
            ->with(compact('total_swot', 'total_swot_category', 'swot_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $SWOT = SWOT::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $SWOT->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            
            if (!empty(request()->{'category_id'})) {
                ${'category_id'} = request()->{'category_id'};
                $SWOT->where('category_id', ${'category_id'});

            }

            $SWOT->get();

            return DataTables::of($SWOT)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SWOT.show', $row->id) . '" data-container="#SWOT_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SWOT.edit', $row->id) . '" data-container="#SWOT_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-SWOT" data-href="' . route('SWOT.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
               ->addColumn('category', function ($row) {
                    $category = SWOTCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                
                
                
                
                
                
                
                
                
                                ->addColumn('Strengths_5', function ($row) {
                                    return strip_tags($row->Strengths_5);
                                })
                            

                                ->addColumn('Weaknesses_6', function ($row) {
                                    return strip_tags($row->Weaknesses_6);
                                })
                            

                                ->addColumn('Opportunities_7', function ($row) {
                                    return strip_tags($row->Opportunities_7);
                                })
                            

                                ->addColumn('Threats_8', function ($row) {
                                    return strip_tags($row->Threats_8);
                                })
                            

                                ->addColumn('Note_9', function ($row) {
                                    return strip_tags($row->Note_9);
                                })
                            
                ->rawColumns(['action', ])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = SWOTCategory::forDropdown($business_id);
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

        return view('swot::SWOT.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('SWOT.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'swot::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'swot::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'swot::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'swot::lang.createdat'],
            [
                            'id' => 'Title_1content',
                            'label' => 'swot::lang.Title_1',
                        ],
[
                            'id' => 'Strengths_5content',
                            'label' => 'swot::lang.Strengths_5',
                        ],
[
                            'id' => 'Weaknesses_6content',
                            'label' => 'swot::lang.Weaknesses_6',
                        ],
[
                            'id' => 'Opportunities_7content',
                            'label' => 'swot::lang.Opportunities_7',
                        ],
[
                            'id' => 'Threats_8content',
                            'label' => 'swot::lang.Threats_8',
                        ],
[
                            'id' => 'Note_9content',
                            'label' => 'swot::lang.Note_9',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('SWOT.qrcodeView', ['id' => $id]);
        $swot = SWOT::findOrFail($id);
        $createdby = User::findOrFail($swot->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('swot::SWOT.qr_view')->with(compact('swot','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $swot_categories = SWOTCategory::forDropdown($business_id);
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

        return view('swot::SWOT.create', compact('swot_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'swot::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'swot::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'swot::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'swot::lang.createdat'],
            [
                            'id' => 'Title_1content',
                            'label' => 'swot::lang.Title_1',
                        ],
[
                            'id' => 'Strengths_5content',
                            'label' => 'swot::lang.Strengths_5',
                        ],
[
                            'id' => 'Weaknesses_6content',
                            'label' => 'swot::lang.Weaknesses_6',
                        ],
[
                            'id' => 'Opportunities_7content',
                            'label' => 'swot::lang.Opportunities_7',
                        ],
[
                            'id' => 'Threats_8content',
                            'label' => 'swot::lang.Threats_8',
                        ],
[
                            'id' => 'Note_9content',
                            'label' => 'swot::lang.Note_9',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('SWOT.qrcodeView', ['id' => $id]);

        $swot = SWOT::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($swot->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;

        return view('swot::SWOT.show')->with(compact('swot','qrcode','link','checkboxes','name'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'swot_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'Title_1' => 'nullable',
                        

                            'Strengths_5' => 'nullable',
                        

                            'Weaknesses_6' => 'nullable',
                        

                            'Opportunities_7' => 'nullable',
                        

                            'Threats_8' => 'nullable',
                        

                            'Note_9' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $swot = new SWOT();
            $swot->business_id = $business_id;
            $swot->category_id = $request->swot_category_id;
            $swot->created_by = auth()->user()->id;
            $SWOTSocial = SWOTSocial::where('business_id', $business_id)->first();
    
            if ($SWOTSocial && $SWOTSocial->social_status == 1) {
                $BotToken = $SWOTSocial->social_token;
                $ChatId = $SWOTSocial->social_id;
                $message = __('swot::lang.swot_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $swot->{'Title_1'} = $request->{'Title_1'};
                        

                            $swot->{'Strengths_5'} = $request->{'Strengths_5'};
                        

                            $swot->{'Weaknesses_6'} = $request->{'Weaknesses_6'};
                        

                            $swot->{'Opportunities_7'} = $request->{'Opportunities_7'};
                        

                            $swot->{'Threats_8'} = $request->{'Threats_8'};
                        

                            $swot->{'Note_9'} = $request->{'Note_9'};
                         
            
            $swot->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $swot = SWOT::find($id);
        $swot_categories = SWOTCategory::forDropdown($business_id);
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
        return view('swot::SWOT.edit', compact('swot', 'swot_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'swot_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'Title_1' => 'nullable',
                        

                            'Strengths_5' => 'nullable',
                        

                            'Weaknesses_6' => 'nullable',
                        

                            'Opportunities_7' => 'nullable',
                        

                            'Threats_8' => 'nullable',
                        

                            'Note_9' => 'nullable',
                                        
        ]);

        try {
            $swot = SWOT::find($id);
            $swot->category_id = $request->swot_category_id;
            $swot->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $swot->{'Title_1'} = $request->{'Title_1'};
                        

                            $swot->{'Strengths_5'} = $request->{'Strengths_5'};
                        

                            $swot->{'Weaknesses_6'} = $request->{'Weaknesses_6'};
                        

                            $swot->{'Opportunities_7'} = $request->{'Opportunities_7'};
                        

                            $swot->{'Threats_8'} = $request->{'Threats_8'};
                        

                            $swot->{'Note_9'} = $request->{'Note_9'};
                         
            

            $swot->save();


            return response()->json(['success' => true, 'msg' => __('swot::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            SWOT::destroy($id);
            return response()->json(['success' => true, 'msg' => __('swot::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = SWOTCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('SWOT-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('SWOT-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('swot::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('swot::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new SWOTCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SWOTCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'swot')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.swot')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = SWOTCategory::find($id);
        return view('swot::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = SWOTCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'SWOTCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('swot::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            SWOTCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('swot::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}