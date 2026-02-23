<?php

namespace Modules\ExpenseAutoFill\Http\Controllers;

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
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFill;
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFillCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use Modules\ExpenseAutoFill\Entities\ExpenseAutoFillSocial;
use Modules\ExpenseAutoFill\Entities\TelegramExpenseImageData;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;



class ExpenseAutoFillController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $total_expenseautofill = ExpenseAutoFill::where('business_id', $business_id)->count();

        $total_expenseautofill_category = ExpenseAutoFillCategory::where('business_id', $business_id)->count();

        $expenseautofill_category = DB::table('expenseautofill_main as expenseautofill')
            ->leftJoin('expenseautofill_category as expenseautofillcategory', 'expenseautofill.category_id', '=', 'expenseautofillcategory.id')
            ->select(
                DB::raw('COUNT(expenseautofill.id) as total'),
                'expenseautofillcategory.name as category'
            )
            ->where('expenseautofill.business_id', $business_id)
            ->groupBy('expenseautofillcategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('expenseautofill::ExpenseAutoFill.dashboard')
            ->with(compact('total_expenseautofill', 'total_expenseautofill_category', 'expenseautofill_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        // Permission check - adjust permissions as needed
        // if (!auth()->user()->can('expenseautofill.view') && !auth()->user()->can('expenseautofill.create')) {
        //     abort(403, 'Unauthorized action.');
        // }

        if ($request->ajax()) {

            // Query the TelegramExpenseImageData table instead of TelegramOcrData
            $query = TelegramExpenseImageData::where('business_id', $business_id)
                ->select([
                    'id',
                    'telegram_file_id',
                    'telegram_user_first_name',
                    'telegram_user_last_name',
                    'telegram_date',
                    'status',
                    'total_amount',
                    'telegram_file_size',
                    'telegram_width',
                    'telegram_height',
                    'file_path', // Changed from image_path to file_path
                    'supplier',
                    'transaction_date',
                    'location',
                    'category',
                    'ref_no'
                ]);

            // Apply filters
            if (!empty(request()->status)) { // Changed from ocr_status to status
                $query->where('status', request()->status);
            }

           
            if (! empty(request()->start_date) && ! empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $query->whereBetween('telegram_date', [$start, $end]);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
    <button type="button" class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown">
        Actions <span class="caret"></span>
    </button>
    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (auth()->user()->can('expenseautofill.view')) {
                        $html .= '<li><a href="#" class="view-image" data-id="' . $row->id . '">
        <i class="fas fa-eye"></i> View
    </a></li>';
                    }

                    if (auth()->user()->can('expenseautofill.delete')) {
                        $html .= '<li><a href="#" class="delete-image" data-id="' . $row->id . '">
        <i class="fas fa-trash"></i> Delete
    </a></li>';
                    }

                    if (auth()->user()->can('expenseautofill.prefill')) {
                        $url = route('expenseautofill.prefill', $row->id);
                        $html .= '<li><a href="' . $url . '" target="_blank" rel="noopener" class="accept-ocr">
    <i class="fa fa-plug"></i> Set Prefill
</a></li>';
                    }

                    $html .= '</ul></div>';
                    return $html;
                })
                ->addColumn('telegram_from', function ($row) {
                    // Combine first_name and last_name to display the full name
                    return $row->telegram_user_first_name . ' ' . ($row->telegram_user_last_name ?? '');
                })
                ->editColumn('image', function ($row) {
                    if (!$row->file_path) { // Changed from image_path to file_path
                        return '<div style="display: flex;">No Image</div>';
                    }

                    $url = asset($row->file_path); // Changed from image_path to file_path
                    return '<div style="display: flex;"><img src="' . $url . '" alt="Telegram Image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('status', function ($row) { // Changed from ocr_status to status
                    $statusClass = '';
                    switch ($row->status) {
                        case 'stored':
                            $statusClass = 'bg-yellow';
                            break;
                        case 'processing':
                            $statusClass = 'bg-blue';
                            break;
                        case 'processed':
                            $statusClass = 'bg-green';
                            break;
                        case 'failed':
                            $statusClass = 'bg-red';
                            break;
                        default:
                            $statusClass = 'bg-gray';
                    }
                    return '<span class="label ' . $statusClass . '">' . ucfirst($row->status) . '</span>';
                })
                ->editColumn('telegram_date', '{{@format_datetime($telegram_date)}}')
                ->editColumn('total_amount', '<span class="total_amount" data-orig-value="{{$total_amount}}">@format_currency($total_amount)</span>') // Changed from final_total to total_amount
               ->editColumn('telegram_file_size', function ($row) {
                    return $row->telegram_file_size;
                })
                ->addColumn('dimensions', function ($row) {
                    return $row->telegram_width . 'x' . $row->telegram_height;
                })
                ->addColumn('supplier_info', function ($row) {
                    return $row->supplier ?? 'N/A';
                })
                ->addColumn('ref_info', function ($row) {
                    return $row->ref_no ?? 'N/A';
                })
                // Remove columns that are not needed in the final table output
                ->removeColumn('file_path') // Changed from image_path to file_path
                ->removeColumn('telegram_width')
                ->removeColumn('telegram_height')
                ->removeColumn('telegram_user_first_name')
                ->removeColumn('telegram_user_last_name')
                ->removeColumn('supplier')
                ->removeColumn('ref_no')
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can('expenseautofill.view')) {
                            return url("/expenseautofill/bot-image/{$row->id}"); // Use DB ID
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns(['action', 'image', 'status', 'total_amount']) // Changed from ocr_status to status and final_total to total_amount
                ->make(true);
        }

        // For non-ajax requests, prepare data for the view
        $statuses = [ // Changed from ocrStatuses to statuses
            '' => 'All Status',
            'stored' => 'Stored', // Changed from pending to stored
            'processing' => 'Processing',
            'processed' => 'Completed', // Changed from completed to processed
            'failed' => 'Failed',
        ];

        return view('expenseautofill::ExpenseAutoFill.bot-images')
            ->with(compact('statuses')); // Changed from ocrStatuses to statuses
    }

    public function showQrcodeUrl($id)
    {

        $url = route('ExpenseAutoFill.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id)
    {
        $checkboxes = [

            [
                'id' => 'categorycontent',
                'label' => 'expenseautofill::lang.category'
            ],
            [
                'id' => 'qrcontent',
                'label' => 'expenseautofill::lang.qrcode'
            ],
            [
                'id' => 'createdbycontent',
                'label' => 'expenseautofill::lang.createdby'
            ],
            [
                'id' => 'createdatcontent',
                'label' => 'expenseautofill::lang.createdat'
            ],
            [
                'id' => 'title_1content',
                'label' => 'expenseautofill::lang.title_1',
            ],
            [
                'id' => 'topic _5content',
                'label' => 'expenseautofill::lang.topic _5',
            ],

        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('ExpenseAutoFill.qrcodeView', ['id' => $id]);
        $expenseautofill = ExpenseAutoFill::findOrFail($id);
        $createdby = User::findOrFail($expenseautofill->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('expenseautofill::ExpenseAutoFill.qr_view')->with(compact('expenseautofill', 'qrcode', 'link', 'checkboxes', 'name'));
    }

    public function create(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $expenseautofill_categories = ExpenseAutoFillCategory::forDropdown($business_id);
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

        return view('expenseautofill::ExpenseAutoFill.create', compact('expenseautofill_categories', 'leads', 'users', 'customer', 'supplier', 'product', 'business_locations', 'departments', 'designations'));
    }

    public function show($id, Request $request)
    {

        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }
        // $qrcode = $this->showQrcodeUrl($id);
        // $link =  route('ExpenseAutoFill.qrcodeView', ['id' => $id]);

        $expenseautofill = ExpenseAutoFill::where('business_id', $business_id)->findOrFail($id);
        $expenseautofill = ExpenseAutoFill::where('business_id', $business_id)->findOrFail($id);

        // Get all attributes from the model
        $attributes = $expenseautofill->getAttributes();

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


        $createdby = User::findOrFail($expenseautofill->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('expenseautofill::ExpenseAutoFill.show')->with(compact('expenseautofill', 'name', 'print_by', 'date_range', 'first_field'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expenseautofill_category_id' => 'nullable|integer',









            'title_1' => 'nullable',


            'topic _5' => 'nullable',

        ]);

        $business_id = request()->session()->get('user.business_id');
        // $document = $this->transactionUtil->uploadFile($request, 'document', 'tracking');

        try {
            $expenseautofill = new ExpenseAutoFill();
            $expenseautofill->business_id = $business_id;
            $expenseautofill->category_id = $request->expenseautofill_category_id;
            $expenseautofill->created_by = auth()->user()->id;
            $ExpenseAutoFillSocial = ExpenseAutoFillSocial::where('business_id', $business_id)->first();

            if ($ExpenseAutoFillSocial && $ExpenseAutoFillSocial->social_status == 1) {
                $BotToken = $ExpenseAutoFillSocial->social_token;
                $ChatId = $ExpenseAutoFillSocial->social_id;
                $message = __('expenseautofill::lang.expenseautofill_created');

                $Url = "https://api.telegram.org/bot$BotToken/sendMessage";
                Http::post($Url, [
                    'chat_id' => $ChatId,
                    'text' => $message,
                ]);
            }









            $expenseautofill->{'title_1'} = $request->{'title_1'};


            $expenseautofill->{'topic _5'} = $request->{'topic _5'};


            $expenseautofill->save();

            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type');
        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }



        $expenseautofill = ExpenseAutoFill::find($id);
        $expenseautofill_categories = ExpenseAutoFillCategory::forDropdown($business_id);
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
        return view('expenseautofill::ExpenseAutoFill.edit', compact('expenseautofill', 'expenseautofill_categories', 'users', 'customer', 'supplier', 'product', 'business_locations', 'departments', 'designations', 'leads'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expenseautofill_category_id' => 'nullable|integer',








            'title_1' => 'nullable',


            'topic _5' => 'nullable',

        ]);

        try {
            $expenseautofill = ExpenseAutoFill::find($id);
            $expenseautofill->category_id = $request->expenseautofill_category_id;
            $expenseautofill->created_by = auth()->user()->id;









            $expenseautofill->{'title_1'} = $request->{'title_1'};


            $expenseautofill->{'topic _5'} = $request->{'topic _5'};



            $expenseautofill->save();


            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }



    public function destroy($id)
    {
        try {
            ExpenseAutoFill::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $categories = ExpenseAutoFillCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ExpenseAutoFill-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('ExpenseAutoFill-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('expenseautofill::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return view('expenseautofill::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new ExpenseAutoFillCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ExpenseAutoFillCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'expenseautofill')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        if ((! auth()->user()->can('module.expenseautofill')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $category = ExpenseAutoFillCategory::find($id);
        return view('expenseautofill::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = ExpenseAutoFillCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ExpenseAutoFillCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            ExpenseAutoFillCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('expenseautofill::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}
