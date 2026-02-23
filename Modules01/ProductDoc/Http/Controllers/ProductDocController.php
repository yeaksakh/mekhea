<?php

namespace Modules\ProductDoc\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Contact;
use App\Product;
use App\Category;
use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Modules\ProductDoc\Entities\ProductDoc;
use Modules\ProductDoc\Entities\ProductDocCategory;
use Modules\ModuleCreateModule\Entities\ModuleCreator;
use Modules\Crm\Utils\CrmUtil;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Illuminate\Support\Str;



class ProductDocController extends Controller
{
    protected $moduleUtil;
    protected $transactionUtil;
    protected $crmUtil;
    protected $botToken;

    public function __construct(
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        CrmUtil $crmUtil
    ) {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->crmUtil = $crmUtil;
        $this->botToken = '7992170847:AAF8-KNWdC5lG3SCUuc8tkGgDRRc6s-MsVA';
        $this->groupChatId = '3332101476';
    }

    public function dashboard()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productdoc')) || ! auth()->user()->can('superadmin') || ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $total_productdoc = ProductDoc::where('business_id', $business_id)->count();

        $total_productdoc_category = ProductDocCategory::where('business_id', $business_id)->count();

        $productdoc_category = DB::table('productdoc_main as productdoc')
            ->leftJoin('productdoc_category as productdoccategory', 'productdoc.category_id', '=', 'productdoccategory.id')
            ->select(
                DB::raw('COUNT(productdoc.id) as total'),
                'productdoccategory.name as category'
            )
            ->where('productdoc.business_id', $business_id)
            ->groupBy('productdoccategory.id')
            ->get();

        $user_id = auth()->user()->id;

        return view('productdoc::ProductDoc.dashboard')
            ->with(compact('total_productdoc', 'total_productdoc_category', 'productdoc_category', 'module'));
    }

    public function index(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        if ($request->ajax()) {
            $location_id = request()->get('location_id', null);
            $permitted_locations = auth()->user()->permitted_locations();

            $products = Product::with(['media'])
                ->select('products.*')
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->join('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                ->join('variations as v', 'v.product_id', '=', 'products.id')
                ->leftJoin('variation_location_details as vld', function ($join) use ($permitted_locations) {
                    $join->on('vld.variation_id', '=', 'v.id');
                    if ($permitted_locations != 'all') {
                        $join->whereIn('vld.location_id', $permitted_locations);
                    }
                })
                ->whereNull('v.deleted_at')
                ->where('products.business_id', $business_id)
                
                ->distinct()
                ->orderBy('products.id', 'desc');

            // Fetch categories as [id => name] Collection (NOT array)
            $product_doc_categories = ProductDocCategory::where('business_id', $business_id)
                ->pluck('name', 'id'); // Keeps it as Collection

            $categoryCount = $product_doc_categories->count();

            // Optional filters - specify table name for created_at
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $products->whereDate('products.created_at', '>=', $request->start_date)
                    ->whereDate('products.created_at', '<=', $request->end_date);
            }

            if (!empty($request->category_id)) {
                $products->where('products.category_id', $request->category_id);
            }

            if (!empty($request->name)) {
                $products->where('products.name', 'LIKE', '%' . $request->name . '%');
            }

            $datatable = DataTables::of($products)
                ->addColumn('action', function ($product) {
                    $html = '<a href="' . route('ProductDoc.productDoc', ['product_id' => $product->id]) . '" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> ' . __('Show Doc') . '</a> ';
                    return $html;
                })
                ->addColumn('category', function () use ($categoryCount) {
                    return $categoryCount;
                })
                ->addColumn('has_document', function ($product) use ($business_id, $product_doc_categories) {
                    // Use Collection method isEmpty()
                    if ($product_doc_categories->isEmpty()) {
                        return '<span class="text-muted">No categories defined</span>';
                    }

                    // Get all ProductDoc records for this product in relevant categories
                    $docs = ProductDoc::where('business_id', $business_id)
                        ->where('Product_1', $product->id)
                        ->whereIn('category_id', $product_doc_categories->keys())
                        ->get();

                    $missing = [];
                    foreach ($product_doc_categories as $id => $name) {
                        $hasFile = $docs->first(function ($doc) use ($id) {
                            return $doc->category_id == $id && !empty($doc->productFile1_5);
                        });

                        if (!$hasFile) {
                            $missing[] = e($name); // Escape for safety
                        }
                    }

                    if (empty($missing)) {
                        return '<span class="text-success">✅ Complete</span>';
                    }

                    return '<span class="text-danger">❌ Missing: ' . implode(', ', $missing) . '</span>';
                })
                ->editColumn('image', function ($product) {
                    $imageUrl = $product->image_url ?? null;
                    if ($imageUrl) {
                        return '<div style="display: flex; justify-content: center;">
                                <img src="' . e(asset($imageUrl)) . '" alt="Product image" class="product-thumbnail-small" style="max-height: 50px; max-width: 50px; object-fit: cover;">
                            </div>';
                    }
                    return '<div style="display: flex; justify-content: center;"><span class="text-muted">No image</span></div>';
                })
                ->rawColumns(['action', 'image', 'has_document']); // Allow HTML rendering

            return $datatable->make(true);
        }

        // Non-AJAX: render the view
        $categories = Category::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::forDropdown($business_id, false, true, true);

        return view('productdoc::ProductDoc.index', compact(
            'module',
            'categories',
            'users'
        ));
    }

    public function productDoc(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $module = ModuleCreator::where('module_name', 'productdoc')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $dynamicFields = ProductDoc::getDynamicFieldsArray();

        $product_id = $request->query('product_id');
        // if (!$request->ajax()) {

        //     if ($product_id) {
        //         $productExists = Product::where('business_id', $business_id)
        //             ->where('id', $product_id)
        //             ->exists();

        //         if (!$productExists) {
        //             abort(404, 'Product not found.');
        //         }

        //         $existingDoc = ProductDoc::where('business_id', $business_id)
        //             ->where('Product_1', $product_id)
        //             ->first();

        //         if ($existingDoc) {
        //             return redirect()->route('ProductDoc.productDoc')
        //                 ->with('auto_open_productdoc_id', $existingDoc->id)
        //                 ->withInput($request->all());
        //         } else {
        //             return redirect()->route('ProductDoc.productDoc')
        //                 ->with('auto_create_for_product', $product_id)
        //                 ->withInput($request->all());
        //         }
        //     }
        // }

        if ($request->ajax()) {
            $ProductDoc = ProductDoc::where('business_id', $business_id)
                ->with(['Product1'])
                ->orderBy('id', 'desc');

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $ProductDoc->whereDate('created_at', '>=', $request->start_date)
                    ->whereDate('created_at', '<=', $request->end_date);
            }

            if (!empty($request->Product_1)) {
                $ProductDoc->where('Product_1', $request->Product_1);
            }
            if (!empty($product_id)) {
                $ProductDoc->where('Product_1', $product_id);
            }

            if (!empty($request->category_id)) {
                $ProductDoc->where('category_id', $request->category_id);
            }

            $datatable = DataTables::of($ProductDoc)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductDoc.show', $row->id) . '" data-container="#ProductDoc_modal"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button> ';
                    $html .= '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductDoc.edit', $row->id) . '" data-container="#ProductDoc_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button> ';
                    $html .= '<button class="btn btn-xs btn-danger delete-ProductDoc" data-href="' . route('ProductDoc.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->addColumn('category', function ($row) {
                    $category = ProductDocCategory::find($row->category_id);
                    return $category ? $category->name : '';
                })
                ->addColumn('create_by', function ($row) {
                    $user = User::find($row->created_by);
                    return $user ? $user->first_name . ' ' . $user->last_name : '';
                })
                ->editColumn('image', function ($row) {
                    $imageUrl = $row->Product1?->image_url ?? null;

                    if ($imageUrl) {
                        return '<div style="display: flex; justify-content: center;"><img src="' . e(asset($imageUrl)) . '" alt="Product image" class="product-thumbnail-small" style="max-height: 50px; max-width: 50px; object-fit: cover;"></div>';
                    }

                    return '<div style="display: flex; justify-content: center;"><span class="text-muted">No image</span></div>';
                })
                ->addColumn('productFile', function ($row) {
                   $content = $row->productFile1_5 ?? '';

                    // Strip all HTML tags, keep only text
                    $preview = strip_tags($content);

                    // Truncate to 100 chars
                    return strlen($preview) > 100 ? substr($preview, 0, 97) . '...' : $preview;
                })
                ->addColumn('Product_1', function ($row) {
                    return optional($row->Product1)->name ?? '';
                });

            

            return $datatable
                ->rawColumns(array_merge(['action', 'image'])) // ✅ critical for HTML rendering
                ->make(true);
        }

        // Dropdown data
        $users = User::forDropdown($business_id, false, true, true);
        $category = ProductDocCategory::forDropdown($business_id);
        $customer = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)->where('type', 'supplier')->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        return view('productdoc::ProductDoc.product_doc', compact(
            'module',
            'leads',
            'users',
            'customer',
            'product',
            'supplier',
            'business_locations',
            'category',
            'departments',
            'designations',
            'dynamicFields'
        ));
    }

    public function showQrcodeUrl($id)
    {

        $url = route('ProductDoc.qrcodeView', ['id' => $id]);
        $qrcode = QrCode::size(50)->generate($url);
        return $qrcode;
    }

    public function qrcodeView($id)
    {
        $checkboxes = [
            [
                'id' => 'categorycontent',
                'label' => 'productdoc::lang.category'
            ],
            [
                'id' => 'qrcontent',
                'label' => 'productdoc::lang.qrcode'
            ],
            [
                'id' => 'createdbycontent',
                'label' => 'productdoc::lang.createdby'
            ],
            [
                'id' => 'createdatcontent',
                'label' => 'productdoc::lang.createdat'
            ],
            [
                'id' => 'Product_1content',
                'label' => 'productdoc::lang.Product_1',
            ],
        ];

        $qrcode = $this->showQrcodeUrl($id);
        $link =  route('ProductDoc.qrcodeView', ['id' => $id]);
        $productdoc = ProductDoc::findOrFail($id);
        $createdby = User::findOrFail($productdoc->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        return view('productdoc::ProductDoc.qr_view')->with(compact('productdoc', 'qrcode', 'link', 'checkboxes', 'name'));
    }


    public function show($id, Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        $productdoc = ProductDoc::where('business_id', $business_id)
            ->with(['Product1'])
            ->findOrFail($id);

        // Get all attributes from the model
        $attributes = $productdoc->getAttributes();

        // Define columns to EXCLUDE from auto-display
        $excludeColumns = [
            'id',
            'business_id',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
            'dynamic_fields', // handled separately if needed

            // Known fields you render manually in Blade
            'productdoc_category_id',
            'Product_1',
            'productFile1_5',
            'productFile2_6',
            'productFile3_7',
            'productFile4_8',
        ];

        // Auto-detected fields: all columns except excluded ones
        $autoFields = array_diff_key($attributes, array_flip($excludeColumns));
        // dd($autoFields);

        // Find the first field that ends with '1' (for "Subject")
        $first_field = null;
        foreach ($attributes as $fieldName => $fieldValue) {
            if (str_ends_with($fieldName, '1') && !empty($fieldValue)) {
                $first_field = $fieldValue;
                break;
            }
        }

        // Fallback: if no field ending with '1' has value, try Product name
        if ($first_field === null && $productdoc->Product1) {
            $first_field = $productdoc->Product1->name;
        }

        $createdby = User::findOrFail($productdoc->created_by);
        $name = $createdby->first_name . ' ' . $createdby->last_name;
        $print_by = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $date_range = $request->query('date_range');

        return view('productdoc::ProductDoc.show', compact(
            'productdoc',
            'name',
            'print_by',
            'date_range',
            'first_field',
            'autoFields'  // ✅ Pass auto-detected fields to view
        ));
    }


    public function store(Request $request)
{
    // Define validation rules
    $rules = [
        'productdoc_category_id' => 'required|integer|exists:categories,id',
        'Product_1' => 'required|integer|exists:products,id',
        'source_type' => 'required|in:file,text,link,telegram',
    ];

    // Conditional validation based on source type
    switch ($request->source_type) {
        case 'file':
            // Simple validation - let uploadFileTest() handle detailed validation
            $rules['productFile1_5'] = 'required|file|max:' . (config('constants.document_size_limit', 10485760) / 1024);
            break;
        case 'text':
            $rules['text_content'] = 'required|string|min:5';
            break;
        case 'link':
            $rules['link_url'] = 'required|url';
            break;
        case 'telegram':
            $rules['telegram_message_id'] = 'required|string';
            break;
    }

    // Custom error messages
    $messages = [
        'productdoc_category_id.required' => __('productdoc::lang.category_required'),
        'productdoc_category_id.exists' => __('productdoc::lang.category_invalid'),
        'Product_1.required' => __('productdoc::lang.product_required'),
        'Product_1.exists' => __('productdoc::lang.product_invalid'),
        'source_type.required' => __('productdoc::lang.source_type_required'),
        'productFile1_5.required' => __('productdoc::lang.file_required'),
        'productFile1_5.file' => __('productdoc::lang.file_required'),
        'productFile1_5.max' => __('productdoc::lang.file_size_exceeds'),
        'text_content.required' => __('productdoc::lang.text_content_required'),
        'text_content.min' => __('productdoc::lang.text_content_minimum'),
        'link_url.required' => __('productdoc::lang.link_url_required'),
        'link_url.url' => __('productdoc::lang.link_url_invalid'),
        'telegram_message_id.required' => __('productdoc::lang.telegram_message_required'),
    ];

    // Validate the request
    $validator = \Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'msg' => __('messages.validation_error'),
            'errors' => $validator->errors()->toArray()
        ], 422);
    }

    $business_id = $request->session()->get('user.business_id');
    $user_id = auth()->id();

    try {
        // Validate business and user
        if (!$business_id || !$user_id) {
            return response()->json([
                'success' => false,
                'msg' => __('messages.authentication_error')
            ], 401);
        }

        // Create a new ProductDoc record
        $productdoc = new ProductDoc();
        $productdoc->business_id = $business_id;
        $productdoc->category_id = $request->productdoc_category_id;
        $productdoc->created_by = $user_id;
        $productdoc->Product_1 = $request->Product_1;

        // Handle different source types
        switch ($request->source_type) {
            case 'file':
                if (!$request->hasFile('productFile1_5')) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('productdoc::lang.file_required')
                    ], 422);
                }

                // Use uploadFileTest for detailed validation (extension, MIME type, etc.)
                try {
                    $path = $this->transactionUtil->uploadFileTest($request, 'productFile1_5', 'ProductDoc');
                    
                    if (!$path) {
                        return response()->json([
                            'success' => false,
                            'msg' => __('productdoc::lang.file_upload_failed')
                        ], 500);
                    }
                    
                    $productdoc->productFile1_5 = $path;
                    $productdoc->file_type = 'upload';
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'msg' => $e->getMessage()
                    ], 422);
                }
                break;

            case 'text':
                $textContent = trim($request->text_content);

                if (empty($textContent)) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('productdoc::lang.text_content_required')
                    ], 422);
                }

                $productdoc->productFile1_5 = $textContent;
                $productdoc->file_type = 'text';
                break;

            case 'link':
                $linkUrl = trim($request->link_url);

                if (empty($linkUrl)) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('productdoc::lang.link_url_required')
                    ], 422);
                }

                // Validate URL format
                if (!filter_var($linkUrl, FILTER_VALIDATE_URL)) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('productdoc::lang.link_url_invalid')
                    ], 422);
                }

                $productdoc->productFile1_5 = $linkUrl;
                $productdoc->file_type = 'link';
                break;

            case 'telegram':
                $telegramId = trim($request->telegram_message_id);

                if (empty($telegramId)) {
                    return response()->json([
                        'success' => false,
                        'msg' => __('productdoc::lang.telegram_message_required')
                    ], 422);
                }

                $productdoc->productFile1_5 = $telegramId;
                $productdoc->file_type = 'telegram';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'msg' => __('productdoc::lang.invalid_source_type')
                ], 422);
        }

        // Save the record
        if (!$productdoc->save()) {
            return response()->json([
                'success' => false,
                'msg' => __('productdoc::lang.save_failed')
            ], 500);
        }

        return response()->json([
            'success' => true,
            'msg' => __('productdoc::lang.added_successfully'),
            'productdoc_id' => $productdoc->id,
            'product_id' => $productdoc->Product_1,
            'source_type' => $request->source_type
        ], 201);

    } catch (\PDOException $e) {
        \Log::error('Database error in ProductDoc store: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'msg' => __('messages.database_error')
        ], 500);
    } catch (\Exception $e) {
        \Log::error('Error in ProductDoc store: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'msg' => __('messages.something_went_wrong'),
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}

    public function update(Request $request, $id)
{
    // Base validation rules
    $rules = [
        'productdoc_category_id' => 'nullable|integer|exists:categories,id',
        'Product_1' => 'required|integer|exists:products,id',
        'source_type' => 'required|in:file,telegram,text,link',
    ];

    // Conditional validation
    if ($request->source_type === 'telegram') {
        $rules['telegram_message_id'] = 'required|string';
    } elseif ($request->source_type === 'text') {
        $rules['text_content'] = 'required|string|max:65535';
    } elseif ($request->source_type === 'link') {
        $rules['link_url'] = 'required|url|max:500';
    }

    // File validation only if uploaded
    if ($request->hasFile('productFile1_5')) {
        $rules['productFile1_5'] = 'file|mimes:' . implode(',', array_keys(config('constants.document_upload_mimes_types', []))) . '|max:' . (config('constants.document_size_limit', 2000000) / 1024);
    }

    $validatedData = $request->validate($rules);

    try {
        $productdoc = ProductDoc::findOrFail($id);
        $business_id = $request->session()->get('user.business_id');

        // if ($productdoc->business_id !== $business_id) {
        //     return response()->json(['success' => false, 'msg' => __('messages.unauthorized')], 403);
        // }

        // Update shared fields
        $productdoc->category_id = $request->productdoc_category_id;
        $productdoc->Product_1 = $request->Product_1;

        // Clear irrelevant fields based on new source_type
        $productdoc->productFile1_5 = null;
        // Handle each source type
        if ($request->source_type === 'telegram') {
            $productdoc->productFile1_5 = $request->telegram_message_id;
            $productdoc->file_type = 'telegram';
        } elseif ($request->source_type === 'text') {
            $productdoc->productFile1_5 = $request->text_content;
            $productdoc->file_type = 'text';
        } elseif ($request->source_type === 'link') {
            $productdoc->productFile1_5 = $request->link_url;
            $productdoc->file_type = 'link';
        } elseif ($request->source_type === 'file') {
            // Keep existing file unless a new one is uploaded
            if ($request->hasFile('productFile1_5')) {
                // Delete old file if it's an uploaded file (not Telegram/text/link)
                if ($productdoc->productFile1_5 && in_array($productdoc->file_type, ['file', 'upload'])) {
                    $oldFilePath = public_path('uploads/ProductDoc/' . basename($productdoc->productFile1_5));
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }

                $file = $request->file('productFile1_5');
                $mime_type = $file->getClientMimeType();
                $documentPath = $this->transactionUtil->uploadFileTest($request, 'productFile1_5', 'ProductDoc');

                if ($documentPath) {
                    $productdoc->productFile1_5 = $documentPath;
                    $productdoc->file_type = 'upload';
                } else {
                    throw new \Exception('File upload failed');
                }
            } else {
                // Preserve existing file if no new upload
                // (Only if current type is file/upload — but we already cleared it above!)
                // So we must **restore** it if source_type is 'file' and no new file
                // Re-fetch original to avoid loss
                $original = ProductDoc::findOrFail($id);
                if (in_array($original->file_type, ['file', 'upload']) && $original->productFile1_5) {
                    $productdoc->productFile1_5 = $original->productFile1_5;
                    $productdoc->file_type = $original->file_type;
                }
            }
        }

        $productdoc->save();

        return response()->json([
            'success' => true,
            'msg' => __('productdoc::lang.updated_successfully'),
            'productdoc_id' => $productdoc->id,
            'source_type' => $request->source_type
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json(['success' => false, 'msg' => $e->getMessage(), 'errors' => $e->errors()], 422);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json(['success' => false, 'msg' => $e->getMessage()], 404);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong') . ': ' . $e->getMessage()], 500);
    }
}

    // ========================================

    // In your create() method, pass dynamic fields to view:

    public function create(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $module = ModuleCreator::where('module_name', 'productdoc')->first();
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if (! (auth()->user()->can('module.productdoc') || auth()->user()->can('superadmin') || $is_admin)) {
        //     abort(403, 'Unauthorized action.');
        // }

        $productdoc_categories = ProductDocCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)->where('type', 'supplier')->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        // Get dynamic fields
        $dynamicFields = ProductDoc::getDynamicFields();

        return view('productdoc::ProductDoc.create', compact(
            'productdoc_categories',
            'users',
            'customer',
            'supplier',
            'product',
            'business_locations',
            'departments',
            'designations',
            'leads',
            'module',
            'dynamicFields'  // Add this
        ));
    }

    // ========================================

    // In your edit() method, pass dynamic fields to view:

    public function edit(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');
        $type = $request->query('type');
        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productdoc')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $productdoc = ProductDoc::find($id);
        $productdoc_categories = ProductDocCategory::forDropdown($business_id);
        $users = User::forDropdown($business_id, false);
        $customer = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $supplier = Contact::where('business_id', $business_id)->where('type', 'supplier')->pluck('supplier_business_name', 'id');
        $product = Product::where('business_id', $business_id)->pluck('name', 'id');
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        $departments = Category::where('business_id', $business_id)->where('category_type', 'hrm_department')->pluck('name', 'id');
        $designations = Category::where('business_id', $business_id)->where('category_type', 'hrm_designation')->pluck('name', 'id');
        $leads = $this->crmUtil->getLeadsListQuery($business_id);

        // Get dynamic fields
        $dynamicFields = ProductDoc::getDynamicFields();

        return view('productdoc::ProductDoc.edit', compact(
            'productdoc',
            'productdoc_categories',
            'users',
            'customer',
            'supplier',
            'product',
            'business_locations',
            'departments',
            'designations',
            'leads',
            'dynamicFields'  // Add this
        ));
    }



    public function destroy($id)
    {
        try {
            ProductDoc::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productdoc::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getCategories(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productdoc')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        if (request()->ajax()) {
            $categories = ProductDocCategory::where('business_id', $business_id)->orderBy('id', 'desc')->get();

            return DataTables::of($categories)
                ->addColumn('action', function ($row) {
                    $html = '<button class="btn btn-xs btn-info btn-modal" data-href="' . route('ProductDoc-categories.edit', $row->id) . '" data-container=".category_modal"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</button>';
                    $html .= ' <button class="btn btn-xs btn-danger delete-category" data-href="' . route('ProductDoc-categories.destroy', $row->id) . '"><i class="fa fa-trash"></i> ' . __('messages.delete') . '</button>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('productdoc::Category.index')->with(compact('module'));
    }

    public function createCategory()
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productdoc')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        return view('productdoc::Category.create');
    }

    public function storeCategory(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = new ProductDocCategory();
            $category->name = $request->name;
            if ($request->hasFile('image')) {
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ProductDocCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->business_id = $business_id;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.saved_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function editCategory($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $module = ModuleCreator::where('module_name', 'productdoc')->first();

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if ((! auth()->user()->can('module.productdoc')) && ! auth()->user()->can('superadmin') && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $category = ProductDocCategory::find($id);
        return view('productdoc::Category.edit', compact('category'));
    }

    public function updateCategory(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        try {
            $category = ProductDocCategory::find($id);
            $category->name = $request->name;
            $category->business_id = $business_id;
            if ($request->hasFile('image')) {
                $oldFile = public_path('uploads/tracking/' . basename($category->{'image'}));
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $documentPath = $this->transactionUtil->uploadFile($request, 'image', 'ProductDocCategory');
                $category->{'image'} = $documentPath;
            }
            $category->description = $request->description;
            $category->save();

            return response()->json(['success' => true, 'msg' => __('productdoc::lang.updated_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function destroyCategory($id)
    {
        try {
            ProductDocCategory::destroy($id);
            return response()->json(['success' => true, 'msg' => __('productdoc::lang.deleted_successfully')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }

    public function getTelegramMessages(Request $request)
    {
        $botToken = $this->botToken;
        $filePath = base_path('FOXEST_ALL_MEDIA.json');

        // Final associative array: message_id => message
        $allMessagesById = [];

        // ===== 1. Load existing messages from JSON file =====
        if (file_exists($filePath)) {
            $jsonContent = file_get_contents($filePath);
            $data = json_decode($jsonContent, true);

            if (is_array($data)) {
                foreach ($data as $item) {
                    $msgId = $item['message_id'] ?? null;
                    if ($msgId !== null) {
                        // Use message_id as key to avoid duplicates later
                        $allMessagesById[$msgId] = $item;
                    }
                }
            }
        }

        // ===== 2. Fetch new messages from Telegram (if token is available) =====
        if ($botToken) {
            try {
                $response = Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/getUpdates");

                if ($response->successful()) {
                    $updates = $response->json('result', []);

                    if (is_array($updates)) {
                        foreach ($updates as $update) {
                            if (!isset($update['message'])) {
                                continue;
                            }

                            $message = $update['message'];
                            $messageId = $message['message_id'] ?? null;
                            if ($messageId === null) {
                                continue;
                            }

                            $from = $message['from'] ?? [];
                            $senderName = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
                            if ($senderName === '') {
                                $senderName = $from['username'] ?? 'Unknown';
                            }

                            $text = $message['text'] ?? null;
                            $date = $message['date'] ?? null; // Unix timestamp
                            $formattedDate = $date ? date('Y-m-d H:i:s', $date) : null;

                            // Media extraction
                            $mediaData = [];
                            $type = 'Text';

                            if (isset($message['photo'])) {
                                $type = 'Photo';
                                $photos = $message['photo'];
                                $largestPhoto = end($photos);
                                $mediaData = [
                                    'type' => 'photo',
                                    'file_id' => $largestPhoto['file_id'] ?? null,
                                    'file_name' => null,
                                    'size_bytes' => $largestPhoto['file_size'] ?? null,
                                    'mime_type' => null,
                                ];
                            } elseif (isset($message['video'])) {
                                $type = 'Video';
                                $v = $message['video'];
                                $mediaData = [
                                    'type' => 'video',
                                    'file_id' => $v['file_id'] ?? null,
                                    'file_name' => $v['file_name'] ?? null,
                                    'size_bytes' => $v['file_size'] ?? null,
                                    'mime_type' => $v['mime_type'] ?? null,
                                ];
                            } elseif (isset($message['document'])) {
                                $type = 'Document';
                                $d = $message['document'];
                                $mediaData = [
                                    'type' => 'document',
                                    'file_id' => $d['file_id'] ?? null,
                                    'file_name' => $d['file_name'] ?? null,
                                    'size_bytes' => $d['file_size'] ?? null,
                                    'mime_type' => $d['mime_type'] ?? null,
                                ];
                            } elseif (isset($message['audio'])) {
                                $type = 'Audio';
                                $a = $message['audio'];
                                $mediaData = [
                                    'type' => 'audio',
                                    'file_id' => $a['file_id'] ?? null,
                                    'file_name' => $a['file_name'] ?? null,
                                    'size_bytes' => $a['file_size'] ?? null,
                                    'mime_type' => $a['mime_type'] ?? null,
                                ];
                            } elseif (isset($message['voice'])) {
                                $type = 'Voice';
                                $v = $message['voice'];
                                $mediaData = [
                                    'type' => 'voice',
                                    'file_id' => $v['file_id'] ?? null,
                                    'file_name' => null,
                                    'size_bytes' => $v['file_size'] ?? null,
                                    'mime_type' => $v['mime_type'] ?? null,
                                ];
                            }

                            // Build message in consistent format
                            $processedMessage = [
                                'message_id' => $messageId,
                                'from' => $senderName,
                                'text' => $text,
                                'caption' => $message['caption'] ?? null,
                                'date' => $formattedDate,
                                'type' => $type,
                                'sender_id' => $from['id'] ?? null,
                                'sender_username' => $from['username'] ?? null,
                                'is_outgoing' => false, // Bots only receive incoming
                                'is_forwarded' => isset($message['forward_from']) || isset($message['forward_from_chat']),
                                'media' => $mediaData,
                                'message_type' => $type,
                            ];

                            // ✅ Overwrite or add: ensures no duplicate message_id
                            $allMessagesById[$messageId] = $processedMessage;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Optionally log: \Log::warning('Telegram fetch failed: ' . $e->getMessage());
                // Continue with existing data only
            }
        }

        // ===== 3. Convert to indexed array and sort =====
        $combinedMessages = array_values($allMessagesById);

        usort($combinedMessages, function ($a, $b) {
            $dateA = strtotime($a['date'] ?? '1970-01-01');
            $dateB = strtotime($b['date'] ?? '1970-01-01');
            return $dateB <=> $dateA; // Descending: newest first
        });

        // ===== 4. Return JSON response =====
        return response()->json([
            'messages' => $combinedMessages,
            'total' => count($combinedMessages),
        ]);
    }

    public function stream(string $fileId)
    {
        $botToken = $this->botToken;

        $fileInfo = Http::timeout(60)->get("https://api.telegram.org/bot{$botToken}/getFile", [
            'file_id' => $fileId
        ]);


        $filePath = $fileInfo->json('result.file_path');
        $videoUrl = "https://api.telegram.org/file/bot{$botToken}/{$filePath}";

        // Redirect to video (browser will play it)
        return redirect($videoUrl);
    }


}
