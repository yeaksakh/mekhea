<?php

namespace Modules\DocumentKeeper\Http\Controllers;

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
use App\Business;
use App\Category; 
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\DocumentKeeper\Entities\DocumentKeeper;
use Modules\DocumentKeeper\Entities\DocumentKeeperCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\DocumentKeeper\Entities\DocumentKeeperSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class DocumentKeeperController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $total_documentkeeper = DocumentKeeper::where('business_id', $business_id)->count();

        $total_documentkeeper_category =DocumentKeeperCategory::where('business_id', $business_id)->count();

        $documentkeeper_category = DB::table('documentkeeper_main as documentkeeper')
            ->leftJoin('documentkeeper_category as documentkeepercategory', 'documentkeeper.category_id', '=', 'documentkeepercategory.id')
            ->select(
                DB::raw('COUNT(documentkeeper.id) as total'),
                'documentkeepercategory.name as category'
            )
            ->where('documentkeeper.business_id', $business_id)
            ->groupBy('documentkeepercategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('documentkeeper::DocumentKeeper.dashboard')
            ->with(compact('total_documentkeeper', 'total_documentkeeper_category', 'documentkeeper_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $DocumentKeeper = DocumentKeeper::where('business_id', $business_id)->orderBy('id','desc');
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $DocumentKeeper->whereDate('created_at', '>=', $start)
                    ->whereDate('created_at', '<=', $end);
            }

            if (!empty(request()->category_id)) {
                $category_id = request()->category_id;
                $DocumentKeeper->where('category_id', $category_id);
            }

            $DocumentKeeper->get();

            return DataTables::of($DocumentKeeper)
                ->addColumn('action', function ($row) {
                    $start_date = request()->get('start_date');
                    $end_date = request()->get('end_date');
                    
                    $url_params = ['id' => $row->id];
                    if (!empty($start_date) && !empty($end_date)) {
                        $url_params['start_date'] = $start_date;
                        $url_params['end_date'] = $end_date;
                    }

                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('DocumentKeeper.show', $url_params) . '" data-container="#DocumentKeeper_modal" style="margin-right: 5px;"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('DocumentKeeper.edit', $row->id) . '" data-container="#DocumentKeeper_modal" style="margin-right: 5px;"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-DocumentKeeper" data-href="' . route('DocumentKeeper.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->addColumn('category', function ($row) {
                    $category = DocumentKeeperCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    $name = $user->first_name . ' ' . $user->last_name;
                    return $name ? $name : '';
                })
                ->addColumn('title_1', function ($row) {
                    return strip_tags($row->title_1); // Strip HTML tags
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        $users = User::forDropdown($business_id, false, true, true);
        $category = DocumentKeeperCategory::forDropdown($business_id);
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

        return view('documentkeeper::DocumentKeeper.index')->with(compact('module','leads', 'users','customer', 'product', 'supplier', 'business_locations','category','departments','designations'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('DocumentKeeper.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'documentkeeper::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'documentkeeper::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'documentkeeper::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'documentkeeper::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'documentkeeper::lang.title_1',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('DocumentKeeper.qrcodeView', ['id' => $id]);
        $documentkeeper = DocumentKeeper::findOrFail($id);
        $createdby = User::findOrFail($documentkeeper->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('documentkeeper::DocumentKeeper.qr_view')->with(compact('documentkeeper','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $documentkeeper_categories = DocumentKeeperCategory::forDropdown($business_id);
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

        return view('documentkeeper::DocumentKeeper.create', compact('documentkeeper_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'documentkeeper::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'documentkeeper::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'documentkeeper::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'documentkeeper::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'documentkeeper::lang.title_1',
                        ],

        ];
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $documentkeeper = DocumentKeeper::where('business_id', $business_id)->findOrFail($id);
        $createdby = User::findOrFail($documentkeeper->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;

        $start_date = $request->query('start_date');
        $end_date = $request->query('end_date');
        $date_range = null;

        if (!empty($start_date) && !empty($end_date)) {
            $date_range = \Carbon\Carbon::parse($start_date)->format('Y-m-d') . ' ~ ' . \Carbon\Carbon::parse($end_date)->format('Y-m-d');
        }

        return view('documentkeeper::DocumentKeeper.show')->with(compact('documentkeeper','checkboxes','name', 'business', 'print_by', 'start_date', 'end_date', 'date_range'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'documentkeeper_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $documentkeeper = new DocumentKeeper();
            $documentkeeper->business_id = $business_id;
            $documentkeeper->category_id = $request->documentkeeper_category_id;
            $documentkeeper->created_by = auth()->user()->id;
            $DocumentKeeperSocial = DocumentKeeperSocial::where('business_id', $business_id)->first();
    
            if ($DocumentKeeperSocial && $DocumentKeeperSocial->social_status == 1) {
                $BotToken = $DocumentKeeperSocial->social_token;
                $ChatId = $DocumentKeeperSocial->social_id;
                $message = __('documentkeeper::lang.documentkeeper_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $documentkeeper->{'title_1'} = $request->{'title_1'};
                         
            
                            if ($request->hasFile('file_2')) {
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_2', 'DocumentKeeper');
                                $documentkeeper->{'file_2'} = $documentPath;
                            }
                        
            $documentkeeper->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        

        $documentkeeper = DocumentKeeper::find($id);
        $documentkeeper_categories = DocumentKeeperCategory::forDropdown($business_id);
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
        return view('documentkeeper::DocumentKeeper.edit', compact('documentkeeper', 'documentkeeper_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'documentkeeper_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                                        
        ]);

        try {
            $documentkeeper = DocumentKeeper::find($id);
            $documentkeeper->category_id = $request->documentkeeper_category_id;
            $documentkeeper->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $documentkeeper->{'title_1'} = $request->{'title_1'};
                         
            
                            if ($request->hasFile('file_2')) {
                                $oldFile = public_path('uploads/tracking/' . basename($documentkeeper->{'file_2'}));
                                if (file_exists($oldFile)) {
                                    unlink($oldFile);
                                }
                                $documentPath = $this->transactionUtil->uploadFile($request, 'file_2', 'DocumentKeeper');
                                $documentkeeper->{'file_2'} = $documentPath;
                            }
                        

            $documentkeeper->save();


            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            DocumentKeeper::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            $categories = DocumentKeeperCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('DocumentKeeper-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('DocumentKeeper-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('documentkeeper::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('documentkeeper::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new DocumentKeeperCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'DocumentKeeperCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'documentkeeper')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        // if ((! auth()->user()->can('module.documentkeeper')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }
            
        $category = DocumentKeeperCategory::find($id);
        return view('documentkeeper::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = DocumentKeeperCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'DocumentKeeperCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            DocumentKeeperCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('documentkeeper::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}