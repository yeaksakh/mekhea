<?php

namespace Modules\PurchaseAutoFill\Http\Controllers;

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
use Modules\PurchaseAutoFill\Entities\PurchaseAutoFill;
use Modules\PurchaseAutoFill\Entities\PurchaseAutoFillCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\PurchaseAutoFill\Entities\PurchaseAutoFillSocial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class PurchaseAutoFillController extends Controller
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
        
        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_purchaseautofill = PurchaseAutoFill::where('business_id', $business_id)->count();

        $total_purchaseautofill_category =PurchaseAutoFillCategory::where('business_id', $business_id)->count();

        $purchaseautofill_category = DB::table('purchaseautofill_main as purchaseautofill')
            ->leftJoin('purchaseautofill_category as purchaseautofillcategory', 'purchaseautofill.category_id', '=', 'purchaseautofillcategory.id')
            ->select(
                DB::raw('COUNT(purchaseautofill.id) as total'),
                'purchaseautofillcategory.name as category'
            )
            ->where('purchaseautofill.business_id', $business_id)
            ->groupBy('purchaseautofillcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('purchaseautofill::PurchaseAutoFill.dashboard')
            ->with(compact('total_purchaseautofill', 'total_purchaseautofill_category', 'purchaseautofill_category', 'module'));
    }

    public function index(Request $request)
    {
         $business_id = request()->session()->get('user.business_id');

        // Permission check - adjust permissions as needed
        // if (!auth()->user()->can('purchaseautofill.view') && !auth()->user()->can('purchaseautofill.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {
            // Query the database instead of the Telegram API
            $query = TelegramOcrData::where('business_id', $business_id)
                ->select([
                    'id',
                    'telegram_file_id',
                    'telegram_from',
                    'telegram_date',
                    'ocr_status',
                    'final_total',
                    'telegram_file_size',
                    'telegram_width',
                    'telegram_height',
                    'image_path' // Add image_path to use for preview
                ]);

            // Apply filters
            if (!empty(request()->ocr_status)) {
                $query->where('ocr_status', request()->ocr_status);
            }

            if (!empty(request()->from_date) && !empty(request()->to_date)) {
                $fromDate = request()->from_date . ' 00:00:00';
                $toDate = request()->to_date . ' 23:59:59';
                $query->whereBetween('telegram_date', [$fromDate, $toDate]);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
        <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
            Actions <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can('purchaseautofill.view')) {
                        $html .= '<li><a href="#" class="view-image" data-id="' . $row->id . '">
            <i class="fas fa-eye"></i> View
        </a></li>';
                    }

                    if (auth()->user()->can('purchaseautofill.delete')) {
                        $html .= '<li><a href="#" class="delete-image" data-id="' . $row->id . '">
            <i class="fas fa-trash"></i> Delete
        </a></li>';
                    }

                    if (auth()->user()->can('purchaseautofill.prefill')) {
                        $url = route('purchaseautofill.prefill', $row->id);
                        $html .= '<li><a href="' . $url . '" class="accept-ocr">
            <i class="fa fa-plug"></i> Set Prefill
        </a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    if (!$row->image_path) {
                        return '<div style="display: flex;">No Image</div>';
                    }

                    $url = asset($row->image_path);
                    // dd($url);
                    return '<div style="display: flex;"><img src="' . $url . '" alt="Telegram Image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('ocr_status', function ($row) {
                    $statusClass = '';
                    switch ($row->ocr_status) {
                        case 'pending':
                            $statusClass = 'bg-yellow';
                            break;
                        case 'processing':
                            $statusClass = 'bg-blue';
                            break;
                        case 'completed':
                            $statusClass = 'bg-green';
                            break;
                        case 'failed':
                            $statusClass = 'bg-red';
                            break;
                        default:
                            $statusClass = 'bg-gray';
                    }
                    return '<span class="label ' . $statusClass . '">' . ucfirst($row->ocr_status) . '</span>';
                })
                ->editColumn('telegram_date', '{{@format_datetime($telegram_date)}}')
                ->editColumn('final_total', '<span class="final_total" data-orig-value="{{$final_total}}">@format_currency($final_total)</span>')
                ->editColumn('telegram_file_size', function ($row) {
                    return $this->formatFileSize($row->telegram_file_size);
                })
                ->addColumn('dimensions', function ($row) {
                    return $row->telegram_width . 'x' . $row->telegram_height;
                })
                // Remove columns that are not needed in the final table output
                ->removeColumn('image_path')
                ->removeColumn('telegram_width')
                ->removeColumn('telegram_height')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('purchaseautofill.view')) {
                            return url("/purchaseautofill/bot-image/{$row->id}"); // Use DB ID
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'image', 'ocr_status', 'final_total'])
                ->make(true);
        }

        // For non-ajax requests, prepare data for the view
        $ocrStatuses = [
            '' => 'All Status',
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];

        return view('purchaseautofill::PurchaseAutoFill.bot-images')
            ->with(compact('ocrStatuses'));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('PurchaseAutoFill.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id){
        $checkboxes = [
            
            ['id' => 'categorycontent',
            'label' => 'purchaseautofill::lang.category'],
            ['id' => 'qrcontent',
            'label' => 'purchaseautofill::lang.qrcode'],
            ['id' => 'createdbycontent',
            'label' => 'purchaseautofill::lang.createdby'],
            ['id' => 'createdatcontent',
            'label' => 'purchaseautofill::lang.createdat'],
            [
                            'id' => 'title_1content',
                            'label' => 'purchaseautofill::lang.title_1',
                        ],
[
                            'id' => 'topic _5content',
                            'label' => 'purchaseautofill::lang.topic _5',
                        ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('PurchaseAutoFill.qrcodeView', ['id' => $id]);
        $purchaseautofill = PurchaseAutoFill::findOrFail($id);
        $createdby = User::findOrFail($purchaseautofill->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('purchaseautofill::PurchaseAutoFill.qr_view')->with(compact('purchaseautofill','qrcode','link','checkboxes','name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $purchaseautofill_categories = PurchaseAutoFillCategory::forDropdown($business_id);
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

        return view('purchaseautofill::PurchaseAutoFill.create', compact('purchaseautofill_categories','leads', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations'));
    }

     public function show($id, Request $request)
    {
        
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('PurchaseAutoFill.qrcodeView', ['id' => $id]);

        $purchaseautofill = PurchaseAutoFill::where('business_id', $business_id)->findOrFail($id);
        $purchaseautofill = PurchaseAutoFill::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $purchaseautofill->getAttributes();

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
        
        
        $createdby = User::findOrFail($purchaseautofill->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('purchaseautofill::PurchaseAutoFill.show')->with(compact('purchaseautofill','name','print_by', 'date_range','first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchaseautofill_category_id' => 'nullable|integer',
            
            
            
            
            
            
            
            
            
                            'title_1' => 'nullable',
                        

                            'topic _5' => 'nullable',
                                        
        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $purchaseautofill = new PurchaseAutoFill();
            $purchaseautofill->business_id = $business_id;
            $purchaseautofill->category_id = $request->purchaseautofill_category_id;
            $purchaseautofill->created_by = auth()->user()->id;
            $PurchaseAutoFillSocial = PurchaseAutoFillSocial::where('business_id', $business_id)->first();
    
            if ($PurchaseAutoFillSocial && $PurchaseAutoFillSocial->social_status == 1) {
                $BotToken = $PurchaseAutoFillSocial->social_token;
                $ChatId = $PurchaseAutoFillSocial->social_id;
                $message = __('purchaseautofill::lang.purchaseautofill_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }
            
            
            
              
            
            
            
             
            
                            $purchaseautofill->{'title_1'} = $request->{'title_1'};
                        

                            $purchaseautofill->{'topic _5'} = $request->{'topic _5'};
                         
            
            $purchaseautofill->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type'); 
        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        

        $purchaseautofill = PurchaseAutoFill::find($id);
        $purchaseautofill_categories = PurchaseAutoFillCategory::forDropdown($business_id);
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
        return view('purchaseautofill::PurchaseAutoFill.edit', compact('purchaseautofill', 'purchaseautofill_categories', 'users', 'customer', 'supplier', 'product', 'business_locations','departments','designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'purchaseautofill_category_id' => 'nullable|integer',
            
            
            
              
            
            
              
            
                            'title_1' => 'nullable',
                        

                            'topic _5' => 'nullable',
                                        
        ]);

        try {
            $purchaseautofill = PurchaseAutoFill::find($id);
            $purchaseautofill->category_id = $request->purchaseautofill_category_id;
            $purchaseautofill->created_by = auth()->user()->id;
            
            
            
            
            
            
            
              
            
                            $purchaseautofill->{'title_1'} = $request->{'title_1'};
                        

                            $purchaseautofill->{'topic _5'} = $request->{'topic _5'};
                         
            

            $purchaseautofill->save();


            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    

    public function destroy($id)
    {
        try {
            PurchaseAutoFill::destroy($id);
            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = PurchaseAutoFillCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('PurchaseAutoFill-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('PurchaseAutoFill-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('purchaseautofill::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('purchaseautofill::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new PurchaseAutoFillCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'PurchaseAutoFillCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'purchaseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        if ((! auth()->user()->can('module.purchaseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
            
        $category = PurchaseAutoFillCategory::find($id);
        return view('purchaseautofill::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = PurchaseAutoFillCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'PurchaseAutoFillCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
    
    public function destroyCategory($id)
    {
        try {
            PurchaseAutoFillCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('purchaseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}